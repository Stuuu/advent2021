<?php

// After one day, its internal timer would become 2.
// After another day, its internal timer would become 1.
// After another day, its internal timer would become 0.
// After another day, its internal timer would reset to 6, and it would create a new lanternfish with an internal timer of 8.
// After another day, the first lanternfish would have an internal timer of 5, and the second lanternfish would have an internal timer of 7.

class LanternFishGrowthModler
{

    const DAYS_TO_MODEL = 80;
    const TEST_DAYS_TO_MODEL = 18;
    private $generation_count = 0;

    private $final_fish_population = [];


    public function run()
    {
        ini_set('memory_limit', '7G');
        ini_set('max_execution_time', '0');


        $input_file = file('puzzle_inputs.txt');

        $initial_fish = explode(',', $input_file[0]);



        // For each day up to x days all fish need to be processed for a given set of rules
        $this->spawn($initial_fish);
        echo "peak memory usage " . memory_get_peak_usage() . PHP_EOL;

        echo "final fish count: " . count($this->final_fish_population) . PHP_EOL;

        // each fish follows the spawn rules each day 

        // days_till spawn is 7 for existing fish, including 0 so setting to 6

        // After day zero they spawn a new fish that needs two additional days before it will first spawn


        self::printGenerationExample();
    }

    public function spawn($fish_population)
    {

        if ($this->generation_count >= self::DAYS_TO_MODEL) {
            $this->final_fish_population = $fish_population;
            return;
        }

        $next_gen_of_fish = [];
        $new_spawn_count = 0;
        foreach ($fish_population as $fish) {
            $fish_age = intval($fish);
            if (!$fish_age) {
                $new_spawn_count++;
                $next_gen_of_fish[] = 6;
            } else {
                $next_gen_of_fish[] = ($fish_age - 1);
            }
        }

        $fish_population = null;

        for ($i = 0; $i < $new_spawn_count; $i++) {
            $next_gen_of_fish[] = 8;
        }


        $this->generation_count++;
        $this->spawn($next_gen_of_fish);
    }




    public static function printGenerationExample()
    {

        echo "<pre>
        Initial state: 3,4,3,1,2
        After  1 day:  2,3,2,0,1
        After  2 days: 1,2,1,6,0,8
        After  3 days: 0,1,0,5,6,7,8
        After  4 days: 6,0,6,4,5,6,7,8,8
        After  5 days: 5,6,5,3,4,5,6,7,7,8
        After  6 days: 4,5,4,2,3,4,5,6,6,7
        After  7 days: 3,4,3,1,2,3,4,5,5,6
        After  8 days: 2,3,2,0,1,2,3,4,4,5
        After  9 days: 1,2,1,6,0,1,2,3,3,4,8
        After 10 days: 0,1,0,5,6,0,1,2,2,3,7,8
        After 11 days: 6,0,6,4,5,6,0,1,1,2,6,7,8,8,8
        After 12 days: 5,6,5,3,4,5,6,0,0,1,5,6,7,7,7,8,8
        After 13 days: 4,5,4,2,3,4,5,6,6,0,4,5,6,6,6,7,7,8,8
        After 14 days: 3,4,3,1,2,3,4,5,5,6,3,4,5,5,5,6,6,7,7,8
        After 15 days: 2,3,2,0,1,2,3,4,4,5,2,3,4,4,4,5,5,6,6,7
        After 16 days: 1,2,1,6,0,1,2,3,3,4,1,2,3,3,3,4,4,5,5,6,8
        After 17 days: 0,1,0,5,6,0,1,2,2,3,0,1,2,2,2,3,3,4,4,5,7,8
        After 18 days: 6,0,6,4,5,6,0,1,1,2,6,0,1,1,1,2,2,3,3,4,6,7,8,8,8,8";
    }
}

(new LanternFishGrowthModler())->run();
