<?php

// Template:     NNCB
// After step 1: NCNBCHB
// After step 2: NBCCNBBBCBHCB
// After step 3: NBBBCNCCNBBNBNBBCHBHHBCHB
// After step 4: NBBNBNBBCCNBCNCCNBBNBBNBBBNBBNBBCBHCBHHNHCBBCBHCBV

class PolymerpolymerizationModler
{

    const TEST_INPUT_FILE_PATH = 'test_puzzle_inputs.txt';
    const TEST_STEPS = 10;


    const INPUT_FILE_PATH = 'puzzle_inputs.txt';
    const STEPS = 10;

    public function run()
    {
        $inputs = file(self::INPUT_FILE_PATH);

        $polymer_template = str_split(trim($inputs[0]));

        $poly_insertion_pairs = self::parseAndSetPolymerInsertionPairs($inputs);


        echo '<pre>';
        print_r($poly_insertion_pairs);
        $polymer_chain = [];
        $result = [];
        for ($i = 0; $i < self::STEPS; $i++) {
            if ($i) {
                print_r($polymer_template);
            }

            $polymer_pair_count = count($polymer_template) - 1;

            // Template:  NNCB
            // Break template into pairs 
            // NN NC CB
            // Another example: NCNBCHB = 7 chars
            // NC CN NB BC CH HB  = 6 pairs
            $pairs_to_match = [];
            for ($x = 0; $x < $polymer_pair_count; $x++) {
                $pairs_to_match[] = [
                    $polymer_template[$x],
                    $polymer_template[$x + 1],
                ];
            }


            // Incert polymer into each pair
            // NNCB
            // NN NC CB
            // After step 1: NCNBCHB
            $polymer_chain = [];
            foreach ($pairs_to_match as $key => $pair) {
                $polymer_chain[] = $pair[0];
                $polymer_chain[] = $poly_insertion_pairs[$pair[0] . $pair[1]];
            }
            $polymer_chain[] = end($polymer_template);
            $letter_frequency_count = array_count_values($polymer_chain);
            $min = min($letter_frequency_count);
            $max = max($letter_frequency_count);
            $result[] = [
                'string' => implode('', $polymer_chain),
                'counts' => $letter_frequency_count,
                'min' => $min,
                'max' => $max,
                'max - min' => $max - $min,
            ];
            $polymer_template = $polymer_chain;
        }

        print_r(end($result));
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
}

(new PolymerpolymerizationModler())->run();
