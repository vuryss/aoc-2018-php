<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);
$bots = [];
$bots2 = [];

foreach ($input as $line) {
    preg_match_all('/\-?\d+/', $line, $matches);
    list ($x, $y, $z, $r) = $matches[0];

    $bots[] = ['x' => $x, 'y' => $y, 'z' => $z, 'r' => $r];
}

uasort($bots, function($a, $b) { return $a['r'] < $b['r'] ? 1 : -1; });

$theBot = reset($bots);
$sum = 0;

foreach ($bots as $key => $bot) {
    $dist = abs($bot['x'] - $theBot['x']) + abs($bot['y'] - $theBot['y']) + abs($bot['z'] - $theBot['z']);

    if ($dist <= $theBot['r']) {
        $sum++;
    }
}

echo 'Answer 1: ' . $sum . PHP_EOL;

$zoom = 100000000;
$minX = $minY = $minZ = PHP_INT_MAX;
$maxX = $maxY = $maxZ = PHP_INT_MIN;
$itr = 0;

while (true) {
    $itr++;
    $bots2 = [];
    $maxRange = 0;
    $ranges = [];

    foreach ($bots as $bot) {
        $x = intval($bot['x'] / $zoom);
        $y = intval($bot['y'] / $zoom);
        $z = intval($bot['z'] / $zoom);
        $r = ceil($bot['r'] / $zoom);
        $bots2[] = ['x' => $x, 'y' => $y, 'z' => $z, 'r' => $r];

        if ($itr === 1) {
            $minX = min($minX, $x);
            $minY = min($minY, $y);
            $minZ = min($minZ, $z);
            $maxX = max($maxX, $x);
            $maxY = max($maxY, $y);
            $maxZ = max($maxZ, $z);
        }
    }

    echo 'Scanning ranges, zoom: ' . $zoom . 'x' . PHP_EOL;
    echo 'X: ' . $minX . ' - ' . $maxX . PHP_EOL;
    echo 'Y: ' . $minY . ' - ' . $maxY . PHP_EOL;
    echo 'Z: ' . $minZ . ' - ' . $maxZ . PHP_EOL;

    for ($x = $minX; $x <= $maxX; $x++) {
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $ranges[$x][$y][$z] = 0;

                foreach ($bots2 as $bot) {
                    $dist = abs($bot['x'] - $x) + abs($bot['y'] - $y) + abs($bot['z'] - $z);

                    if ($dist <= $bot['r']) {
                        $ranges[$x][$y][$z]++;
                    }
                }

                if ($ranges[$x][$y][$z] > $maxRange) {
                    $maxRange = $ranges[$x][$y][$z];
                }
            }
        }
    }

    $minX = $minY = $minZ = PHP_INT_MAX;
    $maxX = $maxY = $maxZ = PHP_INT_MIN;

    foreach ($ranges as $x => $xRange) {
        foreach ($xRange as $y => $yRange) {
            foreach ($yRange as $z => $value) {
                if ($value < $maxRange) {
                    unset($ranges[$x][$y][$z]);
                    continue;
                }

                $minX = min($minX, $x);
                $minY = min($minY, $y);
                $minZ = min($minZ, $z);
                $maxX = max($maxX, $x);
                $maxY = max($maxY, $y);
                $maxZ = max($maxZ, $z);
            }
        }
    }

    echo 'Max bots in range: ' . $maxRange . PHP_EOL;

    $minX = $minX * 10 - 10;
    $maxX = $maxX * 10 + 10;
    $minY = $minY * 10 - 8;
    $maxY = $maxY * 10 + 12;
    $minZ = $minZ * 10 - 10;
    $maxZ = $maxZ * 10 + 10;

    if ($zoom === 1) {
        $dists = [];

        foreach ($ranges as $x => $xRange) {
            foreach ($xRange as $y => $yRange) {
                foreach ($yRange as $z => $value) {
                    $dists[] = $x + $y + $z;
                }
            }
        }

        echo 'Answer 2: ' . max($dists) . PHP_EOL;
        break;
    }

    $zoom /= 10;
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
