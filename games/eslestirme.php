<?php
/**
 * eslestirme.php — Üç Bölümlü Eşleştirme Oyunu
 * Bölüm 1: Doğru/Yanlış Davranış Eşleştirme (140 puan)
 * Bölüm 2: Boşluk Doldurma (80 puan)
 * Bölüm 3: Zorbalık mı, Değil mi? Kategori (170 puan)
 * Toplam: 390 puan
 */

$pageTitle = 'Eşleştirme';
require_once '../includes/header.php';
?>

<!-- ===== İKİ BÖLÜMLÜ OYUN ===== -->
<main class="game-page" id="eslestirmePage">

    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Bölüm ilerleme göstergesi -->
        <div class="section-progress-bar">
            <div class="step active" id="stepIndicator1">
                <span class="material-symbols-outlined">join_inner</span>
                Bölüm 1 — Eşleştirme
            </div>
            <div class="sep"></div>
            <div class="step" id="stepIndicator2">
                <span class="material-symbols-outlined">edit_note</span>
                Bölüm 2 — Boşluk Doldur
            </div>
            <div class="sep"></div>
            <div class="step" id="stepIndicator3">
                <span class="material-symbols-outlined">category</span>
                Bölüm 3 — Kategori
            </div>
        </div>

        <!-- ================================================================
             BÖLÜM 1: EŞLEŞTİRME (flashcard tek kart akışı)
             ================================================================ -->
        <div class="game-section" id="section1">

            <header class="game-header-area">
                <div class="game-page-label">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">local_fire_department</span>
                    Zorbalık Farkındalığı · Bölüm 1 / 3
                </div>
                <div class="game-header-row">
                    <div class="game-header-text">
                        <h1>Doğru mu, Yanlış mı?</h1>
                        <p>Kartı doğru kutuya sürükle veya kutuya tıkla.</p>
                    </div>
                    <div class="game-stats-box">
                        <div class="game-stat-item">
                            <span class="game-stat-label">Kart</span>
                            <span class="game-stat-value text-primary" id="cardCounter">1/14</span>
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

            <!-- Aktif kart alanı -->
            <div class="flashcard-stage" id="flashcardStage">
                <div class="flashcard-wrap" id="flashcardWrap">
                    <!-- JS tarafından doldurulur -->
                </div>
            </div>

            <!-- Drop zone'lar (büyük, tıklanabilir) -->
            <div class="flashcard-zones">
                <div class="flashcard-zone zone-dogru"
                     id="zoneDogru"
                     onclick="placeCard('dogru')"
                     ondragover="allowDrop(event)"
                     ondrop="drop(event,'dogru')"
                     role="button" tabindex="0"
                     aria-label="Doğru Davranış">
                    <span class="material-symbols-outlined zone-icon"
                          style="font-variation-settings:'FILL' 1">check_circle</span>
                    <span class="zone-label">Doğru Davranış</span>
                    <span class="zone-count" id="dogru-count">0</span>
                </div>
                <div class="flashcard-zone zone-yanlis"
                     id="zoneYanlis"
                     onclick="placeCard('yanlis')"
                     ondragover="allowDrop(event)"
                     ondrop="drop(event,'yanlis')"
                     role="button" tabindex="0"
                     aria-label="Yanlış Davranış">
                    <span class="material-symbols-outlined zone-icon"
                          style="font-variation-settings:'FILL' 1">cancel</span>
                    <span class="zone-label">Yanlış Davranış</span>
                    <span class="zone-count" id="yanlis-count">0</span>
                </div>
            </div>

            <!-- Sonuç butonu (başta gizli) -->
            <div id="section1Done" style="display:none;text-align:center;margin-top:2rem">
                <p style="font-size:1.1rem;font-weight:700;color:var(--on-surface-variant);margin-bottom:1rem">
                    Tüm kartları yerleştirdin!
                </p>
                <button class="btn btn-secondary btn-lg" onclick="checkAllEsles()">
                    <span class="material-symbols-outlined">spellcheck</span> Sonuçları Gör
                </button>
            </div>

        </div><!-- /section1 -->

        <!-- ================================================================
             BÖLÜM 2: BOŞLUK DOLDURMA (başlangıçta gizli)
             ================================================================ -->
        <div class="game-section" id="section2" style="display:none">

            <header class="game-header-area">
                <div class="game-page-label">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">local_fire_department</span>
                    Zorbalık Farkındalığı · Bölüm 2 / 3
                </div>
                <div class="game-header-row">
                    <div class="game-header-text">
                        <h1>Boşlukları Doldur</h1>
                        <p>Cümlede boş bırakılan yere doğru seçeneği tıkla. Her doğru cevap <strong>10 puan</strong>!</p>
                    </div>
                    <div class="game-stats-box" aria-label="Oyun istatistikleri">
                        <div class="game-stat-item">
                            <span class="game-stat-label">Soru</span>
                            <span class="game-stat-value text-primary" id="fillQuestionCounter">1/8</span>
                        </div>
                        <div class="game-stat-item">
                            <span class="game-stat-label">Puan</span>
                            <span class="game-stat-value text-secondary" id="fillScoreDisplay">0</span>
                        </div>
                        <div class="game-stat-item">
                            <span class="game-stat-label">Max</span>
                            <span class="game-stat-value text-tertiary">80</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="game-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <span class="progress-info">Tamamlanan: <span id="fillProgressText">0</span>/8</span>
                <div class="progress-bar-outer">
                    <div class="progress-bar-inner" id="fillProgressBar" style="width:0%"></div>
                </div>
                <span class="progress-score">Puan: <span id="fillScoreBar">0</span>/80</span>
            </div>

            <div id="fillQuestionCard" class="fill-question-card"></div>
            <div class="fill-options" id="fillOptions"></div>
            <div class="fill-feedback" id="fillFeedback"></div>

            <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
                <button class="btn btn-outline btn-sm" id="fillHintBtn">
                    <span class="material-symbols-outlined">lightbulb</span> İpucu
                </button>
                <button class="btn btn-secondary btn-lg" id="checkFillBtn">
                    <span class="material-symbols-outlined">spellcheck</span> Kontrol Et
                </button>
            </div>

        </div><!-- /section2 -->

        <!-- ================================================================
             BÖLÜM 3: KATEGORİ (başlangıçta gizli)
             ================================================================ -->
        <div class="game-section" id="section3" style="display:none">

            <header class="game-header-area">
                <div class="game-page-label">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">local_fire_department</span>
                    Zorbalık Farkındalığı · Bölüm 3 / 3
                </div>
                <div class="game-header-row">
                    <div class="game-header-text">
                        <h1>Zorbalık mı, Değil mi?</h1>
                        <p>Bir kelimeye tıkla, sonra doğru kutuya tıkla! Her doğru yerleştirme <strong>10 puan</strong>.</p>
                    </div>
                    <div class="game-stats-box" aria-label="Oyun istatistikleri">
                        <div class="game-stat-item">
                            <span class="game-stat-label">Yerleştirilen</span>
                            <span class="game-stat-value text-primary"><span id="placedCountK">0</span>/17</span>
                        </div>
                        <div class="game-stat-item">
                            <span class="game-stat-label">Puan</span>
                            <span class="game-stat-value text-secondary" id="scoreDisplayK">0</span>
                        </div>
                        <div class="game-stat-item">
                            <span class="game-stat-label">Max</span>
                            <span class="game-stat-value text-tertiary">170</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="game-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <span class="progress-info">Yerleştirilen: <span id="placedCountBarK">0</span>/17</span>
                <div class="progress-bar-outer">
                    <div class="progress-bar-inner" id="progressBarK" style="width:0%"></div>
                </div>
                <span class="progress-score">Puan: <span id="scoreDisplayBarK">0</span>/170</span>
            </div>

            <div class="category-words" aria-label="Kelime seçim alanı">
                <h3>
                    <span class="material-symbols-outlined">touch_app</span>
                    Bir Kelimeye Tıkla, Sonra Kutuya Yerleştir
                </h3>
                <div class="word-chips" id="wordChips"></div>
            </div>

            <div id="selectedHint" class="selected-hint" aria-live="polite"></div>

            <div class="category-zones">
                <div class="category-zone zone-zorbalik" id="zoneZorbalik"
                     onclick="placeSelected('zorbalik')"
                     role="button" tabindex="0" aria-label="Zorbalık kategorisi">
                    <div class="category-zone-header">
                        <h3>
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">back_hand</span>
                            ZORBALIK
                        </h3>
                    </div>
                    <div class="zone-chips" id="chipsZorbalik"></div>
                </div>

                <div class="category-zone zone-zorbalik-degil" id="zoneNot"
                     onclick="placeSelected('not')"
                     role="button" tabindex="0" aria-label="Zorbalık Değil kategorisi">
                    <div class="category-zone-header">
                        <h3>
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">favorite</span>
                            ZORBALIK DEĞİL
                        </h3>
                    </div>
                    <div class="zone-chips" id="chipsNot"></div>
                </div>
            </div>

            <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
                <button class="btn btn-success btn-lg" onclick="checkAll()">
                    <span class="material-symbols-outlined">spellcheck</span> Kontrol Et
                </button>
            </div>

        </div><!-- /section3 -->

    </div>
</main>

<!-- ===== BÖLÜM 1 TAMAMLAMA MODALİ ===== -->
<div class="result-overlay" id="sectionDoneOverlay" role="dialog" aria-modal="true" aria-label="Bölüm 1 sonucu">
    <div class="result-card">
        <div class="result-emoji" id="sectionDoneEmoji">🎯</div>
        <h2>Bölüm 1 Tamamlandı!</h2>
        <p id="sectionDoneMsg">İşte birinci bölüm sonucun:</p>
        <div class="result-score-big" id="sectionDoneScore">0</div>
        <div class="result-score-label">/ 140 puan</div>
        <div id="sectionSaveStatus" style="margin-bottom:1rem;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <button class="btn btn-primary btn-lg" id="goToSection2Btn">
                <span class="material-symbols-outlined">arrow_forward</span> Bölüm 2'ye Geç
            </button>
        </div>
    </div>
</div>

<!-- ===== BÖLÜM 2 TAMAMLAMA MODALİ ===== -->
<div class="result-overlay" id="fillDoneOverlay" role="dialog" aria-modal="true" aria-label="Bölüm 2 sonucu">
    <div class="result-card">
        <div class="result-emoji" id="fillDoneEmoji">✏️</div>
        <h2>Bölüm 2 Tamamlandı!</h2>
        <p id="fillDoneMsg">İşte ikinci bölüm sonucun:</p>
        <div class="result-score-big" id="fillDoneScore">0</div>
        <div class="result-score-label">/ 80 puan</div>
        <div id="fillSaveStatus" style="margin-bottom:1rem;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <button class="btn btn-primary btn-lg" id="goToSection3Btn">
                <span class="material-symbols-outlined">arrow_forward</span> Bölüm 3'e Geç
            </button>
        </div>
    </div>
</div>

<!-- ===== SON SONUÇ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay" role="dialog" aria-modal="true" aria-label="Oyun sonucu">
    <div class="result-card">
        <div class="result-emoji" id="resultFinalEmoji">🏆</div>
        <h2>Oyun Tamamlandı!</h2>
        <p id="resultFinalMsg">Üç bölüm tamamlandı, işte toplam sonucun:</p>
        <div class="result-section-scores">
            <div class="result-section-score">
                <strong id="finalScore1">0</strong>
                Bölüm 1 / 140
            </div>
            <div class="result-section-score">
                <strong id="finalScore2">0</strong>
                Bölüm 2 / 80
            </div>
            <div class="result-section-score">
                <strong id="finalScore3">0</strong>
                Bölüm 3 / 170
            </div>
        </div>
        <div class="result-score-big" id="finalScoreTotal">0</div>
        <div class="result-score-label">/ 390 puan</div>
        <div id="saveStatus2" style="margin-bottom:1rem;font-size:0.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <button class="btn btn-primary" onclick="location.reload()">
                <span class="material-symbols-outlined">refresh</span> Tekrar Oyna
            </button>
            <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

<script src="/genclik-rehberim/assets/js/eslestirme.js"></script>

<?php include '../includes/footer.php'; ?>
