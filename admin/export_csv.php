<?php
/**
 * admin/export_csv.php — Tüm skor loglarını CSV olarak indir
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * Kolonlar: Kullanıcı Adı, Etkinlik, Tür, Puan, Maks Puan, Başarı Oranı (%), Tarih
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$db = getDB();

$filterType = isset($_GET['type']) && $_GET['type'] !== 'all' ? $_GET['type'] : '';
$filterUser = trim((string)($_GET['user'] ?? ''));

$sql    = 'SELECT u.username, a.name AS activity_name, a.type,
                  s.score, s.max_score, s.played_at
           FROM scores s
           JOIN users u ON u.id = s.user_id
           JOIN activities a ON a.id = s.activity_id
           WHERE 1=1';
$params = [];

if ($filterType !== '') {
    $sql     .= ' AND a.type = ?';
    $params[] = $filterType;
}

if ($filterUser !== '') {
    $escaped  = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filterUser);
    $sql     .= " AND u.username LIKE ? ESCAPE '\\\\'";
    $params[] = '%' . $escaped . '%';
}

$sql .= ' ORDER BY s.played_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = 'genclik_rehberim_skorlar_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');

/* UTF-8 BOM — Excel'in Türkçe karakterleri doğru okuması için */
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, ['Kullanıcı Adı', 'Etkinlik', 'Tür', 'Puan', 'Maks Puan', 'Başarı Oranı (%)', 'Tarih']);

foreach ($rows as $row) {
    $percent = $row['max_score'] > 0
        ? (int)round(($row['score'] / $row['max_score']) * 100)
        : 0;
    fputcsv($out, [
        $row['username'],
        $row['activity_name'],
        $row['type'],
        $row['score'],
        $row['max_score'],
        $percent,
        date('d.m.Y H:i', strtotime($row['played_at'])),
    ]);
}

fclose($out);
exit;
