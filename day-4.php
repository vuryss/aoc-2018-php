<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

// Sort
usort($input, function($a, $b) { return strncmp($a, $b, 17); });

// Parse guard data & record most sleep time per guard and most sleep time for each guard per minute.
$guard = $startSleep = 0;
$mostSleepTime = $minuteSleepTime = [];

foreach ($input as $line) {
    preg_match('/\d{4}\-(\d{2}\-\d{2})\s\d{2}\:(\d{2}).*?(\w.*)/', $line, $matches);

    if (preg_match('/#(\d+)/', $matches[3], $m) && $guard = $m[1]) {
        continue;
    }

    if (strpos($matches[3], 'falls') !== false && $startSleep = (int) $matches[2]) {
        continue;
    }

    if (strpos($matches[3], 'wakes') !== false) {
        for ($i = $startSleep; $i < (int) $matches[2]; $i++) {
            $mostSleepTime[$guard] = ($mostSleepTime[$guard] ?? 0) + 1;
            $minuteSleepTime[$i][$guard] = ($minuteSleepTime[$i][$guard] ?? 0) + 1;
        }
    }
}

// Guard with most sleep time (Part 1)
arsort($mostSleepTime);
$guard = key($mostSleepTime);

// Find most sleeping guard most lazy day & the guard sleeping most in a given minute
$max1 = $max2 = $min1 = $min2 = $gid = 0;
foreach ($minuteSleepTime as $minute => $guards) {
    foreach ($guards as $id => $times) {
        $id === $guard && $times > $max1
            && ($max1 = $times)
            && ($min1 = $minute);

        $times > $max2
            && ($max2 = $times)
            && ($min2 = $minute)
            && ($gid = $id);
    }
}

echo 'Answer 1: ' . ($min1 * $guard) . PHP_EOL;
echo 'Answer 2: ' . ($min2 * $gid) . PHP_EOL;
