<?php
/**
 * admin/index.php — Admin Paneli Ana Sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

$pageTitle = 'Yönetim Paneli';

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

        <!-- Sayfa başlığı + eylem butonları -->
        <div class="admin-content__header">
            <div>
                <h1 class="admin-page-title admin-content__header-title">
                    <span class="material-symbols-outlined icon-fill">dashboard</span>
                    Yönetim Paneli
                </h1>
                <p class="text-body-base admin-content__subtitle">
                    Öğrenci etkinlikleri, skorlar ve kullanıcıların genel görünümü. Veriler <strong>PDO / MariaDB</strong> ile güncellenir.
                </p>
            </div>
            <div class="admin-content__actions">
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
                <div class="admin-stat-card__header">
                    <div class="stat-icon purple">
                        <span class="material-symbols-outlined">groups</span>
                    </div>
                    <?php if ($stats['new_students_week'] > 0): ?>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">trending_up</span> +<?= (int)$stats['new_students_week'] ?>
                    </span>
                    <?php else: ?>
                    <span class="kpi-trend kpi-trend-flat">—</span>
                    <?php endif; ?>
                </div>
                <div class="stat-info">
                    <p>Toplam Öğrenci</p>
                    <h3><?= (int)$stats['total_students'] ?></h3>
                    <span class="stat-sub">
                        <span class="material-symbols-outlined">person_add</span>
                        Son 7 günde <?= (int)$stats['new_students_week'] ?> yeni kayıt
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="admin-stat-card__header">
                    <div class="stat-icon pink">
                        <span class="material-symbols-outlined">sports_esports</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">today</span> <?= (int)$stats['games_today'] ?> bugün
                    </span>
                </div>
                <div class="stat-info">
                    <p>Oynanan Oyun</p>
                    <h3><?= (int)$stats['total_games'] ?></h3>
                    <span class="stat-sub">
                        <span class="material-symbols-outlined">schedule</span>
                        Tüm zamanların toplamı
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="admin-stat-card__header">
                    <div class="stat-icon green">
                        <span class="material-symbols-outlined">leaderboard</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">military_tech</span> En yüksek <?= (int)$stats['top_score'] ?>
                    </span>
                </div>
                <div class="stat-info">
                    <p>Ortalama Puan</p>
                    <h3><?= $stats['avg_score'] ?></h3>
                    <span class="stat-sub">
                        <span class="material-symbols-outlined">query_stats</span>
                        Oyun başına ortalama skor
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="admin-stat-card__header">
                    <div class="stat-icon yellow">
                        <span class="material-symbols-outlined">extension</span>
                    </div>
                    <span class="kpi-trend kpi-trend-up">
                        <span class="material-symbols-outlined">check_circle</span> Aktif
                    </span>
                </div>
                <div class="stat-info">
                    <p>Etkinlik Sayısı</p>
                    <h3><?= (int)$stats['total_activities'] ?></h3>
                    <span class="stat-sub">
                        <span class="material-symbols-outlined">grid_view</span>
                        Yayında olan oyun modülü
                    </span>
                </div>
            </div>

        </div>

        <!-- İki sütun: Liderlik + Son Aktiviteler -->
        <div class="admin-two-col">

            <!-- Liderlik Tablosu -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined icon-fill">leaderboard</span>
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
                                    <span class="admin-table__value--primary"><?= (int)$leader['total_score'] ?></span>
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
                        <span class="material-symbols-outlined icon-fill">history</span>
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
                                <td class="admin-table__value">
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
                                        <span class="admin-table__percent"><?= (int)$pct ?>%</span>
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

        <!-- Oyun Modülleri -->
        <div class="card admin-content__section--spaced">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined icon-fill">sports_esports</span>
                    Oyun Modülleri
                </h2>
                <span class="badge badge-success">4 Aktif</span>
            </div>
            <div class="admin-modules__grid">
                <!-- Bulmaca -->
                <div class="admin-modules__card">
                    <div class="admin-modules__icon">
                        <span class="material-symbols-outlined admin-modules__icon-symbol">extension</span>
                    </div>
                    <div class="admin-modules__info">
                        <div class="admin-modules__name">Bulmaca</div>
                        <div class="admin-modules__detail">10 Soru · 100 Puan</div>
                    </div>
                    <span class="badge badge-success">Aktif</span>
                </div>
                <!-- Çengel Bulmaca -->
                <div class="admin-modules__card">
                    <div class="admin-modules__icon" style="background:var(--secondary-container)">
                        <span class="material-symbols-outlined admin-modules__icon-symbol" style="color:var(--secondary)">grid_on</span>
                    </div>
                    <div class="admin-modules__info">
                        <div class="admin-modules__name">Çengel Bulmaca</div>
                        <div class="admin-modules__detail">Günlük Bulmaca · 100 Puan</div>
                    </div>
                    <span class="badge badge-success">Aktif</span>
                </div>
                <!-- Wordle -->
                <div class="admin-modules__card">
                    <div class="admin-modules__icon" style="background:#e3f0ff">
                        <span class="material-symbols-outlined admin-modules__icon-symbol" style="color:#075fab">spellcheck</span>
                    </div>
                    <div class="admin-modules__info">
                        <div class="admin-modules__name">Wordle</div>
                        <div class="admin-modules__detail">5 Harfli Kelime · 100 Puan</div>
                    </div>
                    <span class="badge badge-success">Aktif</span>
                </div>
                <!-- Eşleştirme -->
                <div class="admin-modules__card">
                    <div class="admin-modules__icon--secondary">
                        <span class="material-symbols-outlined admin-modules__icon-symbol--secondary">join_inner</span>
                    </div>
                    <div class="admin-modules__info">
                        <div class="admin-modules__name">Eşleştirme</div>
                        <div class="admin-modules__detail">3 Bölüm · 390 Puan</div>
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
