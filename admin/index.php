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
$stats       = getAdminStats();
$leaderboard = getLeaderboard(10);
$recentScores= getAllScores();

// Son 5 skoru al
$recentScores = array_slice($recentScores, 0, 5);
?>
<?php include '../includes/header.php'; ?>

<!-- ===== ADMİN PANELİ ===== -->
<main>
<div class="admin-wrapper">

    <!-- Sol sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Admin Menü</div>
        <nav aria-label="Admin navigasyon">
            <ul>
                <li>
                    <a href="/genclik-rehberim/admin/index.php" class="active">
                        <i class="fa-solid fa-gauge"></i> Genel Bakış
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/users.php">
                        <i class="fa-solid fa-users"></i> Kullanıcılar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/scores.php">
                        <i class="fa-solid fa-chart-bar"></i> Skorlar
                    </a>
                </li>
                <li style="margin-top:2rem">
                    <a href="/genclik-rehberim/index.php">
                        <i class="fa-solid fa-house"></i> Ana Sayfa
                    </a>
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

        <!-- Sayfa başlığı -->
        <h1 class="admin-page-title">
            <i class="fa-solid fa-gauge"></i>
            Genel Bakış
        </h1>

        <!-- İstatistik kartları -->
        <div class="admin-stats-grid">

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <p>Toplam Öğrenci</p>
                    <h3><?= $stats['total_students'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div class="stat-info">
                    <p>Oynanan Oyun</p>
                    <h3><?= $stats['total_games'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <p>Ortalama Puan</p>
                    <h3><?= $stats['avg_score'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fa-solid fa-puzzle-piece"></i>
                </div>
                <div class="stat-info">
                    <p>Etkinlik Sayısı</p>
                    <h3>3</h3>
                </div>
            </div>

        </div>

        <!-- Liderlik tablosu ve son skorlar (yan yana) -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;flex-wrap:wrap">

            <!-- Liderlik Tablosu -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-ranking-star"></i> Liderlik Tablosu</h2>
                    <a href="/genclik-rehberim/admin/scores.php" class="btn btn-outline btn-sm">Tümünü Gör</a>
                </div>
                <?php if (empty($leaderboard)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-trophy"></i>
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
                                    <span style="font-weight:900;color:<?= $i===0?'#FFBE0B':($i===1?'#a8b0c0':($i===2?'#c47722':'var(--text-muted)')) ?>">
                                        #<?= $i + 1 ?>
                                    </span>
                                </td>
                                <td><strong><?= e($leader['username']) ?></strong></td>
                                <td style="color:var(--primary);font-weight:800"><?= $leader['total_score'] ?></td>
                                <td><?= $leader['games_played'] ?></td>
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
                    <h2><i class="fa-solid fa-clock-rotate-left"></i> Son Aktiviteler</h2>
                    <a href="/genclik-rehberim/admin/scores.php" class="btn btn-outline btn-sm">Tümünü Gör</a>
                </div>
                <?php if (empty($recentScores)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-list"></i>
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
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div>

    </section>

</div>
</main>
<!-- ===== ADMİN PANELİ SONU ===== -->

<?php include '../includes/footer.php'; ?>
