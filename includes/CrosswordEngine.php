<?php
/**
 * CrosswordEngine — Kesişimli Çengel Bulmaca yerleştirme (UTF-8 Türkçe)
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

    /*
     * Açgözlü (greedy) yerleştirme.
     *
     * Eski sürüm "ya hep ya hiç" geri izleme (backtracking) kullanıyordu:
     * tek bir kelime bile yerleşemezse tüm arama ağacını tüketip null
     * dönüyordu. 15x15 ızgarada 14 kelime için bu ağaç devasadır ve
     * sayfayı kilitleyebilir. Üstelik her düğümde tüm ızgara kopyalanıyordu.
     *
     * Yeni sürüm: ilk kelimeyi ortaya yatay koyar, sonraki her kelimeyi
     * yerleşmiş bir kelimeyle en çok kesişecek konuma yerleştirir.
     * Yerleşemeyen kelime sessizce atlanır. Geri izleme yok → her zaman
     * hızlı, sonlanır ve geçerli bir bulmaca döndürür. Deterministiktir:
     * ayni girdi her zaman aynı bulmacayı verir (puan doğrulaması için şart).
     */
    $grid = $empty();
    $placed = [];

    foreach ($words as $w) {
        $ans = $w['answer'];
        $len = mb_strlen($ans, 'UTF-8');

        // İlk kelime: ızgaranın ortasına yatay.
        if ($placed === []) {
            $sr = intdiv($rows, 2);
            $sc = max(0, intdiv($cols - $len, 2));
            if (!$canPlace($grid, $ans, $sr, $sc, true)) {
                continue;
            }
            $grid = $apply($grid, $ans, $sr, $sc, true);
            $placed[] = ['id' => $w['id'], 'clue' => $w['clue'], 'answer' => $ans, 'r' => $sr, 'c' => $sc, 'across' => true];
            continue;
        }

        // Sonraki kelimeler: en çok kesişen geçerli konumu seç.
        $best = null;
        $bestScore = 0;

        foreach ($placed as $p) {
            $pw = $p['answer'];
            $lenp = mb_strlen($pw, 'UTF-8');

            for ($i = 0; $i < $lenp; $i++) {
                $chp = mb_substr($pw, $i, 1, 'UTF-8');
                for ($j = 0; $j < $len; $j++) {
                    if (mb_substr($ans, $j, 1, 'UTF-8') !== $chp) {
                        continue;
                    }

                    if ($p['across']) {
                        $nr = $p['r'] - $j;
                        $nc = $p['c'] + $i;
                        $acrossNew = false;
                    } else {
                        $nr = $p['r'] + $i;
                        $nc = $p['c'] - $j;
                        $acrossNew = true;
                    }

                    if ($nr < 0 || $nc < 0) {
                        continue;
                    }
                    if (!$canPlace($grid, $ans, $nr, $nc, $acrossNew)) {
                        continue;
                    }

                    // Kaç hücre mevcut bir harfin üzerine oturuyor (kesişim sayısı).
                    $crossings = 0;
                    for ($k = 0; $k < $len; $k++) {
                        $rr = $acrossNew ? $nr : $nr + $k;
                        $cc = $acrossNew ? $nc + $k : $nc;
                        if ($grid[$rr][$cc] !== '#') {
                            $crossings++;
                        }
                    }
                    if ($crossings > $bestScore) {
                        $bestScore = $crossings;
                        $best = ['r' => $nr, 'c' => $nc, 'across' => $acrossNew];
                    }
                }
            }
        }

        if ($best !== null) {
            $grid = $apply($grid, $ans, $best['r'], $best['c'], $best['across']);
            $placed[] = [
                'id' => $w['id'], 'clue' => $w['clue'], 'answer' => $ans,
                'r' => $best['r'], 'c' => $best['c'], 'across' => $best['across'],
            ];
        }
        // Hiçbir kesişim bulunamazsa kelime atlanır.
    }

    if ($placed === []) {
        return null;
    }

    return ['grid' => $grid, 'placed' => $placed];
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
