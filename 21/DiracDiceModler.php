<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

class DiracDiceModler
{
    // const INPUT_FILE_PATH = __DIR__ . '/test_puzzle_inputs.txt';
    const INPUT_FILE_PATH = __DIR__ . '/puzzle_inputs.txt';

    const DICE_SIDES = 100;

    const BOARD_SIZE = 10;

    const WIN_SCORE = 1000;

    const ROLLS_PER_TURN = 3;

    public int $dice_value = 0;

    public int $dice_rolls = 0;

    public function run()
    {

        echo '<pre>';
        $players = $this->getAndSetPlayersFromInput(self::INPUT_FILE_PATH);
        $debug = [];
        do {

            foreach ($players as $player_number => &$player) {
                $dice_roll_total = $this->diceRoll($this->dice_value);

                $new_position = $this->movePlayerPosition($player['position'], $dice_roll_total);
                $player['total_score'] += $new_position;
                $player['position'] = $new_position;

                $debug[$player_number] = [
                    'player' => $player_number,
                    'player_values' => $player,
                ];
                if ($player['total_score'] >= self::WIN_SCORE) {
                    break 2;
                }
            }
            print_r($debug);
        } while (true);
        print_r($players);
        echo 'dice rolls: ' . $this->dice_rolls . PHP_EOL;
        echo ' Answer (dice_rolls * loser_score) ' . ($this->dice_rolls * $players[2]['total_score']) . PHP_EOL;
    }

    public function diceRoll(int $dice_value): int
    {
        $dice_roll_values = [];
        for ($i = 0; $i < self::ROLLS_PER_TURN; $i++) {
            $dice_value++;

            // we have to keep track of the number of times 
            // the die was rolled for our answer
            $this->dice_rolls += 1;
            // The dice resets to 1 when it hits dice size
            if ($dice_value > self::DICE_SIDES) {
                $dice_value = 1;
            }
            $dice_roll_values[] = $dice_value;
        }
        // set dice value;
        $this->dice_value = $dice_value;
        return array_sum($dice_roll_values);
    }
    // Player 1 rolls 1+2+3 and moves to space 10 for a total score of 10.
    // Player 2 rolls 4+5+6 and moves to space 3 for a total score of 3.
    // Player 1 rolls 7+8+9 and moves to space 4 for a total score of 14.
    // Player 2 rolls 10+11+12 and moves to space 6 for a total score of 9.
    public function movePlayerPosition(int $player_position, int $dice_roll): int
    {
        $new_position = (int) (($dice_roll + $player_position) % 10);

        if ($new_position === 0) {
            $new_position = (int) 10;
        }

        return $new_position;
    }

    public function getAndSetPlayersFromInput(string $file_path): array
    {
        $raw_inputs = file($file_path);
        $players = [];
        foreach ($raw_inputs as $player_input) {
            $player_input_parts = explode(' ', $player_input);

            $player_number = $player_input_parts[1];
            $player_position = (int) $player_input_parts[4];
            $players[$player_number] = [
                'position' => $player_position,
                'total_score' => 0,
            ];
        }

        return $players;
    }
}

(new DiracDiceModler())->run();
