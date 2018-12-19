<?php

$input = '#ip 3
addi 3 16 3
seti 1 0 4
seti 1 7 2
mulr 4 2 1
eqrr 1 5 1
addr 1 3 3
addi 3 1 3
addr 4 0 0
addi 2 1 2
gtrr 2 5 1
addr 3 1 3
seti 2 6 3
addi 4 1 4
gtrr 4 5 1
addr 1 3 3
seti 1 3 3
mulr 3 3 3
addi 5 2 5
mulr 5 5 5
mulr 3 5 5
muli 5 11 5
addi 1 6 1
mulr 1 3 1
addi 1 13 1
addr 5 1 5
addr 3 0 3
seti 0 6 3
setr 3 1 1
mulr 1 3 1
addr 3 1 1
mulr 3 1 1
muli 1 14 1
mulr 1 3 1
addr 5 1 5
seti 0 0 0
seti 0 3 3';
$start = microtime(true);
$input = explode("\n", $input);

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

$ipValue = (int) explode(' ', array_shift($input))[1];
$program = [];
$pointer = 0;
$registers = [1, 0, 0, 0, 0, 0];

foreach ($input as $line) {
    $part = explode(' ', $line);
    $program[] = [$part[0], (int) $part[1], (int) $part[2], (int) $part[3]];
}

$counter = 0;
$step1 = $step2 = false;

while (true) {
    $counter++;
    $instr = $program[$pointer];
    $registers[$ipValue] = $pointer;
    echo 'Pointer: ' . $pointer . PHP_EOL;
    echo 'Executing ' . implode(' ', $instr) . PHP_EOL;
    $functions[$instr[0]]($instr[1], $instr[2], $instr[3], $registers);
    $pointer = $registers[$ipValue];
    $pointer++;

    echo 'Pointer: ' . $pointer . PHP_EOL;

    print_r($registers);

    if ($pointer < 0 || $pointer >= count($program)) {
        break;
    }

    if (!$step1 && $counter > 10000 && $pointer === 9) {
        $registers[2] = $registers[5] + 1;
        $registers[4] = $registers[5];
        $step1 = true;
    }

    if ($counter > 20000) {
        echo 'Break!' . PHP_EOL;
        break;
    }
}

print_r($registers);

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
exit;

// Pointer -> 3 register
$registers = [1, 0, 0, 0, 0, 0];

//addi 3 16 3
$registers[3] = $registers[3] + 16;                         // L 0
//seti 1 0 4
$registers[4] = 1;                                          // L 1
//seti 1 7 2
$registers[2] = 1;                                          // L 2
//mulr 4 2 1
$registers[1] = $registers[4] * $registers[2];              // L 3
//eqrr 1 5 1
$registers[1] = $registers[1] === $registers[5] ? 1 : 0;    // L 4
//addr 1 3 3
$registers[3] = $registers[1] + $registers[3];              // L 5
//addi 3 1 3
$registers[3] = $registers[3] + 1;                          // L 6
//addr 4 0 0
$registers[0] = $registers[4] + $registers[0];              // L 7
//addi 2 1 2
$registers[2] = $registers[2] + 1;                          // L 8
//gtrr 2 5 1
$registers[1] = $registers[2] > $registers[5] ? 1 : 0;      // L 9
//addr 3 1 3
$registers[3] = $registers[3] + $registers[1];              // L 10
//seti 2 6 3
$registers[3] = 2;                                          // L 11
//addi 4 1 4
$registers[4] = $registers[4] + 1;                          // L 12
//gtrr 4 5 1
$registers[1] = $registers[4] > $registers[5] ? 1 : 0;      // L 13
//addr 1 3 3
$registers[3] = $registers[1] + $registers[3];              // L 14
//seti 1 3 3
$registers[3] = 1;                                          // L 15
//mulr 3 3 3
$registers[3] = $registers[3] * $registers[3];              // L 16
//addi 5 2 5
$registers[5] = $registers[5] + 2;                          // L 17
//mulr 5 5 5
$registers[5] = $registers[5] * $registers[5];              // L 18
//mulr 3 5 5
$registers[5] = $registers[3] * $registers[5];              // L 19
//muli 5 11 5
$registers[5] = $registers[5] * 11;                         // L 20
//addi 1 6 1
$registers[1] = $registers[1] + 6;                          // L 21
//mulr 1 3 1
$registers[1] = $registers[1] * $registers[3];              // L 22
//addi 1 13 1
$registers[1] = $registers[1] + 13;                         // L 23
//addr 5 1 5
$registers[5] = $registers[5] + $registers[1];              // L 24
//addr 3 0 3
$registers[3] = $registers[3] + $registers[0];              // L 25
//seti 0 6 3
$registers[3] = 0;                                          // L 26
//setr 3 1 1
$registers[1] = $registers[3];                              // L 27
//mulr 1 3 1
$registers[1] = $registers[1] * $registers[3];              // L 28
//addr 3 1 1
$registers[1] = $registers[3] + $registers[1];              // L 29
//mulr 3 1 1
$registers[1] = $registers[3] * $registers[1];              // L 30
//muli 1 14 1
$registers[1] = $registers[1] * 14;                         // L 31
//mulr 1 3 1
$registers[1] = $registers[1] * $registers[3];              // L 32
//addr 5 1 5
$registers[5] = $registers[5] + $registers[1];              // L 33
//seti 0 0 0
$registers[0] = 0;                                          // L 34
//seti 0 3 3
$registers[3] = 0;                                          // L 35


