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
    <title>Gençlik Rehberim | Akran Zorbalığı Farkındalığı</title>

    <!-- Ana stil dosyası -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/style.css">

    <!-- Font Awesome ikonları -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap"
          rel="stylesheet">
</head>
<body>

<!-- ===== ANA HEADER / NAVİGASYON ===== -->
<header class="site-header">
    <nav class="navbar" aria-label="Ana navigasyon">

        <!-- Logo -->
        <a href="/genclik-rehberim/index.php" class="nav-logo">
            <i class="fa-solid fa-shield-heart"></i>
            <span>Gençlik Rehberim</span>
        </a>

        <!-- Hamburger menü (mobil) -->
        <button class="nav-toggle" id="navToggle" aria-label="Menüyü aç/kapat">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Navigasyon bağlantıları -->
        <ul class="nav-links" id="navLinks">
            <li><a href="/genclik-rehberim/index.php"><i class="fa-solid fa-house"></i> Ana Sayfa</a></li>
            <li><a href="/genclik-rehberim/games/bulmaca.php"><i class="fa-solid fa-puzzle-piece"></i> Bulmaca</a></li>
            <li><a href="/genclik-rehberim/games/eslestirme.php"><i class="fa-solid fa-arrows-left-right"></i> Eşleştirme</a></li>
            <li><a href="/genclik-rehberim/games/kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>

            <?php if (isLoggedIn()): ?>
                <!-- Giriş yapılmışsa dashboard ve çıkış linkleri -->
                <li><a href="/genclik-rehberim/dashboard.php"><i class="fa-solid fa-chart-line"></i> Puan Takibi</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="/genclik-rehberim/admin/index.php"><i class="fa-solid fa-gear"></i> Admin</a></li>
                <?php endif; ?>
                <li>
                    <a href="/genclik-rehberim/logout.php" class="btn-nav-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <?= e($_SESSION['username']) ?>
                    </a>
                </li>
            <?php else: ?>
                <!-- Giriş yapılmamışsa giriş/kayıt linkleri -->
                <li><a href="/genclik-rehberim/login.php" class="btn-nav-login"><i class="fa-solid fa-right-to-bracket"></i> Giriş</a></li>
                <li><a href="/genclik-rehberim/register.php" class="btn-nav-register"><i class="fa-solid fa-user-plus"></i> Kayıt Ol</a></li>
            <?php endif; ?>
        </ul>

    </nav>
</header>
<!-- ===== HEADER SONU ===== -->
