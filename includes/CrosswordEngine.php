<?php
/**
 * CrosswordEngine — Kesişimli kare bulmaca yerleştirme (UTF-8 Türkçe)
 */
declare(strict_types=1);

/**
 * @param list<array{id:int,clue:string,answer:string}> $entries
 * @return array{
 *   grid: list<list<string>>,
 *   placed: list<array{id:int,clue:string,answer:string,r:int,c:int,across:bool}>,
 *   rows:int,
 *   cols:int
 * }|null
 */
function crossword_generate(array $entries, int $rows, int $cols, int $maxWords = 14): ?array
{
    if ($entries === []) {
        return null;
    }

    usort($entries, static function ($a, $b) {
        return mb_strlen($b['answer'], 'UTF-8') <=> mb_strlen($a['answer'], 'UTF-8');
    });

    $words = array_slice($entries, 0, min($maxWords, count($entries)));

    $empty = static function () use ($rows, $cols): array {
        $g = [];
        for ($r = 0; $r < $rows; $r++) {
            $g[$r] = array_fill(0, $cols, '#');
        }

        return $g;
    };

    $copy = static function (array $g): array {
        $o = [];
        foreach ($g as $r => $row) {
            $o[$r] = array_values($row);
        }

        return $o;
    };

    $at = static function (array $g, int $r, int $c) use ($rows, $cols): string {
        if ($r < 0 || $c < 0 || $r >= $rows || $c >= $cols) {
            return '#';
        }

        return $g[$r][$c];
    };

    $canPlace = static function (array $g, string $word, int $r, int $c, bool $across) use ($at, $rows, $cols): bool {
        $len = mb_strlen($word, 'UTF-8');
        if ($across) {
            if ($c + $len > $cols) {
                return false;
            }
            if ($at($g, $r, $c - 1) !== '#') {
                return false;
            }
            if ($at($g, $r, $c + $len) !== '#') {
                return false;
            }
        } else {
            if ($r + $len > $rows) {
                return false;
            }
            if ($at($g, $r - 1, $c) !== '#') {
                return false;
            }
            if ($at($g, $r + $len, $c) !== '#') {
                return false;
            }
        }

        for ($i = 0; $i < $len; $i++) {
            $rr = $across ? $r : $r + $i;
            $cc = $across ? $c + $i : $c;
            $ch = mb_substr($word, $i, 1, 'UTF-8');
            $cell = $g[$rr][$cc];
            if ($cell === '#') {
                if ($across) {
                    if ($at($g, $rr - 1, $cc) !== '#' || $at($g, $rr + 1, $cc) !== '#') {
                        return false;
                    }
                } else {
                    if ($at($g, $rr, $cc - 1) !== '#' || $at($g, $rr, $cc + 1) !== '#') {
                        return false;
                    }
                }
                continue;
            }
            if ($cell !== $ch) {
                return false;
            }
        }

        return true;
    };

    $apply = static function (array $g, string $word, int $r, int $c, bool $across) use ($copy): array {
        $ng = $copy($g);
        $len = mb_strlen($word, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $rr = $across ? $r : $r + $i;
            $cc = $across ? $c + $i : $c;
            $ng[$rr][$cc] = mb_substr($word, $i, 1, 'UTF-8');
        }

        return $ng;
    };

    $search = null;
    $search = static function (array $grid, array $placed, int $idx) use (
        &$search,
        $words,
        $rows,
        $cols,
        $canPlace,
        $apply,
        $empty
    ): ?array {
        if ($idx >= count($words)) {
            return ['grid' => $grid, 'placed' => $placed];
        }

        $w = $words[$idx];
        $ans = $w['answer'];

        if ($idx === 0) {
            $len = mb_strlen($ans, 'UTF-8');
            $sr = (int)floor($rows / 2);
            $sc = (int)max(0, floor(($cols - $len) / 2));
            if (!$canPlace($grid, $ans, $sr, $sc, true)) {
                return null;
            }
            $ng = $apply($grid, $ans, $sr, $sc, true);
            $np = $placed;
            $np[] = ['id' => $w['id'], 'clue' => $w['clue'], 'answer' => $ans, 'r' => $sr, 'c' => $sc, 'across' => true];

            return $search($ng, $np, $idx + 1);
        }

        foreach ($placed as $p) {
            $pw = $p['answer'];
            $pr = $p['r'];
            $pc = $p['c'];
            $pac = $p['across'];
            $lenp = mb_strlen($pw, 'UTF-8');
            $lenw = mb_strlen($ans, 'UTF-8');

            for ($i = 0; $i < $lenp; $i++) {
                $chp = mb_substr($pw, $i, 1, 'UTF-8');
                for ($j = 0; $j < $lenw; $j++) {
                    if (mb_substr($ans, $j, 1, 'UTF-8') !== $chp) {
                        continue;
                    }

                    if ($pac) {
                        $nr = $pr - $j;
                        $nc = $pc + $i;
                        $acrossNew = false;
                    } else {
                        $nr = $pr + $i;
                        $nc = $pc - $j;
                        $acrossNew = true;
                    }

                    if ($nr < 0 || $nc < 0) {
                        continue;
                    }
                    if (! $canPlace($grid, $ans, $nr, $nc, $acrossNew)) {
                        continue;
                    }
                    $ng = $apply($grid, $ans, $nr, $nc, $acrossNew);
                    $np = $placed;
                    $np[] = ['id' => $w['id'], 'clue' => $w['clue'], 'answer' => $ans, 'r' => $nr, 'c' => $nc, 'across' => $acrossNew];
                    $done = $search($ng, $np, $idx + 1);
                    if ($done !== null) {
                        return $done;
                    }
                }
            }
        }

        return null;
    };

    $start = $empty();
    $res = $search($start, [], 0);

    return $res;
}

/**
 * Boş kalan hücreleri blok yap; sınıra kırp.
 *
 * @param list<list<string>> $grid
 * @return array{grid: list<list<string>>, r0:int, c0:int, rows:int, cols:int}
 */
function crossword_trim_and_fill(array $grid): array
{
    $rows = count($grid);
    $cols = count($grid[0]);
    $rMin = $rows;
    $rMax = -1;
    $cMin = $cols;
    $cMax = -1;

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            if ($grid[$r][$c] !== '#') {
                $rMin = min($rMin, $r);
                $rMax = max($rMax, $r);
                $cMin = min($cMin, $c);
                $cMax = max($cMax, $c);
            }
        }
    }

    if ($rMax < 0) {
        return ['grid' => [[]], 'r0' => 0, 'c0' => 0, 'rows' => 0, 'cols' => 0];
    }

    $out = [];
    for ($r = $rMin; $r <= $rMax; $r++) {
        $row = [];
        for ($c = $cMin; $c <= $cMax; $c++) {
            $row[] = $grid[$r][$c] === '#' ? '#' : $grid[$r][$c];
        }
        $out[] = $row;
    }

    return ['grid' => $out, 'r0' => $rMin, 'c0' => $cMin, 'rows' => count($out), 'cols' => count($out[0])];
}

/**
 * @param list<list<string>> $grid
 * @return array{
 *   numbers: list<list<int>>,
 *   across: array<int, array{n:int,r:int,c:int,word:string,clue:string,id:int}>,
 *   down: array<int, array{n:int,r:int,c:int,word:string,clue:string,id:int}>
 * }
 */
function crossword_number_clues(array $grid, array $clueByWordUpper): array
{
    $h = count($grid);
    $w = count($grid[0]);

    $isBlock = static function (int $r, int $c) use ($grid, $h, $w): bool {
        if ($r < 0 || $c < 0 || $r >= $h || $c >= $w) {
            return true;
        }

        return $grid[$r][$c] === '#';
    };

    $numberAt = array_fill(0, $h, array_fill(0, $w, 0));
    $n = 0;

    for ($r = 0; $r < $h; $r++) {
        for ($c = 0; $c < $w; $c++) {
            if ($isBlock($r, $c)) {
                continue;
            }
            $startAcross = ($c === 0 || $isBlock($r, $c - 1)) && !$isBlock($r, $c + 1);
            $startDown = ($r === 0 || $isBlock($r - 1, $c)) && !$isBlock($r + 1, $c);
            if ($startAcross || $startDown) {
                $n++;
                $numberAt[$r][$c] = $n;
            }
        }
    }

    $across = [];
    $down = [];

    for ($r = 0; $r < $h; $r++) {
        for ($c = 0; $c < $w; $c++) {
            if ($isBlock($r, $c)) {
                continue;
            }
            $startAcross = ($c === 0 || $isBlock($r, $c - 1)) && !$isBlock($r, $c + 1);
            if ($startAcross) {
                $word = '';
                $cc = $c;
                while ($cc < $w && !$isBlock($r, $cc)) {
                    $word .= $grid[$r][$cc];
                    $cc++;
                }
                $num = $numberAt[$r][$c];
                $meta = $clueByWordUpper[$word] ?? null;
                $across[$num] = [
                    'n' => $num,
                    'r' => $r,
                    'c' => $c,
                    'word' => $word,
                    'clue' => $meta['clue'] ?? ('Kelime: ' . $word),
                    'id' => (int)($meta['id'] ?? 0),
                ];
            }
        }
    }

    for ($r = 0; $r < $h; $r++) {
        for ($c = 0; $c < $w; $c++) {
            if ($isBlock($r, $c)) {
                continue;
            }
            $startDown = ($r === 0 || $isBlock($r - 1, $c)) && !$isBlock($r + 1, $c);
            if ($startDown) {
                $word = '';
                $rr = $r;
                while ($rr < $h && !$isBlock($rr, $c)) {
                    $word .= $grid[$rr][$c];
                    $rr++;
                }
                $num = $numberAt[$r][$c];
                $meta = $clueByWordUpper[$word] ?? null;
                $down[$num] = [
                    'n' => $num,
                    'r' => $r,
                    'c' => $c,
                    'word' => $word,
                    'clue' => $meta['clue'] ?? ('Kelime: ' . $word),
                    'id' => (int)($meta['id'] ?? 0),
                ];
            }
        }
    }

    return ['numbers' => $numberAt, 'across' => $across, 'down' => $down];
}

/**
 * Yerleşim sonrası (global koordinatlarda) ipucu haritası.
 *
 * @param list<array{r:int,c:int,across:bool,...}> $placed
 */
function crossword_clue_map_from_placed(array $placed, int $r0, int $c0): array
{
    $map = [];
    foreach ($placed as $p) {
        $rr = $p['r'] - $r0;
        $cc = $p['c'] - $c0;
        $map[$p['answer']] = [
            'id' => $p['id'],
            'clue' => $p['clue'],
            'answer' => $p['answer'],
            'r' => $rr,
            'c' => $cc,
            'across' => $p['across'],
        ];
    }

    return $map;
}
