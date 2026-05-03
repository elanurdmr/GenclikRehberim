<?php
require __DIR__ . '/../includes/CrosswordEngine.php';
$w = [
    ['id' => 1, 'clue' => 'a', 'answer' => 'YARDIM'],
    ['id' => 2, 'clue' => 'b', 'answer' => 'DİK'],
    ['id' => 3, 'clue' => 'c', 'answer' => 'ARAMAK'],
    ['id' => 4, 'clue' => 'd', 'answer' => 'NEFES'],
    ['id' => 5, 'clue' => 'e', 'answer' => 'EMPATİ'],
];
$r = crossword_generate($w, 19, 19, 8);
echo $r ? 'OK' : 'FAIL';
