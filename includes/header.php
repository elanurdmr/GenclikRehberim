<?php
/**
 * header.php — Ortak Sayfa Başlığı ve Navigasyon
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8').' | ' : '' ?>Gençlik Rehberim</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tasarım token'ları -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/tokens.css">
    <!-- Temel yardımcı sınıflar -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/base.css">
    <!-- Navigasyon + Footer -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/layout.css">
    <!-- UI Bileşenleri (butonlar, kartlar, tablolar) -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/components.css">
    <!-- Ana stil dosyası -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/style.css">
    <?php
    $__pb = basename($_SERVER['PHP_SELF']);
    $__path = $_SERVER['PHP_SELF'];
    ?>
    <?php if ($__pb === 'index.php' && strpos($__path, '/admin/') === false): ?>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/home.css">
    <?php endif; ?>
    <?php if ($__pb === 'girisyap.php' || $__pb === 'kayitol.php'): ?>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/auth.css">
    <?php endif; ?>
    <?php if ($__pb === 'ogrencipanel.php'): ?>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/ogrencipanel.css">
    <?php endif; ?>
    <?php if (strpos($__path, '/games/') !== false): ?>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/games.css">
    <?php endif; ?>
    <?php if (strpos($__path, '/admin/') !== false): ?>
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/admin.css">
    <?php endif; ?>
</head>
<body>

<!-- ===== ANA HEADER / NAVİGASYON ===== -->
<?php $hideNav = $hideNav ?? false; if (!$hideNav): ?>
<header class="site-header">
    <nav class="navbar" aria-label="Ana navigasyon">

        <!-- Logo / Marka -->
        <a href="/genclik-rehberim/index.php" class="nav-logo">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">shield_person</span>
            <span>Gençlik Rehberim</span>
        </a>

        <!-- Hamburger menü (mobil) -->
        <button class="nav-toggle" id="navToggle" aria-label="Menüyü aç/kapat" aria-expanded="false">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <!-- Navigasyon bağlantıları -->
        <ul class="nav-links" id="navLinks">
            <li>
                <a href="/genclik-rehberim/index.php">
                    <span class="material-symbols-outlined">home</span> Ana Sayfa
                </a>
            </li>
            <li>
                <a href="/genclik-rehberim/games/bulmaca.php">
                    <span class="material-symbols-outlined">extension</span> Bulmaca
                </a>
            </li>
            <li>
                <a href="/genclik-rehberim/games/cengelbulmaca.php">
                    <span class="material-symbols-outlined">grid_on</span> Çengel
                </a>
            </li>
            <li>
                <a href="/genclik-rehberim/games/wordle.php">
                    <span class="material-symbols-outlined">spellcheck</span> Wordle
                </a>
            </li>
            <li>
                <a href="/genclik-rehberim/games/eslestirme.php">
                    <span class="material-symbols-outlined">join_inner</span> Eşleştirme
                </a>
            </li>
            <?php if (isLoggedIn()): ?>
                <li>
                    <a href="/genclik-rehberim/ogrencipanel.php">
                        <span class="material-symbols-outlined">bar_chart</span> Öğrenci Paneli
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                    <li>
                        <a href="/genclik-rehberim/admin/index.php">
                            <span class="material-symbols-outlined">settings</span> Admin
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="/genclik-rehberim/logout.php" class="btn-nav-logout">
                        <span class="material-symbols-outlined">logout</span>
                        <?= e($_SESSION['username']) ?>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="/genclik-rehberim/girisyap.php" class="btn-nav-login">
                        <span class="material-symbols-outlined">login</span> Giriş Yap
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/kayitol.php" class="btn-nav-register">
                        <span class="material-symbols-outlined">person_add</span> Kayıt Ol
                    </a>
                </li>
            <?php endif; ?>
        </ul>

    </nav>
</header>
<!-- ===== HEADER SONU ===== -->
<?php endif; /* !$hideNav */
