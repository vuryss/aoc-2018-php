<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$grid = $points = $infinite = $area = [];

foreach ($input as $index => $line) {
    $points[] = explode(', ', $line);
}

// Part 1
for ($i = 0; $i < 400; $i++) {
    for ($j = 0; $j < 400; $j++) {
        $min = PHP_INT_MAX;
        $p = 0;

        foreach ($points as $index => $point) {
            $dist = abs($point[0] - $i) + abs($point[1] - $j);

            if ($dist < $min) {
                $min = $dist;
                $p = $index;
            } elseif ($dist == $min) {
                $p = '.';
            }
        }

        $grid[$i][$j] = $p;

        if ($i == 0 || $i == 399 || $j == 0 || $j == 399) {
            $infinite[$grid[$i][$j]] = true;
        }
    }
}

for ($i = 0; $i < 400; $i++) {
    for ($j = 0; $j < 400; $j++) {
        if (!empty($infinite[$grid[$i][$j]])) {
            continue;
        }

        $area[$grid[$i][$j]] = ($area[$grid[$i][$j]] ?? 0) + 1;
    }
}

rsort($area);
echo 'Answer 1: ' . $area[0] . PHP_EOL;

// Part 2
$count = 0;

for ($i = 0; $i < 400; $i++) {
    for ($j = 0; $j < 400; $j++) {
        $total = 0;

        foreach ($points as $point) {
            $total += abs($point[0] - $i) + abs($point[1] - $j);

            if ($total >= 10000) {
                break;
            }
        }

        if ($total < 10000) {
            $count++;
        }
    }
}

echo 'Answer 2: ' . $count . PHP_EOL;
