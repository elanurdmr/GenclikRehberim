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
$highBulmaca        = getUserHighScore($userId, getActivityId('bulmaca'));
$highEslestirme     = getUserHighScore($userId, getActivityId('eslestirme'));
$highWordle         = getUserHighScore($userId, getActivityId('wordle'));
$highCengel         = getUserHighScore($userId, getActivityId('cengel'));
$eslestirmeMaxScore = getActivityMaxScore(getActivityId('eslestirme'));
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
            <h1 class="text-display-lg" style="font-size:clamp(1.75rem,4vw,2.5rem);font-weight:800;margin-bottom:0.35rem">
                Merhaba, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!
            </h1>
            <p class="text-body-base" style="color:var(--on-surface-variant);max-width:520px;margin:0;line-height:1.55">
                Etkinlikleri tamamla, puan topla ve haftalık hedefinde ilerle. Tasarım: güvenli ve motive edici öğrenci paneli.
            </p>
        </div>
        <div class="student-xp-pill">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:var(--secondary)">star</span>
            <span><?= (int)$totalScore ?> XP</span>
        </div>
    </header>

    <div class="student-bento">
        <section class="student-card-bento" aria-label="Haftalık hedef">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem">
                <h2 class="text-headline-md" style="font-size:1.15rem;font-weight:800">İlerleme</h2>
                <span class="badge badge-bulmaca"><?= (int)$weeklyPct ?>% tamamlandı</span>
            </div>
            <div class="progress-bar-outer" style="height:12px;margin-bottom:0.5rem">
                <div class="progress-bar-inner" style="width:<?= (int)$weeklyPct ?>%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;color:var(--on-surface-variant)">
                <span><?= (int)$gamesPlayed ?> oyun kaydı</span>
                <span>Hedef: daha fazla etkinlik</span>
            </div>
            <div style="display:flex;gap:0.75rem;margin-top:1rem;flex-wrap:wrap">
                <article style="flex:1;min-width:140px;background:var(--surface);padding:0.85rem;border-radius:12px;border:1px solid var(--outline-variant);display:flex;gap:0.65rem;align-items:center">
                    <div class="student-badge-icon-wrap" style="margin:0;background:var(--secondary-fixed);color:var(--secondary)">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:22px">check_circle</span>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.85rem">Bulmaca</div>
                        <div style="font-size:0.75rem;color:var(--on-surface-variant)">En iyi: <?= (int)$highBulmaca ?></div>
                    </div>
                </article>
                <article style="flex:1;min-width:140px;background:var(--surface);padding:0.85rem;border-radius:12px;border:1px solid var(--outline-variant);display:flex;gap:0.65rem;align-items:center">
                    <div class="student-badge-icon-wrap" style="margin:0;background:var(--primary-fixed);color:var(--primary)">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:22px">play_circle</span>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.85rem">Wordle</div>
                        <div style="font-size:0.75rem;color:var(--on-surface-variant)">En iyi: <?= (int)$highWordle ?></div>
                    </div>
                </article>
            </div>
        </section>

        <section class="student-card-bento" aria-label="Rozetler">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                <h2 class="text-headline-md" style="font-size:1.15rem;font-weight:800">Rozetler</h2>
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
                    <span style="font-size:0.78rem;font-weight:700"><?= htmlspecialchars($badge['name'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="student-card-bento" style="margin-top:1.25rem" aria-label="Oyunlara devam et">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem">
            <h2 class="text-headline-md" style="font-size:1.15rem;font-weight:800">Tekrar Oyna</h2>
            <a href="/genclik-rehberim/index.php" class="font-label-caps text-label-caps" style="color:var(--primary);font-size:0.8rem">Tümü</a>
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
        </div>
    </section>

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
                                    <span style="font-size:0.75rem;font-weight:700"><?= (int)$percent ?>%</span>
                                </div>
                            </td>
                            <td style="font-size:0.82rem;color:var(--on-surface-variant)"><?= date('d.m.Y H:i', strtotime($row['played_at'])) ?></td>
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
                <div style="display:flex;align-items:center;gap:1rem;padding:0.85rem 1rem;border-bottom:1px solid var(--surface-variant)">
                    <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:0.85rem;color:#fff;flex-shrink:0;background:<?= $i===0?'#FFBE0B':($i===1?'#94a3b8':($i===2?'#c47722':'var(--surface-container-high)')) ?>">
                        <?= $i + 1 ?>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:800;font-size:0.9rem">
                            <?= htmlspecialchars($leader['username'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($leader['username'] === $username): ?>
                                <span style="color:var(--primary);font-size:0.72rem">(sen)</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.78rem;color:var(--on-surface-variant)"><?= (int)$leader['games_played'] ?> oyun</div>
                    </div>
                    <div style="font-weight:900;color:var(--primary)"><?= (int)$leader['total_score'] ?></div>
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
