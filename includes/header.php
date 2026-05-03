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

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Ana stil dosyası -->
    <link rel="stylesheet" href="/genclik-rehberim/assets/css/style.css">
</head>
<body>

<!-- ===== ANA HEADER / NAVİGASYON ===== -->
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
            <li>
                <a href="/genclik-rehberim/games/kategori.php">
                    <span class="material-symbols-outlined">category</span> Kategori
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
