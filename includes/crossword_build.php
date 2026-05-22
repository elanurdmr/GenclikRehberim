<?php
/**
 * çengel bulmaca — veri + derleme (tarih tohumu)
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/CrosswordEngine.php';

/**
 * Cevabı ızgara için normalize et (Türkçe).
 * Izgara yerleşimi ve depolama için kullanılır; i/ı ayrımını KORUR.
 */
function crossword_normalize_answer(string $a): string
{
    $a = preg_replace('/\s+/u', '', trim($a));
    $a = str_replace(['ı', 'i'], ['I', 'İ'], $a);

    return mb_strtoupper($a, 'UTF-8');
}

/**
 * YALNIZCA KARŞILAŞTIRMA için normalize et.
 * i/ı/İ/I ayrımını yok sayar; kullanıcı hatalarına karşı hoşgörülüdür.
 */
function crossword_compare_key(string $a): string
{
    $a = preg_replace('/\s+/u', '', trim($a));
    $a = str_replace(['i', 'ı', 'İ', 'I'], 'I', $a);

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
        if ($len < 3 || $len > 10) {
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
    foreach ([14, 12, 11, 10, 9, 8, 7, 6] as $maxW) {
        $gen = crossword_generate($shuffled, 15, 15, $maxW);
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
            'maxScore' => $totalClues * $pointsPer,
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
        ['id' =>  1, 'clue' => 'Zorbalık olunca ne istemeliyiz?',                              'answer' => 'YARDIM'],
        ['id' =>  2, 'clue' => 'Zorba karşısında nasıl durmalıyız?',                           'answer' => 'DİK'],
        ['id' =>  3, 'clue' => 'Yardım bulamazsak ne yapmaya devam etmeliyiz?',               'answer' => 'ARAMAK'],
        ['id' =>  4, 'clue' => 'Korkunca sakinleşmek için ne alıp veririz?',                  'answer' => 'NEFES'],
        ['id' =>  5, 'clue' => 'Kendini karşıdakinin yerine koymak.',                          'answer' => 'EMPATİ'],
        ['id' =>  6, 'clue' => 'Yaşadığımız olayı kime anlatırız?',                           'answer' => 'YETİŞKİN'],
        ['id' =>  7, 'clue' => 'Zorluklara göğüs gerebilen, korkusuz.',                       'answer' => 'CESUR'],
        ['id' =>  8, 'clue' => 'Birine inanma ve kendini güvende hissetme duygusu.',          'answer' => 'GÜVEN'],
        ['id' =>  9, 'clue' => 'Arkadaşına yardım edip yanında durma.',                       'answer' => 'DESTEK'],
        ['id' => 10, 'clue' => 'Birini değerli görme, itibar etme.',                          'answer' => 'SAYGI'],
        ['id' => 11, 'clue' => 'Birine karşı duyulan sıcak ve güçlü his.',                   'answer' => 'SEVGİ'],
        ['id' => 12, 'clue' => 'Huzurlu ve neşeli olmak.',                                    'answer' => 'MUTLU'],
        ['id' => 13, 'clue' => 'Güzel şeylerin olacağına inanmak.',                           'answer' => 'UMUT'],
        ['id' => 14, 'clue' => 'Herkese hakkını vererek eşit davranmak.',                     'answer' => 'ADALET'],
        ['id' => 15, 'clue' => 'İki kişi arasındaki özel ve güçlü bağ.',                     'answer' => 'DOSTLUK'],
        ['id' => 16, 'clue' => 'Sahip olduklarını başkasıyla bölmek.',                        'answer' => 'PAYLAŞ'],
        ['id' => 17, 'clue' => 'Birini tehlikeden saklamak veya kollamak.',                   'answer' => 'KORUMA'],
        ['id' => 18, 'clue' => 'Başkalarına değer veren, nezaket gösteren.',                  'answer' => 'SAYGILI'],
        ['id' => 19, 'clue' => 'Yalan söylemeyen, açık sözlü davranan.',                      'answer' => 'DÜRÜST'],
        ['id' => 20, 'clue' => 'Zorlukların üstesinden gelebilen.',                           'answer' => 'GÜÇLÜ'],
        ['id' => 21, 'clue' => 'Öğrenimin yapıldığı kurum.',                                  'answer' => 'OKUL'],
        ['id' => 22, 'clue' => 'Uyulması gereken davranış ilkesi.',                           'answer' => 'KURAL'],
        ['id' => 23, 'clue' => 'Kimsesi olmadan tek başına kalan.',                           'answer' => 'YALNIZ'],
        ['id' => 24, 'clue' => 'Birlikte vakit geçirilen, güvenilen yakın kişi.',             'answer' => 'ARKADAŞ'],
        ['id' => 25, 'clue' => 'Sınıfta ders anlatan yetişkin.',                              'answer' => 'ÖĞRETMEN'],
        ['id' => 26, 'clue' => 'Bir grubun içinde bulunmak, ait hissetmek.',                 'answer' => 'KATILIM'],
        ['id' => 27, 'clue' => 'Zorbalığı büyüklere haber vermek.',                           'answer' => 'BİLDİRME'],
        ['id' => 28, 'clue' => '"Başarabilirim" gibi pozitif iç ses.',                        'answer' => 'ÖZGÜVEN'],
        ['id' => 29, 'clue' => 'Birinin haklarını çiğnemek, zarar vermek.',                   'answer' => 'ZORBALIK'],
        ['id' => 30, 'clue' => 'Kavga etmeden, uyum içinde yaşayan.',                         'answer' => 'BARIŞÇIL'],
        ['id' => 31, 'clue' => 'İnsanları bir arada tutan toplumsal bağ.',                    'answer' => 'DAYANIŞMA'],
        ['id' => 32, 'clue' => 'Farklılıklara hoşgörüyle bakma.',                             'answer' => 'HOŞGÖRÜ'],
        ['id' => 33, 'clue' => 'Kendine olan inanç ve güven.',                                'answer' => 'ÖZGÜVEN'],
        ['id' => 34, 'clue' => 'Zor anlarda yılmadan devam etmek.',                           'answer' => 'KARARLIL'],
        ['id' => 35, 'clue' => 'Birinin acısını hissedip üzülme.',                            'answer' => 'MERHAMETLİ'],
        ['id' => 36, 'clue' => 'Birden fazla kişinin birlikte çalışması.',                    'answer' => 'İŞBİRLİĞİ'],
        ['id' => 37, 'clue' => 'Başkalarını etkileyen, yol gösteren kişi.',                   'answer' => 'LİDER'],
        ['id' => 38, 'clue' => 'Yanlışı kabul edip özür dilemek.',                            'answer' => 'ÖZÜR'],
        ['id' => 39, 'clue' => 'Herkesin eşit haklara sahip olması.',                         'answer' => 'EŞİTLİK'],
        ['id' => 40, 'clue' => 'Başkasının başarısından sevinmek.',                           'answer' => 'TEBRİK'],
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
                'seed'       => $dateSeed,
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
