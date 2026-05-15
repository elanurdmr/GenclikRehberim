<?php
/**
 * cengelbulmaca.php — Kesişimli kare bulmaca (PDO soru bankası + yerleştirme motoru)
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
    echo '<main class="cengel-page"><p>Bulmaca oluşturulamadı.</p></main>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$payload = [
    'h' => $puzzle['height'],
    'w' => $puzzle['width'],
    'sol' => $puzzle['grid'],
    'nums' => $puzzle['numbers'],
    'across' => $puzzle['across'],
    'down' => $puzzle['down'],
    'acrossList' => $puzzle['acrossList'],
    'downList' => $puzzle['downList'],
    'shuffleKey' => $puzzle['shuffleKey'],
    'seed' => $puzzle['seed'],
    'pointsPerWord' => $puzzle['pointsPerWord'],
];
$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

$h = $puzzle['height'];
$w = $puzzle['width'];
$grid = $puzzle['grid'];
$nums = $puzzle['numbers'];
$tabAcross = $puzzle['acrossList'];
$tabDown = $puzzle['downList'];
?>
<main class="cengel-page">
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <section class="cengel-shell">
        <header class="cengel-hero">
            <div class="cengel-hero-text">
                <div class="cengel-badge">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">grid_on</span>
                    Kesişimli bulmaca
                </div>
                <h1 class="cengel-title text-display-lg">Kare Bulmaca</h1>
                <p class="cengel-lead text-body-base">
                    Kelimeler <strong>kesişir</strong>. Sekmeyle yön değiştir: <kbd class="crossword-kbd">Boşluk</kbd> veya alttaki düğme.
                    Seçili hücre <strong>mavi</strong>, tamamlanan kelime <strong>yeşil</strong>. Tarih: <strong><?= htmlspecialchars($puzzle['seed'], ENT_QUOTES, 'UTF-8') ?></strong>
                </p>
            </div>
            <aside class="cengel-hud" aria-label="Oyun sayaçları">
                <div class="cengel-hud-item">
                    <span class="cengel-hud-label text-label-caps">Süre</span>
                    <span class="cengel-hud-value text-primary js-cw-timer">00:00</span>
                </div>
                <div class="cengel-hud-item">
                    <span class="cengel-hud-label text-label-caps">Kazanılan</span>
                    <span class="cengel-hud-value text-secondary js-cw-earned">0</span>
                </div>
                <div class="cengel-hud-item cengel-hud-item-streak">
                    <span class="cengel-hud-label text-label-caps">Kelime puanı</span>
                    <span class="cengel-hud-value crossword-hud-hint">+<?= (int)$puzzle['pointsPerWord'] ?></span>
                </div>
            </aside>
        </header>

        <div class="cengel-bento">
            <article class="cengel-grid-wrap ambient-shadow" aria-label="Bulmaca ızgarası">
                <div class="cengel-grid-header crossword-dir-bar">
                    <span class="material-symbols-outlined">swap_horiz</span>
                    <span class="js-cw-dir-label">Yön: soldan sağa</span>
                    <button type="button" class="btn btn-outline btn-sm js-cw-toggle-dir">Yönü değiştir</button>
                </div>
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

            <div class="cengel-side">
                <section class="cengel-active-clue ambient-shadow" aria-live="polite">
                    <div class="cengel-active-label text-label-caps">
                        <span class="material-symbols-outlined">help</span>
                        <span class="js-cw-active-dir">İpucu</span>
                        <span class="js-cw-active-num"></span>
                    </div>
                    <p class="cengel-active-text js-cw-active-text">Boşluk ile yön değiştirebilirsin.</p>
                    <p class="crossword-hint-note">Her doğru kelime sunucuda <code>crossword_word_scores</code> tablosuna yazılır (giriş gerekir).</p>
                </section>

                <section class="cengel-clue-card ambient-shadow">
                    <nav class="cengel-clue-tabs" aria-label="İpucu sekmeleri">
                        <button type="button" class="cengel-tab is-active js-cw-tab" data-tab="across">Soldan sağa</button>
                        <button type="button" class="cengel-tab js-cw-tab" data-tab="down">Yukarıdan aşağıya</button>
                    </nav>
                    <div class="cengel-clue-panel is-visible js-cw-panel-across">
                        <ul class="cengel-clue-list">
                            <?php foreach ($tabAcross as $cl): ?>
                                <li class="cengel-clue-item js-cw-clue" data-dir="across" data-num="<?= (int)$cl['n'] ?>">
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
                                <li class="cengel-clue-item js-cw-clue" data-dir="down" data-num="<?= (int)$cl['n'] ?>">
                                    <span class="cengel-clue-no"><?= (int)$cl['n'] ?></span>
                                    <span class="cengel-clue-txt"><?= htmlspecialchars($cl['clue'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="material-symbols-outlined cengel-clue-done js-cw-done-down-<?= (int)$cl['n'] ?>" hidden>check_circle</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>

                <div class="cengel-actions">
                    <button type="button" class="btn btn-outline js-cw-reset">
                        <span class="material-symbols-outlined">restart_alt</span>
                        Temizle
                    </button>
                    <a href="/genclik-rehberim/games/cengelbulmaca.php?tarih=<?= urlencode($dateSeed) ?>" class="btn btn-secondary">
                        <span class="material-symbols-outlined">today</span>
                        Bugünkü tur
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>window.GAME_CONFIG = <?= $json ?>;</script>
<script src="/genclik-rehberim/assets/js/cengelbulmaca.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
