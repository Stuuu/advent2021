<?php


class LowPointLocator
{

    const INPUT_FILE_NAME = "puzzle_inputs.txt";


    public function run()
    {

        $elevations_array = self::convertInputsToArray(
            self::INPUT_FILE_NAME
        );


        echo '<pre>';
        $risk_points = [];
        foreach ($elevations_array as $y_axis_key => $row) {
            foreach ($row as $x_axis_key => $elevation_value) {


                if (self::isLowestValue($elevations_array, $x_axis_key, $y_axis_key)) {
                    echo "<b>{$elevation_value}</b>";
                    $risk_points[] = ($elevation_value + 1);
                } else {
                    echo $elevation_value;
                }
            }
            echo PHP_EOL;
        }

        echo 'risk value sum: ' . array_sum($risk_points);
        die;
    }

    private static function isLowestValue($elevations_array, $x_value, $y_value)
    {


        // value being evaluated
        $current_value = $elevations_array[$y_value][$x_value];

        // If any of the
        $lower_than_twelve = false;
        $lower_than_three = false;
        $lower_than_six = false;
        $lower_than_nine = false;
        // 12 o clock check
        if (isset($elevations_array[$y_value - 1][$x_value])) {
            if ($current_value < $elevations_array[$y_value - 1][$x_value]) {
                $lower_than_twelve = true;
            }
        } else {
            $lower_than_twelve = true;
        }

        // 3 o clock check
        if (isset($elevations_array[$y_value][$x_value + 1])) {
            if ($current_value < $elevations_array[$y_value][$x_value + 1]) {
                $lower_than_three = true;
            }
        } else {
            $lower_than_three = true;
        }
        // 6 o clock check
        if (isset($elevations_array[$y_value + 1][$x_value])) {
            if ((intval($current_value) < intval($elevations_array[$y_value + 1][$x_value]))) {
                $lower_than_six = true;
            }
        } else {
            $lower_than_six = true;
        }
        // 9 o clock check 
        if (isset($elevations_array[$y_value][$x_value - 1])) {
            if ($current_value < $elevations_array[$y_value][$x_value - 1]) {
                $lower_than_nine = true;
            }
        } else {
            $lower_than_nine = true;
        }


        return ($lower_than_three && $lower_than_twelve && $lower_than_six && $lower_than_nine);
    }


    private static function convertInputsToArray(
        string $input_file_name
    ): array {
        $inputs = file($input_file_name);

        $array_of_elevations = [];
        foreach ($inputs as $line_index => $line_values) {

            $line_values = trim($line_values);

            $array_of_elevations[$line_index] = str_split($line_values);
        }
        return $array_of_elevations;
    }
}


(new LowPointLocator())->run();
