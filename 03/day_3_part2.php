<?php

// For example, to determine the oxygen generator rating value using the same example diagnostic report from above:

// Start with all 12 numbers and consider only the first bit of each number. There are more 1 bits (7) than 0 bits (5), so keep only the 7 numbers with a 1 in the first position: 11110, 10110, 10111, 10101, 11100, 10000, and 11001.
// Then, consider the second bit of the 7 remaining numbers: there are more 0 bits (4) than 1 bits (3), so keep only the 4 numbers with a 0 in the second position: 10110, 10111, 10101, and 10000.
// In the third position, three of the four numbers have a 1, so keep those three: 10110, 10111, and 10101.
// In the fourth position, two of the three numbers have a 1, so keep those two: 10110 and 10111.
// In the fifth position, there are an equal number of 0 bits and 1 bits (one each). So, to find the oxygen generator rating, keep the number with a 1 in that position: 10111.
// As there is only one number left, stop; the oxygen generator rating is 10111, or 23 in decimal.




class DiagnosticDecoder
{

    private $oxygen_rating = null;
    private $co2_rating;

    public function run()
    {

        $this->reducer(null, 0, 'oxygen');
        $this->reducer(null, 0, 'co2');
        $oxy_rating_decimal = bindec($this->oxygen_rating);
        $co2_rating_decimal = bindec($this->co2_rating);
        echo 'oxygen rating: ' . bindec($this->oxygen_rating) . PHP_EOL;
        echo 'co2 rating: ' . bindec($this->co2_rating) . PHP_EOL;
        echo 'life support rating: ' . ($oxy_rating_decimal * $co2_rating_decimal), PHP_EOL;
    }

    public  function reducer($debug_output = null, $current_key = 0, $desired_rating = null)
    {
        if (is_null($debug_output)) {
            // put bits into an array
            $debug_output = file('puzzle_inputs.txt', FILE_IGNORE_NEW_LINES);
        }

        // determine bit count for index
        $bit_counts_for_index = self::tabulateBitForIndex($debug_output, $current_key);

        if ($bit_counts_for_index['one'] > $bit_counts_for_index['zero']) {
            foreach ($debug_output as $binary) {

                if (($binary[$current_key] == 1)) {
                    $oxygen_reduced_set[] = $binary;
                } else {
                    $co2_reduced_set[] = $binary;
                }
            }
        } elseif ($bit_counts_for_index['zero'] > $bit_counts_for_index['one']) {

            foreach ($debug_output as $binary) {

                if (($binary[$current_key] == 0)) {
                    $oxygen_reduced_set[] = $binary;
                } else {
                    $co2_reduced_set[] = $binary;
                }
            }
        } else { // if most common bit is equal for the index prefer 1 for oxygen & 0 for co2
            foreach ($debug_output as $binary) {
                if (($binary[$current_key] == 1)) {
                    $oxygen_reduced_set[] = $binary;
                } else {
                    $co2_reduced_set[] = $binary;
                }
            }
        }

        $current_key++;
        print_r($oxygen_reduced_set);
        if ($desired_rating === 'oxygen') {
            if (count($oxygen_reduced_set) == 1) {
                $this->oxygen_rating = $oxygen_reduced_set[0];
                return;
            } elseif (!$this->oxygen_rating) {
                $this->reducer($oxygen_reduced_set, $current_key, 'oxygen');
            }
        } else {
            if (count($co2_reduced_set) == 1) {
                $this->co2_rating = $co2_reduced_set[0];
                return;
            } elseif (!$this->co2_rating) {
                $this->reducer($co2_reduced_set, $current_key, 'co2');
            }
        }
    }


    private static function tabulateBitForIndex($array_of_binaries, $index_key)
    {
        $bit_tabulation['zero'] = 0;
        $bit_tabulation['one'] = 0;


        // count bit per index
        foreach ($array_of_binaries as $binary) {
            if ($binary[$index_key]) {
                $bit_tabulation['one']++;
            } else {
                $bit_tabulation['zero']++;
            }
        }
        return $bit_tabulation;
    }
}


(new DiagnosticDecoder())->run();
