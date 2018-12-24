<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
$start = microtime(true);

[$immuneData, $infectionData] = explode("\n\n", $input);
$immuneData    = array_slice(explode("\n", $immuneData), 1);
$infectionData = array_slice(explode("\n", $infectionData), 1);
$immune = $infection = $groups = [];
$sort = [
    'byInitiative' => function($a, $b) { return $a->initiative < $b->initiative ? 1 : -1; },
    'byPowerAndInitiative' => function($a, $b) { return $a->power < $b->power ? 1 : ($a->power === $b->power && $a->initiative < $b->initiative ? 1 : -1); },
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

class UnitGroup
{
    public $type;
    public $count;
    public $health;
    public $damage;
    public $damageType;
    public $initiative;
    public $power;
    public $weakTo = [];
    public $immuneTo = [];
    public $target;

    public function __construct($type, $count, $health, $damage, $damageType, $initiative)
    {
        $this->type       = $type;
        $this->count      = $count;
        $this->health     = $health;
        $this->damage     = $damage;
        $this->damageType = $damageType;
        $this->initiative = $initiative;
        $this->power      = $this->damage * $this->count;
    }
}

foreach ([$immuneData, $infectionData] as $key => $dataArr) {
    foreach ($dataArr as $data) {
        preg_match('/(\d+).*?(\d+).*?(?:.*\((.*)\))?.*?(\d+)\s(\w+).*?(\d+)/', $data,$m);
        $unit = new UnitGroup($key ? 'infection' : 'immune', $m[1], $m[2], $m[4], $m[5], $m[6]);

        if (!empty($m[3])) {
            $attr = explode('; ', $m[3]);

            foreach ($attr as $item) {
                if (strpos($item, 'weak to') === 0) {
                    $unit->weakTo = array_flip(explode(', ', substr($item, 8)));
                } elseif (strpos($item, 'immune to') === 0) {
                    $unit->immuneTo = array_flip(explode(', ', substr($item, 10)));
                }
            }
        }

        if ($key) {
            $infection[] = $unit;
        } else {
            $immune[] = $unit;
        }
        $groups[] = $unit;
    }
}


function cloneGroups($groups, $boost = 0)
{
    $newGroups = [];

    foreach ($groups as $item) {
        $newItem = clone $item;

        if ($newItem->type === 'immune') {
            $newItem->damage += $boost;
            $newItem->power = $newItem->damage * $newItem->count;
        }

        $newGroups[] = $newItem;
    }

    return $newGroups;
}

function battle(array $groups)
{
    global $sort;

    while (true) {
        // Phase 1 - target selection
        usort($groups, $sort['byPowerAndInitiative']);
        $targetsSet = new Ds\Set();

        foreach ($groups as $group) {
            $damageMap = [];

            foreach ($groups as $key => $target) {
                if ($targetsSet->contains($target)
                    || $target->type === $group->type
                    || isset($target->immuneTo[$group->damageType])
                ) {
                    continue;
                }

                $damage = isset($target->weakTo[$group->damageType]) ? $group->power * 2 : $group->power;
                $damageMap[$key][$damage][$target->power] = $target->initiative;
            }

            uasort($damageMap, $sort['byDamagePowerAndInitiative']);
            $group->target = empty($damageMap) ? null : $groups[key($damageMap)];
            $targetsSet->add($group->target);
        }

        // Phase 2 - attack
        $hasCasualties = false;
        usort($groups, $sort['byInitiative']);

        foreach ($groups as $group) if ($group->count && $group->target) {
            $target        = $group->target;
            $group->target = null;

            $damage = isset($target->weakTo[$group->damageType]) ? $group->power * 2 : $group->power;

            $killedUnits = (int) ($damage / $target->health);
            if ($killedUnits > $target->count) $killedUnits = $target->count;

            $hasCasualties = $hasCasualties || $killedUnits > 0;
            $target->count -= $killedUnits;
            $target->power = $target->count * $target->damage;
        }

        if (!$hasCasualties) {
            return false;
        }

        // Check
        $count1 = $count2 = 0;

        foreach ($groups as $key => $group) {
            if ($group->count <= 0) {
                unset($groups[$key]);
            } else {
                $group->type === 'immune' ? $count1++ : $count2++;
            }
        }

        if (!$count1 || !$count2) {
            break;
        }
    }

    return $groups;
}

$cloneGroups = cloneGroups($groups, 0);
$result = battle($cloneGroups);

$sum = 0;

foreach ($result as $group) {
    $sum += $group->count;
}

echo 'Answer 1: ' . $sum . PHP_EOL;

$boost = 1;
$leastBoost = null;

while (true) {
    $cloneGroups = cloneGroups($groups, $boost);
    $result = battle($cloneGroups);

    if ($result && current($result)->type === 'immune') {
        $sum = 0;

        foreach ($result as $group) {
            $sum += $group->count;
        }

        $leastBoost = $sum;
    } elseif ($leastBoost) {
        echo 'Answer 2: ' . $sum . PHP_EOL;
        break;
    }

    $boost = $leastBoost ? $boost - 1 : $boost + 10;
}

echo 'Execution time: ' . (microtime(true) - $start) . PHP_EOL;
