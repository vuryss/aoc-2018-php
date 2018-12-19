<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);

$ipValue = (int) explode(' ', array_shift($input))[1];
$program = [];

foreach ($input as $line) {
    $part = explode(' ', $line);
    $program[] = [$part[0], (int) $part[1], (int) $part[2], (int) $part[3]];
}
$steps = count($program);

$functions = [
    'addr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] + $reg[$b]; },
    'addi' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] + $b; },
    'mulr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] * $reg[$b]; },
    'muli' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] * $b; },
    'banr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] & $reg[$b]; },
    'bani' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] & $b; },
    'borr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] | $reg[$b]; },
    'bori' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] | $b; },
    'setr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a]; },
    'seti' => function($a, $b, $c, array &$reg) { $reg[$c] = $a; },
    'gtir' => function($a, $b, $c, array &$reg) { $reg[$c] = $a > $reg[$b] ? 1 : 0; },
    'gtri' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] > $b ? 1 : 0; },
    'gtrr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] > $reg[$b] ? 1 : 0; },
    'eqir' => function($a, $b, $c, array &$reg) { $reg[$c] = $a === $reg[$b] ? 1 : 0; },
    'eqri' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] === $b ? 1 : 0; },
    'eqrr' => function($a, $b, $c, array &$reg) { $reg[$c] = $reg[$a] === $reg[$b] ? 1 : 0; },
];

// Part 1
$pointer = -1;
$registers = [0, 0, 0, 0, 0, 0];

while (++$pointer < $steps) {
    $instr = $program[$pointer];
    $registers[$ipValue] = $pointer;
    $functions[$instr[0]]($instr[1], $instr[2], $instr[3], $registers);
    $pointer = $registers[$ipValue];
}

echo 'Answer 1: ' . $registers[0] . PHP_EOL;

// Part 2
$pointer = -1;
$registers = [1, 0, 0, 0, 0, 0];
$counter = 0;

while (++$pointer < $steps) {
    $counter++;
    $instr = $program[$pointer];
    $registers[$ipValue] = $pointer;
    $functions[$instr[0]]($instr[1], $instr[2], $instr[3], $registers);
    $pointer = $registers[$ipValue];

    if ($counter > 100) {
        $sqrt = (int) sqrt($registers[5]);
        $result = 0;
        for ($i = 1; $i <= $sqrt; $i++) {
            if ($registers[5] % $i === 0) {
                $result += $i + $registers[5] / $i;
            }
        }
        echo 'Answer 2: ' . $result . PHP_EOL;
        break;
    }
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
