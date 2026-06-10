<?php
/**
 * admin/feedback.php — Geri bildirim yönetim paneli
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

$pageTitle = 'Geri Bildirimler';
requireAdmin();

// Okundu işaretle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $feedbackId = (int)($_POST['feedback_id'] ?? 0);
    if ($feedbackId > 0) {
        markFeedbackRead($feedbackId);
    }
    header('Location: /genclik-rehberim/admin/feedback.php');
    exit;
}

$feedbackList  = getAllFeedback();
$unreadCount   = getUnreadFeedbackCount();

// Kategori filtresi
$filterCat = $_GET['cat'] ?? '';
if ($filterCat && in_array($filterCat, ['konu','platform','oyun','diger'], true)) {
    $feedbackList = array_filter($feedbackList, fn($f) => $f['category'] === $filterCat);
}

$categoryLabels = [
    'konu'     => 'Konu Hakkında',
    'platform' => 'Platform / Site',
    'oyun'     => 'Oyunlar',
    'diger'    => 'Diğer',
];
$categoryColors = [
    'konu'     => ['bg' => 'var(--primary-fixed)',   'color' => 'var(--primary)'],
    'platform' => ['bg' => 'var(--secondary-fixed)', 'color' => 'var(--secondary)'],
    'oyun'     => ['bg' => 'var(--tertiary-fixed)',  'color' => 'var(--tertiary)'],
    'diger'    => ['bg' => 'var(--surface-container-high)', 'color' => 'var(--on-surface-variant)'],
];
?>
<?php include '../includes/header.php'; ?>

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
                    <a href="/genclik-rehberim/admin/scores.php">
                        <span class="material-symbols-outlined">bar_chart</span> Skorlar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/feedback.php" class="active">
                        <span class="material-symbols-outlined">feedback</span> Geri Bildirimler
                        <?php if ($unreadCount > 0): ?>
                        <span style="margin-left:auto;background:var(--error);color:#fff;border-radius:9999px;padding:0 .45rem;font-size:.72rem;font-weight:700;line-height:1.6"><?= $unreadCount ?></span>
                        <?php endif; ?>
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
            <div>
                <h1 class="admin-page-title admin-content__header-title">
                    <span class="material-symbols-outlined icon-fill">feedback</span>
                    Geri Bildirimler
                </h1>
                <p class="text-body-base admin-content__subtitle">
                    Öğrencilerden gelen geri bildirimler. Toplam <strong><?= count(getAllFeedback()) ?></strong> bildirim,
                    <strong><?= $unreadCount ?></strong> okunmamış.
                </p>
            </div>
            <!-- Kategori filtresi -->
            <div class="admin-content__actions">
                <a href="?" class="btn btn-sm <?= !$filterCat ? 'btn-primary' : 'btn-surface' ?>">Tümü</a>
                <?php foreach ($categoryLabels as $val => $label): ?>
                <a href="?cat=<?= $val ?>" class="btn btn-sm <?= $filterCat === $val ? 'btn-primary' : 'btn-surface' ?>">
                    <?= e($label) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="admin-stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:1.5rem">
            <?php
            $all = getAllFeedback();
            $counts = ['konu'=>0,'platform'=>0,'oyun'=>0,'diger'=>0];
            foreach ($all as $f) { $counts[$f['category']] = ($counts[$f['category']] ?? 0) + 1; }
            $statItems = [
                ['label'=>'Konu Hakkında','count'=>$counts['konu'],'icon'=>'psychology','cls'=>'purple'],
                ['label'=>'Platform/Site','count'=>$counts['platform'],'icon'=>'computer','cls'=>'green'],
                ['label'=>'Oyunlar','count'=>$counts['oyun'],'icon'=>'sports_esports','cls'=>'pink'],
                ['label'=>'Diğer','count'=>$counts['diger'],'icon'=>'help','cls'=>'yellow'],
            ];
            foreach ($statItems as $si):
            ?>
            <div class="stat-card">
                <div class="admin-stat-card__header">
                    <div class="stat-icon <?= $si['cls'] ?>">
                        <span class="material-symbols-outlined"><?= $si['icon'] ?></span>
                    </div>
                </div>
                <div class="stat-info">
                    <p><?= e($si['label']) ?></p>
                    <h3><?= $si['count'] ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Geri bildirim listesi -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined icon-fill">list_alt</span>
                    <?= $filterCat ? e($categoryLabels[$filterCat]) : 'Tüm Bildirimler' ?>
                </h2>
                <span style="font-size:.85rem;color:var(--on-surface-variant)"><?= count($feedbackList) ?> kayıt</span>
            </div>

            <?php if (empty($feedbackList)): ?>
            <div class="empty-state">
                <span class="material-symbols-outlined">inbox</span>
                <p>Bu kategoride henüz geri bildirim yok.</p>
            </div>
            <?php else: ?>
            <div style="padding:1rem;display:flex;flex-direction:column;gap:.75rem">
                <?php foreach ($feedbackList as $fb):
                    $cat   = $fb['category'];
                    $clr   = $categoryColors[$cat] ?? $categoryColors['diger'];
                    $isNew = !$fb['is_read'];
                ?>
                <div style="background:<?= $isNew ? 'var(--primary-fixed)' : 'var(--surface-container-low)' ?>;border-radius:14px;padding:1rem 1.25rem;border:1px solid <?= $isNew ? 'rgba(0,93,167,0.25)' : 'var(--outline-variant)' ?>">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;flex-wrap:wrap">
                        <!-- Kullanıcı -->
                        <span style="font-weight:800;font-size:.9rem"><?= e($fb['username']) ?></span>
                        <!-- Kategori badge -->
                        <span style="display:inline-flex;align-items:center;padding:.2rem .65rem;border-radius:9999px;font-size:.75rem;font-weight:700;background:<?= e($clr['bg']) ?>;color:<?= e($clr['color']) ?>">
                            <?= e($categoryLabels[$cat] ?? $cat) ?>
                        </span>
                        <?php if ($isNew): ?>
                        <span class="badge" style="background:var(--error);color:#fff">Yeni</span>
                        <?php endif; ?>
                        <span style="margin-left:auto;font-size:.78rem;color:var(--on-surface-variant)">
                            <?= date('d.m.Y H:i', strtotime($fb['created_at'])) ?>
                        </span>
                    </div>
                    <!-- Mesaj -->
                    <p style="font-size:.9rem;color:var(--on-surface);margin:0 0 .75rem;line-height:1.7;word-break:break-word">
                        <?= nl2br(e($fb['message'])) ?>
                    </p>
                    <!-- Okundu işareti -->
                    <?php if ($isNew): ?>
                    <form method="POST" style="margin:0">
                        <input type="hidden" name="feedback_id" value="<?= (int)$fb['id'] ?>">
                        <button type="submit" name="mark_read" class="btn btn-outline btn-sm">
                            <span class="material-symbols-outlined" style="font-size:16px">done</span>
                            Okundu İşaretle
                        </button>
                    </form>
                    <?php else: ?>
                    <span style="font-size:.78rem;color:var(--on-surface-variant);display:flex;align-items:center;gap:.3rem">
                        <span class="material-symbols-outlined" style="font-size:14px">done_all</span> Okundu
                    </span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </section>
</div>
</main>

<?php include '../includes/footer.php'; ?>
