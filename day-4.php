<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

// Sort
sort($input);

// Parse guard data & record most sleep time per guard and most sleep time for each guard per minute.
$guard = $startSleep = 0;
$mostSleepTime = $minuteSleepTime = [];

foreach ($input as $line) {
    preg_match('/\:(\d{2}).*?(?:(?<guard>\d+)|(?<wakes>wakes)|(?<falls>falls))/', $line, $matches);

    $matches['guard'] && $guard = (int) $matches['guard'];
    isset($matches['falls']) && $startSleep = (int) $matches[1];

    if (isset($matches['wakes'])) {
        for ($i = $startSleep; $i < (int) $matches[1]; $i++) {
            $mostSleepTime[$guard]       = ($mostSleepTime[$guard] ?? 0) + 1;
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
    isset($guards[$guard]) && $guards[$guard] > $max1
        && ($max1 = $guards[$guard])
        && ($min1 = $minute);
    
    foreach ($guards as $id => $times) {
        $times > $max2
            && ($max2 = $times)
            && ($min2 = $minute)
            && ($gid = $id);
    }
}

echo 'Answer 1: ' . ($min1 * $guard) . PHP_EOL;
echo 'Answer 2: ' . ($min2 * $gid) . PHP_EOL;
