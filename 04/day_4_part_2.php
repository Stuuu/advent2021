<?php

class BingoCardFinder
{

    private $draw_numbers;
    private $bingo_cards = [];
    private $all_winning_cards = [];

    public function run()
    {
        $input_file = file('puzzle_inputs.txt');

        $input_file_without_draw_numbers = $this->setAndRemoveDrawNumbers($input_file);

        $this->setBingoCards($input_file_without_draw_numbers);

        foreach ($this->draw_numbers as $number_index => $current_number) {
            // don't start checking cards until at least 5 numbers have been called
            if ($number_index < 4) {
                continue;
            }


            // Array of numbers that have been drawn;
            $drawn_numbers = array_slice($this->draw_numbers, 0, $number_index + 1);


            foreach ($this->bingo_cards as $card_index => $bingo_card) {

                // check each row for a match of all numbers in the row
                foreach ($bingo_card as $column_index => $row) {
                    if (count(array_intersect($drawn_numbers, $row)) == count($row)) {
                        $this->calculateAnswer($card_index, $current_number, $drawn_numbers);
                    }

                    // if we don't find a row match push the row nums into a column array

                    foreach ($row as $row_index => $col_value) {
                        $columns[$row_index][$column_index] = $col_value;
                    }
                }

                foreach ($columns as $column) {
                    if (count(array_intersect($drawn_numbers, $column)) == count($column)) {
                        $this->calculateAnswer($card_index, $current_number, $drawn_numbers);
                    }
                }
            }
        }
        print_r($this->all_winning_cards);
    }

    public function calculateAnswer(
        $bingo_card_number,
        $current_drawn_number,
        $drawn_numbers
    ) {
        // flatten bingo card values for easier summing 
        $all_card_values = call_user_func_array('array_merge', $this->bingo_cards[$bingo_card_number]);

        $uncalled_card_values = array_diff($all_card_values, $drawn_numbers);

        $sum_uncalled_numbers = array_sum($uncalled_card_values);

        if (array_key_exists($bingo_card_number, $this->all_winning_cards)) {
            return;
        }
        $this->all_winning_cards[$bingo_card_number] =
            [
                'sum of uncalled values' => $sum_uncalled_numbers,
                'last number called' => $current_drawn_number,
                'last draw * sum uncalled' => $sum_uncalled_numbers * $current_drawn_number,
            ];
    }

    public function setBingoCards($input_file_without_draw_numbers)
    {
        $card_count = 0;
        foreach ($input_file_without_draw_numbers as $input_set) {
            $input_set = trim($input_set);
            if ($input_set == '') {
                $row_count = 0;
                $card_count++;
                continue;
            }

            //reduce double spaces to single space so we can explode the ints
            $input_set = preg_replace('!\s+!', ' ', $input_set);

            //split row into individual array values
            $row_string = explode(',', $input_set);
            foreach ($row_string as $values) {
                $row_values_as_ints = array_map('intval', explode(' ', $values));
            }

            $this->bingo_cards[$card_count][$row_count] = $row_values_as_ints;

            $row_count++;
        }
    }

    public function setAndRemoveDrawNumbers($input_file)
    {
        $this->draw_numbers = array_map('intval', explode(',', $input_file[0]));
        // remove the draw numbers off the input array
        array_shift($input_file);
        return $input_file;
    }
}

(new BingoCardFinder())->run();
