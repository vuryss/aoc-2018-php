<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$input = explode("\n", $input);

// Sort
sort($input);

// Parse guard data & record most sleep time per guard and most sleep time for each guard per minute.
$guard = $startSleep = 0;
$mostSleepTime = $minuteSleepTime = $guardSleep = [];

foreach ($input as $line) {
    preg_match('/\:(\d{2}).*?(?:(?<guard>\d+)|(?<wakes>wakes)|(?<falls>falls))/', $line, $matches);

    $matches['guard'] && $guard = (int) $matches['guard'];
    isset($matches['falls']) && $startSleep = (int) $matches[1];

    if (isset($matches['wakes'])) {
        for ($i = $startSleep; $i < (int) $matches[1]; $i++) {
            $mostSleepTime[$guard]         = ($mostSleepTime[$guard] ?? 0) + 1;
            $minuteSleepTime[$guard][$i]   = ($minuteSleepTime[$guard][$i] ?? 0) + 1;
            $guardSleep[$guard . '.' . $i] = ($guardSleep[$guard . '.' . $i] ?? 0) + 1;
        }
    }
}

arsort($mostSleepTime);
$guard = key($mostSleepTime);
arsort($minuteSleepTime[$guard]);
arsort($guardSleep);

echo 'Answer 1: ' . ($guard * key($minuteSleepTime[$guard])) . PHP_EOL;
echo 'Answer 2: ' . array_product(explode('.', key($guardSleep))) . PHP_EOL;
