<?php

// Template:     NNCB
// After step 1: NCNBCHB
// After step 2: NBCCNBBCBHCB -> NBCCNBBCBHCB
// After step 3: NBBBCNCCNBBNBNBBCHBHHBCHB
// After step 4: NBBNBNBBCCNBCNCCNBBNBBNBBBNBBNBBCBHCBHHNHCBBCBHCB

class PolymerpolymerizationModler
{

    const TEST_INPUT_FILE_PATH = 'test_puzzle_inputs.txt';
    const TEST_STEPS = 10;


    const INPUT_FILE_PATH = 'puzzle_inputs.txt';
    const STEPS = 20;

    public function run()
    {
        $this->createDbAndTable();
        // one of many hacks to get this to run
        ini_set('memory_limit', '10G');
        $inputs = file(self::INPUT_FILE_PATH);
        $poly_insertion_pairs = self::parseAndSetPolymerInsertionPairs($inputs);

        $polymer_template = str_split(trim($inputs[0]));
        $this->loadTemplateIntoTable($polymer_template);

        for ($i = 0; $i < self::STEPS; $i++) {
            echo "Step: {$i}" . PHP_EOL;
            // reads from polymer_chain
            // writes to pairs_to_match
            $this->insertPairsToMatch();
            $last_polymer_incoming_chain = $this->getLastPolymerFromTemplate();
            $this->truncateTable('polymer_chain');


            // Insert polymer into each pair
            // NNCB
            // NN NC CB
            // After step 1: NCNBCHB
            // reads from pairs_to_match
            foreach ($this->getPairsToMatch() as $key => $pair) {

                // add first char to polymer chain 
                $this->addToPolymerChain($pair[0]);

                $this->addToPolymerChain($poly_insertion_pairs[$pair]);
            }
            // Append last polymer in template to polymer chain.
            $this->addToPolymerChain(
                // $this->getLastPolymerFromTemplate()
                $last_polymer_incoming_chain
            );
        }

        $this->calculateResults();
    }


    public function calculateResults()
    {

        $result = $this->db->query(
            "SELECT polymer, count(polymer) poly_count
            FROM polymer_chain
            GROUP BY polymer
            ORDER BY poly_count DESC;"
        )->fetchAll();

        foreach ($result as $row) {
            echo $row['polymer'] . "  " . $row['poly_count'] .  PHP_EOL;
        }
    }

    public function addToPolymerChain(string $polymer)
    {
        $this->db->beginTransaction();
        $this->db->prepare("INSERT INTO polymer_chain (polymer) VALUES (?)")->execute([$polymer]);
        $this->db->commit();
    }

    public function getPairsToMatch(): Generator
    {
        $result = $this->db->query("SELECT pair FROM pairs_to_match")
            ->fetchAll(PDO::FETCH_COLUMN);
        foreach ($result as $row) {
            yield $row;
        }
        $this->truncateTable('pairs_to_match');
        return;
    }

    /**
     * breaks polymer template into matchable pairs 
     * stores them in table: pairs_to_match
     *
     * @return void
     */
    public function insertPairsToMatch()
    {
        $polymer_pair_count = $this->getTemplatePartsCount() - 1;
        // Template:  NNCB
        // Break template into pairs 
        // NN NC CB
        // Another example: NCNBCHB = 7 chars
        // NC CN NB BC CH HB  = 6 pairs
        $this->db->beginTransaction();
        for ($x = 0; $x < $polymer_pair_count; $x++) {
            $pair = $this->fetchPolymerPair($x);
            $this->db->prepare("INSERT INTO pairs_to_match (pair) VALUES (?)")->execute([$pair]);
        }
        $this->db->commit();
    }

    public function fetchPolymerPair(int $offset): string
    {
        $polymer_pair = '';
        $result = $this->db->query("SELECT polymer FROM polymer_chain LIMIT 2 OFFSET {$offset}")
            ->fetchAll(PDO::FETCH_COLUMN);

        foreach ($result as $row) {
            $polymer_pair .= $row;
        }

        return $polymer_pair;
    }

    public static function parseAndSetPolymerInsertionPairs(array $inputs): array
    {
        // remove the first two elements from the array
        // as 1 is template and 2 is empty
        array_shift($inputs);
        array_shift($inputs);

        $pasred_insertion_pairs = [];
        foreach ($inputs as $key => $insertion_pair_value) {
            $pair_parts = explode('->', trim($insertion_pair_value));

            $pasred_insertion_pairs[trim($pair_parts[0])] = trim($pair_parts[1]);
        }

        return $pasred_insertion_pairs;
    }

    public function createDbAndTable()
    {

        try {
            $this->db = new PDO('mysql:host=localhost;dbname=advent', 'root', '');
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        $this->db->exec("CREATE TABLE IF NOT EXISTS pairs_to_match( pair varchar(2))");

        $this->db->exec("CREATE TABLE IF NOT EXISTS polymer_chain(
            ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
             polymer varchar(1))");




        $this->truncateTable('polymer_chain');
        $this->truncateTable('pairs_to_match');
    }

    public function loadTemplateIntoTable(array $polymer_template)
    {
        $this->db->beginTransaction();
        foreach ($polymer_template as $key => $polymer) {
            $this->db->prepare("INSERT INTO polymer_chain (polymer) VALUES (?)")->execute([$polymer]);
        }
        $this->db->commit();
    }

    /**
     * Get number of Chars in a polymer template
     *
     * @return integer 
     */
    public function getTemplatePartsCount(): int
    {
        $result = $this->db->query('SELECT count(1) FROM polymer_chain LIMIT 1')->fetchAll(PDO::FETCH_COLUMN);
        return (int) $result[0];
    }

    /**
     * Get last polymer from current template
     *
     * @return integer 
     */
    public function getLastPolymerFromTemplate(): string
    {
        $result = $this->db->query('SELECT polymer FROM polymer_chain ORDER BY ID DESC LIMIT 1')
            ->fetchAll(PDO::FETCH_COLUMN);
        return $result[0];
    }

    public function truncateTable(string $table_name)
    {
        // truncate table
        // $this->db->beginTransaction();
        $this->db->exec("TRUNCATE TABLE {$table_name}");
        // $this->db->commit();
    }
}

(new PolymerpolymerizationModler())->run();
