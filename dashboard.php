<?php
/**
 * dashboard.php — Öğrenci paneline yönlendirme (geriye dönük uyumluluk)
 */
require_once __DIR__ . '/includes/auth.php';

requireLogin();

if (isAdmin()) {
    header('Location: /genclik-rehberim/admin/index.php');
    exit;
}

header('Location: /genclik-rehberim/ogrencipanel.php', true, 302);
exit;
