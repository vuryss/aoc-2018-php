<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = (int) $input;

$cells = [];

for ($y = 1; $y <= 300; $y++) {
    for ($x = 1; $x <= 300; $x++) {
        $power = (($x + 10) * $y + $input) * ($x + 10);
        $power = $power > 99 ? (int) substr($power, -3, 1) : 0;
        $cells[$x][$y] = $power - 5;
    }
}

$max = 0;
$coord = '';

for ($y = 1; $y <= 297; $y++) {
    for ($x = 1; $x <= 297; $x++) {
        $sumPower = $cells[$x][$y] + $cells[$x+1][$y] + $cells[$x+2][$y]
        + $cells[$x][$y+1] + $cells[$x+1][$y+1] + $cells[$x+2][$y+1]
        + $cells[$x][$y+2] + $cells[$x+1][$y+2] + $cells[$x+2][$y+2];

        if ($sumPower > $max) {
            $max = $sumPower;
            $coord = $x . ',' . $y;
        }
    }
}

for ($y = 1; $y <= 300; $y++) {
    for ($x = 1; $x <= 300; $x++) {
        echo str_pad($cells[$x][$y], 4, ' ', STR_PAD_LEFT);
    }

    echo PHP_EOL;
}

echo 'Answer 1: ' . $coord . PHP_EOL;

$max = 0;
$coord = '';

for ($y = 1; $y <= 300; $y++) {
    for ($x = 1; $x <= 300; $x++) {
        $maxSize = min(300 - $x + 1, 300 - $y + 1);

        for ($s = 1; $s <= $maxSize; $s++) {
            $sumPower = 0;

            for ($x1 = $x; $x1 < $x + $s; $x1++) {
                for ($y1 = $y; $y1 < $y + $s; $y1++) {
                    $sumPower += $cells[$x1][$y1];
                }
            }

            if ($sumPower > $max) {
                $max = $sumPower;
                $coord = $x . ',' . $y . ',' . $s;
            }
        }
    }
}

echo 'Answer 2: ' . $coord . PHP_EOL;
