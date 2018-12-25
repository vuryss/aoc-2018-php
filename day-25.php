<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);

$nodes = [];

foreach ($input as $line) {
    $nodes[] = explode(',', $line);
}

function findConnected($node, &$nodes) {
    foreach ($nodes as $key => $node2) {
        $dist = abs($node[0] - $node2[0]) + abs($node[1] - $node2[1])
              + abs($node[2] - $node2[2]) + abs($node[3] - $node2[3]);

        if ($dist <= 3) {
            unset($nodes[$key]);
            findConnected($node2, $nodes);
        }
    }
}

$count = 0;

while (!empty($nodes)) {
    $count++;
    $node = array_pop($nodes);
    findConnected($node, $nodes);
}

echo 'Answer 1: ' . $count . PHP_EOL;
echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
