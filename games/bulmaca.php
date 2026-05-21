<?php
/**
 * bulmaca.php — Zorba Davranışa Karşı Koyma Yöntemleri Bulmacası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * PDF kaynağı: 1. Etkinlik — 10 soru & cevap
 */

$pageTitle = 'Zorbalık Bulmacası';
require_once '../includes/header.php';
requireLogin();
?>

<!-- ===== BULMACA OYUNU ===== -->
<main class="game-page" id="bulmacaPage">

    <!-- Dekoratif arka plan lekeleri -->
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Oyun Başlık Alanı -->
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">local_fire_department</span>
                Zorbalık Farkındalığı · Etkinlik 1
            </div>

            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>Zorbalık Bulmacası</h1>
                    <p>Soruları okuyup boş kutulara doğru harfleri yaz. Her doğru cevap <strong>10 puan</strong>!</p>
                </div>

                <!-- İstatistik kutusu -->
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">İlerleme</span>
                        <span class="game-stat-value text-primary" id="progressText">0/10</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Puan</span>
                        <span class="game-stat-value text-secondary" id="scoreDisplay">0</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Max</span>
                        <span class="game-stat-value text-tertiary">100</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- İlerleme çubuğu -->
        <div class="game-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <span class="progress-info">İlerleme: <span id="progressTextBar">0/10</span></span>
            <div class="progress-bar-outer">
                <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
            </div>
            <span class="progress-score">Puan: <span id="scoreDisplayBar">0</span>/100</span>
        </div>

        <!-- Sorular -->
        <div class="quiz-container" id="quizContainer">

            <!-- Soru 1 -->
            <article class="quiz-card" id="q1">
                <div class="question-number">Soru 1 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Zorbalık olunca ne istemeliyiz?
                </p>
                <div class="letter-boxes" data-answer="YARDIM" id="boxes1"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input1">Soru 1 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input1" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 1 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(1)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb1"></div>
            </article>

            <!-- Soru 2 -->
            <article class="quiz-card" id="q2">
                <div class="question-number">Soru 2 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Yaşadığımız olayı kime anlatırız?
                </p>
                <div class="letter-boxes" data-answer="YETİŞKİN" id="boxes2"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input2">Soru 2 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input2" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 2 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(2)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb2"></div>
            </article>

            <!-- Soru 3 -->
            <article class="quiz-card" id="q3">
                <div class="question-number">Soru 3 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Yardım bulamazsak ne yapmaya devam etmeliyiz?
                </p>
                <div class="letter-boxes" data-answer="ARAMAK" id="boxes3"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input3">Soru 3 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input3" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 3 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(3)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb3"></div>
            </article>

            <!-- Soru 4 -->
            <article class="quiz-card" id="q4">
                <div class="question-number">Soru 4 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Zorba karşısında nasıl durmalıyız?
                </p>
                <div class="letter-boxes" data-answer="DİK" id="boxes4"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input4">Soru 4 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input4" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 4 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(4)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb4"></div>
            </article>

            <!-- Soru 5 -->
            <article class="quiz-card" id="q5">
                <div class="question-number">Soru 5 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Korktuğumuzu göstermemek zorbaya ne yapar?
                </p>
                <div class="letter-boxes" data-answer="UZAKLAŞTIRIR" id="boxes5"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input5">Soru 5 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input5" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 5 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(5)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb5"></div>
            </article>

            <!-- Soru 6 -->
            <article class="quiz-card" id="q6">
                <div class="question-number">Soru 6 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Korkunca sakinleşmek için ne alıp veririz?
                </p>
                <div class="letter-boxes" data-answer="NEFES" id="boxes6"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input6">Soru 6 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input6" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 6 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(6)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb6"></div>
            </article>

            <!-- Soru 7 -->
            <article class="quiz-card" id="q7">
                <div class="question-number">Soru 7 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    "Başarabilirim" gibi sözlere ne denir?
                </p>
                <div class="letter-boxes" data-answer="OLUMLU SÖZ" id="boxes7"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input7">Soru 7 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input7" placeholder="Cevabınızı yazın..." maxlength="25" autocomplete="off" aria-label="Soru 7 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(7)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb7"></div>
            </article>

            <!-- Soru 8 -->
            <article class="quiz-card" id="q8">
                <div class="question-number">Soru 8 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Sözel zorbalıkta cevap vermeden ne yaparız?
                </p>
                <div class="letter-boxes" data-answer="UZAKLAŞIRIZ" id="boxes8"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input8">Soru 8 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input8" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 8 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(8)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb8"></div>
            </article>

            <!-- Soru 9 -->
            <article class="quiz-card" id="q9">
                <div class="question-number">Soru 9 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Fiziksel zorbalıkta nereye gideriz?
                </p>
                <div class="letter-boxes" data-answer="GÜVENLİ YER" id="boxes9"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input9">Soru 9 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input9" placeholder="Cevabınızı yazın..." maxlength="25" autocomplete="off" aria-label="Soru 9 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(9)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb9"></div>
            </article>

            <!-- Soru 10 -->
            <article class="quiz-card" id="q10">
                <div class="question-number">Soru 10 / 10</div>
                <p class="question-text">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">help</span>
                    Daha güvende olmak için nerede bulunuruz?
                </p>
                <div class="letter-boxes" data-answer="KALABALIK" id="boxes10"></div>
                <div class="quiz-input-wrap">
                    <label class="visually-hidden" for="input10">Soru 10 cevap kutusu</label>
                    <input type="text" class="quiz-input" id="input10" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off" aria-label="Soru 10 cevap kutusu">
                    <button class="btn btn-primary" onclick="checkAnswer(10)">
                        <span class="material-symbols-outlined">check</span> Kontrol
                    </button>
                </div>
                <div class="answer-feedback" id="fb10"></div>
            </article>

        </div>

        <!-- Oyunu bitir butonu -->
        <div class="text-center mt-4">
            <button class="btn btn-secondary btn-lg" onclick="finishGame()">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">flag</span>
                Oyunu Bitir
            </button>
        </div>

    </div>
</main>

<!-- ===== SONUÇ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay" role="dialog" aria-modal="true" aria-label="Oyun sonucu">
    <div class="result-card">
        <div class="result-emoji">🏆</div>
        <h2>Oyun Bitti!</h2>
        <p>Tebrikler! İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 100 puan</div>
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

<script>window.GAME_CONFIG = { activityId: <?= getActivityId('bulmaca') ?> };</script>
<script src="/genclik-rehberim/assets/js/bulmaca.js"></script>

<?php include '../includes/footer.php'; ?>
