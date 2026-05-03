<?php
/**
 * functions.php — Genel Yardımcı Fonksiyonlar
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once __DIR__ . '/db.php';

/**
 * XSS saldırılarına karşı çıktıyı temizler.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Kullanıcının belirli bir etkinlikteki en yüksek puanını döndürür.
 */
function getUserHighScore(int $userId, int $activityId): int {
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT MAX(score) as high FROM scores WHERE user_id = ? AND activity_id = ?'
    );
    $stmt->execute([$userId, $activityId]);
    $row = $stmt->fetch();
    return (int)($row['high'] ?? 0);
}

/**
 * Kullanıcının tüm etkinliklerdeki toplam puanını döndürür.
 */
function getUserTotalScore(int $userId): int {
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT COALESCE(SUM(score), 0) as total FROM scores WHERE user_id = ?'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return (int)($row['total'] ?? 0);
}

/**
 * Kullanıcının oyun geçmişini son N kayıt olarak döndürür.
 */
function getUserHistory(int $userId, int $limit = 10): array {
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT s.score, s.max_score, s.played_at, a.name, a.type
         FROM scores s
         JOIN activities a ON a.id = s.activity_id
         WHERE s.user_id = ?
         ORDER BY s.played_at DESC
         LIMIT ?'
    );
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Puan kaydeder veya mevcut kaydı günceller (her etkinlik için bir satır tutar en yüksek skoru).
 */
function saveScore(int $userId, int $activityId, int $score, int $maxScore): void {
    $db = getDB();

    // Her oynama ayrı satır olarak kaydedilir
    $stmt = $db->prepare(
        'INSERT INTO scores (user_id, activity_id, score, max_score) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $activityId, $score, $maxScore]);
}

/**
 * Skor tablosunu (liderlik) döndürür.
 */
function getLeaderboard(int $limit = 10): array {
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT u.username, SUM(s.score) as total_score, COUNT(s.id) as games_played
         FROM scores s
         JOIN users u ON u.id = s.user_id
         WHERE u.role = "student"
         GROUP BY u.id, u.username
         ORDER BY total_score DESC
         LIMIT ?'
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Tüm kullanıcıları döndürür (admin paneli için).
 */
function getAllUsers(): array {
    $db   = getDB();
    $stmt = $db->query(
        'SELECT u.id, u.username, u.email, u.role, u.created_at,
                COALESCE(SUM(s.score),0) as total_score,
                COUNT(s.id) as games_played
         FROM users u
         LEFT JOIN scores s ON s.user_id = u.id
         GROUP BY u.id
         ORDER BY u.created_at DESC'
    );
    return $stmt->fetchAll();
}

/**
 * Tüm skorları admin paneli için döndürür.
 */
function getAllScores(): array {
    $db   = getDB();
    $stmt = $db->query(
        'SELECT s.id, u.username, a.name as activity_name, a.type,
                s.score, s.max_score, s.played_at
         FROM scores s
         JOIN users u ON u.id = s.user_id
         JOIN activities a ON a.id = s.activity_id
         ORDER BY s.played_at DESC
         LIMIT 100'
    );
    return $stmt->fetchAll();
}

/**
 * Admin paneli için özet istatistikler.
 */
function getAdminStats(): array {
    $db = getDB();

    $students = $db->query('SELECT COUNT(*) FROM users WHERE role="student"')->fetchColumn();
    $games    = $db->query('SELECT COUNT(*) FROM scores')->fetchColumn();
    $avgScore = $db->query('SELECT COALESCE(AVG(score),0) FROM scores')->fetchColumn();

    return [
        'total_students' => (int)$students,
        'total_games'    => (int)$games,
        'avg_score'      => round((float)$avgScore, 1),
    ];
}
