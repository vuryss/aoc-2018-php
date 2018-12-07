<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$inp = [];
$dict = [];

foreach ($input as $line) {
    $parts = explode(' ', $line);
    $inp[$parts[7]][] = $parts[1];
    $dict[$parts[1]] = true;
    $dict[$parts[7]] = true;
}

$available = [];

foreach ($dict as $part => $dummy) {
    if (!isset($inp[$part])) {
        $available[] = $part;
    }
}

$copy = $available;

// Part 1
$result = '';

while (strlen($result) != count($dict)) {
    foreach ($inp as $step => $requirements) {
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

    sort($available);
    $result .= array_shift($available);
}

echo 'Answer 1: ' . $result . PHP_EOL;

// Part 2
$available = $copy;

$numWorkers = 5;
$workers = [];

for ($i = 0; $i < $numWorkers; $i++) {
    $workers[$i] = ['idle', null, 0];
}

$second = 0;
$notAvailable = [];
$completed = [];

while (true) {
    sort($available);

    foreach ($inp as $step => $requirements) {
        if (isset($completed[$step]) || isset($notAvailable[$step])) {
            continue;
        }

        if (in_array($step, $available)) {
            continue;
        }

        foreach ($requirements as $st) {
            if (!isset($completed[$st])) {
                continue 2;
            }
        }

        $available[] = $step;
    }

    foreach ($workers as $index => $data) {
        if ($data[0] === 'idle') {
            if (!empty($available)) {
                $workers[$index] = ['working', $available[0], $second];
                $notAvailable[$available[0]] = true;
                array_shift($available);
            }
        }

        $data = $workers[$index];

        if ($data[0] === 'working') {
            if ($second - $data[2] === ord($data[1]) - 5) {
                $completed[$data[1]] = true;
                $workers[$index] = ['idle', null, $second];
            }
        }
    }

    if (count($completed) == count($dict)) {
        break;
    }
    $second++;
}

echo 'Answer 2: ' . ($second + 1) . PHP_EOL;
