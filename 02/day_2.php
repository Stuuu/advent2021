<?php

class SubNavigator
{

    const FORWARD_CMD = 'forward';
    const DOWN_CMD = 'down';
    const UP_CMD = 'up';

    public $horizontal_position = 0;

    public $depth = 0;

    public function run()
    {
        // put commands into an array
        $commands = file('puzzle_inputs.txt', FILE_IGNORE_NEW_LINES);

        foreach ($commands as $command) {

            $command_components = explode(' ', $command);
            $command = $command_components[0];
            $command_intcrement = $command_components[1];

            switch ($command) {
                case self::UP_CMD:
                    $this->depth -= $command_intcrement;
                    break;
                case self::DOWN_CMD:
                    $this->depth += $command_intcrement;
                    break;
                case self::FORWARD_CMD:
                    $this->horizontal_position += $command_intcrement;
                    break;
                default:
                    throw new OutOfBoundsException('Command not valid');
                    break;
            }
        }
        echo "horizontal position: " . $this->horizontal_position . PHP_EOL
            . "depth: " . $this->depth . PHP_EOL
            . "HP * Depth = " . ($this->depth * $this->horizontal_position) . PHP_EOL;
    }
}

(new SubNavigator())->run();
