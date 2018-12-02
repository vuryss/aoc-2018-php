<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

// Part 1
$two = $three = 0;

foreach ($input as $line) {
    $chars = count_chars($line, 1);
    $found2 = $found3 = false;
    rsort($chars);

    foreach ($chars as $num) {
        !$found2 && $num == 2 && ++$two && $found2 = true;
        !$found3 && $num == 3 && ++$three && $found3 = true;
    }
}

echo 'Answer 1: ' . ($two * $three) . PHP_EOL;

// Part 2
$map = [];

foreach ($input as $line) {
    $chars = str_split($line);

    foreach ($map as $item) {
        if (count(array_diff_assoc($chars, $item)) != 1) {
            continue;
        }

        echo 'Answer 2: ' . implode('', array_intersect_assoc($chars, $item)) . PHP_EOL;
        break;
    }

    $map[] = $chars;
}

