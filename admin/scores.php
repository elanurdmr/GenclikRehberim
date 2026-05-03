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
$filterType = $_GET['type'] ?? 'all';   // 'all', 'bulmaca', 'eslestirme', 'kategori'
$filterUser = trim($_GET['user'] ?? '');

// Temel sorgu
$sql = 'SELECT s.id, u.username, a.name as activity_name, a.type,
               s.score, s.max_score, s.played_at
        FROM scores s
        JOIN users u ON u.id = s.user_id
        JOIN activities a ON a.id = s.activity_id
        WHERE 1=1';

$params = [];

// Tür filtresi
if ($filterType !== 'all') {
    $sql .= ' AND a.type = ?';
    $params[] = $filterType;
}

// Kullanıcı filtresi
if ($filterUser !== '') {
    $sql .= ' AND u.username LIKE ?';
    $params[] = '%' . $filterUser . '%';
}

$sql .= ' ORDER BY s.played_at DESC LIMIT 200';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$scores = $stmt->fetchAll();

// Toplu istatistikler
$totalGames = count($scores);
$totalScore = array_sum(array_column($scores, 'score'));
$avgScore   = $totalGames > 0 ? round($totalScore / $totalGames, 1) : 0;
?>
<?php include '../includes/header.php'; ?>

<!-- ===== SKOR İZLEME ===== -->
<main>
<div class="admin-wrapper">

    <!-- Sol sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Admin Menü</div>
        <nav aria-label="Admin navigasyon">
            <ul>
                <li><a href="/genclik-rehberim/admin/index.php"><i class="fa-solid fa-gauge"></i> Genel Bakış</a></li>
                <li><a href="/genclik-rehberim/admin/users.php"><i class="fa-solid fa-users"></i> Kullanıcılar</a></li>
                <li><a href="/genclik-rehberim/admin/scores.php" class="active"><i class="fa-solid fa-chart-bar"></i> Skorlar</a></li>
                <li style="margin-top:2rem">
                    <a href="/genclik-rehberim/index.php"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
                </li>
                <li>
                    <a href="/genclik-rehberim/logout.php" style="color:var(--secondary)!important">
                        <i class="fa-solid fa-right-from-bracket"></i> Çıkış
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Ana içerik -->
    <section class="admin-content">

        <h1 class="admin-page-title">
            <i class="fa-solid fa-chart-bar"></i>
            Skor İzleme
        </h1>

        <!-- Filtre kartı -->
        <div class="card" style="margin-bottom:2rem">
            <div class="card-header">
                <h2><i class="fa-solid fa-filter"></i> Filtrele</h2>
            </div>
            <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">

                <!-- Etkinlik türü filtresi -->
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
                    <label>Etkinlik Türü</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-gamepad"></i>
                        <select name="type" style="width:100%;padding:0.9rem 1rem 0.9rem 2.8rem;border:2px solid #e2e8f0;border-radius:var(--radius);font-family:var(--font);font-size:1rem;background:var(--bg-light);color:var(--text-dark);cursor:pointer">
                            <option value="all"        <?= $filterType==='all'         ?'selected':'' ?>>Tümü</option>
                            <option value="bulmaca"    <?= $filterType==='bulmaca'     ?'selected':'' ?>>Bulmaca</option>
                            <option value="eslestirme" <?= $filterType==='eslestirme'  ?'selected':'' ?>>Eşleştirme</option>
                            <option value="kategori"   <?= $filterType==='kategori'    ?'selected':'' ?>>Kategori</option>
                        </select>
                    </div>
                </div>

                <!-- Kullanıcı adı filtresi -->
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
                    <label>Kullanıcı Ara</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" name="user" placeholder="Kullanıcı adı..."
                               value="<?= e($filterUser) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-bottom:0;height:48px">
                    <i class="fa-solid fa-search"></i> Filtrele
                </button>

                <a href="/genclik-rehberim/admin/scores.php" class="btn btn-outline" style="height:48px">
                    <i class="fa-solid fa-xmark"></i> Temizle
                </a>

            </form>
        </div>

        <!-- Mini istatistikler -->
        <div class="admin-stats-grid" style="margin-bottom:2rem">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa-solid fa-list-check"></i></div>
                <div class="stat-info"><p>Sonuç Sayısı</p><h3><?= $totalGames ?></h3></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pink"><i class="fa-solid fa-trophy"></i></div>
                <div class="stat-info"><p>Toplam Puan</p><h3><?= $totalScore ?></h3></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-chart-line"></i></div>
                <div class="stat-info"><p>Ortalama Puan</p><h3><?= $avgScore ?></h3></div>
            </div>
        </div>

        <!-- Skor tablosu -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-table"></i> Skor Listesi</h2>
                <span style="color:var(--text-muted);font-size:0.85rem">(En fazla 200 kayıt)</span>
            </div>

            <?php if (empty($scores)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-chart-bar"></i>
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
                            <th>Oran</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $i => $row): ?>
                        <?php $percent = $row['max_score'] > 0
                            ? round(($row['score'] / $row['max_score']) * 100) : 0; ?>
                        <tr>
                            <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                            <td><strong><?= e($row['username']) ?></strong></td>
                            <td><?= e($row['activity_name']) ?></td>
                            <td>
                                <span class="badge badge-<?= e($row['type']) ?>">
                                    <?= e($row['type']) ?>
                                </span>
                            </td>
                            <td style="font-weight:800;color:var(--primary)">
                                <?= (int)$row['score'] ?>/<?= (int)$row['max_score'] ?>
                            </td>
                            <td>
                                <div class="score-bar-wrap">
                                    <div class="score-bar">
                                        <div class="score-bar-fill" data-width="<?= $percent ?>%"
                                             style="width:0%"></div>
                                    </div>
                                    <span style="font-size:0.8rem;font-weight:700;color:var(--primary);min-width:36px">
                                        <?= $percent ?>%
                                    </span>
                                </div>
                            </td>
                            <td style="color:var(--text-muted);font-size:0.85rem">
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
