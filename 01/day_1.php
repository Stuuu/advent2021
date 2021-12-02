<?php

class DepthDeterminer
{

    public function run()
    {
        // put depths in array
        $depth_measures = file('puzzle_inputs.txt', FILE_IGNORE_NEW_LINES);
        $increase_count = 0;
        foreach ($depth_measures as $key => $measure) {
            // the first measure doesn't have a prior reading
            // so it can be ignored
            if ($key === 0) continue;

            if ($measure > $depth_measures[$key - 1]) {
                $increase_count++;
            }
        }
        echo $increase_count . PHP_EOL;
    }
}

(new DepthDeterminer())->run();
