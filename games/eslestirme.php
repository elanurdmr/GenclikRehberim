<?php
/**
 * eslestirme.php — Doğru/Yanlış Davranış Eşleştirme Oyunu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * PDF kaynağı: 2. Etkinlik — Sürükle-bırak eşleştirme
 */

$pageTitle = 'Eşleştirme';
require_once '../includes/header.php';
?>

<!-- ===== EŞLEŞTİRME OYUNU ===== -->
<main class="game-page" id="eslestirmePage">

    <!-- Dekoratif arka plan lekeleri -->
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Oyun Başlık Alanı -->
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">local_fire_department</span>
                Zorbalık Farkındalığı · Etkinlik 2
            </div>

            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>Doğru mu, Yanlış mı?</h1>
                    <p>Kartları sürükle ve doğru sütuna bırak. Her doğru yerleştirme <strong>10 puan</strong>!</p>
                </div>

                <!-- İstatistik kutusu -->
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">Yerleştirilen</span>
                        <span class="game-stat-value text-primary"><span id="placedCount">0</span>/14</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Puan</span>
                        <span class="game-stat-value text-secondary" id="scoreDisplay">0</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Max</span>
                        <span class="game-stat-value text-tertiary">140</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- İlerleme çubuğu -->
        <div class="game-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <span class="progress-info">Yerleştirilen: <span id="placedCountBar">0</span>/14</span>
            <div class="progress-bar-outer">
                <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
            </div>
            <span class="progress-score">Puan: <span id="scoreDisplayBar">0</span>/140</span>
        </div>

        <!-- Sürüklenebilir kartlar havuzu -->
        <div class="matching-items-column" aria-label="Sürüklenebilir kartlar">
            <h3>
                <span class="material-symbols-outlined">touch_app</span>
                Kartları Sürükle
            </h3>
            <div id="itemsPool" class="items-pool-wrap">
                <!-- Kartlar JavaScript tarafından oluşturulur (karıştırılmış) -->
            </div>
        </div>

        <!-- Bırakma alanları -->
        <div class="drop-zones">

            <!-- Doğru Davranışlar Kutusu -->
            <div class="drop-zone zone-dogru" id="zoneDogru"
                 ondragover="allowDrop(event)" ondrop="drop(event, 'dogru')"
                 role="region" aria-label="Doğru Davranışlar bölgesi">
                <div class="drop-zone-bg-icon" aria-hidden="true">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">verified</span>
                </div>
                <div class="drop-zone-header">
                    <h3>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
                        Doğru Davranışlar
                    </h3>
                    <small>8 kart</small>
                </div>
                <div id="droppedDogru" class="drop-zone-content" style="min-height:60px"></div>
            </div>

            <!-- Yanlış Davranışlar Kutusu -->
            <div class="drop-zone zone-yanlis" id="zoneYanlis"
                 ondragover="allowDrop(event)" ondrop="drop(event, 'yanlis')"
                 role="region" aria-label="Yanlış Davranışlar bölgesi">
                <div class="drop-zone-bg-icon" aria-hidden="true">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">sentiment_very_dissatisfied</span>
                </div>
                <div class="drop-zone-header">
                    <h3>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">cancel</span>
                        Yanlış Davranışlar
                    </h3>
                    <small>6 kart</small>
                </div>
                <div id="droppedYanlis" class="drop-zone-content" style="min-height:60px"></div>
            </div>

        </div>

        <!-- Kontrol ve Tekrar Butonları -->
        <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
            <button class="btn btn-secondary btn-lg" onclick="checkAll()">
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
        <div class="result-emoji">🎯</div>
        <h2>Eşleştirme Tamamlandı!</h2>
        <p>İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 140 puan</div>
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
<script src="/genclik-rehberim/assets/js/eslestirme.js"></script>

<?php include '../includes/footer.php'; ?>
