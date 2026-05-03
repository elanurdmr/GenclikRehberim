<?php
/**
 * login.php — Kullanıcı Giriş Sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once 'includes/auth.php';

// Zaten giriş yapmışsa yönlendir
if (isLoggedIn()) {
    header('Location: /genclik-rehberim/' . (isAdmin() ? 'admin/index.php' : 'dashboard.php'));
    exit;
}

$error   = '';
$success = '';

// Form gönderildiğinde işle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Girdi temizleme (trim ile baş/son boşlukları sil)
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Temel doğrulama
    if (empty($username) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        $result = loginUser($username, $password);
        if ($result['success']) {
            // Başarılı giriş: role'e göre yönlendir
            $redirect = isAdmin()
                ? '/genclik-rehberim/admin/index.php'
                : '/genclik-rehberim/dashboard.php';
            header('Location: ' . $redirect);
            exit;
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
    <title>Giriş Yap | Gençlik Rehberim</title>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<!-- Giriş sayfasında tam ekran auth wrapper kullanılır -->
<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Başlık -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fa-solid fa-shield-heart"></i>
            </div>
            <h1>Tekrar Hoş Geldin!</h1>
            <p>Hesabına giriş yaparak devam et</p>
        </div>

        <!-- Hata mesajı -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Giriş Formu -->
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
                        placeholder="Kullanıcı adınızı girin"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                        autocomplete="username"
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
                        placeholder="Şifrenizi girin"
                        required
                        autocomplete="current-password"
                    >
                </div>
            </div>

            <!-- Giriş butonu -->
            <button type="submit" class="btn btn-primary btn-full" style="margin-top:0.5rem">
                <i class="fa-solid fa-right-to-bracket"></i>
                Giriş Yap
            </button>

        </form>

        <!-- Kayıt ol bağlantısı -->
        <p class="auth-footer-link">
            Hesabın yok mu? <a href="/genclik-rehberim/register.php">Kayıt Ol</a>
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
