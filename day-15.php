<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);
$input = explode("\n", $input);

class Unit
{
    public $type;
    public $health;
    public $attack;
    public $x;
    public $y;
    public $alive = true;

    public function __construct($type, $health, $attack, $x, $y)
    {
        $this->type = $type;
        $this->health = $health;
        $this->attack = $attack;
        $this->x = $x;
        $this->y = $y;
    }

    public function attack(Unit $unit)
    {
        $unit->health -= $this->attack;

        if ($unit->health <= 0) {
            $unit->alive = false;

            if ($unit->type === 'E') {
                return false;
            }
        }

        return true;
    }

    public function getEnemy(array $units)
    {
        $positions = [
            [$this->x, $this->y - 1],
            [$this->x - 1, $this->y],
            [$this->x + 1, $this->y],
            [$this->x, $this->y + 1]
        ];

        $enemies = [];

        foreach ($positions as $p) {
            if (isset($units[$p[1]][$p[0]])
                && $units[$p[1]][$p[0]]->alive
                && $units[$p[1]][$p[0]]->type !== $this->type
            ) {
                $enemies[] = $units[$p[1]][$p[0]];
            }
        }

        if (empty($enemies)) {
            return null;
        }

        usort(
            $enemies,
            function($a, $b) {
                if ($a->health < $b->health) {
                    return -1;
                } elseif ($a->health == $b->health) {
                    if ($a->y < $b->y) {
                        return -1;
                    } elseif ($a->y == $b->y) {
                        return $a->x < $b->x ? -1 : 1;
                    }
                }

                return 1;
            }
        );

        return $enemies[0];
    }
}

$map = [];
$units = [];

foreach ($input as $y => $line) {
    $a = str_split($line);
    foreach ($a as $x => $item) {
        switch ($item) {
            case '#': $map[$y][$x] = '#'; break;
            case '.': $map[$y][$x] = '.'; break;
            case 'E':
                $map[$y][$x] = '.';
                $units[$y][$x] = new Unit('E', 200, 3, $x, $y);
                break;
            case 'G':
                $map[$y][$x] = '.';
                $units[$y][$x] = new Unit('G', 200, 3, $x, $y);
                break;
        }
    }
}

echo 'Answer: ' . solve($map, $units) . PHP_EOL;

$attack = 4;

while ($attack++) {
    $units = [];

    foreach ($input as $y => $line) {
        $a = str_split($line);
        foreach ($a as $x => $item) {
            if ($item === 'E' || $item === 'G') {
                $units[$y][$x] = new Unit($item, 200, $item === 'E' ? $attack : 3, $x, $y);
            }
        }
    }

    if ($result = solve($map, $units, true)) {
        echo 'Answer 2: ' . $result . PHP_EOL;
        break;
    }
}

/**
 * @param Unit[][] $units
 *
 * @return bool
 */
function isGameOver($units) {
    $elves = $goblins = 0;

    foreach ($units as $xUnits) {
        foreach ($xUnits as $unit) {
            if (!$unit->alive) {
                continue;
            }
            if ($unit->type === 'E') {
                $elves++;
            } else {
                $goblins++;
            }
        }
    }

    return $elves === 0 || $goblins === 0;
}

/**
 * @param array    $map
 * @param Unit[][] $units
 * @param bool     $flag
 *
 * @return int
 */
function solve($map, $units, $flag = false)
{
    $cycles = 0;

    while (true) {
        $cycles++;
        ksort($units);

        foreach ($units as $y => $lineUnits) {
            ksort($lineUnits);
            foreach ($lineUnits as $x => $unit) {
                if (!$unit->alive) {
                    continue;
                }

                if ($target = $unit->getEnemy($units)) {
                    $result = $unit->attack($target);
                    if ($flag && !$result) {
                        return false;
                    }
                    continue;
                }

                $closest = null;
                $targets = [];
                $queue = [[$x, $y - 1, 0, []], [$x - 1, $y, 0, []], [$x + 1, $y, 0, []], [$x, $y + 1, 0, []]];
                $checked = [$x => [$y => true]];

                while (!empty($queue)) {
                    $a = array_shift($queue);

                    if (isset($checked[$a[0]][$a[1]])) continue;
                    $checked[$a[0]][$a[1]] = true;

                    if ($closest !== null && $closest < $a[2]) {
                        break;
                    }

                    if ($map[$a[1]][$a[0]] === '#') continue;

                    if (isset($units[$a[1]][$a[0]]) && $units[$a[1]][$a[0]]->alive) {
                        if ($units[$a[1]][$a[0]]->type === $unit->type) continue;
                        $closest = $a[2];
                        $targets[] = [
                            'first' => empty($a[3]) ? [$a[0], $a[1]] : $a[3],
                            'last'  => [$a[0], $a[1]],
                        ];
                    }

                    $nextDistance = $a[2] + 1;
                    $a[3] = empty($a[3]) ? [$a[0], $a[1]] : $a[3];

                    array_push(
                        $queue,
                        [$a[0], $a[1] - 1, $nextDistance, $a[3]],
                        [$a[0] - 1, $a[1], $nextDistance, $a[3]],
                        [$a[0] + 1, $a[1], $nextDistance, $a[3]],
                        [$a[0], $a[1] + 1, $nextDistance, $a[3]]
                    );
                }

                if ($closest === null && isGameOver($units)) {
                    $cycles--;
                    break 3;
                }

                // MOVE!
                $moves = [];

                foreach ($targets as $target) {
                    $last = $target['last'];
                    $moves[$last[1] . '.' . $last[0]] = $target;
                }

                ksort($moves);

                foreach ($moves as $target) {
                    $x2 = $target['first'][0];
                    $y2 = $target['first'][1];
                    unset($units[$y][$x]);
                    $unit->x = $x2;
                    $unit->y = $y2;
                    $units[$y2][$x2] = $unit;
                    break;
                }

                // ATTACK AFTER MOVE
                if ($target = $unit->getEnemy($units)) {
                    $result = $unit->attack($target);
                    if ($flag && !$result) {
                        return false;
                    }
                    continue;
                }
            }
        }
    }

    $sum = 0;

    foreach ($units as $y => $lineUnits) {
        foreach ($lineUnits as $x => $unit) {
            if ($unit->alive) {
                $sum += $unit->health;
            }
        }
    }

    return $cycles * $sum;
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
