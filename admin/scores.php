<?php
/**
 * admin/scores.php — Admin Skor İzleme Sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

$pageTitle = 'Skor İzleme';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

$db = getDB();

// Filtre parametreleri (GET)
$filterType = $_GET['type'] ?? 'all';
$filterUser = trim($_GET['user'] ?? '');

// Temel sorgu
$sql = 'SELECT s.id, u.username, a.name as activity_name, a.type,
               s.score, s.max_score, s.played_at
        FROM scores s
        JOIN users u ON u.id = s.user_id
        JOIN activities a ON a.id = s.activity_id
        WHERE 1=1';

$params = [];

if ($filterType !== 'all') {
    $sql .= ' AND a.type = ?';
    $params[] = $filterType;
}

if ($filterUser !== '') {
    $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filterUser);
    $sql .= " AND u.username LIKE ? ESCAPE '\\\\'";
    $params[] = '%' . $escaped . '%';
}

$sql .= ' ORDER BY s.played_at DESC LIMIT 200';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$scores = $stmt->fetchAll();

// Özet istatistikler
$totalGames = count($scores);
$totalScore = array_sum(array_column($scores, 'score'));
$avgScore   = $totalGames > 0 ? round($totalScore / $totalGames, 1) : 0;
?>
<?php include '../includes/header.php'; ?>

<!-- ===== SKOR İZLEME ===== -->
<main>
<div class="admin-wrapper">

    <!-- Sol sidebar -->
    <aside class="admin-sidebar" aria-label="Admin menü">
        <div class="admin-sidebar-title">Yönetim Paneli</div>
        <nav aria-label="Admin navigasyon">
            <ul>
                <li>
                    <a href="/genclik-rehberim/admin/index.php">
                        <span class="material-symbols-outlined">dashboard</span> Genel Bakış
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/users.php">
                        <span class="material-symbols-outlined">groups</span> Kullanıcılar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/scores.php" class="active">
                        <span class="material-symbols-outlined">bar_chart</span> Skorlar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/feedback.php">
                        <span class="material-symbols-outlined">feedback</span> Geri Bildirimler
                    </a>
                </li>
                <li class="admin-sidebar__nav-item--spaced">
                    <a href="/genclik-rehberim/index.php">
                        <span class="material-symbols-outlined">home</span> Ana Sayfa
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/logout.php" class="admin-sidebar__link--danger">
                        <span class="material-symbols-outlined">logout</span> Çıkış
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Ana içerik -->
    <section class="admin-content">

        <div class="admin-content__header">
            <h1 class="admin-page-title admin-content__header-title">
                <span class="material-symbols-outlined icon-fill">bar_chart</span>
                Skor İzleme
            </h1>
            <div class="admin-content__actions">
                <?php
                $exportQuery = http_build_query(array_filter([
                    'type' => $filterType !== 'all' ? $filterType : '',
                    'user' => $filterUser,
                ]));
                ?>
                <a href="/genclik-rehberim/admin/export.php<?= $exportQuery ? '?' . $exportQuery : '' ?>"
                   class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined">download</span> CSV İndir
                </a>
            </div>
        </div>

        <!-- Filtre kartı -->
        <div class="card" style="margin-bottom:2rem">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined">filter_list</span>
                    Filtrele
                </h2>
            </div>
            <div class="admin-filter__body">
                <form method="GET" class="admin-filter__form">

                    <!-- Etkinlik türü filtresi -->
                    <div class="admin-filter__field">
                        <label class="form-label">Etkinlik Türü</label>
                        <select name="type" class="form-control admin-filter__select">
                            <option value="all"        <?= $filterType==='all'        ?'selected':'' ?>>Tümü</option>
                            <option value="bulmaca"    <?= $filterType==='bulmaca'    ?'selected':'' ?>>Bulmaca</option>
                            <option value="eslestirme" <?= $filterType==='eslestirme' ?'selected':'' ?>>Eşleştirme</option>
                            <option value="wordle"     <?= $filterType==='wordle'     ?'selected':'' ?>>Wordle</option>
                            <option value="cengel"     <?= $filterType==='cengel'     ?'selected':'' ?>>Çengel Bulmaca</option>
                        </select>
                    </div>

                    <!-- Kullanıcı adı filtresi -->
                    <div class="admin-filter__field">
                        <label class="form-label">Kullanıcı Ara</label>
                        <input type="text" name="user" class="form-control"
                               placeholder="Kullanıcı adı..." value="<?= e($filterUser) ?>">
                    </div>

                    <div class="admin-filter__actions">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined">search</span> Filtrele
                        </button>
                        <a href="/genclik-rehberim/admin/scores.php" class="btn btn-outline">
                            <span class="material-symbols-outlined">close</span> Temizle
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <!-- Mini istatistikler -->
        <div class="admin-stats-grid admin-stats-grid--bottom-spaced">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <span class="material-symbols-outlined">format_list_numbered</span>
                </div>
                <div class="stat-info">
                    <p>Sonuç Sayısı</p>
                    <h3><?= $totalGames ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <span class="material-symbols-outlined">emoji_events</span>
                </div>
                <div class="stat-info">
                    <p>Toplam Puan</p>
                    <h3><?= $totalScore ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <span class="material-symbols-outlined">leaderboard</span>
                </div>
                <div class="stat-info">
                    <p>Ortalama Puan</p>
                    <h3><?= $avgScore ?></h3>
                </div>
            </div>
        </div>

        <!-- Skor tablosu -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined">table_view</span>
                    Skor Listesi
                </h2>
                <span class="admin-table__meta">(En fazla 200 kayıt)</span>
            </div>

            <?php if (empty($scores)): ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">bar_chart</span>
                    <p>Filtreye uygun skor bulunamadı.</p>
                </div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kullanıcı</th>
                            <th>Etkinlik</th>
                            <th>Tür</th>
                            <th>Puan</th>
                            <th>Başarı Oranı</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $i => $row): ?>
                        <?php $percent = $row['max_score'] > 0
                            ? round(($row['score'] / $row['max_score']) * 100) : 0; ?>
                        <tr>
                            <td class="admin-table__meta"><?= $i + 1 ?></td>
                            <td><strong><?= e($row['username']) ?></strong></td>
                            <td class="admin-table__subtle"><?= e($row['activity_name']) ?></td>
                            <td>
                                <span class="badge badge-<?= e($row['type']) ?>">
                                    <?= e($row['type']) ?>
                                </span>
                            </td>
                            <td class="admin-table__value--primary">
                                <?= (int)$row['score'] ?>/<?= (int)$row['max_score'] ?>
                            </td>
                            <td>
                                <div class="progress-cell">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width:<?= $percent ?>%"></div>
                                    </div>
                                    <span class="admin-table__percent--wide">
                                        <?= $percent ?>%
                                    </span>
                                </div>
                            </td>
                            <td class="admin-table__meta">
                                <?= date('d.m.Y H:i', strtotime($row['played_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </section>

</div>
</main>

<?php include '../includes/footer.php'; ?>
