<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
preg_match_all('/\d+/', $input, $matches);

$depth = (int) $matches[0][0];
$target = [(int) $matches[0][1], (int) $matches[0][2]];
$map = $emap = [];

for ($y = 0; $y <= $target[1]; $y++) {
    for ($x = 0; $x <= $target[0]; $x++) {
        getMapItem($map, $x, $y);
    }
}

function getMapItem(&$map, $x, $y) : int {
    return $map[$y][$x] ?? $map[$y][$x] = getErosionLevel($x, $y) % 3;
}

function getErosionLevel($x, $y) {
    global $target, $depth, $emap;

    if (!isset($emap[$y][$x])) {
        if (($x === 0 && $y === 0) || ($x === $target[0] && $y === $target[1])) {
            $index = 0;
        } elseif ($y === 0) {
            $index = $x * 16807;
        } elseif ($x === 0) {
            $index = $y * 48271;
        } else {
            $index = getErosionLevel($x - 1, $y) * getErosionLevel($x, $y - 1);
        }

        $emap[$y][$x] = ($index + $depth) % 20183;
    }

    return $emap[$y][$x];
}

$risk = 0;

for ($y = 0; $y <= $target[1]; $y++) {
    $risk += array_sum($map[$y]);
}

echo 'Answer 1: ' . $risk . PHP_EOL;

$queue = new Ds\PriorityQueue();
$queue->push(['x' => 0, 'y' =>  0, 'it' => 'T', 'minutes' => 0], PHP_INT_MAX);
$visited = [];

while (!$queue->isEmpty() && $item = $queue->pop()) {
    ['x' => $x, 'y' => $y, 'it' => $it, 'minutes' => $minutes] = $item;

    if (isset($visited[$x][$y][$it])) {
        continue;
    }
    $visited[$x][$y][$it] = true;

    if ($x === $target[0] && $y === $target[1]) {
        echo 'Answer 2: ' . $minutes . PHP_EOL;
        break;
    }

    $currentType = $map[$y][$x];
    $dirs = [[$x, $y - 1], [$x + 1, $y], [$x, $y + 1], [$x - 1, $y]];

    foreach ($dirs as [$x, $y]) if ($x >= 0 && $y >= 0 && !isset($visited[$x][$y][$it])) {
        if (!isset($map[$y][$x])) {
            getMapItem($map, $x, $y);
        }

        addQueueItem($queue, $x, $y, $it, $minutes, $currentType);
    }
}

function addQueueItem(Ds\PriorityQueue $queue, $x, $y, $item, $minutes, $currentType)
{
    global $map, $target;

    $type = $map[$y][$x];

    $forceTorch = $x === $target[0] && $y === $target[1];

    if ($type === 0) {
        if ($item === 'T') {
            $queue->push(
                ['x' => $x, 'y' => $y, 'it' => 'T', 'minutes' => $minutes + 1],
                PHP_INT_MAX - $minutes - 1
            );
            if ($currentType === 0) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'C', 'minutes' => $minutes + 8 + ($forceTorch ? 7 : 0)],
                    PHP_INT_MAX - $minutes - 8 - ($forceTorch ? 7 : 0)
                );
            }
        } elseif ($item === 'C') {
            if ($currentType === 0) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'T', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
            $queue->push(
                ['x' => $x, 'y' => $y, 'it' => 'C', 'minutes' => $minutes + 1 + ($forceTorch ? 7 : 0)],
                PHP_INT_MAX - $minutes - 1 - ($forceTorch ? 7 : 0)
            );
        } elseif ($item === 'N') {
            if ($currentType === 2) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'T', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
            if ($currentType === 1) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'C', 'minutes' => $minutes + 8 + ($forceTorch ? 7 : 0)],
                    PHP_INT_MAX - $minutes - 8 - ($forceTorch ? 7 : 0)
                );
            }
        }
    } elseif ($type === 1) {
        if ($item === 'N') {
            $queue->push(
                ['x' => $x, 'y' => $y, 'it' => 'N', 'minutes' => $minutes + 1],
                PHP_INT_MAX - $minutes - 1
            );
            if ($currentType === 1) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'C', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
        } elseif ($item === 'C') {
            if ($currentType === 1) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'N', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
            $queue->push(
                ['x' => $x, 'y' => $y, 'it' => 'C', 'minutes' => $minutes + 1],
                PHP_INT_MAX - $minutes - 1
            );
        } elseif ($item === 'T') {
            if ($currentType === 2) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'N', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
            if ($currentType === 0) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'C', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
        }
    } elseif ($type === 2) {
        if ($item === 'T') {
            $queue->push(
                ['x' => $x, 'y' => $y, 'it' => 'T', 'minutes' => $minutes + 1],
                PHP_INT_MAX - $minutes - 1
            );
            if ($currentType === 2) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'N', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
        } elseif ($item === 'N') {
            if ($currentType === 2) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'T', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
            $queue->push(
                ['x' => $x, 'y' => $y, 'it' => 'N', 'minutes' => $minutes + 1],
                PHP_INT_MAX - $minutes - 1
            );
        } elseif ($item === 'C') {
            if ($currentType === 0) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'T', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
            if ($currentType === 1) {
                $queue->push(
                    ['x' => $x, 'y' => $y, 'it' => 'N', 'minutes' => $minutes + 8],
                    PHP_INT_MAX - $minutes - 8
                );
            }
        }
    }
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
