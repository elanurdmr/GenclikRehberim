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
 * Bir etkinlik türünün (type) veritabanı ID'sini döndürür.
 * Böylece oyun JS dosyalarına sabit ID gömmek yerine ID, sunucudan
 * dinamik olarak geçirilir. Sonuç bellekte tutulur; bulunamazsa 0 döner.
 */
function getActivityId(string $type): int {
    static $cache = [];
    if (array_key_exists($type, $cache)) {
        return $cache[$type];
    }
    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM activities WHERE type = ? ORDER BY id ASC LIMIT 1');
    $stmt->execute([$type]);
    $cache[$type] = (int)($stmt->fetchColumn() ?: 0);

    return $cache[$type];
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
 * Kullanıcının veritabanında kayıtlı rozet adlarını döndürür.
 */
function getEarnedBadges(int $userId): array {
    $db   = getDB();
    $stmt = $db->prepare('SELECT badge_name FROM user_badges WHERE user_id = ?');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Bir etkinliğin veritabanındaki maksimum puanını döndürür.
 * Bulunamazsa 100 döner (güvenli varsayılan).
 */
function getActivityMaxScore(int $activityId): int {
    $db   = getDB();
    $stmt = $db->prepare('SELECT max_score FROM activities WHERE id = ? LIMIT 1');
    $stmt->execute([$activityId]);
    return (int)($stmt->fetchColumn() ?: 100);
}

/**
 * Rozeti kullanıcıya kaydeder; zaten varsa sessizce atlar.
 * Yeni kazanıldıysa true, zaten vardıysa false döner.
 */
function awardBadge(int $userId, string $badgeName): bool {
    $db   = getDB();
    $stmt = $db->prepare(
        'INSERT IGNORE INTO user_badges (user_id, badge_name) VALUES (?, ?)'
    );
    $stmt->execute([$userId, $badgeName]);
    return $stmt->rowCount() > 0;
}

/**
 * Tek kullanıcıyı ID ile döndürür.
 */
function getUserById(int $userId): array {
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, username, email, role, created_at FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: [];
}

/**
 * Kullanıcı adı/e-posta'nın başka bir hesapta olup olmadığını kontrol eder.
 */
function isUsernameOrEmailTaken(string $username, string $email, int $excludeId): bool {
    $db   = getDB();
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?');
    $stmt->execute([$username, $email, $excludeId]);
    return (bool)$stmt->fetchColumn();
}

/**
 * Kullanıcı adı ve e-posta günceller.
 */
function updateUserInfo(int $userId, string $username, string $email): bool {
    $db   = getDB();
    $stmt = $db->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
    return $stmt->execute([$username, $email, $userId]);
}

/**
 * Kullanıcı şifresini bcrypt hash ile günceller.
 */
function updateUserPassword(int $userId, string $newHash): bool {
    $db   = getDB();
    $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
    return $stmt->execute([$newHash, $userId]);
}

/**
 * Kullanıcının mevcut şifre hash'ini döndürür.
 */
function getUserPasswordHash(int $userId): string {
    $db   = getDB();
    $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    return (string)($stmt->fetchColumn() ?: '');
}

/**
 * Geri bildirim kaydeder.
 */
function saveFeedback(int $userId, string $category, string $message): bool {
    $db   = getDB();
    $stmt = $db->prepare('INSERT INTO feedback (user_id, category, message) VALUES (?, ?, ?)');
    return $stmt->execute([$userId, $category, $message]);
}

/**
 * Admin için tüm geri bildirimleri döndürür.
 */
function getAllFeedback(): array {
    $db   = getDB();
    $stmt = $db->query(
        'SELECT f.id, u.username, f.category, f.message, f.is_read, f.created_at
         FROM feedback f
         JOIN users u ON u.id = f.user_id
         ORDER BY f.created_at DESC'
    );
    return $stmt->fetchAll();
}

/**
 * Kullanıcının kendi geri bildirimlerini döndürür.
 */
function getUserFeedback(int $userId): array {
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT id, category, message, created_at FROM feedback WHERE user_id = ? ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Geri bildirimi okundu olarak işaretler.
 */
function markFeedbackRead(int $feedbackId): void {
    $db   = getDB();
    $stmt = $db->prepare('UPDATE feedback SET is_read = 1 WHERE id = ?');
    $stmt->execute([$feedbackId]);
}

/**
 * Okunmamış geri bildirim sayısını döndürür (admin sidebar için).
 */
function getUnreadFeedbackCount(): int {
    $db = getDB();
    try {
        return (int)$db->query('SELECT COUNT(*) FROM feedback WHERE is_read = 0')->fetchColumn();
    } catch (\PDOException $e) {
        return 0;
    }
}

/**
 * Admin paneli için özet istatistikler.
 */
function getAdminStats(): array {
    $db = getDB();

    $students   = $db->query('SELECT COUNT(*) FROM users WHERE role="student"')->fetchColumn();
    $games      = $db->query('SELECT COUNT(*) FROM scores')->fetchColumn();
    $avgScore   = $db->query('SELECT COALESCE(AVG(score),0) FROM scores')->fetchColumn();
    $gamesToday = $db->query('SELECT COUNT(*) FROM scores WHERE DATE(played_at) = CURDATE()')->fetchColumn();
    $newWeek    = $db->query(
        'SELECT COUNT(*) FROM users WHERE role="student" AND created_at >= (NOW() - INTERVAL 7 DAY)'
    )->fetchColumn();
    $topScore   = $db->query('SELECT COALESCE(MAX(score),0) FROM scores')->fetchColumn();
    /* kategori ve bosluk, eslestirme oyununun iç puanlama tipleridir; bağımsız oyun sayılmaz. */
    $activities = $db->query(
        "SELECT COUNT(*) FROM activities WHERE type NOT IN ('kategori','bosluk')"
    )->fetchColumn();

    return [
        'total_students'    => (int)$students,
        'total_games'       => (int)$games,
        'avg_score'         => round((float)$avgScore, 1),
        'games_today'       => (int)$gamesToday,
        'new_students_week' => (int)$newWeek,
        'top_score'         => (int)$topScore,
        'total_activities'  => (int)$activities,
    ];
}
