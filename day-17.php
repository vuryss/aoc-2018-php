<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);

$grid = [0 => [500 => '+']];
$minX = 500;
$maxX = 500;
$minY = PHP_INT_MAX;
$maxY = PHP_INT_MIN;
$water = [[500, 0]];

foreach ($input as $line) {
    preg_match_all('/\d+/', $line, $matches);
    $matches = array_map('intval', $matches[0]);
    $char = $line[0];
    if ($char === 'x') {
        $minX = min($matches[0], $minX);
        $maxX = max($matches[0], $maxX);
        $minY = min($matches[1], $minY);
        $maxY = max($matches[2], $maxY);

        for ($i = $matches[1]; $i <= $matches[2]; $i++) {
            $grid[$i][$matches[0]] = '#';
        }
    } else {
        $minX = min($matches[1], $minX);
        $maxX = max($matches[2], $maxX);
        $minY = min($matches[0], $minY);
        $maxY = max($matches[0], $maxY);

        for ($i = $matches[1]; $i <= $matches[2]; $i++) {
            $grid[$matches[0]][$i] = '#';
        }
    }

}

$containerStart = null;
$cycles = 1;

$lastWater = [];

while (!empty($water)) {
    foreach ($water as $key => $drop) {
        unset($water[$key]);

        if ($drop[1] > $maxY) {
            continue;
        }

        if (!isset($grid[$drop[1] + 1][$drop[0]])) {
            $drop[1]++;
            $grid[$drop[1]][$drop[0]] = '|';
            $lastWater[] = $drop;
            $water[] = [$drop[0], $drop[1]];
        } elseif ($grid[$drop[1] + 1][$drop[0]] === '#' || $grid[$drop[1] + 1][$drop[0]] === '~') {
            $drops = [$drop];

            $hasWay = false;

            while (!empty($drops)) {
                foreach ($drops as $dkey => $drp) {
                    unset($drops[$dkey]);

                    $right = $grid[$drp[1]][$drp[0] + 1] ?? '.';
                    $left  = $grid[$drp[1]][$drp[0] - 1] ?? '.';

                    if ($right === '.') {
                        $grid[$drp[1]][$drp[0] + 1] = '|';
                        $lastWater[] = [$drp[0] + 1, $drp[1]];
                        $down = $grid[$drp[1] + 1][$drp[0] + 1] ?? '.';
                        if ($down !== '.') {
                            // If down is not empty - continue flow to right
                            $drops[] = [$drp[0] + 1, $drp[1]];
                        } else {
                            // If down is empty - add new water, do not continue right.
                            $water[] = [$drp[0] + 1, $drp[1]];
                            $hasWay = true;
                            $containerStart = null;
                        }
                    }

                    if ($left === '.') {
                        $grid[$drp[1]][$drp[0] - 1] = '|';
                        $lastWater[] = [$drp[0] - 1, $drp[1]];
                        $down = $grid[$drp[1] + 1][$drp[0] - 1] ?? '.';
                        if ($down !== '.') {
                            // If down is not empty - continue flow to left
                            $drops[] = [$drp[0] - 1, $drp[1]];
                        } else {
                            // If down is empty - add new water, do not continue left.
                            $water[] = [$drp[0] - 1, $drp[1]];
                            $hasWay = true;
                            $containerStart = null;
                        }
                    }
                }
            }

            // If container full - go up
            if (!$hasWay) {
                // Check if there is move moving water on the same level
                $hasMore = false;
                $x = $drop[0];
                do {
                    $current = $grid[$drop[1]][$x] ?? '.';
                    $next = $grid[$drop[1]][$x + 1] ?? '.';
                    if ($current === '|' && $next === '.') {
                        $water[] = [$x, $drop[1]];
                        $hasMore = true;
                        break;
                    }
                    $x++;
                } while ($next != '#');

                $x = $drop[0];
                do {
                    $current = $grid[$drop[1]][$x] ?? '.';
                    $next = $grid[$drop[1]][$x - 1] ?? '.';
                    if ($current === '|' && $next === '.') {
                        $water[] = [$x, $drop[1]];
                        $hasMore = true;
                        break;
                    }
                    $x--;
                } while ($next != '#');

                if (!$hasMore) {
                    // Convert to still water
                    $x = $drop[0];
                    do {
                        $grid[$drop[1]][$x] = '~';
                    } while (($grid[$drop[1]][++$x] ?? '.') == '|');

                    $x = $drop[0];
                    do {
                        $grid[$drop[1]][$x] = '~';
                    } while (($grid[$drop[1]][--$x] ?? '.') == '|');

                    // Get last water
                    foreach ($lastWater as $key2 => $drop2) {
                        if ($grid[$drop2[1]][$drop2[0]] === '~') {
                            unset($lastWater[$key2]);
                        }
                    }
                    $lastWater = array_values($lastWater);
                    $water[] = end($lastWater);
                }
            }
        }
    }

    if (empty($water) && !empty($lastWater)) {
        array_push($water, array_pop($lastWater));
    }
}

$sum = 0;

for ($y = $minY; $y <= $maxY; $y++) {
    for ($x = $minX; $x <= $maxX + 1; $x++) {
        if (isset($grid[$y][$x]) && ($grid[$y][$x] === '|' || $grid[$y][$x] === '~')) {
            $sum++;
        }
    }
}

echo 'Answer 1: ' . $sum . PHP_EOL;

$sum = 0;

for ($y = $minY; $y <= $maxY; $y++) {
    for ($x = $minX; $x <= $maxX + 1; $x++) {
        if (isset($grid[$y][$x]) && $grid[$y][$x] === '~') {
            $sum++;
        }
    }
}

echo 'Answer 2: ' . $sum . PHP_EOL;

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
