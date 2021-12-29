<?php

class CrabSubAligner
{
    public function run()
    {
        $inputs = file('puzzle_inputs.txt');
        echo '<pre>';
        $inputs = explode(',', $inputs[0]);
        asort($inputs);
        $low = reset($inputs);
        $high = end($inputs);


        // Go through each number in the range 
        // And for each number between high and low determine differnce to get there
        // Then for each number in the range determine the total

        $all_crab_calcs = [];
        foreach ($inputs as $crab_position) {

            $crab_unique_id = uniqid();
            for ($i = $low; $i <= $high; $i++) {
                $all_crab_calcs[$crab_unique_id][$i] = abs($crab_position - $i);
            }
        }
        // echo '<pre>';
        // print_r($all_crab_calcs);
        // die;

        $fuel_per_location = [];
        foreach ($all_crab_calcs as $calc_index => $crab_calc) {
            foreach ($crab_calc as $loction_index => $fuel_spent) {
                if (!isset($fuel_per_location[$loction_index])) {
                    $fuel_per_location[$loction_index] = 0;
                }
                $fuel_per_location[$loction_index] += $fuel_spent;
            }
        }
        asort($fuel_per_location);
        print_r($fuel_per_location);
    }
}


(new CrabSubAligner())->run();
