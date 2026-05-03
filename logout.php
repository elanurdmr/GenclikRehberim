<?php
/**
 * logout.php — Oturumu Sonlandırma
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once 'includes/auth.php';

// Oturumu güvenli şekilde sonlandır
logoutUser();
// logoutUser() içinde header() ve exit çağrıldığı için buraya ulaşılmaz
