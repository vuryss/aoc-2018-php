<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$map = [];

// Part 1
foreach ($input as $line) {
    preg_match('/(\d+).*?(\d+).*?(\d+).*?(\d+).*?(\d+)/', $line, $parts);

    for ($x = $parts[2]; $x < $parts[2] + $parts[4]; $x++) {
        for ($y = $parts[3]; $y < $parts[3] + $parts[5]; $y++) {
            $map[$x . '.' . $y] = isset($map[$x . '.' . $y]) ? 2 : 1;
        }
    }
}

echo 'Answer 1: ' . array_count_values($map)[2] . PHP_EOL;

// Part 2
foreach ($input as $line) {
    preg_match('/(\d+).*?(\d+).*?(\d+).*?(\d+).*?(\d+)/', $line, $parts);
    $isOverlap = false;

    for ($x = $parts[2]; $x < $parts[2] + $parts[4]; $x++) {
        for ($y = $parts[3]; $y < $parts[3] + $parts[5]; $y++) {
            if ($map[$x . '.' . $y] == 2 && $isOverlap = true) {
                break 2;
            }
        }
    }

    if (!$isOverlap) {
        echo 'Answer 2: ' . $parts[1] . PHP_EOL;
        break;
    }
}
