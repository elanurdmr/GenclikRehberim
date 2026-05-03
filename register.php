<?php
/**
 * register.php — Kullanıcı Kayıt Sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once 'includes/auth.php';

// Zaten giriş yapmışsa yönlendir
if (isLoggedIn()) {
    header('Location: /genclik-rehberim/dashboard.php');
    exit;
}

$error   = '';
$success = '';

// Form gönderildiğinde işle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $password2 = $_POST['password2']      ?? '';

    // Doğrulama kuralları
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi girin.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $password2) {
        $error = 'Şifreler eşleşmiyor.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Kullanıcı adı 3-50 karakter arasında olmalıdır.';
    } else {
        // Kayıt işlemini dene
        $result = registerUser($username, $email, $password);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | Gençlik Rehberim</title>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Başlık -->
        <div class="auth-header">
            <div class="auth-icon" style="background: linear-gradient(135deg, var(--secondary), #e0485e);">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1>Hesap Oluştur</h1>
            <p>Oyunları oynamak için kayıt ol</p>
        </div>

        <!-- Hata mesajı -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Başarı mesajı -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Kayıt Formu -->
        <?php if (!$success): ?>
        <form method="POST" action="" novalidate>

            <!-- Kullanıcı adı -->
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Kullanıcı adı seçin (min. 3 karakter)"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                        minlength="3"
                        maxlength="50"
                    >
                </div>
            </div>

            <!-- E-posta -->
            <div class="form-group">
                <label for="email">E-posta Adresi</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="E-posta adresinizi girin"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>
            </div>

            <!-- Şifre -->
            <div class="form-group">
                <label for="password">Şifre</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Şifre (min. 6 karakter)"
                        required
                        minlength="6"
                    >
                </div>
            </div>

            <!-- Şifre tekrar -->
            <div class="form-group">
                <label for="password2">Şifre Tekrar</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        type="password"
                        id="password2"
                        name="password2"
                        placeholder="Şifreyi tekrar girin"
                        required
                    >
                </div>
            </div>

            <!-- Kayıt butonu -->
            <button type="submit" class="btn btn-secondary btn-full" style="margin-top:0.5rem">
                <i class="fa-solid fa-user-plus"></i>
                Kayıt Ol
            </button>

        </form>
        <?php endif; ?>

        <!-- Giriş yap bağlantısı -->
        <p class="auth-footer-link" style="margin-top:1.2rem">
            Hesabın var mı? <a href="/genclik-rehberim/login.php">Giriş Yap</a>
        </p>

        <!-- Ana sayfaya dön -->
        <p class="auth-footer-link" style="margin-top:0.5rem">
            <a href="/genclik-rehberim/index.php">
                <i class="fa-solid fa-arrow-left"></i> Ana Sayfaya Dön
            </a>
        </p>

    </div>
</div>

<script src="/genclik-rehberim/assets/js/main.js"></script>
</body>
</html>
