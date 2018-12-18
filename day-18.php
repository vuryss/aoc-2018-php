<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);

$area = [];

foreach ($input as $y => $line) {
    foreach (str_split($line) as $x => $item) {
        $area[$y][$x] = $item;
    }
}

function getAdjacent($x, $y, $area) {
    $adjacent = [];

    if (isset($area[$y - 1][$x]))     $adjacent[] = $area[$y - 1][$x];
    if (isset($area[$y - 1][$x + 1])) $adjacent[] = $area[$y - 1][$x + 1];
    if (isset($area[$y][$x + 1]))     $adjacent[] = $area[$y][$x + 1];
    if (isset($area[$y + 1][$x + 1])) $adjacent[] = $area[$y + 1][$x + 1];
    if (isset($area[$y + 1][$x]))     $adjacent[] = $area[$y + 1][$x];
    if (isset($area[$y + 1][$x - 1])) $adjacent[] = $area[$y + 1][$x - 1];
    if (isset($area[$y][$x - 1]))     $adjacent[] = $area[$y][$x - 1];
    if (isset($area[$y - 1][$x - 1])) $adjacent[] = $area[$y - 1][$x - 1];

    return $adjacent;
}

function calculate($area, $minutes) {
    $checkpoint = null;
    $start      = null;
    $interval   = null;
    $area       = serialize($area);
    $state      = [];

    for ($i = 1; $i <= $minutes; $i++) {
        if ($checkpoint && $interval && $checkpoint === $area && $i + $interval < $minutes) {
            $i += $interval;
            continue;
        }

        if ($checkpoint && !$interval && $checkpoint === $area) {
            $interval = $i - $start - 1;
        }

        if ($i === 500) {
            $checkpoint = $area;
            $start = $i;
        }

        if ($checkpoint && $area === $checkpoint && $minutes < 999999000) {
            $minutes += 27;
            continue;
        }

        if (isset($state[$area])) {
            $area = $state[$area];
            continue;
        }

        $area = unserialize($area);
        $new = $area;

        foreach ($area as $y => $line) {
            foreach ($line as $x => $item) {
                $adjacent = getAdjacent($x, $y, $area);
                $chars = count_chars(implode('', $adjacent), 1);

                if ($item === '.' && isset($chars[124]) && $chars[124] >= 3) {
                    $new[$y][$x] = '|';
                } elseif ($item === '|' && isset($chars[35]) && $chars[35] >= 3) {
                    $new[$y][$x] = '#';
                } elseif ($item === '#' && (!isset($chars[35]) || !isset($chars[124]))) {
                    $new[$y][$x] = '.';
                }
            }
        }

        $state[serialize($area)] = serialize($new);
        $area = serialize($new);
    }

    return unserialize($area);
}


$wood = $lumberyards = 0;
$area1 = calculate($area, 10);

foreach ($area1 as $y => $line) {
    foreach ($line as $x => $item) {
        if ($item === '|') $wood++;
        if ($item === '#') $lumberyards++;
    }
}

echo 'Answer 1: ' . ($wood * $lumberyards) . PHP_EOL;

$wood = $lumberyards = 0;
$area2 = calculate($area, 1000000000);

foreach ($area2 as $y => $line) {
    foreach ($line as $x => $item) {
        if ($item === '|') $wood++;
        if ($item === '#') $lumberyards++;
    }
}

echo 'Answer 2: ' . ($wood * $lumberyards) . PHP_EOL;
echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
