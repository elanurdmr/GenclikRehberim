<?php
/**
 * dashboard.php — Öğrenci Paneli (Puan Takibi)
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Giriş yapmamışsa login'e yönlendir
requireLogin();

// Kullanıcı verilerini al
$userId      = (int)$_SESSION['user_id'];
$username    = $_SESSION['username'];

// Puan ve geçmiş verilerini veritabanından getir
$totalScore  = getUserTotalScore($userId);
$history     = getUserHistory($userId, 15);
$leaderboard = getLeaderboard(5);

// Her etkinliğin en yüksek puanı (activity_id: 1=bulmaca, 2=eslestirme, 3=kategori)
$highBulmaca   = getUserHighScore($userId, 1);
$highEslestirme= getUserHighScore($userId, 2);
$highKategori  = getUserHighScore($userId, 3);

// Toplam oyun sayısı
$gamesPlayed = count($history);
?>
<?php include 'includes/header.php'; ?>

<!-- ===== ÖĞRENCİ PANELI ===== -->
<main>
<div class="dashboard-wrapper">

    <!-- Karşılama kartı -->
    <div class="dashboard-hero">
        <h1><i class="fa-solid fa-star" style="color:#FFBE0B"></i> Merhaba, <?= e($username) ?>!</h1>
        <p>Etkinlikleri tamamla, puan kazan ve liderlik tablosuna çık!</p>
    </div>

    <!-- İstatistik kartları -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="stat-info">
                <p>Toplam Puan</p>
                <h3><?= $totalScore ?></h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon pink">
                <i class="fa-solid fa-gamepad"></i>
            </div>
            <div class="stat-info">
                <p>Oynanan Oyun</p>
                <h3><?= $gamesPlayed ?></h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fa-solid fa-puzzle-piece"></i>
            </div>
            <div class="stat-info">
                <p>Bulmaca En İyi</p>
                <h3><?= $highBulmaca ?></h3>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon yellow">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="stat-info">
                <p>Kategori En İyi</p>
                <h3><?= $highKategori ?></h3>
            </div>
        </div>
    </div>

    <!-- Etkinlik Kartları -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-gamepad"></i> Etkinlikler</h2>
        </div>
        <div class="game-grid" style="padding: 0 0 1rem">

            <!-- Bulmaca -->
            <div class="game-card card-bulmaca">
                <div class="game-card-icon">
                    <i class="fa-solid fa-puzzle-piece"></i>
                </div>
                <h3>Zorbalık Bulmacası</h3>
                <p>Zorba davranışa karşı koyma yöntemlerini öğren!</p>
                <?php if ($highBulmaca > 0): ?>
                    <div class="score-badge">
                        <i class="fa-solid fa-star"></i> En yüksek: <?= $highBulmaca ?> puan
                    </div>
                <?php endif; ?>
                <a href="/genclik-rehberim/games/bulmaca.php" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-play"></i> Oyna
                </a>
            </div>

            <!-- Eşleştirme -->
            <div class="game-card card-eslestirme">
                <div class="game-card-icon">
                    <i class="fa-solid fa-arrows-left-right"></i>
                </div>
                <h3>Doğru mu, Yanlış mı?</h3>
                <p>Davranışları sürükle-bırak ile doğru kutuya yerleştir!</p>
                <?php if ($highEslestirme > 0): ?>
                    <div class="score-badge">
                        <i class="fa-solid fa-star"></i> En yüksek: <?= $highEslestirme ?> puan
                    </div>
                <?php endif; ?>
                <a href="/genclik-rehberim/games/eslestirme.php" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-play"></i> Başla
                </a>
            </div>

            <!-- Kategori -->
            <div class="game-card card-kategori">
                <div class="game-card-icon">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <h3>Zorbalık mı, Değil mi?</h3>
                <p>Davranışları doğru kategoriye yerleştirerek farkındalığını artır!</p>
                <?php if ($highKategori > 0): ?>
                    <div class="score-badge">
                        <i class="fa-solid fa-star"></i> En yüksek: <?= $highKategori ?> puan
                    </div>
                <?php endif; ?>
                <a href="/genclik-rehberim/games/kategori.php" class="btn btn-success btn-sm">
                    <i class="fa-solid fa-play"></i> Sırala
                </a>
            </div>

        </div>
    </div>

    <!-- Oyun Geçmişi ve Liderlik Tablosu (yan yana) -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 2rem;">

        <!-- Oyun Geçmişi -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-clock-rotate-left"></i> Son Oyunlar</h2>
            </div>
            <?php if (empty($history)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-gamepad"></i>
                    <p>Henüz oyun oynamadın. Hadi başla!</p>
                </div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Etkinlik</th>
                            <th>Puan</th>
                            <th>Oran</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $row): ?>
                        <?php
                            $percent = $row['max_score'] > 0
                                ? round(($row['score'] / $row['max_score']) * 100)
                                : 0;
                        ?>
                        <tr>
                            <td>
                                <span class="badge badge-<?= e($row['type']) ?>">
                                    <?= e($row['name']) ?>
                                </span>
                            </td>
                            <td><strong><?= (int)$row['score'] ?></strong>/<?= (int)$row['max_score'] ?></td>
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

        <!-- Liderlik Tablosu -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fa-solid fa-ranking-star"></i> Liderler</h2>
            </div>
            <?php if (empty($leaderboard)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-trophy"></i>
                    <p>Henüz liderlik verisi yok.</p>
                </div>
            <?php else: ?>
            <?php foreach ($leaderboard as $i => $leader): ?>
            <div style="display:flex;align-items:center;gap:1rem;padding:0.8rem 0;border-bottom:1px solid var(--bg-light)">
                <!-- Sıra rozeti -->
                <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:0.85rem;flex-shrink:0;
                    background: <?= $i===0?'#FFBE0B':($i===1?'#a8b0c0':($i===2?'#c47722':'var(--bg-light)')) ?>;
                    color: <?= $i<3?'white':'var(--text-muted)' ?>">
                    <?= $i + 1 ?>
                </div>
                <div style="flex:1;overflow:hidden">
                    <div style="font-weight:700;font-size:0.9rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        <?= e($leader['username']) ?>
                        <?php if ($leader['username'] === $username): ?>
                            <span style="color:var(--primary);font-size:0.75rem"> (Sen)</span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:0.78rem;color:var(--text-muted)"><?= $leader['games_played'] ?> oyun</div>
                </div>
                <div style="font-weight:900;color:var(--primary)"><?= $leader['total_score'] ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

</div>
</main>
<!-- ===== PANEL SONU ===== -->

<?php include 'includes/footer.php'; ?>
