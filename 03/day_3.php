<?php

class DiagnosticDecoder
{

    // Most common bit
    public $gamma;
    // Least common bit
    public $epsilon;

    public function run()
    {
        // put bits into an array
        $debug_output = file('puzzle_inputs.txt', FILE_IGNORE_NEW_LINES);

        // Set the bit counts to zero so we can intrement them as we count them later
        foreach (str_split($debug_output[0]) as $key => $value) {
            $bit_tabulation[$key][0] = 0;
            $bit_tabulation[$key][1] = 0;
        }

        foreach ($debug_output as $key => $individual_binary) {

            // split each binary number into bits
            $bits = str_split($individual_binary);


            // count bit per index
            foreach ($bits as $bit_key => $bit_value) {
                if ($bit_value) {
                    $bit_tabulation[$bit_key][1]++;
                } else {
                    $bit_tabulation[$bit_key][0]++;
                }
            }
        }

        // set gamma and epsilon based on totals
        foreach ($bit_tabulation as $total_key => $total_value) {

            if ($total_value[0] > $total_value[1]) {
                $gamma[$total_key] = 0;
                $epsilon[$total_key] = 1;
            } else {
                $gamma[$total_key] = 1;
                $epsilon[$total_key] = 0;
            }
        }

        $gamma_binary = implode($gamma);
        $epsilon_binary = implode($epsilon);

        // convert binary of both to decimal
        $gamma_decimal = bindec($gamma_binary);
        $epsilon_decimal = bindec($epsilon_binary);


        // multiply decimal of gamma and epsolon
        echo 'Gamma_decimal: ' . $gamma_decimal . PHP_EOL;
        echo 'Epsilon_decimal: ' . $epsilon_decimal . PHP_EOL;
        echo 'Power consumption rate (E * G): ' . ($epsilon_decimal * $gamma_decimal) . PHP_EOL;
    }
}

(new DiagnosticDecoder())->run();
