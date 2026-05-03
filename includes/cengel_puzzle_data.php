<?php
/**
 * Kare çengel bulmaca — çözüm ızgarası ve ipuçları (UTF-8)
 * Grid: # = blok, harf = beyaz hücre
 */
declare(strict_types=1);

$cengelRawRows = [
    'UZAKLAŞTIRIR#DİK#',
    'UZAKLAŞIRIZ#NEFES',
    'GÜVENLİYER#YARDIM',
    'OLUMLUSÖZ#ARAMAK#',
    'KALABALIK#EMPATİ#',
    'YETİŞKİN#BİLDİRME',
];

$cengelClueByAnswer = [
    'YARDIM' => 'Zorbalık olunca ne istemeliyiz?',
    'YETİŞKİN' => 'Yaşadığımız olayı kime anlatırız?',
    'ARAMAK' => 'Yardım bulamazsak ne yapmaya devam etmeliyiz?',
    'DİK' => 'Zorba karşısında nasıl durmalıyız?',
    'NEFES' => 'Korkunca sakinleşmek için ne alıp veririz?',
    'OLUMLUSÖZ' => '"Başarabilirim" gibi sözlere ne denir? (bitişik yazılır)',
    'GÜVENLİYER' => 'Fiziksel zorbalıkta nereye gideriz? (bitişik)',
    'KALABALIK' => 'Daha güvende olmak için nerede bulunuruz?',
    'UZAKLAŞTIRIR' => 'Korktuğumuzu göstermemek zorbayı ne yapar?',
    'UZAKLAŞIRIZ' => 'Sözel zorbalıkta cevap vermeden ne yaparız?',
    'BİLDİRME' => 'Zorbalığı yetişkine haber vermek.',
    'EMPATİ' => 'Kendini karşıdakinin yerine koymak.',
];

/** @return list<list<string>> */
function cengelRowsToGrid(array $rows): array
{
    $g = [];
    foreach ($rows as $line) {
        $g[] = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
    }

    return $g;
}

/**
 * @return array{across: array<int, array{n:int,r:int,c:int,word:string,clue:string}>, tabOne: list<array>, tabTwo: list<array>, numbers: array<int, array<int, int>>}
 */
function cengelNumbering(array $grid, array $clueByAnswer): array
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
            if ($startAcross) {
                $n++;
                $numberAt[$r][$c] = $n;
            }
        }
    }

    $across = [];
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
                $across[$num] = [
                    'n' => $num,
                    'r' => $r,
                    'c' => $c,
                    'word' => $word,
                    'clue' => $clueByAnswer[$word] ?? ('Kelime: ' . $word),
                ];
            }
        }
    }

    $acrossList = array_values($across);
    usort($acrossList, static function ($a, $b) {
        return $a['r'] <=> $b['r'] ?: $a['c'] <=> $b['c'];
    });
    $half = (int)ceil(count($acrossList) / 2);
    $cluesTabOne = array_slice($acrossList, 0, $half);
    $cluesTabTwo = array_slice($acrossList, $half);

    return [
        'across' => $across,
        'tabOne' => $cluesTabOne,
        'tabTwo' => $cluesTabTwo,
        'numbers' => $numberAt,
    ];
}

$cengelGrid = cengelRowsToGrid($cengelRawRows);
$meta = cengelNumbering($cengelGrid, $cengelClueByAnswer);

return [
    'grid' => $cengelGrid,
    'rawRows' => $cengelRawRows,
    'cluesAcross' => $meta['across'],
    'cluesTabOne' => $meta['tabOne'],
    'cluesTabTwo' => $meta['tabTwo'],
    'numbers' => $meta['numbers'],
    'height' => count($cengelGrid),
    'width' => count($cengelGrid[0]),
];
