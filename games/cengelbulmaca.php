<?php
/**
 * cengelbulmaca.php — Kesişimli Kare Bulmaca
 * 12×12 ızgara · max 8 kelime · max 8 harf · gizli kelime mekaniği
 */
declare(strict_types=1);

$pageTitle = 'Çengel Bulmaca';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/crossword_build.php';

$dateSeed = preg_replace('/[^0-9\-]/', '', (string)($_GET['tarih'] ?? ''));
if ($dateSeed === '') {
    $dateSeed = gmdate('Y-m-d');
}

$puzzle = crossword_build_puzzle($dateSeed);
if ($puzzle === null) {
    http_response_code(500);
    echo '<main class="cengel-page"><div class="cengel-shell"><p style="padding:3rem;text-align:center;color:var(--on-surface-variant)">Bulmaca oluşturulamadı. Lütfen sayfayı yenileyin.</p></div></main>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$h          = $puzzle['height'];
$w          = $puzzle['width'];
$grid       = $puzzle['grid'];
$nums       = $puzzle['numbers'];
$tabAcross  = $puzzle['acrossList'];
$tabDown    = $puzzle['downList'];
$payload = [
    'h'            => $h,
    'w'            => $w,
    'sol'          => $grid,
    'nums'         => $nums,
    'across'       => $puzzle['across'],
    'down'         => $puzzle['down'],
    'acrossList'   => $tabAcross,
    'downList'     => $tabDown,
    'shuffleKey'   => $puzzle['shuffleKey'],
    'seed'         => $puzzle['seed'],
    'pointsPerWord'=> $puzzle['pointsPerWord'],
    'maxScore'     => $puzzle['maxScore'],
];
$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
?>
<main class="cengel-page">
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <section class="cengel-shell">

        <!-- Başlık / HUD -->
        <header class="cengel-hero">
            <div class="cengel-hero-text">
                <div class="cengel-badge">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">grid_on</span>
                    Çengel Bulmaca · <?= htmlspecialchars($puzzle['seed'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <h1 class="cengel-title text-display-lg">Kare Bulmaca</h1>
                <p class="cengel-lead text-body-base">
                    Hücreye tıkla, yön otomatik seçilir. Aynı hücreye tekrar tıkla yönü değiştir.
                </p>
            </div>
            <aside class="cengel-hud" aria-label="Oyun sayaçları">
                <div class="cengel-hud-item">
                    <span class="cengel-hud-label text-label-caps">Süre</span>
                    <span class="cengel-hud-value text-primary js-cw-timer">00:00</span>
                </div>
                <div class="cengel-hud-item">
                    <span class="cengel-hud-label text-label-caps">Puan</span>
                    <span class="cengel-hud-value text-secondary js-cw-earned">0</span>
                </div>
                <div class="cengel-hud-item">
                    <span class="cengel-hud-label text-label-caps">Kelime puan</span>
                    <span class="cengel-hud-value crossword-hud-hint">+<?= (int)$puzzle['pointsPerWord'] ?></span>
                </div>
            </aside>
        </header>

        <div class="cengel-bento">

            <!-- Izgara -->
            <article class="cengel-grid-wrap ambient-shadow" aria-label="Bulmaca ızgarası">
                <div
                    class="crossword-board js-cw-board"
                    style="--crossword-cols: <?= (int)$w ?>; --crossword-rows: <?= (int)$h ?>;"
                >
                    <?php
                    for ($r = 0; $r < $h; $r++) {
                        for ($c = 0; $c < $w; $c++) {
                            $ch = $grid[$r][$c];
                            if ($ch === '#') {
                                echo '<div class="crossword-cell crossword-cell-block" aria-hidden="true"></div>';
                                continue;
                            }
                            $nm = (int)($nums[$r][$c] ?? 0);
                            ?>
                    <div class="crossword-cell crossword-cell-letter" data-r="<?= $r ?>" data-c="<?= $c ?>">
                        <?php if ($nm > 0): ?>
                            <span class="cengel-cell-num"><?= $nm ?></span>
                        <?php endif; ?>
                        <label class="visually-hidden" for="cw-r<?= $r ?>-c<?= $c ?>">Satır <?= $r + 1 ?> sütun <?= $c + 1 ?></label>
                        <input
                            type="text"
                            class="crossword-cell-input"
                            maxlength="1"
                            autocomplete="off"
                            id="cw-r<?= $r ?>-c<?= $c ?>"
                            data-r="<?= $r ?>"
                            data-c="<?= $c ?>"
                            aria-label="Satır <?= $r + 1 ?> sütun <?= $c + 1 ?>"
                        >
                    </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </article>

            <!-- Yan panel -->
            <div class="cengel-side">

                <!-- Aktif ipucu -->
                <section class="cengel-active-clue ambient-shadow" aria-live="polite">
                    <div class="cengel-active-label text-label-caps">
                        <span class="material-symbols-outlined">help</span>
                        <span class="js-cw-active-dir">İpucu</span>
                        <span class="js-cw-active-num"></span>
                    </div>
                    <p class="cengel-active-text js-cw-active-text">Bir hücreye tıkla.</p>
                    <button type="button" class="btn btn-outline btn-sm js-cw-hint js-cw-hint-btn" style="display:none;margin-top:0.5rem;font-size:0.82rem;">
                        <span class="material-symbols-outlined" style="font-size:16px">lightbulb</span>
                        Harf Al <span class="js-cw-hint-cost" style="opacity:0.75;font-size:0.78rem">(−5 puan)</span>
                    </button>
                </section>

                <!-- İpucu listesi sekmeleri -->
                <section class="cengel-clue-card ambient-shadow">
                    <nav class="cengel-clue-tabs" aria-label="İpucu sekmeleri">
                        <button type="button" class="cengel-tab is-active js-cw-tab" data-tab="across">Soldan sağa</button>
                        <button type="button" class="cengel-tab js-cw-tab" data-tab="down">Yukarıdan aşağıya</button>
                    </nav>
                    <div class="cengel-clue-panel is-visible js-cw-panel-across">
                        <ul class="cengel-clue-list">
                            <?php foreach ($tabAcross as $cl): ?>
                            <li class="cengel-clue-item js-cw-clue"
                                data-dir="across" data-num="<?= (int)$cl['n'] ?>">
                                <span class="cengel-clue-no"><?= (int)$cl['n'] ?></span>
                                <span class="cengel-clue-txt"><?= htmlspecialchars($cl['clue'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="material-symbols-outlined cengel-clue-done js-cw-done-across-<?= (int)$cl['n'] ?>" hidden>check_circle</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="cengel-clue-panel js-cw-panel-down">
                        <ul class="cengel-clue-list">
                            <?php foreach ($tabDown as $cl): ?>
                            <li class="cengel-clue-item js-cw-clue"
                                data-dir="down" data-num="<?= (int)$cl['n'] ?>">
                                <span class="cengel-clue-no"><?= (int)$cl['n'] ?></span>
                                <span class="cengel-clue-txt"><?= htmlspecialchars($cl['clue'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="material-symbols-outlined cengel-clue-done js-cw-done-down-<?= (int)$cl['n'] ?>" hidden>check_circle</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>

                <!-- Eylemler -->
                <div class="cengel-actions">
                    <button type="button" class="btn btn-outline js-cw-reset">
                        <span class="material-symbols-outlined">restart_alt</span> Temizle
                    </button>
                    <button type="button" class="btn btn-secondary js-cw-finish">
                        <span class="material-symbols-outlined">done_all</span> Oyunu Bitir
                    </button>
                </div>

            </div>
        </div>
    </section>
</main>

<!-- ===== OYUN BİTİŞ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay" role="dialog" aria-modal="true" aria-label="Oyun sonucu">
    <div class="result-card">
        <div class="result-emoji" id="resultEmoji">🎯</div>
        <h2 id="resultTitle">Bulmaca Tamamlandı!</h2>
        <p id="resultMsg">İşte puanın:</p>
        <div class="result-score-big js-cw-final-score">0</div>
        <div class="result-score-label">/ <span class="js-cw-max-score">0</span> puan</div>
        <div id="finalSaveStatus" style="margin-bottom:1rem;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <a href="/genclik-rehberim/games/cengelbulmaca.php" class="btn btn-primary">
                <span class="material-symbols-outlined">refresh</span> Yeni Bulmaca
            </a>
            <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

<script>window.GAME_CONFIG = <?= $json ?>;</script>
<script src="/genclik-rehberim/assets/js/cengelbulmaca.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
