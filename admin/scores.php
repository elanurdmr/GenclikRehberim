<?php
/**
 * admin/scores.php — Admin Skor İzleme Sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

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
    $sql .= ' AND u.username LIKE ?';
    $params[] = '%' . $filterUser . '%';
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
                <li style="margin-top:1.5rem">
                    <a href="/genclik-rehberim/index.php">
                        <span class="material-symbols-outlined">home</span> Ana Sayfa
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/logout.php" style="color:var(--error)!important">
                        <span class="material-symbols-outlined">logout</span> Çıkış
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Ana içerik -->
    <section class="admin-content">

        <h1 class="admin-page-title">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">bar_chart</span>
            Skor İzleme
        </h1>

        <!-- Filtre kartı -->
        <div class="card" style="margin-bottom:2rem">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined">filter_list</span>
                    Filtrele
                </h2>
            </div>
            <div style="padding:1.25rem">
                <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">

                    <!-- Etkinlik türü filtresi -->
                    <div style="flex:1;min-width:180px">
                        <label class="form-label">Etkinlik Türü</label>
                        <select name="type" class="form-control" style="border-radius:10px;cursor:pointer">
                            <option value="all"        <?= $filterType==='all'        ?'selected':'' ?>>Tümü</option>
                            <option value="bulmaca"    <?= $filterType==='bulmaca'    ?'selected':'' ?>>Bulmaca</option>
                            <option value="eslestirme" <?= $filterType==='eslestirme' ?'selected':'' ?>>Eşleştirme</option>
                            <option value="kategori"   <?= $filterType==='kategori'   ?'selected':'' ?>>Kategori</option>
                            <option value="wordle"     <?= $filterType==='wordle'     ?'selected':'' ?>>Wordle</option>
                        </select>
                    </div>

                    <!-- Kullanıcı adı filtresi -->
                    <div style="flex:1;min-width:180px">
                        <label class="form-label">Kullanıcı Ara</label>
                        <input type="text" name="user" class="form-control"
                               placeholder="Kullanıcı adı..." value="<?= e($filterUser) ?>">
                    </div>

                    <div style="display:flex;gap:0.5rem;align-items:flex-end">
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
        <div class="admin-stats-grid" style="margin-bottom:2rem">
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
                <span style="color:var(--on-surface-variant);font-size:0.82rem">(En fazla 200 kayıt)</span>
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
                            <td style="color:var(--on-surface-variant);font-size:0.82rem"><?= $i + 1 ?></td>
                            <td><strong><?= e($row['username']) ?></strong></td>
                            <td style="color:var(--on-surface-variant)"><?= e($row['activity_name']) ?></td>
                            <td>
                                <span class="badge badge-<?= e($row['type']) ?>">
                                    <?= e($row['type']) ?>
                                </span>
                            </td>
                            <td style="font-weight:800;color:var(--primary)">
                                <?= (int)$row['score'] ?>/<?= (int)$row['max_score'] ?>
                            </td>
                            <td>
                                <div class="progress-cell">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width:<?= $percent ?>%"></div>
                                    </div>
                                    <span style="font-size:0.78rem;font-weight:700;color:var(--on-surface-variant);min-width:34px">
                                        <?= $percent ?>%
                                    </span>
                                </div>
                            </td>
                            <td style="color:var(--on-surface-variant);font-size:0.82rem">
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
