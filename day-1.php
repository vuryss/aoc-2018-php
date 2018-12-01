<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

// Part 1
echo 'Frequency: ' . array_sum($input) . PHP_EOL;

// Part 2
$freq = 0;
$mem = [0 => 1];

while (true) {
    foreach ($input as $line) {
        $freq += (int) $line;

        if (!empty($mem[$freq])) {
            echo 'Second frequency: ' . $freq . PHP_EOL;
            break 2;
        }

        $mem[$freq] = 1;
    }
}
