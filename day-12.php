<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$state = str_split(substr($input[0], 15));
$rulesInput = array_slice($input, 2);
$rules = [];

foreach ($rulesInput as $line) {
    $parts = explode(' => ', $line);
    $rules[] = [str_split($parts[0]), $parts[1]];
}

$save = 0;

for ($i = 1; $i <= 2000; $i++) {
    ksort($state, SORT_NUMERIC);
    $first = array_search('#', $state) - 2;
    $last = array_search('#', array_reverse($state, true)) + 2;
    $newState = array_slice($state, $first, $last - $first + 1, true);

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
                $newState[$pos] = $rule[1];
                continue 2;
            }
        }

        $newState[$pos] = '.';
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

    if ($i === 1000) {
        $sum = 0;

        foreach ($state as $key => $value) {
            if ($value === '#') {
                $sum += $key;
            }
        }

        $save = $sum;
    }

    if ($i === 2000) {
        $sum = 0;

        foreach ($state as $key => $value) {
            if ($value === '#') {
                $sum += $key;
            }
        }

        $diff = $sum - $save;
        $times = 50000000000 / 1000 - 1;
        echo 'Answer 2: ' . ($times * $diff + $save) . PHP_EOL;
    }
}