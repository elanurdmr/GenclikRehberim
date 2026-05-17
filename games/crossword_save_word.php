<?php
/**
 * crossword_save_word.php — Tamamlanan kelime başına puan (crossword_word_scores)
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/crossword_build.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Yalnızca POST.']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'login_required' => true, 'message' => 'Giriş gerekli.']);
    exit;
}

$raw = file_get_contents('php://input');
$cwRateKey = 'last_cw_' . substr(md5($raw ?? ''), 0, 8);
if (isset($_SESSION[$cwRateKey]) && time() - $_SESSION[$cwRateKey] < 2) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Çok hızlı istek.']);
    exit;
}
$_SESSION[$cwRateKey] = time();
$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz JSON.']);
    exit;
}

$shuffleKey = isset($data['shuffle_key']) ? (string)$data['shuffle_key'] : '';
$direction = isset($data['direction']) ? (string)$data['direction'] : '';
$clueNum = isset($data['clue_number']) ? (int)$data['clue_number'] : 0;
$userWord = isset($data['word']) ? crossword_normalize_answer((string)$data['word']) : '';

if ($shuffleKey === '' || ($direction !== 'across' && $direction !== 'down') || $clueNum < 1 || $userWord === '') {
    echo json_encode(['success' => false, 'message' => 'Eksik parametre.']);
    exit;
}

$puzzle = crossword_rebuild_for_shuffle($shuffleKey);
if ($puzzle === null) {
    echo json_encode(['success' => false, 'message' => 'Bulmaca üretilemedi.']);
    exit;
}

$clueEntry = $direction === 'across'
    ? ($puzzle['across'][$clueNum] ?? null)
    : ($puzzle['down'][$clueNum] ?? null);

if ($clueEntry === null) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ipucu.']);
    exit;
}

$expected = crossword_normalize_answer($clueEntry['word']);
if ($userWord !== $expected) {
    echo json_encode(['success' => false, 'message' => 'Kelime eşleşmiyor.']);
    exit;
}

$points = (int)$puzzle['pointsPerWord'];
$userId = (int)$_SESSION['user_id'];

try {
    $db = getDB();
    $check = $db->prepare(
        'SELECT id FROM crossword_word_scores
         WHERE user_id = ? AND puzzle_seed = ? AND direction = ? AND clue_number = ?'
    );
    $check->execute([$userId, $shuffleKey, $direction, $clueNum]);
    if ($check->fetch()) {
        echo json_encode([
            'success' => true,
            'points' => $points,
            'already_awarded' => true,
            'message' => 'Bu kelime için puan daha önce verilmişti.',
        ]);
        exit;
    }

    $stmt = $db->prepare(
        'INSERT INTO crossword_word_scores (user_id, puzzle_seed, direction, clue_number, points_awarded)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $shuffleKey, $direction, $clueNum, $points]);
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'crossword_word_scores')
        || str_contains($e->getMessage(), 'Unknown table')) {
        echo json_encode([
            'success' => false,
            'message' => 'crossword_word_scores tablosu yok. migration_crossword_bank.sql çalıştırın.',
        ]);
        exit;
    }
    error_log('crossword_save_word: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.']);
    exit;
}

echo json_encode([
    'success' => true,
    'points' => $points,
    'already_awarded' => false,
    'message' => 'Puan kaydedildi.',
]);
