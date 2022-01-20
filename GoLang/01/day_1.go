package main

import (
	"bufio"
	"fmt"
	"log"
	"os"
	"strconv"
)

// const input_file string = "test_puzzle_inputs.txt"
const input_file string = "puzzle_inputs.txt"

func main() {
	lines, err := readLines(input_file)
	if err != nil {
		log.Fatalf("readLines: %s", err)
	}

	increment_count := 0
	var last_value int64
	for i, line := range lines {
		fmt.Println(i, line)
		line, _ := strconv.ParseInt(line, 10, 64)

		if i == 0 {
			last_value = line
			fmt.Println("first line skipping increment check")
			fmt.Println(last_value)
			continue
		}

		if last_value < line {
			increment_count++
		}

		last_value = line

		fmt.Println(increment_count)
	}
}

// readLines reads a whole file into memory
// and returns a slice of its lines.
func readLines(path string) ([]string, error) {
	file, err := os.Open(path)
	if err != nil {
		return nil, err
	}
	defer file.Close()

	var lines []string
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		lines = append(lines, scanner.Text())
	}
	return lines, scanner.Err()
}
