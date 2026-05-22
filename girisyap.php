<?php
/**
 * girisyap.php — Giriş (tasarım: docs/tasarımlar/girişyap)
 * Gençlik Rehberim
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

$pageTitle = 'Giriş Yap';

if (isLoggedIn()) {
    header('Location: /genclik-rehberim/' . (isAdmin() ? 'admin/index.php' : 'ogrencipanel.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        $result = loginUser($username, $password);
        if ($result['success']) {
            $redirect = isAdmin()
                ? '/genclik-rehberim/admin/index.php'
                : '/genclik-rehberim/ogrencipanel.php';
            header('Location: ' . $redirect);
            exit;
        }
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Gençlik Rehberim</title>
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

<nav class="auth-nav" aria-label="Giriş sayfası navigasyonu">
    <a href="/genclik-rehberim/index.php" class="auth-nav-brand">
        <span class="material-symbols-outlined">shield_person</span>
        Gençlik Rehberim
    </a>
    <span class="auth-nav-tagline">
        Hesabın yok mu?
        <a href="/genclik-rehberim/kayitol.php">Kayıt Ol</a>
    </span>
</nav>

<div class="auth-split-page">
    <div class="auth-split-inner">

        <section class="auth-split-art" aria-hidden="true">
            <div class="auth-split-art-pattern"></div>
            <div class="auth-split-art-content">
                <img class="auth-split-art-img" src="https://lh3.googleusercontent.com/aida-public/AB6AXuByUpmVWdqaFeLt-DJANw2Eiwp1sawHX396hmJCMdsinHpE8ewhfWBYk48HXPL6UAsgH0YbDnfA6T1MHhXGBEcAd0LL0Efudt2Db67tmhZ560D67IHx53skZzZYegjrPxRFDtiJwqclSb33KKvtL2BXMH8QL6PwNLFuxYBauZ1yL7xbVasY55k41spQdHiQZBnMXzPEtTduoyIfsoaeUOiAXH8cxrPa8bG7ptqWmGC6Gsxst9pkubks0gjF2SIT7i6yu2jcqo9jR_u4" alt="" loading="lazy">
                <h2>Yolculuğun Burada Başlıyor</h2>
                <p>Zorbalığa karşı farkındalık ve güvenli öğrenme için ihtiyacın olan araçlar burada.</p>
            </div>
        </section>

        <div class="auth-split-main">
            <div class="auth-split-box">
                <header>
                    <div class="auth-split-brand">
                        <div class="auth-split-brand-icon">
                            <span class="material-symbols-outlined">school</span>
                        </div>
                        <span class="auth-split-brand-text">Gençlik Rehberim</span>
                    </div>
                    <h1 class="auth-split-title">Giriş Yap</h1>
                    <p class="auth-split-sub">Kullanıcı adı veya e-posta ile oturum aç.</p>
                </header>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <span class="material-symbols-outlined alert-icon">error</span>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <label class="auth-field-label" for="username">Kullanıcı adı veya e-posta</label>
                    <div class="auth-input-wrap">
                        <span class="material-symbols-outlined input-icon">alternate_email</span>
                        <input class="form-control-auth" type="text" id="username" name="username" autocomplete="username"
                               placeholder="kullanici veya mail@ornek.com"
                               value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <label class="auth-field-label" for="password">Şifre</label>
                    <div class="auth-input-wrap">
                        <span class="material-symbols-outlined input-icon">lock</span>
                        <input class="form-control-auth" type="password" id="password" name="password" autocomplete="current-password" placeholder="••••••••" required>
                        <button class="toggle-visibility" type="button" id="togglePw" aria-label="Şifreyi göster">
                            <span class="material-symbols-outlined" id="togglePwIcon">visibility</span>
                        </button>
                    </div>

                    <div class="auth-row-between">
                        <label class="auth-checkbox-label">
                            <input type="checkbox" name="remember" value="1" class="auth-checkbox-input">
                            Beni hatırla
                        </label>
                        <span class="auth-footer-link auth-footer-link--no-margin"><a href="/genclik-rehberim/girisyap.php">Şifremi unuttum</a></span>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn-full">
                        Giriş Yap
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </form>

                <div class="auth-divider"><span>veya şununla devam et</span></div>
                <div class="auth-oauth-row">
                    <button type="button" class="auth-oauth-btn" title="Yakında" disabled>
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBzL6jpY8_3oJWQdKe4WDOOQwN_5Z743Wn2GSL8rNALID2JnZONrSyquG5BsgVl9rjxvnlmB9H47g6sTrCv4Y1WHCayWvoVfXCNQ51Vwk1sNSy0YzfiKgcXQp9kcqFS3lFxq7dRJCUV4r8MGeDfx6lPCjOLivz4LBMaZEAcPaIXALH2x1tPpGU5hPOkAdgdWUDyyk7hDpf07j1FbtTcQIS-CKw17A8y4Be0hdWKK4MICQXKIwVhaa1P7MsYTzNQp2kPb6M5GJEaB7y2" alt="">
                        Google
                    </button>
                    <button type="button" class="auth-oauth-btn" title="Yakında" disabled>
                        <span class="material-symbols-outlined">phone_iphone</span>
                        Apple
                    </button>
                </div>

                <p class="auth-footer-link">
                    Hesabın yok mu?
                    <a href="/genclik-rehberim/kayitol.php">Kayıt Ol</a>
                </p>
                <p class="auth-footer-link">
                    <a href="/genclik-rehberim/index.php">
                        <span class="material-symbols-outlined alert-icon">arrow_back</span>
                        Ana Sayfa
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>

<script>
document.getElementById('togglePw').addEventListener('click', function () {
    var inp = document.getElementById('password');
    var ic = document.getElementById('togglePwIcon');
    if (inp.type === 'password') { inp.type = 'text'; ic.textContent = 'visibility_off'; }
    else { inp.type = 'password'; ic.textContent = 'visibility'; }
});
</script>
<script src="/genclik-rehberim/assets/js/main.js"></script>
</body>
</html>
