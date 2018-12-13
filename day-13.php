<?php

$input = file_get_contents('input/' . substr(basename(__FILE__), 0, -4));
$start = microtime(true);
$input = explode("\n", $input);
$grid = [];
$carts = [];
$num = 1;

$movesL = ['>' => '^', '^' => '<', '<' => 'v', 'v' => '>'];
$movesR = ['>' => 'v', 'v' => '<', '<' => '^', '^' => '>'];

foreach ($input as $line) {
    $grid[] = str_split($line);
}

foreach ($grid as $y => $line) {
    foreach ($line as $x => $value) {
        if (!in_array($value, ['^', 'v', '>', '<'])) {
            continue;
        }

        $carts[] = ['x' => $x, 'y' => $y, 'move' => 'left', 'icon' => $value];

        switch ($value) {
            case '^':
            case 'v':
                $grid[$y][$x] = '|';
                break;
            case '>':
            case '<':
                $grid[$y][$x] = '-';
                break;
        }
    }
}

$removed = [];
$first = false;

while (true) {
    uasort(
        $carts, function ($a, $b) {
            if ($a['y'] < $b['y']) {
                return -1;
            } elseif ($a['y'] === $b['y'] && $a['x'] < $b['x']) {
                return -1;
            }

            return 1;
        }
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
        $targetX = $targetY = 0;
        switch ($cart['icon']) {
            case '^':
                $targetX = $cart['x'];
                $targetY = $cart['y'] - 1;
                break;
            case 'v':
                $targetX = $cart['x'];
                $targetY = $cart['y'] + 1;
                break;
            case '>':
                $targetX = $cart['x'] + 1;
                $targetY = $cart['y'];
                break;
            case '<':
                $targetX = $cart['x'] - 1;
                $targetY = $cart['y'];
                break;
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

        switch ($grid[$targetY][$targetX]) {
            case '|':
                $carts[$key]['y'] = $targetY;
                break;
            case '-':
                $carts[$key]['x'] = $targetX;
                break;
            case '/':
                $carts[$key]['x'] = $targetX;
                $carts[$key]['y'] = $targetY;
                switch ($cart['icon']) {
                    case '^':
                        $carts[$key]['icon'] = '>';
                        break;

                    case 'v':
                        $carts[$key]['icon'] = '<';
                        break;

                    case '>':
                        $carts[$key]['icon'] = '^';
                        break;

                    case '<':
                        $carts[$key]['icon'] = 'v';
                        break;
                }
                break;
            case '\\':
                $carts[$key]['x'] = $targetX;
                $carts[$key]['y'] = $targetY;
                switch ($cart['icon']) {
                    case '^':
                        $carts[$key]['icon'] = '<';
                        break;

                    case 'v':
                        $carts[$key]['icon'] = '>';
                        break;

                    case '>':
                        $carts[$key]['icon'] = 'v';
                        break;

                    case '<':
                        $carts[$key]['icon'] = '^';
                        break;
                }
                break;
            case '+':
                $carts[$key]['x'] = $targetX;
                $carts[$key]['y'] = $targetY;
                switch ($cart['move']) {
                    case 'left':
                        $carts[$key]['icon'] = $movesL[$carts[$key]['icon']];
                        $carts[$key]['move'] = 'straight';
                        break;

                    case 'straight':
                        $carts[$key]['move'] = 'right';
                        break;

                    case 'right':
                        $carts[$key]['icon'] = $movesR[$carts[$key]['icon']];
                        $carts[$key]['move'] = 'left';
                        break;
                }
                break;
        }
    }
}
