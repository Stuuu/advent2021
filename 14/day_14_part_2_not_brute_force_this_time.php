<?php

// Template:     NNCB
// After step 1: NCNBCHB
// After step 2: NBCCNBBCBHCB
// After step 3: NBBBCNCCNBBNBNBBCHBHHBCHB
// After step 4: NBBNBNBBCCNBCNCCNBBNBBNBBBNBBNBBCBHCBHHNHCBBCBHCB

class PolymerpolymerizationModler
{

    // const INPUT_FILE_PATH = 'test_puzzle_inputs.txt';
    const INPUT_FILE_PATH = 'puzzle_inputs.txt';
    const STEPS = 40;


    private array $insertion_pairs = [];
    private array $polymer_template = [];

    public function __construct()
    {
        $inputs = file(self::INPUT_FILE_PATH);
        $this->insertion_pairs = self::parseAndSetPolymerInsertionPairs($inputs);
        $this->polymer_template = str_split(trim($inputs[0]));
    }

    public function run()
    {

        echo '<pre>';


        // Template:  NNCB
        // Break template into pairs
        // NN NC CB
        $polymer_pair_count = count($this->polymer_template) - 1;
        $pairs = [];
        for ($x = 0; $x < $polymer_pair_count; $x++) {
            if (isset($pairs[$this->polymer_template[$x] . $this->polymer_template[$x + 1]])) {
                $pairs[$this->polymer_template[$x] . $this->polymer_template[$x + 1]]++;
            } else {
                $pairs[$this->polymer_template[$x] . $this->polymer_template[$x + 1]] = 1;
            }
        }


        $this->chainPolymer(
            $pairs,
            1
        );
    }


    private function chainPolymer(
        array $pairs,
        int $current_step
    ) {
        echo 'Step ' . $current_step . PHP_EOL;
        $new_pairs = [];
        foreach ($pairs as $pair_chars => $pair_count) {


            $insert_char = $this->insertion_pairs[$pair_chars];
            $first_pair = $pair_chars[0] . $insert_char;
            $next_pair  = $insert_char . $pair_chars[1];


            if (isset($new_pairs[$first_pair])) {
                $new_pairs[$first_pair] += $pair_count;
            } else {
                $new_pairs[$first_pair] = $pair_count;
            }

            if (isset($new_pairs[$next_pair])) {
                $new_pairs[$next_pair] += $pair_count;
            } else {
                $new_pairs[$next_pair] = $pair_count;
            }
        }
        if ($current_step >= self::STEPS) {
            $this->calculateResults($new_pairs);
            die;
        };
        $current_step++;
        $this->chainPolymer($new_pairs, $current_step);
    }

    private function calculateResults(array $pairs)
    {
        $results = [];
        foreach ($pairs as $pair_chars => $pair_count) {
            // if we already have a count for a given char increment it
            if (isset($results[$pair_chars[0]])) {
                $results[$pair_chars[0]] += $pair_count;
            } else { // we don't yet have a count so start one
                $results[$pair_chars[0]] = $pair_count;
            }
        }

        // because the last char doesn't have a pair, we need to add it seperately to results
        $remaing_char_from_end_of_template = end($this->polymer_template);
        if (isset($results[$remaing_char_from_end_of_template])) {
            $results[$remaing_char_from_end_of_template]++;
        } else { // it's posible this char is totally new to us so we should account for it being unique
            $results[$remaing_char_from_end_of_template] = 1;
        }

        // sort array by value counts
        arsort($results);
        $highest_char = array_key_first($results);
        $lowest_char = array_key_last($results);
        echo '<pre>';
        echo 'highest char: <b>' . $highest_char . '</b> count: ' . $results[$highest_char] . PHP_EOL;
        echo 'lowest char: <b>' . $lowest_char . '</b> count: ' . $results[$lowest_char] . PHP_EOL;
        echo 'High count - low count: ' . $results[$highest_char] - $results[$lowest_char] . PHP_EOL;
        print_r($results);
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
