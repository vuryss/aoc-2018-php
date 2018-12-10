<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$points = [];

foreach ($input as $line) {
    preg_match('/<.*?(\-?\d+).*?(\-?\d+)>.*?<(.*?\-?\d+).*?(\-?\d+)>/', $line, $matches);
    $points[] = ['pos' => [(int) $matches[1], (int) $matches[2]], 'vel' => [(int) $matches[3], (int) $matches[4]]];
}

$found = false;
$seconds = 0;

while (true) {
    $seconds++;
    $grid = [];
    $minX = $minY = PHP_INT_MAX;
    $maxX = $maxY = PHP_INT_MIN;

    foreach ($points as $key => &$point) {
        $point['pos'][0] += $point['vel'][0];
        $point['pos'][1] += $point['vel'][1];
        $grid[$point['pos'][0]][$point['pos'][1]] = true;

        $minX = min($point['pos'][0], $minX);
        $maxX = max($point['pos'][0], $maxX);
        $minY = min($point['pos'][1], $minY);
        $maxY = max($point['pos'][1], $maxY);
    }

    unset($point);

    if ($maxX - $minX < 100 && $maxY - $minY < 15) {
        $found = true;
    }

    if ($found) {
        for ($i = $minY; $i <= $maxY; $i++) {
            for ($j = $minX; $j <= $maxX; $j++) {
                echo isset($grid[$j][$i]) ? '#' : '.';
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
        echo 'Answer 2: ' . $seconds . PHP_EOL;
        break;
    }
}
