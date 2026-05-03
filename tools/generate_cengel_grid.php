<?php
/**
 * 12 kelimelik kare bulmaca — geri izleme (Türkçe UTF-8)
 */

declare(strict_types=1);

$clues = [
    ['q' => 'Zorbalık olunca ne istemeliyiz?', 'a' => 'YARDIM'],
    ['q' => 'Yaşadığımız olayı kime anlatırız?', 'a' => 'YETİŞKİN'],
    ['q' => 'Yardım bulamazsak ne yapmaya devam etmeliyiz?', 'a' => 'ARAMAK'],
    ['q' => 'Zorba karşısında nasıl durmalıyız?', 'a' => 'DİK'],
    ['q' => 'Korkunca sakinleşmek için ne alıp veririz?', 'a' => 'NEFES'],
    ['q' => '"Başarabilirim" gibi sözlere ne denir?', 'a' => 'OLUMLUSÖZ'],
    ['q' => 'Fiziksel zorbalıkta nereye gideriz?', 'a' => 'GÜVENLİYER'],
    ['q' => 'Daha güvende olmak için nerede bulunuruz?', 'a' => 'KALABALIK'],
    ['q' => 'Korktuğumuzu göstermemek zorbayı ne yapar?', 'a' => 'UZAKLAŞTIRIR'],
    ['q' => 'Sözel zorbalıkta cevap vermeden ne yaparız?', 'a' => 'UZAKLAŞIRIZ'],
    ['q' => 'Zorbalığı yetişkine haber vermek.', 'a' => 'BİLDİRME'],
    ['q' => 'Kendini karşıdakinin yerine koymak.', 'a' => 'EMPATİ'],
];

function normalizeAnswer(string $raw): string
{
    $s = preg_replace('/\s+/u', '', $raw);
    $s = str_replace(['ı', 'i'], ['I', 'İ'], $s);

    return mb_strtoupper($s, 'UTF-8');
}

$words = array_map(static fn ($c) => normalizeAnswer($c['a']), $clues);

$H = 17;
$W = 17;

/** @return array<int, array<int, string>> */
function emptyGrid(int $h, int $w): array
{
    $g = [];
    for ($r = 0; $r < $h; $r++) {
        $g[$r] = array_fill(0, $w, '.');
    }

    return $g;
}

function canPlaceWord(array $g, string $w, int $r, int $c, bool $horizontal): bool
{
    $h = count($g);
    $wlen = count($g[0]);
    $len = mb_strlen($w, 'UTF-8');
    if ($horizontal) {
        if ($c + $len > $wlen) {
            return false;
        }
        if ($c > 0 && $g[$r][$c - 1] !== '.') {
            return false;
        }
        if ($c + $len < $wlen && $g[$r][$c + $len] !== '.') {
            return false;
        }
    } else {
        if ($r + $len > $h) {
            return false;
        }
        if ($r > 0 && $g[$r - 1][$c] !== '.') {
            return false;
        }
        if ($r + $len < $h && $g[$r + $len][$c] !== '.') {
            return false;
        }
    }
    for ($i = 0; $i < $len; $i++) {
        $rr = $horizontal ? $r : $r + $i;
        $cc = $horizontal ? $c + $i : $c;
        $ch = mb_substr($w, $i, 1, 'UTF-8');
        $cell = $g[$rr][$cc];
        if ($cell === '.') {
            continue;
        }
        if ($cell !== $ch) {
            return false;
        }
    }

    return true;
}

/** @param array<int, array<int, string>> $g */
function applyWord(array $g, string $w, int $r, int $c, bool $horizontal): array
{
    $ng = $g;
    $len = mb_strlen($w, 'UTF-8');
    for ($i = 0; $i < $len; $i++) {
        $rr = $horizontal ? $r : $r + $i;
        $cc = $horizontal ? $c + $i : $c;
        $ch = mb_substr($w, $i, 1, 'UTF-8');
        $ng[$rr][$cc] = $ch;
    }

    return $ng;
}

/** @param list<string> $wordList */
function backtrack(array $g, array $wordList, int $idx): ?array
{
    if ($idx >= count($wordList)) {
        return $g;
    }
    $word = $wordList[$idx];
    $len = mb_strlen($word, 'UTF-8');
    $H = count($g);
    $W = count($g[0]);

    foreach ([true, false] as $horizontal) {
        for ($r = 0; $r < $H; $r++) {
            for ($c = 0; $c < $W; $c++) {
                if ($horizontal && $c + $len > $W) {
                    continue;
                }
                if (!$horizontal && $r + $len > $H) {
                    continue;
                }
                if (!canPlaceWord($g, $word, $r, $c, $horizontal)) {
                    continue;
                }
                $next = applyWord($g, $word, $r, $c, $horizontal);
                $res = backtrack($next, $wordList, $idx + 1);
                if ($res !== null) {
                    return $res;
                }
            }
        }
    }

    return null;
}

$indexed = [];
foreach ($words as $i => $w) {
    $indexed[] = ['w' => $w, 'len' => mb_strlen($w, 'UTF-8')];
}
usort($indexed, static fn ($a, $b) => $b['len'] <=> $a['len']);
$sorted = array_column($indexed, 'w');

$grid = backtrack(emptyGrid($H, $W), $sorted, 0);
if ($grid === null) {
    fwrite(STDERR, "17x17 başarısız, 9 kelime 14x14.\n");
    $short = array_slice($sorted, 0, 9);
    $grid = backtrack(emptyGrid(14, 14), $short, 0);
}
if ($grid === null) {
    die("Başarısız\n");
}

for ($r = 0; $r < count($grid); $r++) {
    $line = '';
    for ($c = 0; $c < count($grid[0]); $c++) {
        $line .= $grid[$r][$c] === '.' ? '#' : $grid[$r][$c];
    }
    echo $line . "\n";
}
