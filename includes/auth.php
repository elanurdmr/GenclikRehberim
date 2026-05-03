<?php
/**
 * auth.php — Oturum ve Kimlik Doğrulama Yardımcıları
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once __DIR__ . '/db.php';

// Oturumu başlat (henüz başlatılmamışsa)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kullanıcının oturum açıp açmadığını kontrol eder.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Kullanıcının admin olup olmadığını kontrol eder.
 */
function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Giriş yapmayan kullanıcıyı login sayfasına yönlendirir.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /genclik-rehberim/login.php');
        exit;
    }
}

/**
 * Admin olmayan kullanıcıyı ana sayfaya yönlendirir.
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: /genclik-rehberim/index.php');
        exit;
    }
}

/**
 * Kullanıcı kaydı oluşturur.
 * @return array ['success' => bool, 'message' => string]
 */
function registerUser(string $username, string $email, string $password): array {
    $db = getDB();

    // Kullanıcı adı veya e-posta zaten var mı?
    $stmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Bu kullanıcı adı veya e-posta zaten kullanılıyor.'];
    }

    // Şifreyi güvenli şekilde hashle (bcrypt)
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare(
        'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "student")'
    );
    $stmt->execute([$username, $email, $hash]);

    return ['success' => true, 'message' => 'Kayıt başarılı! Giriş yapabilirsiniz.'];
}

/**
 * Kullanıcı girişini doğrular ve oturumu başlatır.
 * @return array ['success' => bool, 'message' => string]
 */
function loginUser(string $username, string $password): array {
    $db = getDB();

    // Kullanıcıyı bul
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Kullanıcı adı veya şifre hatalı.'];
    }

    // Oturum değişkenlerini ayarla
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    return ['success' => true, 'message' => 'Giriş başarılı!'];
}

/**
 * Oturumu sonlandırır.
 */
function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: /genclik-rehberim/login.php');
    exit;
}
