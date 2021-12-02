<?php

class DepthDeterminer
{

    public function run()
    {
        // put depths in array
        $depth_measures = file('puzzle_inputs.txt', FILE_IGNORE_NEW_LINES);
        $increase_count = 0;
        foreach ($depth_measures as $key => $measure) {
            // the first 3 measures don't have a prior reading to compare against
            // so it can be ignored
            if ($key <= 2) continue;


            $prior_window_sum = $depth_measures[$key - 1] + $depth_measures[$key - 2] + $depth_measures[$key - 3];

            $current_window_sum = $depth_measures[$key] + $depth_measures[$key - 1] +
                $depth_measures[$key - 2];

            if ($current_window_sum > $prior_window_sum) {
                $increase_count++;
            }
        }
        echo $increase_count . PHP_EOL;
    }
}

(new DepthDeterminer())->run();
