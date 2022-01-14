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
    const STEPS = 40;

    public function run()
    {
        $this->createDbAndTable();
        // one of many hacks to get this to run
        ini_set('memory_limit', '5G');
        $inputs = file(self::TEST_INPUT_FILE_PATH);
        $poly_insertion_pairs = self::parseAndSetPolymerInsertionPairs($inputs);

        $polymer_template = str_split(trim($inputs[0]));
        $this->loadTemplateIntoTable($polymer_template);

        for ($i = 0; $i < self::TEST_STEPS; $i++) {
            // reads from polymer_template
            // writes to pairs_to_match
            $this->insertPairsToMatch();


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
                $this->getLastPolymerFromTemplate()
            );

            // clear table polymer_template
            $this->truncateTable('polymer_template');

            $this->writePolymerChainToPolymerTemplateTable();
        }

        $this->calculateResults();
    }

    /**
     * Copy polymer chain table to polymer_template
     *
     * @return void
     */
    public function writePolymerChainToPolymerTemplateTable()
    {
        $result = $this->db->query("SELECT polymer FROM polymer_chain");
        $this->db->exec('BEGIN;');
        while ($row = $result->fetchArray()) {
            $this->db->exec("INSERT INTO polymer_template ('polymer') 
            VALUES ('{$row[0]}')");
        }
        $this->db->exec('COMMIT;');
        $this->truncateTable('polymer_chain');

        return;
    }

    public function calculateResults()
    {

        $result = $this->db->query(
            "SELECT polymer, count(polymer) poly_count
            FROM polymer_template
            GROUP BY polymer
            ORDER BY poly_count DESC;"
        );

        while ($row = $result->fetchArray()) {
            echo $row['polymer'] . "  " . $row['poly_count'] .  PHP_EOL;
        }
    }

    public function addToPolymerChain(string $polymer)
    {
        $this->db->exec('BEGIN;');
        $this->db->exec("INSERT INTO polymer_chain ('polymer') 
            VALUES ('{$polymer}')");
        $this->db->exec('COMMIT;');
    }

    public function getPairsToMatch(): Generator
    {
        $result = $this->db->query("SELECT pair FROM pairs_to_match");
        while ($row = $result->fetchArray()) {
            yield $row[0];
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
        $this->db->exec('BEGIN;');
        for ($x = 0; $x < $polymer_pair_count; $x++) {
            $pair = $this->fetchPolymerPair($x);
            $this->db->exec("INSERT INTO pairs_to_match ('pair') 
            VALUES ('{$pair}')");
        }
        $this->db->exec('COMMIT;');
    }

    public function fetchPolymerPair(int $offset): string
    {
        $polymer_pair = '';
        $result = $this->db->query("SELECT polymer FROM polymer_template LIMIT 2 OFFSET {$offset}");
        while ($row = $result->fetchArray()) {
            $polymer_pair .= $row[0];
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
        $this->db = new SQLite3('day_14_part_2.db');
        $this->db->busyTimeout(5000);
        $this->db->query("PRAGMA synchronous = OFF");

        $this->db->exec("CREATE TABLE IF NOT EXISTS pairs_to_match( pair TEXT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS polymer_template(
             ID INTEGER PRIMARY KEY AUTOINCREMENT,
             polymer TEXT
             )");
        $this->db->exec("CREATE TABLE IF NOT EXISTS polymer_chain( polymer TEXT)");


        $this->truncateTable('polymer_template');
        $this->truncateTable('polymer_chain');
        $this->truncateTable('pairs_to_match');
    }

    public function loadTemplateIntoTable(array $polymer_template)
    {
        $this->db->exec('BEGIN;');
        foreach ($polymer_template as $key => $polymer) {
            $this->db->exec("INSERT INTO polymer_template ('polymer') 
            VALUES ('{$polymer}')");
        }
        $this->db->exec('COMMIT;');
    }

    /**
     * Get number of Chars in a polymer template
     *
     * @return integer 
     */
    public function getTemplatePartsCount(): int
    {
        $result = $this->db->query('SELECT count(1) FROM polymer_template LIMIT 1');
        return (int) $result->fetchArray()[0];
    }

    /**
     * Get last polymer from current template
     *
     * @return integer 
     */
    public function getLastPolymerFromTemplate(): string
    {
        $result = $this->db->query('SELECT polymer FROM polymer_template ORDER BY ID DESC LIMIT 1');
        return $result->fetchArray()[0];
    }

    public function truncateTable(string $table_name)
    {
        // truncate table
        $this->db->exec("DELETE FROM {$table_name}");
    }
}

(new PolymerpolymerizationModler())->run();
