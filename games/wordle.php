<?php
/**
 * wordle.php — 5 harfli kelime tahmini (Türkçe klavye)
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

$pageTitle = 'Wordle';
require_once '../includes/header.php';
requireLogin();

$wordleDateSeed = gmdate('Y-m-d'); // Günlük kelime (UTC; tutarlı seed)
?>
<!-- ===== WORDLE ===== -->
<main class="game-page wordle-page" id="wordlePage">

    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">spellcheck</span>
                Zorbalık Farkındalığı · Wordle
            </div>
            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>5 Harfli Kelime</h1>
                    <p>Günde bir kelime, <strong>8 deneme</strong>. Doğru harf yerinde yeşil, yanlış yerde sarı, yoksa gri.</p>
                </div>
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">Deneme</span>
                        <span class="game-stat-value text-primary" id="wordleAttempt">0/6</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Puan</span>
                        <span class="game-stat-value text-secondary" id="wordleScoreHint">0–100</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Max</span>
                        <span class="game-stat-value text-tertiary">100</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="wordle-panel">
            <p id="wordleMessage" class="wordle-message" role="status" style="min-height:1.5rem;text-align:center;font-weight:600;color:var(--on-surface-variant)"></p>
            <div class="wordle-board" id="wordleBoard" aria-label="Tahmin tahtası"></div>
            <div class="wordle-keyboard" id="wordleKeyboard" aria-label="Harf klavyesi"></div>
        </div>
    </div>
</main>

<div class="result-overlay" id="wordleResultOverlay" role="dialog" aria-modal="true" aria-label="Wordle sonucu">
    <div class="result-card">
        <div class="result-emoji">🏆</div>
        <h2 id="wordleResultTitle">Oyun Bitti!</h2>
        <p id="wordleResultMsg">Sonuç</p>
        <div class="result-score-big" id="wordleFinalScore">0</div>
        <div class="result-score-label">/ 100 puan</div>
        <div class="result-score-label" id="wordleReveal" style="font-size:0.95rem;font-weight:700;color:var(--primary);margin-top:0.25rem"></div>
        <div id="wordleSaveStatus" style="margin:0.75rem 0;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <button type="button" class="btn btn-primary" id="wordleRestartBtn">
                <span class="material-symbols-outlined">refresh</span> Yeni Kelime
            </button>
            <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

<script>window.GAME_CONFIG = {
    dateSeed: <?php echo json_encode($wordleDateSeed, JSON_UNESCAPED_UNICODE); ?>,
    activityId: <?= getActivityId('wordle') ?>
};</script>
<script src="/genclik-rehberim/assets/js/wordle.js"></script>

<?php include '../includes/footer.php'; ?>
