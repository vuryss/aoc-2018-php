<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);

$input = substr($input, 1, -1);
$grid = [];

function parse($regex, $x, $y) {
    global $grid;

    $initX = $x;
    $initY = $y;

    $chars = str_split($regex);
    $group = false;
    $brackets = 0;
    $content = '';

    while ($char = array_shift($chars)) {
        if ($group) {
            if ($char === ')' && $brackets === 1) {
                $group = false;
                $brackets = 0;
                parse($content, $x, $y);
                continue;
            }

            $content .= $char;

            if ($char === '(') {
                $brackets++;
            } elseif ($char === ')') {
                $brackets--;
            }

            continue;
        }

        if ($char === '(') {
            $group = true;
            $brackets = 1;
            $content = '';
            continue;
        }

        if ($char !== '|') {
            $grid[$y][$x][] = $char;
        }

        switch ($char) {
            case 'N': $y--; break;
            case 'S': $y++; break;
            case 'W': $x--; break;
            case 'E': $x++; break;
            case '|':
                $x = $initX;
                $y = $initY;
                break;
        }
    }
}

parse($input, 0, 0);

$x = $y = 0;
$queue = [[0, 0, 0]];
$maxDistance = 0;
$passed = [];
$count2 = [];

while (!empty($queue)) {
    $item = array_shift($queue);
    $maxDistance = $item[2];
    $passed[$item[0] . '.' . $item[1]] = true;

    if ($maxDistance >= 1000) {
        $count2[$item[0] . '.' . $item[1]] = true;
    }

    if (!isset($grid[$item[1]][$item[0]])) {
        continue;
    }

    $gridItem = $grid[$item[1]][$item[0]];

    foreach ($gridItem as $dir) {
        switch ($dir) {
            case 'N': $next = [$item[0], $item[1] - 1, $item[2] + 1]; break;
            case 'S': $next = [$item[0], $item[1] + 1, $item[2] + 1]; break;
            case 'W': $next = [$item[0] - 1, $item[1], $item[2] + 1]; break;
            case 'E': $next = [$item[0] + 1, $item[1], $item[2] + 1]; break;
        }

        if (!isset($passed[$next[0] . '.' . $next[1]])) {
            $queue[] = $next;
        }
    }
}

echo 'Answer 1: ' . $maxDistance . PHP_EOL;
echo 'Answer 2: ' . count($count2) . PHP_EOL;
echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;