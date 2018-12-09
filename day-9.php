<?php

$input = trim(file_get_contents('input/' . substr(basename(__FILE__), 0, -4)));
preg_match('/(\d+).*?(\d+)/', $input, $matches);

echo 'Answer 1: ' . play((int) $matches[1], (int) $matches[2]) . PHP_EOL;
echo 'Answer 2: ' . play((int) $matches[1], (int) $matches[2] * 100) . PHP_EOL;

function play($players, $marbles) {
    $list = new SplDoublyLinkedList();
    $list->push(1);
    $list->push(0);
    $player = 1;
    $score = [];

    for ($i = 2; $i <= $marbles; $i++) {
        if (++$player > $players) {
            $player = 1;
        }

        if ($i % 23 == 0) {
            $score[$player] = ($score[$player] ?? 0) + $i;

            for ($j = 0; $j < 8; $j++) {
                $list->unshift($list->pop());
            }

            $score[$player] += $list->pop();

            $list->push($list->shift());
            $list->push($list->shift());

            continue;
        }

        $list->push($i);
        $list->push($list->shift());
    }

    rsort($score);

    return $score[0];
}
