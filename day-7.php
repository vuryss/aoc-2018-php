<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$reqs = $available = [];

foreach ($input as $line) {
    $parts = explode(' ', $line);
    $reqs[$parts[7]][] = $parts[1];
    $available[$parts[1]] = true;
}

$start = array_keys(array_diff_key($available, $reqs));

// Part 1
$result    = '';
$available = $start;

do {
    sort($available);
    $result .= array_shift($available);

    foreach ($reqs as $step => $requirements) {
        if (strpos($result, $step) !== false || in_array($step, $available)) {
            continue;
        }

        foreach ($requirements as $st) {
            if (strpos($result, $st) === false) {
                continue 2;
            }
        }

        $available[] = $step;
    }
} while (!empty($available));

echo 'Answer 1: ' . $result . PHP_EOL;

// Part 2
$available    = $start;
$workers      = array_fill(0, 5, ['idle', null, 0]);
$second       = 0;
$notAvailable = [];
$completed    = [];
$allIdle      = false;

while (!$allIdle) {
    sort($available);
    $allIdle = true;

    foreach ($reqs as $step => $requirements) {
        if (isset($completed[$step]) || isset($notAvailable[$step]) || in_array($step, $available)) {
            continue;
        }

        foreach ($requirements as $st) {
            if (!isset($completed[$st])) {
                continue 2;
            }
        }

        $available[] = $step;
    }

    foreach ($workers as $index => &$data) {
        if ($data[0] === 'idle' && !empty($available)) {
            $data = ['working', $available[0], $second];
            $notAvailable[array_shift($available)] = true;
        }

        $allIdle = $data[0] !== 'working' && $allIdle;

        if ($data[0] === 'working' && $second - $data[2] === ord($data[1]) - 5) {
            $completed[$data[1]] = true;
            $data = ['idle', null, $second];
        }
    }

    $second++;
}

echo 'Answer 2: ' . ($second - 1) . PHP_EOL;
