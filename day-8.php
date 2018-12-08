<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = $input2 = explode(' ', $input);
$nodes = [];

echo 'Answer 1: ' . tree($input) . PHP_EOL;

function tree(&$input) {
    $children = $input[0];
    $metadata = $input[1];
    $sum      = 0;
    $input    = array_slice($input, 2);

    for ($i = 0; $i < $children; $i++) {
        $sum += tree($input);
    }

    for ($i = 0; $i < $metadata; $i++) {
        $sum += $input[$i];
    }

    $input = array_slice($input, $metadata);

    return $sum;
}

echo 'Answer 2: ' . tree2($input2) . PHP_EOL;

function tree2(&$input) {
    $children = $input[0];
    $metadata = $input[1];

    $input = array_slice($input, 2);

    $sum = 0;

    if ($children == 0) {
        for ($i = 0; $i < $metadata; $i++) {
            $sum += $input[$i];
        }

        $input = array_slice($input, $metadata);

        return $sum;
    }

    $childValues = [];

    for ($i = 0; $i < $children; $i++) {
        $childValues[$i + 1] = tree2($input);
    }

    for ($i = 0; $i < $metadata; $i++) {
        if (isset($childValues[$input[$i]])) {
            $sum += $childValues[$input[$i]];
        }
    }

    $input = array_slice($input, $metadata);

    return $sum;
}
