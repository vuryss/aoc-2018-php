<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));

$matches = [];

foreach (range('a', 'z') as $c) {
    array_push($matches, $c . strtoupper($c), strtoupper($c) . $c);
}

$results = [];

foreach (range('`', 'z') as $char) {
    $chars = str_replace([$char, strtoupper($char)], '', $input);

    do $chars = str_replace($matches, '', $chars, $cont);
    while ($cont);

    $results[$char] = strlen($chars);
}

echo 'Answer 1: ' . $results['`'] . PHP_EOL;
sort($results);
echo 'Answer 2: ' . $results[0] . PHP_EOL;
