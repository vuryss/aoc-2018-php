<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);

[$immuneData, $infectionData] = explode("\n\n", $input);
$immuneData    = array_slice(explode("\n", $immuneData), 1);
$infectionData = array_slice(explode("\n", $infectionData), 1);
$immune        = $infection = [];
$sort = [
    'byInitiative' => function($a, $b) { return $a->init < $b->init ? 1 : -1; },
    'byPowerAndInitiative' => function($a, $b) { return $a->power < $b->power ? 1 : ($a->power === $b->power && $a->init < $b->init ? 1 : -1); },
    'byDamagePowerAndInitiative' => function($a, $b) {
        if (key($a) < key($b)) {
            return 1;
        } elseif (key($a) === key($b) && key(current($a)) < key(current($b))) {
            return 1;
        } elseif (key($a) === key($b) && key(current($a)) === key(current($b)) && current(current($a)) < current(current($b))) {
            return 1;
        } else {
            return -1;
        }
    }
];

foreach ([$immuneData, $infectionData] as $key => $dataArr) {
    foreach ($dataArr as $data) {
        preg_match(
            '/(?<units>\d+).*?(?<health>\d+).*?(?<damage>\d+)\s(?<type>\w+).*?(?<initiative>\d+)/',
            $data,
            $matches
        );
        preg_match('/\((.*)\)/', $data, $attrMatches);

        $group = (object)[
            'type'     => $key ? 'infection' : 'immune',
            'hp'       => (int)$matches['health'],
            'dmg'      => (int)$matches['damage'],
            'dmgType'  => $matches['type'],
            'init'     => (int)$matches['initiative'],
            'units'    => (int)$matches['units'],
            'power'    => (int)$matches['damage'] * (int)$matches['units'],
            'weakTo'   => [],
            'immuneTo' => [],
        ];

        if (!empty($attrMatches[1])) {
            $attr = explode('; ', $attrMatches[1]);

            foreach ($attr as $item) {
                if (strpos($item, 'weak to') === 0) {
                    $group->weakTo = explode(', ', substr($item, 8));
                } elseif (strpos($item, 'immune to') === 0) {
                    $group->immuneTo = explode(', ', substr($item, 10));
                }
            }
        }

        if ($key) {
            $infection[] = $group;
        } else {
            $immune[] = $group;
        }
        $groups[] = $group;
    }
}


function cloneGroups($groups, $boost = 0)
{
    $newGroups = $infections = $immune = [];

    foreach ($groups as $item) {
        $newItem = clone $item;

        if ($newItem->type === 'immune') {
            $newItem->dmg += $boost;
            $newItem->power = $newItem->dmg * $newItem->units;
        }

        $newGroups[] = $newItem;

        if ($newItem->type === 'immune') {
            $immune[] = $newItem;
        } else {
            $infections[] = $newItem;
        }
    }

    return [$newGroups, $immune, $infections];
}

function battle($groups, $infection, $immune)
{
    global $sort;

    while (true) {
        // Phase 1 - target selection
        usort($groups, $sort['byPowerAndInitiative']);

        foreach ($groups as $group) {
            $targets = $group->type === 'immune' ? $infection : $immune;

            $damageMap = [];

            foreach ($targets as $key => $target) {
                // Check if this isn't already a target to someone else
                foreach ($groups as $group2) {
                    if (isset($group2->target) && $group2->target === $target) {
                        continue 2;
                    }
                }

                if (in_array($group->dmgType, $target->immuneTo)) {
                    continue;
                } elseif (in_array($group->dmgType, $target->weakTo)) {
                    $damage = $group->power * 2;
                } else {
                    $damage = $group->power;
                }

                $damageMap[$key][$damage][$target->power] = $target->init;
            }

            uasort($damageMap, $sort['byDamagePowerAndInitiative']);
            $group->target = empty($damageMap) ? null : $targets[key($damageMap)];
        }

        // Phase 2 - attack
        $hasCasualties = false;
        usort($groups, $sort['byInitiative']);

        foreach ($groups as $group) if ($group->units && $group->target) {
            $target        = $group->target;
            $group->target = null;

            if (in_array($group->dmgType, $target->weakTo)) {
                $damage = $group->power * 2;
            } else {
                $damage = $group->power;
            }

            $killedUnits = (int) floor($damage / $target->hp);
            if ($killedUnits > 0) {
                $hasCasualties = true;
            }
            if ($killedUnits > $target->units) {
                $killedUnits = $target->units;
            }
            $target->units -= $killedUnits;
            $target->power = $target->units * $target->dmg;
        }

        if (!$hasCasualties) {
            return false;
        }

        // Check
        foreach ($groups as $key => $group) {
            if ($group->units <= 0) {
                unset($groups[$key]);
            }
        }

        foreach ($immune as $key => $group) {
            if ($group->units <= 0) {
                unset($immune[$key]);
            }
        }

        foreach ($infection as $key => $group) {
            if ($group->units <= 0) {
                unset($infection[$key]);
            }
        }

        if (empty($immune) || empty($infection)) {
            break;
        }
    }

    return $groups;
}

[$cloneGroups, $cloneImmune, $cloneInfection] = cloneGroups($groups, 0);
$result = battle($cloneGroups, $cloneInfection, $cloneImmune);

$sum = 0;

foreach ($result as $group) {
    $sum += $group->units;
}

echo 'Answer 1: ' . $sum . PHP_EOL;
echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;

$boost = 1;
$leastBoost = null;

while (true) {
    [$cloneGroups, $cloneImmune, $cloneInfection] = cloneGroups($groups, $boost);
    $result = battle($cloneGroups, $cloneInfection, $cloneImmune);

    if ($result && current($result)->type === 'immune') {
        $sum = 0;

        foreach ($result as $group) {
            $sum += $group->units;
        }

        $leastBoost = $sum;
    } elseif ($leastBoost) {
        echo 'Answer 2: ' . $sum . PHP_EOL;
        break;
    }

    $boost = $leastBoost ? $boost - 1 : $boost + 10;
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
