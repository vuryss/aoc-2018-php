<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

$reqs = $initReqs = $available = [];

foreach ($input as $line) {
    $parts = explode(' ', $line);
    $reqs[$parts[7]][] = $initReqs[$parts[7]][] = $parts[1];
    $available[$parts[1]] = true;
}

$start = array_keys(array_diff_key($available, $reqs));

// Part 1
$result    = '';
$available = $start;

do {
    sort($available);
    unset($reqs[$available[0]]);
    $result .= array_shift($available);

    foreach ($reqs as $step => $requirements) {
        foreach ($requirements as $st) {
            if (strpos($result, $st) === false) {
                continue 2;
            }
        }

        $available[] = $step;
        unset($reqs[$step]);
    }
} while (!empty($available));

echo 'Answer 1: ' . $result . PHP_EOL;

// Part 2
[$available, $reqs] = [$start, $initReqs];
$workers   = array_fill(0, 5, ['idle', null, 0]);
$second    = 0;
$completed = $notAvailable = [];

while (count($completed) != strlen($result) && ++$second) {
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

        if ($data[0] === 'working' && $second - $data[2] === ord($data[1]) - 5) {
            $completed[$data[1]] = true;
            $data = ['idle', null, $second];
        }
    }
}

echo 'Answer 2: ' . $second . PHP_EOL;
