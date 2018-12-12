<?php

$input = 'initial state: #..####.##..#.##.#..#.....##..#.###.#..###....##.##.#.#....#.##.####.#..##.###.#.......#............

##... => .
##.## => .
.#.#. => #
#..#. => .
#.### => #
.###. => .
#.#.. => .
##..# => .
..... => .
...#. => .
.#..# => .
####. => #
...## => #
..### => #
#.#.# => #
###.# => #
#...# => #
..#.# => .
.##.. => #
.#... => #
.##.# => #
.#### => .
.#.## => .
..##. => .
##.#. => .
#.##. => .
#..## => .
###.. => .
....# => .
##### => #
#.... => .
..#.. => #';
$input = explode("\n", $input);

$state = str_split(substr($input[0], 15));
$rulesInput = array_slice($input, 2);
$rules = [];

foreach ($rulesInput as $line) {
    $parts = explode(' => ', $line);
    $rules[] = [str_split($parts[0]), $parts[1]];
}

//echo 'State: ' . implode('', $state) . PHP_EOL;

//for ($q = -5; $q < 40; $q++) {
//    echo $state[$q] ?? ' ';
//}
//echo PHP_EOL;

$mem = [];

for ($i = 1; $i <= 20; $i++) {
    ksort($state, SORT_NUMERIC);
    $first = array_search('#', $state) - 2;
    $last = array_search('#', array_reverse($state, true)) + 2;
    $newState = array_slice($state, $first, $last - $first + 1, true);
    //print_r($state);

    //$hash = implode('', $newState);

    //if (isset($mem[$hash])) {
    //    echo 'HAS IT!' . PHP_EOL;
    //}

    //echo 'First: ' . $first . ' | Last: ' . $last . PHP_EOL;

    for ($pos = $first; $pos <= $last; $pos++) {
        if (!isset($newState[$pos])) {
            $newState[$pos] = '.';
        }

        foreach ($rules as $rule) {
            $a = $rule[0];

            if ($a[0] === ($state[$pos - 2] ?? '.')
                && $a[1] === ($state[$pos -1] ?? '.')
                && $a[2] === ($state[$pos] ?? '.')
                && $a[3] === ($state[$pos + 1] ?? '.')
                && $a[4] === ($state[$pos + 2] ?? '.')
            ) {
                // Match!
                //echo 'Matched rule: ' . implode('', $a) . ' for pos: ' . $pos . PHP_EOL;
                $newState[$pos] = $rule[1];
                continue 2;
            }
        }

        $newState[$pos] = '.';
    }

    //$mem[implode('', $state)] = implode('', $newState);
    $state = $newState;
    //print_r($newState);

    //for ($q = -5; $q < 200; $q++) {
    //    echo $state[$q] ?? ' ';
    //}
    //echo PHP_EOL . PHP_EOL;

    if ($i % 1000 == 0) {
        //print_r($mem);
        echo $i . PHP_EOL;
    }
}

$sum = 0;

foreach ($state as $key => $value) {
    if ($value === '#') {
        $sum += $key;
    }
}

echo 'Answer 1: ' . $sum . PHP_EOL;

