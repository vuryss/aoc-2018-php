<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));

$matches = [];

for ($char = 97; $char <= 122 && $c = chr($char); $char++) {
    array_push($matches, $c . strtoupper($c), strtoupper($c) . $c);
}

$results = [];

for ($char = 96; $char <= 122; $char++) {
    $chars = str_replace([chr($char), strtoupper(chr($char))], '', $input);

    do $chars = str_replace($matches, '', $chars, $cont);
    while ($cont);

    $results[$char] = strlen($chars);
}

echo 'Answer 1: ' . $results[96] . PHP_EOL;

sort($results);

echo 'Answer 2: ' . $results[0] . PHP_EOL;
