<?php
/**
 * Kare bulmaca — veri + derleme (tarih tohumu)
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/CrosswordEngine.php';

/**
 * Cevabı ızgara için normalize et (Türkçe).
 */
function crossword_normalize_answer(string $a): string
{
    $a = preg_replace('/\s+/u', '', trim($a));
    $a = str_replace(['ı', 'i'], ['I', 'İ'], $a);

    return mb_strtoupper($a, 'UTF-8');
}

/**
 * @return list<array{id:int,clue:string,answer:string}>
 */
function crossword_fetch_bank(): array
{
    $db = getDB();
    try {
        $stmt = $db->query(
            'SELECT id, clue, answer FROM crossword_bank WHERE active = 1 ORDER BY sort_order ASC, id ASC'
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException) {
        return [];
    }

    $out = [];
    foreach ($rows as $r) {
        $len = mb_strlen(crossword_normalize_answer((string)$r['answer']), 'UTF-8');
        if ($len < 3 || $len > 16) {
            continue;
        }
        $out[] = [
            'id' => (int)$r['id'],
            'clue' => (string)$r['clue'],
            'answer' => crossword_normalize_answer((string)$r['answer']),
        ];
    }

    return $out;
}

/** @template T
 * @param list<T> $items
 * @return list<T>
 */
function crossword_seeded_shuffle(array $items, string $seed): array
{
    $arr = $items;
    if ($arr === []) {
        return [];
    }
    mt_srand(crc32($seed));
    for ($i = count($arr) - 1; $i > 0; $i--) {
        $j = mt_rand(0, $i);
        $tmp = $arr[$i];
        $arr[$i] = $arr[$j];
        $arr[$j] = $tmp;
    }
    mt_srand();

    return $arr;
}

/**
 * @param array<string, array{id:int, clue:string}> $clueByAnswer
 * @return array<string, mixed>|null
 */
function crossword_attempt_generate(array $bank, array $clueByAnswer, string $shuffleKey): ?array
{
    $shuffled = crossword_seeded_shuffle($bank, $shuffleKey);
    foreach ([12, 10, 9, 8, 7, 6] as $maxW) {
        $gen = crossword_generate($shuffled, 19, 19, $maxW);
        if ($gen === null) {
            continue;
        }

        $trimPack = crossword_trim_and_fill($gen['grid']);
        $grid = $trimPack['grid'];
        if ($trimPack['rows'] === 0) {
            continue;
        }

        $numbered = crossword_number_clues($grid, $clueByAnswer);

        $acrossList = array_values($numbered['across']);
        usort($acrossList, static fn ($a, $b) => $a['n'] <=> $b['n']);
        $downList = array_values($numbered['down']);
        usort($downList, static fn ($a, $b) => $a['n'] <=> $b['n']);

        $totalClues = count($acrossList) + count($downList);
        $pointsPer = $totalClues > 0 ? (int)max(1, floor(100 / $totalClues)) : 10;

        return [
            'grid' => $grid,
            'numbers' => $numbered['numbers'],
            'across' => $numbered['across'],
            'down' => $numbered['down'],
            'acrossList' => $acrossList,
            'downList' => $downList,
            'height' => $trimPack['rows'],
            'width' => $trimPack['cols'],
            'placed' => $gen['placed'],
            'pointsPerWord' => $pointsPer,
        ];
    }

    return null;
}

/**
 * @return list<array{id:int,clue:string,answer:string}>
 */
function crossword_embedded_fallback_bank(): array
{
    return [
        ['id' => 1, 'clue' => 'Zorbalık olunca ne istemeliyiz?', 'answer' => 'YARDIM'],
        ['id' => 2, 'clue' => 'Zorba karşısında nasıl durmalıyız?', 'answer' => 'DİK'],
        ['id' => 3, 'clue' => 'Yardım bulamazsak ne yapmaya devam etmeliyiz?', 'answer' => 'ARAMAK'],
        ['id' => 4, 'clue' => 'Korkunca sakinleşmek için ne alıp veririz?', 'answer' => 'NEFES'],
        ['id' => 5, 'clue' => 'Kendini karşıdakinin yerine koymak.', 'answer' => 'EMPATİ'],
        ['id' => 6, 'clue' => 'Yaşadığımız olayı kime anlatırız?', 'answer' => 'YETİŞKİN'],
    ];
}

/** @return list<array{id:int,clue:string,answer:string}> */
function crossword_bank_or_fallback(): array
{
    $bank = crossword_fetch_bank();

    return $bank === [] ? crossword_embedded_fallback_bank() : $bank;
}

/** @return array<string, array{id:int, clue:string}> */
function crossword_clue_map(array $bank): array
{
    $clueByAnswer = [];
    foreach ($bank as $e) {
        $clueByAnswer[$e['answer']] = ['id' => $e['id'], 'clue' => $e['clue']];
    }

    return $clueByAnswer;
}

/** @return array<string, mixed>|null */
function crossword_build_puzzle(string $dateSeed): ?array
{
    $bank = crossword_bank_or_fallback();
    $clueByAnswer = crossword_clue_map($bank);

    foreach ([$dateSeed, $dateSeed . '_b', $dateSeed . '_c'] as $trySeed) {
        $r = crossword_attempt_generate($bank, $clueByAnswer, $trySeed);
        if ($r !== null) {
            return array_merge($r, [
                'seed' => $dateSeed,
                'shuffleKey' => $trySeed,
            ]);
        }
    }

    return null;
}

/** @return array<string, mixed>|null */
function crossword_rebuild_for_shuffle(string $shuffleKey): ?array
{
    $bank = crossword_bank_or_fallback();
    $clueByAnswer = crossword_clue_map($bank);
    $r = crossword_attempt_generate($bank, $clueByAnswer, $shuffleKey);

    return $r === null ? null : array_merge($r, ['shuffleKey' => $shuffleKey]);
}
