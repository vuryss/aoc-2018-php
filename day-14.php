<?php

$input = (int) trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);

$recipies = '37';
$elf1 = 0;
$elf2 = 1;
$found = false;

while (true) {
    $recipies .= $recipies[$elf1] + $recipies[$elf2];
    $num = strlen($recipies);

    $elf1 = ($elf1 + $recipies[$elf1] + 1) % $num;
    $elf2 = ($elf2 + $recipies[$elf2] + 1) % $num;

    if (!$found) {
        if ($num > $input + 10) {
            echo 'Answer 1: ' . substr($recipies, $input, 10) . PHP_EOL;
            $found = true;
        }
    } elseif (strpos($recipies, (string) $input, -8) !== false) {
        echo 'Answer 2: ' . strpos($recipies, (string) $input, -8) . PHP_EOL;
        break;
    }
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
