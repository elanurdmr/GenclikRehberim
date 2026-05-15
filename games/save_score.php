<?php
/**
 * save_score.php — AJAX Puan Kaydetme Endpoint'i
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * Kabul eder: POST JSON { activity_id, score, max_score }
 * Döndürür:   JSON { success, message }
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Yalnızca POST isteklerine yanıt ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST istekleri kabul edilir.']);
    exit;
}

// JSON çıktı başlığı
header('Content-Type: application/json; charset=utf-8');

// Kullanıcı giriş yapmamışsa hata döndür
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'login_required' => true, 'message' => 'Giriş yapmanız gerekiyor.']);
    exit;
}

// JSON gövdesini oku ve çözümle
$body = file_get_contents('php://input');
$data = json_decode($body, true);

// Girdi doğrulama
$activityId = isset($data['activity_id']) ? (int)$data['activity_id'] : 0;

$rateKey = 'last_save_' . $activityId;
if (isset($_SESSION[$rateKey]) && time() - $_SESSION[$rateKey] < 3) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Çok hızlı istek.']);
    exit;
}
$_SESSION[$rateKey] = time();

$score = isset($data['score']) ? (int)$data['score'] : 0;

// Değerlerin geçerli aralıkta olduğunu kontrol et
if ($activityId < 1 || $activityId > 4) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz etkinlik ID.']);
    exit;
}

// max_score veritabanından okunur (client verisi kullanılmaz)
$db = getDB();
$actStmt = $db->prepare('SELECT max_score FROM activities WHERE id = ?');
$actStmt->execute([$activityId]);
$activity = $actStmt->fetch();

if (!$activity) {
    echo json_encode(['success' => false, 'message' => 'Etkinlik bulunamadı.']);
    exit;
}
$maxScore = (int)$activity['max_score'];

if ($score < 0 || $score > $maxScore) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz puan değeri.']);
    exit;
}

// Puanı kaydet
try {
    saveScore((int)$_SESSION['user_id'], $activityId, $score, $maxScore);
    echo json_encode(['success' => true, 'message' => 'Puan başarıyla kaydedildi.']);
} catch (Exception $e) {
    error_log('Puan kaydetme hatası: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Puan kaydedilemedi.']);
}
