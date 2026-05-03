<?php
/**
 * admin/index.php — Admin Paneli Ana Sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Yalnızca adminler erişebilir
requireAdmin();

// Özet istatistikler
$stats        = getAdminStats();
$leaderboard  = getLeaderboard(10);
$recentScores = getAllScores();

// Son 5 skoru al
$recentScores = array_slice($recentScores, 0, 5);
?>
<?php include '../includes/header.php'; ?>

<!-- ===== ADMİN PANELİ ===== -->
<main>
<div class="admin-wrapper">

    <!-- Sol sidebar -->
    <aside class="admin-sidebar" aria-label="Admin menü">
        <div class="admin-sidebar-title">Yönetim Paneli</div>
        <nav aria-label="Admin navigasyon">
            <ul>
                <li>
                    <a href="/genclik-rehberim/admin/index.php" class="active">
                        <span class="material-symbols-outlined">dashboard</span> Genel Bakış
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/users.php">
                        <span class="material-symbols-outlined">groups</span> Kullanıcılar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/scores.php">
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

        <!-- Sayfa başlığı + eylem butonları -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
            <h1 class="admin-page-title" style="margin-bottom:0">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">dashboard</span>
                Admin Paneli
            </h1>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
                <a href="/genclik-rehberim/admin/scores.php" class="btn btn-surface btn-sm">
                    <span class="material-symbols-outlined">download</span> Tüm Skorlar
                </a>
                <a href="/genclik-rehberim/admin/users.php" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined">groups</span> Kullanıcılar
                </a>
            </div>
        </div>

        <!-- KPI Bento Grid -->
        <div class="admin-stats-grid">

            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div class="stat-icon purple">
                        <span class="material-symbols-outlined">groups</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">trending_up</span> Aktif
                    </span>
                </div>
                <div class="stat-info">
                    <p>Toplam Öğrenci</p>
                    <h3><?= (int)$stats['total_students'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div class="stat-icon pink">
                        <span class="material-symbols-outlined">sports_esports</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">trending_up</span>
                    </span>
                </div>
                <div class="stat-info">
                    <p>Oynanan Oyun</p>
                    <h3><?= (int)$stats['total_games'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div class="stat-icon green">
                        <span class="material-symbols-outlined">leaderboard</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">trending_up</span>
                    </span>
                </div>
                <div class="stat-info">
                    <p>Ortalama Puan</p>
                    <h3><?= (int)$stats['avg_score'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div class="stat-icon yellow">
                        <span class="material-symbols-outlined">extension</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">trending_up</span>
                    </span>
                </div>
                <div class="stat-info">
                    <p>Etkinlik Sayısı</p>
                    <h3>3</h3>
                </div>
            </div>

        </div>

        <!-- İki sütun: Liderlik + Son Aktiviteler -->
        <div class="admin-two-col">

            <!-- Liderlik Tablosu -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">leaderboard</span>
                        Liderlik Tablosu
                    </h2>
                    <a href="/genclik-rehberim/admin/scores.php" class="btn btn-outline btn-sm">Tümünü Gör</a>
                </div>
                <?php if (empty($leaderboard)): ?>
                    <div class="empty-state">
                        <span class="material-symbols-outlined">emoji_events</span>
                        <p>Henüz puan verisi yok.</p>
                    </div>
                <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kullanıcı</th>
                                <th>Toplam Puan</th>
                                <th>Oyun</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboard as $i => $leader): ?>
                            <tr>
                                <td>
                                    <strong style="color:<?= $i===0?'#FFBE0B':($i===1?'#64748b':($i===2?'#c47722':'var(--on-surface-variant)')) ?>">
                                        <?= $i===0?'🥇':($i===1?'🥈':($i===2?'🥉':'#'.($i+1))) ?>
                                    </strong>
                                </td>
                                <td><strong><?= e($leader['username']) ?></strong></td>
                                <td>
                                    <span style="color:var(--primary);font-weight:800"><?= (int)$leader['total_score'] ?></span>
                                </td>
                                <td><?= (int)$leader['games_played'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Son Aktiviteler -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">history</span>
                        Son Aktiviteler
                    </h2>
                    <a href="/genclik-rehberim/admin/scores.php" class="btn btn-outline btn-sm">Tümünü Gör</a>
                </div>
                <?php if (empty($recentScores)): ?>
                    <div class="empty-state">
                        <span class="material-symbols-outlined">format_list_bulleted</span>
                        <p>Henüz aktivite yok.</p>
                    </div>
                <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>Etkinlik</th>
                                <th>Puan</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentScores as $row): ?>
                            <tr>
                                <td><strong><?= e($row['username']) ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= e($row['type']) ?>">
                                        <?= e($row['activity_name']) ?>
                                    </span>
                                </td>
                                <td style="font-weight:800">
                                    <?= (int)$row['score'] ?>/<?= (int)$row['max_score'] ?>
                                </td>
                                <td>
                                    <?php
                                        $pct = $row['max_score'] > 0 ? ($row['score']/$row['max_score'])*100 : 0;
                                    ?>
                                    <div class="progress-cell">
                                        <div class="progress-track">
                                            <div class="progress-fill" style="width:<?= min(100,(int)$pct) ?>%"></div>
                                        </div>
                                        <span style="font-size:0.75rem;color:var(--on-surface-variant);font-weight:700"><?= (int)$pct ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Oyun Modülleri (Tasarımdan ilham alınmış) -->
        <div class="card" style="margin-top:1.5rem">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">sports_esports</span>
                    Oyun Modülleri
                </h2>
                <span class="badge badge-success">3 Aktif</span>
            </div>
            <div style="padding:1.5rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
                <!-- Bulmaca -->
                <div style="border:1px solid var(--outline-variant);border-radius:12px;padding:1rem;display:flex;gap:1rem;align-items:center">
                    <div style="width:52px;height:52px;border-radius:12px;background:var(--primary-fixed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:var(--primary);font-size:26px">extension</span>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:700;font-size:0.9rem;color:var(--on-surface)">Bulmaca</div>
                        <div style="font-size:0.78rem;color:var(--on-surface-variant)">10 Soru · 100 Puan</div>
                    </div>
                    <span class="badge badge-success">Aktif</span>
                </div>
                <!-- Eşleştirme -->
                <div style="border:1px solid var(--outline-variant);border-radius:12px;padding:1rem;display:flex;gap:1rem;align-items:center">
                    <div style="width:52px;height:52px;border-radius:12px;background:var(--secondary-fixed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:var(--secondary);font-size:26px">join_inner</span>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:700;font-size:0.9rem;color:var(--on-surface)">Eşleştirme</div>
                        <div style="font-size:0.78rem;color:var(--on-surface-variant)">14 Kart · 140 Puan</div>
                    </div>
                    <span class="badge badge-success">Aktif</span>
                </div>
                <!-- Kategori -->
                <div style="border:1px solid var(--outline-variant);border-radius:12px;padding:1rem;display:flex;gap:1rem;align-items:center">
                    <div style="width:52px;height:52px;border-radius:12px;background:var(--tertiary-fixed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:var(--tertiary);font-size:26px">category</span>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:700;font-size:0.9rem;color:var(--on-surface)">Kategori</div>
                        <div style="font-size:0.78rem;color:var(--on-surface-variant)">17 Kelime · 170 Puan</div>
                    </div>
                    <span class="badge badge-success">Aktif</span>
                </div>
            </div>
        </div>

    </section>

</div>
</main>
<!-- ===== ADMİN PANELİ SONU ===== -->

<?php include '../includes/footer.php'; ?>
