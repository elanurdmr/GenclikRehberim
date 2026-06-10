<?php
/**
 * ogrencipanel.php — Öğrenci paneli (tasarım: docs/tasarımlar/ogrencipanel/code1)
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Öğrenci Paneli';

requireLogin();

if (isAdmin()) {
    header('Location: /genclik-rehberim/admin/index.php');
    exit;
}

$userId         = (int)$_SESSION['user_id'];
$username       = $_SESSION['username'];
$totalScore     = getUserTotalScore($userId);
$history        = getUserHistory($userId, 15);
$leaderboard    = getLeaderboard(5);
$highBulmaca           = getUserHighScore($userId, getActivityId('bulmaca'));
$highEslestirme        = getUserHighScore($userId, getActivityId('eslestirme'));
$highWordle            = getUserHighScore($userId, getActivityId('wordle'));
$highCengel            = getUserHighScore($userId, getActivityId('cengel'));
$highBenimHikayem      = getUserHighScore($userId, getActivityId('benimhikayem'));
$highFarkindalikZinciri = getUserHighScore($userId, getActivityId('farkindalikzinciri'));
$eslestirmeMaxScore    = getActivityMaxScore(getActivityId('eslestirme'));
$gamesPlayed    = count($history);

$weeklyPct = min(100, $totalScore > 0 ? (int)round(($totalScore % 400) / 4) : 0);
if ($totalScore >= 400) {
    $weeklyPct = 100;
} elseif ($gamesPlayed > 0 && $weeklyPct < 15) {
    $weeklyPct = min(100, $gamesPlayed * 15);
}

$badges = [
    ['icon' => 'local_fire_department', 'bg' => 'var(--tertiary-fixed)',          'color' => 'var(--tertiary)',           'name' => 'Aktif Öğrenci',  'desc' => 'İlk oyununu tamamla',            'unlocked' => $gamesPlayed >= 1],
    ['icon' => 'waving_hand',           'bg' => 'var(--primary-fixed)',            'color' => 'var(--primary)',            'name' => 'Destekçi',       'desc' => '50 puana ulaş',                  'unlocked' => $totalScore >= 50],
    ['icon' => 'psychology',            'bg' => 'var(--secondary-fixed)',           'color' => 'var(--secondary)',          'name' => 'Farkındalık',    'desc' => '2 farklı oyun oyna',             'unlocked' => $gamesPlayed >= 2],
    ['icon' => 'emoji_events',          'bg' => 'rgba(255,190,11,0.15)',            'color' => '#9a6700',                  'name' => 'Şampiyon',       'desc' => '200 puana ulaş',                 'unlocked' => $totalScore >= 200],
    ['icon' => 'favorite',              'bg' => 'rgba(186,26,26,0.12)',             'color' => 'var(--error)',              'name' => 'Kalp Dolu',      'desc' => '3 farklı oyun oyna',             'unlocked' => $gamesPlayed >= 3],
    ['icon' => 'shield',                'bg' => 'rgba(58,106,0,0.12)',              'color' => 'var(--secondary)',          'name' => 'Koruyucu',       'desc' => '100 puana ulaş',                 'unlocked' => $totalScore >= 100],
    ['icon' => 'group',                 'bg' => 'var(--surface-container-high)',    'color' => 'var(--on-surface-variant)', 'name' => 'Takım Oyuncusu', 'desc' => '4 veya daha fazla oyun oyna',    'unlocked' => $gamesPlayed >= 4],
    ['icon' => 'star',                  'bg' => 'rgba(212,164,0,0.15)',             'color' => '#9a6700',                  'name' => 'Yıldız',         'desc' => '300 puana ulaş',                 'unlocked' => $totalScore >= 300],
];

// Yeni kazanılan rozetleri DB'ye kaydet ve popup için JS'e aktar
$earnedBadges = getEarnedBadges($userId);
$newlyEarned  = [];
foreach ($badges as $badge) {
    if ($badge['unlocked'] && !in_array($badge['name'], $earnedBadges, true)) {
        if (awardBadge($userId, $badge['name'])) {
            $newlyEarned[] = ['name' => $badge['name'], 'desc' => $badge['desc'], 'icon' => $badge['icon']];
        }
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="student-dashboard">
    <header class="student-dash-header">
        <div>
            <h1 class="student-page-title">
                Merhaba, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!
            </h1>
            <p class="student-page-subtitle">
                Etkinlikleri tamamla, puan topla ve haftalık hedefinde ilerle.
            </p>
        </div>
        <div class="student-xp-pill">
            <span class="material-symbols-outlined">star</span>
            <span><?= (int)$totalScore ?> XP</span>
        </div>
    </header>

    <div class="student-bento">
        <section class="student-card-bento" aria-label="Haftalık hedef">
            <div class="panel-card-header">
                <h2 class="panel-section-title">İlerleme</h2>
                <span class="badge badge-bulmaca"><?= (int)$weeklyPct ?>% tamamlandı</span>
            </div>
            <div class="progress-bar-outer" style="height:12px;margin-bottom:0.5rem">
                <div class="progress-bar-inner" style="width:<?= (int)$weeklyPct ?>%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;color:var(--on-surface-variant)">
                <span><?= (int)$gamesPlayed ?> oyun kaydı</span>
                <span>Hedef: daha fazla etkinlik</span>
            </div>
            <div class="panel-stat-row" style="display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;margin-top:1rem">
                <article class="panel-stat-item">
                    <div class="student-badge-icon-wrap" style="margin:0;background:var(--primary-fixed);color:var(--primary);flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">extension</span>
                    </div>
                    <div>
                        <div class="panel-stat-label">Bulmaca</div>
                        <div class="panel-stat-sub">En iyi: <?= (int)$highBulmaca ?></div>
                    </div>
                </article>
                <article class="panel-stat-item">
                    <div class="student-badge-icon-wrap" style="margin:0;background:#e3f0ff;color:#075fab;flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">spellcheck</span>
                    </div>
                    <div>
                        <div class="panel-stat-label">Wordle</div>
                        <div class="panel-stat-sub">En iyi: <?= (int)$highWordle ?></div>
                    </div>
                </article>
                <article class="panel-stat-item">
                    <div class="student-badge-icon-wrap" style="margin:0;background:var(--tertiary-fixed);color:var(--tertiary);flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">grid_on</span>
                    </div>
                    <div>
                        <div class="panel-stat-label">Çengel Bulmaca</div>
                        <div class="panel-stat-sub">En iyi: <?= (int)$highCengel ?></div>
                    </div>
                </article>
                <article class="panel-stat-item">
                    <div class="student-badge-icon-wrap" style="margin:0;background:var(--secondary-fixed);color:var(--secondary);flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">join_inner</span>
                    </div>
                    <div>
                        <div class="panel-stat-label">Eşleştirme</div>
                        <div class="panel-stat-sub">En iyi: <?= (int)$highEslestirme ?></div>
                    </div>
                </article>
                <article class="panel-stat-item">
                    <div class="student-badge-icon-wrap" style="margin:0;background:#fff3e0;color:#e65100;flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">auto_stories</span>
                    </div>
                    <div>
                        <div class="panel-stat-label">Benim Hikayem</div>
                        <div class="panel-stat-sub">En iyi: <?= (int)$highBenimHikayem ?></div>
                    </div>
                </article>
                <article class="panel-stat-item">
                    <div class="student-badge-icon-wrap" style="margin:0;background:#e0f2f1;color:#00695c;flex-shrink:0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:20px">link</span>
                    </div>
                    <div>
                        <div class="panel-stat-label">Fark. Zinciri</div>
                        <div class="panel-stat-sub">En iyi: <?= (int)$highFarkindalikZinciri ?></div>
                    </div>
                </article>
            </div>
        </section>

        <section class="student-card-bento" aria-label="Rozetler">
            <div class="panel-card-header">
                <h2 class="panel-section-title">Rozetler</h2>
                <a href="/genclik-rehberim/index.php#oyunlar" class="btn btn-outline btn-sm" style="padding:0.35rem 0.65rem">
                    <span class="material-symbols-outlined" style="font-size:18px">arrow_forward</span>
                </a>
            </div>
            <div class="student-badges-grid" style="grid-template-columns:repeat(4,1fr)">
                <?php foreach ($badges as $badge): ?>
                <div class="student-badge-item<?= $badge['unlocked'] ? '' : ' locked' ?>">
                    <div class="student-badge-icon-wrap"
                         style="background:<?= htmlspecialchars($badge['bg'], ENT_QUOTES, 'UTF-8') ?>;color:<?= htmlspecialchars($badge['color'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="material-symbols-outlined"<?= $badge['unlocked'] ? ' style="font-variation-settings:\'FILL\' 1"' : '' ?>>
                            <?= $badge['unlocked'] ? htmlspecialchars($badge['icon'], ENT_QUOTES, 'UTF-8') : 'lock' ?>
                        </span>
                    </div>
                    <span class="badge-name"><?= htmlspecialchars($badge['name'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="student-card-bento" style="margin-top:1.25rem" aria-label="Oyunlara devam et">
        <div class="panel-card-header">
            <h2 class="panel-section-title">Tekrar Oyna</h2>
            <a href="/genclik-rehberim/index.php" class="panel-view-all-link">Tümü</a>
        </div>
        <div class="student-jump-grid">
            <a class="student-jump-card" href="/genclik-rehberim/games/bulmaca.php">
                <div class="student-jump-visual" style="background:linear-gradient(135deg,var(--primary-container),var(--primary))">
                    <span class="material-symbols-outlined">extension</span>
                </div>
                <div class="student-jump-body">
                    <h3>Zorbalık Bulmacası</h3>
                    <div class="student-jump-bar"><div style="width:<?= min(100, (int)$highBulmaca) ?>%;background:var(--primary)"></div></div>
                </div>
            </a>
            <a class="student-jump-card" href="/genclik-rehberim/games/cengelbulmaca.php">
                <div class="student-jump-visual" style="background:linear-gradient(135deg,var(--secondary-container),var(--secondary))">
                    <span class="material-symbols-outlined">grid_on</span>
                </div>
                <div class="student-jump-body">
                    <h3>Çengel Bulmaca</h3>
                    <div class="student-jump-bar"><div style="width:<?= min(100, (int)$highCengel) ?>%;background:var(--secondary)"></div></div>
                </div>
            </a>
            <a class="student-jump-card" href="/genclik-rehberim/games/wordle.php">
                <div class="student-jump-visual" style="background:linear-gradient(135deg,#5d9cec,#075fab)">
                    <span class="material-symbols-outlined">spellcheck</span>
                </div>
                <div class="student-jump-body">
                    <h3>Wordle</h3>
                    <div class="student-jump-bar"><div style="width:<?= min(100, (int)$highWordle) ?>%;background:#075fab"></div></div>
                </div>
            </a>
            <a class="student-jump-card" href="/genclik-rehberim/games/eslestirme.php">
                <div class="student-jump-visual" style="background:linear-gradient(135deg,var(--secondary-fixed),var(--secondary))">
                    <span class="material-symbols-outlined">join_inner</span>
                </div>
                <div class="student-jump-body">
                    <h3>Eşleştirme</h3>
                    <div class="student-jump-bar"><div style="width:<?= min(100, (int)round($highEslestirme * 100 / max(1, $eslestirmeMaxScore))) ?>%;background:var(--secondary)"></div></div>
                </div>
            </a>
            <a class="student-jump-card" href="/genclik-rehberim/games/benimhikayem.php">
                <div class="student-jump-visual" style="background:linear-gradient(135deg,#f9a825,#e65100)">
                    <span class="material-symbols-outlined">auto_stories</span>
                </div>
                <div class="student-jump-body">
                    <h3>Benim Hikayem</h3>
                    <div class="student-jump-bar"><div style="width:<?= min(100, (int)$highBenimHikayem) ?>%;background:#e65100"></div></div>
                </div>
            </a>
            <a class="student-jump-card" href="/genclik-rehberim/games/farkindalikzinciri.php">
                <div class="student-jump-visual" style="background:linear-gradient(135deg,#26c6da,#00838f)">
                    <span class="material-symbols-outlined">link</span>
                </div>
                <div class="student-jump-body">
                    <h3>Farkındalık Zinciri</h3>
                    <div class="student-jump-bar"><div style="width:<?= min(100, (int)$highFarkindalikZinciri) ?>%;background:#00838f"></div></div>
                </div>
            </a>
        </div>
    </section>

    <!-- Hızlı erişim: Profil + Geri Bildirim -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.25rem">
        <a href="/genclik-rehberim/profil.php" class="student-card-bento" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:1rem;padding:1.1rem 1.4rem">
            <div class="student-badge-icon-wrap" style="background:var(--primary-fixed);color:var(--primary);flex-shrink:0">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">manage_accounts</span>
            </div>
            <div>
                <div style="font-weight:800;font-size:.95rem">Profilim</div>
                <div style="font-size:.8rem;color:var(--on-surface-variant)">Bilgilerini güncelle</div>
            </div>
            <span class="material-symbols-outlined" style="margin-left:auto;color:var(--outline)">chevron_right</span>
        </a>
        <a href="/genclik-rehberim/geri-bildirim.php" class="student-card-bento" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:1rem;padding:1.1rem 1.4rem">
            <div class="student-badge-icon-wrap" style="background:var(--secondary-fixed);color:var(--secondary);flex-shrink:0">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">feedback</span>
            </div>
            <div>
                <div style="font-weight:800;font-size:.95rem">Geri Bildirim</div>
                <div style="font-size:.8rem;color:var(--on-surface-variant)">Düşüncelerini paylaş</div>
            </div>
            <span class="material-symbols-outlined" style="margin-left:auto;color:var(--outline)">chevron_right</span>
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;margin-top:1.25rem">
        <?php if ($gamesPlayed > 0): ?>
        <section class="card" aria-label="Son oyunlar">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">history</span>
                    Son Oyunlar
                </h2>
            </div>
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
                        <?php $percent = $row['max_score'] > 0 ? round(($row['score'] / $row['max_score']) * 100) : 0; ?>
                        <tr>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td><strong><?= (int)$row['score'] ?></strong>/<?= (int)$row['max_score'] ?></td>
                            <td>
                                <div class="progress-cell">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width:<?= (int)$percent ?>%"></div>
                                    </div>
                                    <span class="progress-pct"><?= (int)$percent ?>%</span>
                                </div>
                            </td>
                            <td class="cell-date"><?= date('d.m.Y H:i', strtotime($row['played_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <section class="card" aria-label="Liderlik">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">leaderboard</span>
                    Liderler
                </h2>
            </div>
            <?php if (empty($leaderboard)): ?>
                <div class="empty-state"><span class="material-symbols-outlined">emoji_events</span><p>Henüz veri yok.</p></div>
            <?php else: ?>
                <?php foreach ($leaderboard as $i => $leader): ?>
                <?php $rankBg = $i===0 ? '#FFBE0B' : ($i===1 ? '#94a3b8' : ($i===2 ? '#c47722' : 'var(--surface-container-high)')); ?>
                <div class="panel-leader-row">
                    <div class="panel-leader-rank" style="background:<?= $rankBg ?>">
                        <?= $i + 1 ?>
                    </div>
                    <div class="panel-leader-info">
                        <div class="panel-leader-name">
                            <?= htmlspecialchars($leader['username'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($leader['username'] === $username): ?>
                                <span class="panel-leader-you">(sen)</span>
                            <?php endif; ?>
                        </div>
                        <div class="panel-leader-meta"><?= (int)$leader['games_played'] ?> oyun</div>
                    </div>
                    <div class="panel-leader-score"><?= (int)$leader['total_score'] ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php if (!empty($newlyEarned)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const newBadges = <?= json_encode($newlyEarned, JSON_UNESCAPED_UNICODE) ?>;
    async function showBadgePopups() {
        for (const badge of newBadges) {
            await Swal.fire({
                title: '🏅 Yeni Rozet Kazandın!',
                html: `<strong style="font-size:1.1rem">${badge.name}</strong><br><span style="color:#666">${badge.desc}</span>`,
                icon: 'success',
                confirmButtonText: 'Harika!',
                confirmButtonColor: 'var(--primary, #6750A4)',
                timer: 5000,
                timerProgressBar: true,
                showClass: { popup: 'swal2-show' },
                hideClass: { popup: 'swal2-hide' }
            });
        }
    }
    document.addEventListener('DOMContentLoaded', showBadgePopups);
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
