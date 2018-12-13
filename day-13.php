<?php

$input = file_get_contents('input/' . substr(basename(__FILE__), 0, -4));
$start = microtime(true);
$input = explode("\n", $input);
$grid = [];
$carts = [];
$num = 1;

$move    = ['left' => 'straight', 'straight' => 'right', 'right' => 'left'];
$moves   = [
    'left'     => ['>' => '^', '^' => '<', '<' => 'v', 'v' => '>'],
    'right'    => ['>' => 'v', 'v' => '<', '<' => '^', '^' => '>'],
    'straight' => ['>' => '>', 'v' => 'v', '^' => '^', '<' => '<'],
];
$movesLS = ['^' => '<', 'v' => '>', '>' => 'v', '<' => '^'];
$movesRS = ['^' => '>', 'v' => '<', '>' => '^', '<' => 'v'];

foreach ($input as $line) {
    $grid[] = str_split($line);
}

foreach ($grid as $y => $line) {
    foreach ($line as $x => $value) {
        if (in_array($value, ['^', 'v', '>', '<'])) {
            $carts[] = ['x' => $x, 'y' => $y, 'move' => 'left', 'icon' => $value];
            $grid[$y][$x] = ($value === '^' || $value === 'v') ? '|' : '-';
        }
    }
}

$removed = [];
$first = false;

while (true) {
    uasort(
        $carts,
        function ($a, $b) { return $a['y'] < $b['y'] || $a['y'] === $b['y'] && $a['x'] < $b['x'] ? -1 : 1; }
    );

    if (count($carts) === 1) {
        $cart = current($carts);
        echo 'Answer 2: ' . $cart['x'] . ',' . $cart['y'] . PHP_EOL;
        echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
        exit;
    }

    foreach ($carts as $key => $cart) {
        if (in_array($key, $removed, true)) {
            continue;
        }

        $targetX = $cart['x'];
        $targetY = $cart['y'];

        switch ($cart['icon']) {
            case '^': $targetY = $cart['y'] - 1; break;
            case 'v': $targetY = $cart['y'] + 1; break;
            case '>': $targetX = $cart['x'] + 1; break;
            case '<': $targetX = $cart['x'] - 1; break;
        }

        foreach ($carts as $key2 => $cart2) {
            if ($targetX === $cart2['x'] && $targetY === $cart2['y']) {
                if (!$first) {
                    echo 'Answer 1: ' . $targetX . ',' . $targetY . PHP_EOL;
                    $first = true;
                }

                unset($carts[$key]);
                unset($carts[$key2]);
                $removed[] = $key;
                $removed[] = $key2;
                continue 2;
            }
        }

        $carts[$key]['x'] = $targetX;
        $carts[$key]['y'] = $targetY;

        switch ($grid[$targetY][$targetX]) {
            case '/':
                $carts[$key]['icon'] = $movesRS[$carts[$key]['icon']];
                break;
            case '\\':
                $carts[$key]['icon'] = $movesLS[$carts[$key]['icon']];
                break;
            case '+':
                $carts[$key]['icon'] = $moves[$cart['move']][$carts[$key]['icon']];
                $carts[$key]['move'] = $move[$carts[$key]['move']];
                break;
        }
    }
}
