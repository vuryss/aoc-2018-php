<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);

list ($part1, $part2) = explode("\n\n\n\n", $input);
$part1 = explode("\n\n", $part1);

$functions = [
    'addr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $reg[$a] + $reg[$b];
        return true;
    },
    'addi' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a] + $b;
        return true;
    },
    'mulr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $reg[$a] * $reg[$b];
        return true;
    },
    'muli' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a] * $b;
        return true;
    },
    'banr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $reg[$a] & $reg[$b];
        return true;
    },
    'bani' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a] & $b;
        return true;
    },
    'borr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $reg[$a] | $reg[$b];
        return true;
    },
    'bori' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a] | $b;
        return true;
    },
    'setr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a];
        return true;
    },
    'seti' => function($a, $b, $c, array &$reg) {
        $reg[$c] = $a;
        return true;
    },
    'gtir' => function($a, $b, $c, array &$reg) {
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $a > $reg[$b] ? 1 : 0;
        return true;
    },
    'gtri' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a] > $b ? 1 : 0;
        return true;
    },
    'gtrr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $reg[$a] > $reg[$b] ? 1 : 0;
        return true;
    },
    'eqir' => function($a, $b, $c, array &$reg) {
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $a === $reg[$b] ? 1 : 0;
        return true;
    },
    'eqri' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        $reg[$c] = $reg[$a] === $b ? 1 : 0;
        return true;
    },
    'eqrr' => function($a, $b, $c, array &$reg) {
        if ($a < 0 || $a > 3) return false;
        if ($b < 0 || $b > 3) return false;
        $reg[$c] = $reg[$a] === $reg[$b] ? 1 : 0;
        return true;
    },
];

// Part 1
$result = 0;

foreach ($part1 as $data) {
    list($before, $instr, $after) = explode("\n", $data);
    preg_match_all('/\d+/', $before, $before);
    preg_match_all('/\d+/', $after, $after);
    $instr = explode(' ', $instr);
    $count = 0;

    foreach ($functions as $code => $fn) {
        $tempReg = array_map('intval', $before[0]);
        $fn((int) $instr[1], (int) $instr[2], (int) $instr[3], $tempReg);

        if (count(array_intersect_assoc($tempReg, array_map('intval', $after[0]))) == 4) {
            $count++;
        }
    }

    if ($count >= 3) {
        $result++;
    }
}

echo 'Answer 1: ' . $result . PHP_EOL;

// Part 2
$map = [];

while (count($map) < count($functions)) {
    foreach ($part1 as $data) {
        list($before, $instr, $after) = explode("\n", $data);
        preg_match_all('/\d+/', $before, $before);
        preg_match_all('/\d+/', $after, $after);
        $instr   = explode(' ', $instr);
        $matched = null;
        $count   = 0;

        if (isset($map[(int) $instr[0]])) {
            continue;
        }

        foreach ($functions as $code => $fn) {
            if (in_array($code, $map)) {
                continue;
            }

            $tempReg = array_map('intval', $before[0]);
            $fn((int) $instr[1], (int) $instr[2], (int) $instr[3], $tempReg);

            if (count(array_intersect_assoc($tempReg, array_map('intval', $after[0]))) == 4) {
                $matched = $code;
                $count++;
            }
        }

        if ($count === 1) {
            $map[(int) $instr[0]] = $matched;
        }
    }
}

$part2 = explode("\n", $part2);
$registers = [0,0,0,0];

foreach ($part2 as $line) {
    list($code, $a, $b, $c) = explode(' ', $line);
    $code = $map[$code];
    $functions[$code]((int) $a, (int) $b, (int) $c, $registers);
}

echo 'Answer 2: ' . $registers[0] . PHP_EOL;
echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
