<?php

// 0,9 -> 5,9 *
// 8,0 -> 0,8 -
// 9,4 -> 3,4 ?
// 2,2 -> 2,1 ?
// 7,0 -> 7,4 *
// 6,4 -> 2,0 -
// 0,9 -> 2,9 *
// 3,4 -> 1,4 ?
// 0,0 -> 8,8 -
// 5,5 -> 8,2 -

// 
//y.......1..
//y..1....1..
//y..1....1..
//y.......1..
//y.112111211
//y..........
//y..........
//y..........
//y..........
//y222111....

class ThermalVentFinder
{


    private $max_x_cord = 0;
    private $max_y_cord = 0;

    public function run()
    {


        $raw_quardenant_inputs = file('puzzle_inputs.txt');

        foreach ($raw_quardenant_inputs as $line_count => $line) {
            $line = explode(' -> ', $line);
            $start = explode(',', $line[0]);
            $end = explode(',', $line[1]);




            $processed_coordinates[$line_count] =
                [
                    'start' => [
                        'x' => $start[0],
                        'y' => $start[1],
                    ],
                    'end' => [
                        'x' => $end[0],
                        'y' => $end[1],
                    ],
                ];

            if (
                $processed_coordinates[$line_count]['start']['x'] > $this->max_x_cord
            ) {
                $this->max_x_cord = $processed_coordinates[$line_count]['start']['x'];
            }
            if ($processed_coordinates[$line_count]['end']['x'] > $this->max_x_cord) {
                $this->max_x_cord = $processed_coordinates[$line_count]['end']['x'];
            }

            if (
                $processed_coordinates[$line_count]['start']['y'] > $this->max_y_cord
            ) {
                $this->max_y_cord = $processed_coordinates[$line_count]['start']['y'];
            }
            if ($processed_coordinates[$line_count]['end']['y'] > $this->max_y_cord) {
                $this->max_y_cord = $processed_coordinates[$line_count]['end']['y'];
            }
        }




        $line_grid = $this->buildGrid();
        echo '<pre>';
        foreach ($processed_coordinates as $cordinate) {

            // Ignore diagnols
            if (
                $cordinate['start']['x'] != $cordinate['end']['x'] &&
                $cordinate['start']['y'] != $cordinate['end']['y']
            ) {
                echo "x {$cordinate['start']['x']}, y {$cordinate['start']['y']} -> ";
                echo "x {$cordinate['end']['x']}, y {$cordinate['end']['y']}" . PHP_EOL;
                continue;
            }

            if (
                $cordinate['start']['y'] > $cordinate['end']['y'] ||
                $cordinate['start']['x'] > $cordinate['end']['x']
            ) {
                for ($y = $cordinate['start']['y']; $y >= $cordinate['end']['y']; $y--) {
                    for ($x = $cordinate['start']['x']; $x >= $cordinate['end']['x']; $x--) {

                        echo "x: {$x} y: {$y}" . PHP_EOL;
                        $line_grid[$y][$x] =  $line_grid[$y][$x] + 1;
                    }
                }
            } else {
                for ($y = $cordinate['start']['y']; $y <= $cordinate['end']['y']; $y++) {
                    for ($x = $cordinate['start']['x']; $x <= $cordinate['end']['x']; $x++) {
                        echo "x: {$x} y: {$y}" . PHP_EOL;
                        $line_grid[$y][$x] =  $line_grid[$y][$x] + 1;
                    }
                }
            }
        }
        echo '<pre>';

        $this->showTableViz($line_grid);

        $more_than_one_count = 0;
        foreach ($line_grid as $key => $line) {
            foreach ($line as $cell_val) {
                if (intval($cell_val) > 1) {
                    $more_than_one_count++;
                }
            }
        }
        echo "more than one count: {$more_than_one_count}";
    }
    public function buildGrid()
    {
        for ($y = 0; $y <= $this->max_y_cord; $y++) {
            for ($x = 0; $x <= $this->max_x_cord; $x++) {

                $grid[$y][$x] = 0;
            }
        }
        return $grid;
    }

    public function showTableViz($line_grid)
    {
        echo '<table >';
        foreach ($line_grid as $line) {
            echo '<tr>';
            foreach ($line as $value) {



                echo '<td style="padding: 5px">' . $value . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
}

(new ThermalVentFinder())->run();
