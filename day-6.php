<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);
$count = 0;

for ($i = 0; $i < 400; $i++) {
    for ($j = 0; $j < 400; $j++) {
        $min = PHP_INT_MAX;
        $total = $p = 0;

        foreach ($input as $index => $line) {
            $point = explode(', ', $line);
            $dist  = abs($point[0] - $i) + abs($point[1] - $j);
            $total += $dist;

            if ($dist < $min) {
                $min = $dist;
                $p = $index;
            } elseif ($dist == $min) {
                $p = '.';
            }
        }

        if ($total < 10000) {
            $count++;
        }

        $area[$p] = ($area[$p] ?? 0) + 1;

        if ($i == 0 || $i == 399 || $j == 0 || $j == 399) {
            $infinite[$p] = true;
        }
    }
}

$area = array_diff_key($area, $infinite);
rsort($area);

echo 'Answer 1: ' . $area[0] . PHP_EOL;
echo 'Answer 2: ' . $count . PHP_EOL;
