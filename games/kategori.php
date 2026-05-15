<?php
/**
 * kategori.php — Zorbalık / Zorbalık Değil Kategori Oyunu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * PDF kaynağı: 3. Etkinlik — Kelimeleri doğru kutuya yerleştirme
 */

$pageTitle = 'Kategori';
require_once '../includes/header.php';
?>

<!-- ===== KATEGORİ OYUNU ===== -->
<main class="game-page" id="kategoriPage">

    <!-- Dekoratif arka plan lekeleri -->
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Oyun Başlık Alanı -->
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">local_fire_department</span>
                Zorbalık Farkındalığı · Etkinlik 3
            </div>

            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>Zorbalık mı, Değil mi?</h1>
                    <p>Bir kelimeye tıkla, sonra doğru kutuya tıkla! Her doğru yerleştirme <strong>10 puan</strong>.</p>
                </div>

                <!-- İstatistik kutusu -->
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">Yerleştirilen</span>
                        <span class="game-stat-value text-primary"><span id="placedCount">0</span>/17</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Puan</span>
                        <span class="game-stat-value text-secondary" id="scoreDisplay">0</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Max</span>
                        <span class="game-stat-value text-tertiary">170</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- İlerleme çubuğu -->
        <div class="game-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <span class="progress-info">Yerleştirilen: <span id="placedCountBar">0</span>/17</span>
            <div class="progress-bar-outer">
                <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
            </div>
            <span class="progress-score">Puan: <span id="scoreDisplayBar">0</span>/170</span>
        </div>

        <!-- Seçilecek kelimeler -->
        <div class="category-words" aria-label="Kelime seçim alanı">
            <h3>
                <span class="material-symbols-outlined">touch_app</span>
                Bir Kelimeye Tıkla, Sonra Kutuya Yerleştir
            </h3>
            <div class="word-chips" id="wordChips">
                <!-- Kelimeler JavaScript tarafından dinamik oluşturulur -->
            </div>
        </div>

        <!-- Seçili kelime göstergesi -->
        <div id="selectedHint" class="selected-hint" aria-live="polite">
            <!-- Seçilen kelime burada gösterilir -->
        </div>

        <!-- Kategori kutuları -->
        <div class="category-zones">

            <!-- Zorbalık Kutusu -->
            <div class="category-zone zone-zorbalik" id="zoneZorbalik"
                 onclick="placeSelected('zorbalik')"
                 role="button" tabindex="0" aria-label="Zorbalık kategorisi">
                <div class="category-zone-header">
                    <h3>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">back_hand</span>
                        ZORBALIK
                    </h3>
                </div>
                <div class="zone-chips" id="chipsZorbalik">
                    <!-- Yerleştirilen kelimeler buraya eklenir -->
                </div>
            </div>

            <!-- Zorbalık Değil Kutusu -->
            <div class="category-zone zone-zorbalik-degil" id="zoneNot"
                 onclick="placeSelected('not')"
                 role="button" tabindex="0" aria-label="Zorbalık Değil kategorisi">
                <div class="category-zone-header">
                    <h3>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">favorite</span>
                        ZORBALIK DEĞİL
                    </h3>
                </div>
                <div class="zone-chips" id="chipsNot">
                    <!-- Yerleştirilen kelimeler buraya eklenir -->
                </div>
            </div>

        </div>

        <!-- Kontrol butonu -->
        <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
            <button class="btn btn-success btn-lg" onclick="checkAll()">
                <span class="material-symbols-outlined">spellcheck</span> Kontrol Et
            </button>
            <button class="btn btn-outline" onclick="restartGame()">
                <span class="material-symbols-outlined">refresh</span> Yeniden Başla
            </button>
        </div>

    </div>
</main>

<!-- ===== SONUÇ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay" role="dialog" aria-modal="true" aria-label="Oyun sonucu">
    <div class="result-card">
        <div class="result-emoji">🏷️</div>
        <h2>Kategori Tamamlandı!</h2>
        <p>İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 170 puan</div>
        <div id="saveStatus" style="margin-bottom:1rem;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <button class="btn btn-primary" onclick="restartGame()">
                <span class="material-symbols-outlined">refresh</span> Tekrar Oyna
            </button>
            <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

<script>window.GAME_CONFIG = {};</script>
<script src="/genclik-rehberim/assets/js/kategori.js"></script>

<?php include '../includes/footer.php'; ?>
