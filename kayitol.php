<?php
/**
 * kayitol.php — Kayıt (tasarım: docs/tasarımlar/kayıtol)
 * Gençlik Rehberim
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

$pageTitle = 'Kayıt Ol';

if (isLoggedIn()) {
    header('Location: /genclik-rehberim/ogrencipanel.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($fullName === '' || $username === '' || $email === '' || $password === '') {
        $error = 'Lütfen tüm alanları doldurun.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Öğrenci numarası / kullanıcı adı 3-50 karakter olmalıdır.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi girin.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $password2) {
        $error = 'Şifreler eşleşmiyor.';
    } else {
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/tokens.css">
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/base.css">
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/layout.css">
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/components.css">
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/auth.css">
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/style.css">
</head>
<body>

<nav class="auth-nav" aria-label="Kayıt sayfası navigasyonu">
    <a href="/genclik-rehberim/index.php" class="auth-nav-brand">
        <span class="material-symbols-outlined">shield_person</span>
        Gençlik Rehberim
    </a>
    <span class="auth-nav-tagline">
        Hesabın var mı?
        <a href="/genclik-rehberim/girisyap.php">Giriş Yap</a>
    </span>
</nav>

<div class="auth-page-decor" aria-hidden="true">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<main class="auth-register-layout">

    <section class="auth-register-left" aria-hidden="true">
        <div class="auth-register-left-inner">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmnoFJpySXcdPWSXnU52kJjutNSq3bSNzE_0wqbBBjDMOHkf0-BDFye-5343E1ZcC05M8XYnaRlL34diyXfFx7xfEDJleAdb1p_AdcT9T_S0mHerBTi7UP0EQQ_DR7ElD1LxD5lmGh75uBHdRZfV6JVQRplbv00rZznv_yxmFs5V7QwtPuXP57anzXkBC0DWbjUvAEqMtsQpv-vPqCNuiPf-1M5krDfyuPcg7bZLSKgwLRL6HiFffwBFfxF16ZkKuzFCjlkeWK8YwrY" alt="" loading="lazy">
            <div class="auth-glass-caption">
                <p class="caption-title">Güvenli Bir Alan</p>
                <p class="auth-glass-caption-text">Zorbalığa karşı farkındalık ve destek odaklı öğrenci rehberliği.</p>
            </div>
        </div>
    </section>

    <div class="auth-split-box auth-split-box--register">
        <article class="auth-register-card">
            <header>
                <div class="auth-brand-icon-lg">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <h1 class="auth-split-title">Aramıza Katıl</h1>
                <p class="auth-split-sub">Hesabını oluştur, etkinliklere katıl.</p>
            </header>

            <div class="auth-safe-badge">
                <span class="material-symbols-outlined">verified_user</span>
                Burası öğrenciler için güvenli bir alan.
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <span class="material-symbols-outlined alert-icon">error</span>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <span class="material-symbols-outlined alert-icon">check_circle</span>
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST" action="" novalidate>
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <label class="auth-field-label" for="full_name">Ad Soyad</label>
                <div class="auth-input-wrap">
                    <span class="material-symbols-outlined input-icon">person</span>
                    <input class="form-control-auth" type="text" id="full_name" name="full_name" placeholder="Adın Soyadın"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <label class="auth-field-label" for="username">Öğrenci numarası (kullanıcı adın)</label>
                <div class="auth-input-wrap">
                    <span class="material-symbols-outlined input-icon">badge</span>
                    <input class="form-control-auth" type="text" id="username" name="username" required minlength="3" maxlength="50"
                           placeholder="2024123456"
                           value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <label class="auth-field-label" for="email">E-posta</label>
                <div class="auth-input-wrap">
                    <span class="material-symbols-outlined input-icon">mail</span>
                    <input class="form-control-auth" type="email" id="email" name="email" required
                           placeholder="ogrenci@okul.edu.tr"
                           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <label class="auth-field-label" for="password">Şifre</label>
                <div class="auth-input-wrap">
                    <span class="material-symbols-outlined input-icon">lock</span>
                    <input class="form-control-auth" type="password" id="password" name="password" required minlength="6" placeholder="En az 6 karakter">
                </div>

                <label class="auth-field-label" for="password2">Şifre tekrar</label>
                <div class="auth-input-wrap">
                    <span class="material-symbols-outlined input-icon">lock_reset</span>
                    <input class="form-control-auth" type="password" id="password2" name="password2" required placeholder="Şifreyi tekrarla">
                </div>

                <button type="submit" class="btn btn-primary auth-btn-full">
                    Kayıt Ol
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>
            <?php endif; ?>

            <div class="auth-register-footer">
                <p>Zaten hesabın var mı?</p>
                <a href="/genclik-rehberim/girisyap.php" class="btn btn-outline">Giriş Yap</a>
            </div>
        </article>

        <div class="auth-gift-badge">
            <div class="auth-gift-inner">
                <span class="material-symbols-outlined">star</span>
                ETKİNLİKLERLE PUAN TOPLA !
            </div>
        </div>

        <p class="auth-footer-link">
            <a href="/genclik-rehberim/index.php">← Ana Sayfa</a>
        </p>
    </div>

</main>

<script src="/genclik-rehberim/assets/js/main.js"></script>
</body>
</html>
