<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start   = microtime(true);
$min = PHP_INT_MAX;

foreach (range('`', 'z') as $char) {
    $result = new \Ds\Stack();
    $chars = strtr($input, [$char => '', strtoupper($char) => '']);

    for ($i = 0; $i < strlen($chars); $i++) {
        if ($result->isEmpty() || strtolower($result->peek()) != strtolower($chars[$i]) || $result->peek() === $chars[$i]) {
            $result->push($chars[$i]);
        } else {
            $result->pop();
        }
    }

    if ($char === '`') {
        echo 'Answer 1: ' . $result->count() . PHP_EOL;
    } else {
        $min = $result->count() < $min ? $result->count() : $min;
    }
}

echo 'Answer 2: ' . $min . PHP_EOL;
echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
