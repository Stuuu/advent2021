<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

class LowestRiskPathFinder
{

    // const FILE_PATH = __DIR__ . '/test_puzzle_inputs.txt';
    const FILE_PATH = __DIR__ . '/puzzle_inputs.txt';

    // For part 2 we have to expand the size of the cavern by 5 times
    const MAP_EXPANSION_FACTOR = 5;


    public function run()
    {
        echo '<pre>';
        $map  = self::setAndGetMap();

        // The destination point is bottom left
        // we are dealing with a square so it will be the same for x & y 
        $end_location = count($map[1]) - 1;


        $grid = new BlackScorp\Astar\Grid($map);
        $startPosition = $grid->getPoint(0, 0);
        $endPosition = $grid->getPoint($end_location, $end_location);

        $astar = new BlackScorp\Astar\Astar($grid);
        $nodes = $astar->search($startPosition, $endPosition);
        $path = [];
        $total_risk = 0;
        if (count($nodes) === 0) {
            echo "Path not found";
        } else {
            foreach ($nodes as $node_count => $node) {
                $y = $node->getY();
                $x = $node->getX();
                $path[$y][$x] = 1;
                $total_risk += $map[$y][$x];
            }
        }
        // we don't need to count the starting points risk
        $total_risk -= $map[0][0];
        echo '<pre>';
        echo 'total risk: ' . $total_risk . PHP_EOL;

        foreach ($map as $y_index => $row) {
            foreach ($row as $x_index => $value) {
                if (isset($path[$x_index][$y_index])) {
                    echo '<b>' . $value . '</b>';
                } else {
                    echo $value;
                }
            }
            echo  PHP_EOL;
        }
        die;
    }

    public static function setAndGetMap()
    {
        $inputs = file(self::FILE_PATH);
        $map = [];
        foreach ($inputs as $key => $value) {
            $map[$key] = str_split(trim($value));
        }
        return self::expandMap($map);
    }

    public static function expandMap(array $map)
    {
        $initial_map_lengh = count($map[0]);
        foreach ($map as $y_index_key => $map_row) { // Per row
            foreach ($map_row as $x_index_key => $risk_value) { // per value in row

                for ($i = 1; $i < self::MAP_EXPANSION_FACTOR; $i++) { // expanded by expansion factor
                    $new_x_index = $x_index_key + ($initial_map_lengh * $i);
                    $new_y_index = $y_index_key + ($initial_map_lengh * $i);
                    if ($risk_value == 9) {
                        $risk_value = 1;
                    } else {
                        $risk_value++;
                    }
                    $map[$y_index_key][$new_x_index] = $risk_value;
                    $map[$new_y_index][$x_index_key] = $risk_value;
                }
            }
        }

        // beyond the y depth of the initial map we 
        // have to wait for the first y set to be generated from the initial values
        // get a count of the new row width now that it has been expanded
        $new_map_count = count($map[0]);
        // to build out y + 10 and x + 10 loop through y
        for ($y_syn = 10; $y_syn < $new_map_count; $y_syn++) {
            // for each x risk value 
            foreach ($map[$y_syn] as $x_syn => $syn_risk) {
                // generate its projected out values
                for ($b = 1; $b < self::MAP_EXPANSION_FACTOR; $b++) {
                    $syn_new_x_index = $x_syn + ($initial_map_lengh * $b);
                    // echo $syn_new_x_index . PHP_EOL;
                    if ($syn_risk == 9) {
                        $syn_risk = 1;
                    } else {
                        $syn_risk++;
                    }

                    $map[$y_syn][$syn_new_x_index] = $syn_risk;
                }
            }
        }
        return $map;
    }
}

(new LowestRiskPathFinder())->run();
