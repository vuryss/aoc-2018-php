<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);

$state      = str_split(substr($input[0], 15));
$rulesInput = array_slice($input, 2);
$rules      = [];

foreach ($rulesInput as $line) {
    $parts = explode(' => ', $line);
    $rules[$parts[0]] = $parts[1];
}

$save = 0;

for ($i = 1; $i <= 1000; $i++) {
    $first    = array_search('#', $state) - 2;
    $last     = array_search('#', array_reverse($state, true)) + 2;
    $newState = [];

    for ($pos = $first; $pos <= $last; $pos++) {
        foreach ($rules as $pattern => $result) {
            if ($pattern[0] === ($state[$pos - 2] ?? '.')
                && $pattern[1] === ($state[$pos -1] ?? '.')
                && $pattern[2] === ($state[$pos] ?? '.')
                && $pattern[3] === ($state[$pos + 1] ?? '.')
                && $pattern[4] === ($state[$pos + 2] ?? '.')
            ) {
                $newState[$pos] = $result;
                continue 2;
            }
        }
    }

    $state = $newState;

    if ($i === 20) {
        $sum = 0;

        foreach ($state as $key => $value) {
            if ($value === '#') {
                $sum += $key;
            }
        }

        echo 'Answer 1: ' . $sum . PHP_EOL;
    }

    if ($i === 500) {
        foreach ($state as $key => $value) {
            if ($value === '#') {
                $save += $key;
            }
        }
    }

    if ($i === 1000) {
        $sum = 0;

        foreach ($state as $key => $value) {
            if ($value === '#') {
                $sum += $key;
            }
        }

        $times = 50000000000 / 500 - 1;
        echo 'Answer 2: ' . ($times * ($sum - $save) + $save) . PHP_EOL;
    }
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
