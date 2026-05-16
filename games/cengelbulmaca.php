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
$secretWord = $puzzle['secretWord'] ?? '';

/* Gizli kelime hücrelerini bul */
$secretCells = [];
if ($secretWord !== '') {
    foreach ($tabAcross as $cl) {
        if ($cl['word'] === $secretWord) {
            $len = mb_strlen($cl['word'], 'UTF-8');
            for ($i = 0; $i < $len; $i++) {
                $secretCells[$cl['r'] . '_' . ($cl['c'] + $i)] = true;
            }
            break;
        }
    }
    if ($secretCells === []) {
        foreach ($tabDown as $cl) {
            if ($cl['word'] === $secretWord) {
                $len = mb_strlen($cl['word'], 'UTF-8');
                for ($i = 0; $i < $len; $i++) {
                    $secretCells[($cl['r'] + $i) . '_' . $cl['c']] = true;
                }
                break;
            }
        }
    }
}

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
    'secretWord'   => $secretWord,
    'secretClue'   => $puzzle['secretClue'] ?? '',
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
                    Hücreye tıkla ve yaz. <kbd class="crossword-kbd">Boşluk</kbd> tuşu veya düğme ile yönü değiştir.
                    <?php if ($secretWord !== ''): ?>
                    <strong style="color:var(--tertiary)">🌟 Gizli kelimeyi bul!</strong>
                    <?php endif; ?>
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

        <?php if ($secretWord !== '' && ($puzzle['secretClue'] ?? '') !== ''): ?>
        <!-- Gizli kelime ipucu kartı -->
        <div class="secret-word-modal">
            <span class="material-symbols-outlined" style="color:var(--tertiary);vertical-align:middle;margin-right:0.4rem;font-variation-settings:'FILL' 1">star</span>
            <strong style="color:var(--tertiary)">Gizli Kelime İpucu:</strong>
            <?= htmlspecialchars($puzzle['secretClue'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            <span style="display:inline-block;margin-left:0.5rem;background:rgba(245,158,11,0.15);border:1.5px solid #f59e0b;border-radius:6px;padding:0.15rem 0.5rem;font-size:0.82rem;color:#b45309;font-weight:700">
                <?= mb_strlen($secretWord, 'UTF-8') ?> harf
            </span>
        </div>
        <?php endif; ?>

        <div class="cengel-bento">

            <!-- Izgara -->
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
                            $nm       = (int)($nums[$r][$c] ?? 0);
                            $isSecret = isset($secretCells[$r . '_' . $c]) ? ' secret-cell' : '';
                            ?>
                    <div class="crossword-cell crossword-cell-letter<?= $isSecret ?>" data-r="<?= $r ?>" data-c="<?= $c ?>">
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
                            <li class="cengel-clue-item js-cw-clue<?= $cl['word'] === $secretWord ? ' secret-clue' : '' ?>"
                                data-dir="across" data-num="<?= (int)$cl['n'] ?>">
                                <span class="cengel-clue-no"><?= (int)$cl['n'] ?></span>
                                <?php if ($cl['word'] === $secretWord): ?>
                                    <span class="material-symbols-outlined" style="font-size:14px;color:var(--tertiary);vertical-align:middle;font-variation-settings:'FILL' 1">star</span>
                                <?php endif; ?>
                                <span class="cengel-clue-txt"><?= htmlspecialchars($cl['clue'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="material-symbols-outlined cengel-clue-done js-cw-done-across-<?= (int)$cl['n'] ?>" hidden>check_circle</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="cengel-clue-panel js-cw-panel-down">
                        <ul class="cengel-clue-list">
                            <?php foreach ($tabDown as $cl): ?>
                            <li class="cengel-clue-item js-cw-clue<?= $cl['word'] === $secretWord ? ' secret-clue' : '' ?>"
                                data-dir="down" data-num="<?= (int)$cl['n'] ?>">
                                <span class="cengel-clue-no"><?= (int)$cl['n'] ?></span>
                                <?php if ($cl['word'] === $secretWord): ?>
                                    <span class="material-symbols-outlined" style="font-size:14px;color:var(--tertiary);vertical-align:middle;font-variation-settings:'FILL' 1">star</span>
                                <?php endif; ?>
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

<!-- ===== GİZLİ KELİME BULUNDU MODALİ ===== -->
<div class="result-overlay" id="secretWordOverlay" role="dialog" aria-modal="true" aria-label="Gizli kelime bulundu">
    <div class="result-card">
        <div class="result-emoji">🌟</div>
        <h2>Gizli Kelimeyi Buldun!</h2>
        <p id="secretWordReveal" style="font-size:1.3rem;font-weight:800;color:var(--tertiary);letter-spacing:0.1em"></p>
        <div class="result-score-big js-cw-final-score">0</div>
        <div class="result-score-label">/ <span class="js-cw-max-score">0</span> puan</div>
        <div id="secretSaveStatus" style="margin-bottom:1rem;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <button class="btn btn-primary" id="continueAfterSecretBtn">
                <span class="material-symbols-outlined">arrow_forward</span> Devam Et
            </button>
            <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

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
