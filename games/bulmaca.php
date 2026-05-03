<?php
/**
 * bulmaca.php — Zorba Davranışa Karşı Koyma Yöntemleri Bulmacası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * PDF kaynağı: 1. Etkinlik — 10 soru & cevap
 */

require_once '../includes/header.php';
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
                    <input type="text" class="quiz-input" id="input3" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input4" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input5" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input6" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input7" placeholder="Cevabınızı yazın..." maxlength="25" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input8" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input9" placeholder="Cevabınızı yazın..." maxlength="25" autocomplete="off">
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
                    <input type="text" class="quiz-input" id="input10" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
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
            <a href="/genclik-rehberim/dashboard.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

<!-- Bulmaca JavaScript Mantığı -->
<script>
/* ===========================================================
   BULMACA OYUNU — JavaScript
   10 sorulu kelime tahmin oyunu
   =========================================================== */

// Doğru cevaplar dizisi (indeks 1'den başlar)
const ANSWERS = {
    1:  'YARDIM',
    2:  'YETİŞKİN',
    3:  'ARAMAK',
    4:  'DİK',
    5:  'UZAKLAŞTIRIR',
    6:  'NEFES',
    7:  'OLUMLU SÖZ',
    8:  'UZAKLAŞIRIZ',
    9:  'GÜVENLİ YER',
    10: 'KALABALIK'
};

const TOTAL_QUESTIONS = 10;
const POINTS_PER_Q    = 10;
let score           = 0;
let answeredCount   = 0;
let answered        = {};

/* ------ Sayfa yüklenince harf kutularını oluştur ------ */
document.addEventListener('DOMContentLoaded', function () {
    for (let i = 1; i <= TOTAL_QUESTIONS; i++) {
        buildLetterBoxes(i, ANSWERS[i]);
        document.getElementById('input' + i).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') checkAnswer(i);
        });
    }
});

function buildLetterBoxes(qNum, answer) {
    const container = document.getElementById('boxes' + qNum);
    container.innerHTML = '';

    for (let i = 0; i < answer.length; i++) {
        if (answer[i] === ' ') {
            const spacer = document.createElement('div');
            spacer.className = 'letter-space';
            container.appendChild(spacer);
        } else {
            const box = document.createElement('div');
            box.className = 'letter-box';
            box.id = 'box_' + qNum + '_' + i;
            box.textContent = '_';
            container.appendChild(box);
        }
    }
}

function normalizeTurkish(str) {
    return str
        .toUpperCase()
        .replace(/i/g, 'İ')
        .replace(/ı/g, 'I')
        .trim();
}

function checkAnswer(qNum) {
    if (answered[qNum]) return;

    const input      = document.getElementById('input' + qNum);
    const feedback   = document.getElementById('fb' + qNum);
    const userAnswer = normalizeTurkish(input.value);
    const correct    = normalizeTurkish(ANSWERS[qNum]);

    if (!userAnswer) {
        input.classList.add('shake');
        setTimeout(() => input.classList.remove('shake'), 500);
        return;
    }

    if (userAnswer === correct) {
        score += POINTS_PER_Q;
        answered[qNum] = true;
        answeredCount++;
        revealLetters(qNum, ANSWERS[qNum], 'revealed');
        feedback.className  = 'answer-feedback show correct-fb';
        feedback.innerHTML  = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;">check_circle</span> Harika! Doğru cevap: <strong>' + ANSWERS[qNum] + '</strong>';
        input.disabled = true;
        input.parentElement.querySelector('button').disabled = true;
        updateProgress();
    } else {
        revealLetters(qNum, ANSWERS[qNum], 'wrong');
        feedback.className = 'answer-feedback show wrong-fb';
        feedback.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;">cancel</span> Yanlış! Doğru cevap: <strong>' + ANSWERS[qNum] + '</strong>';
        answered[qNum] = true;
        answeredCount++;
        input.disabled = true;
        input.parentElement.querySelector('button').disabled = true;
        updateProgress();
    }
}

function revealLetters(qNum, answer, cssClass) {
    let boxIdx = 0;
    for (let i = 0; i < answer.length; i++) {
        if (answer[i] === ' ') continue;
        const box = document.getElementById('box_' + qNum + '_' + i);
        if (box) {
            (function(b, letter, delay) {
                setTimeout(function () {
                    b.textContent = letter;
                    b.classList.add(cssClass);
                }, delay);
            })(box, answer[i], boxIdx * 80);
            boxIdx++;
        }
    }
}

function updateProgress() {
    const percent = (answeredCount / TOTAL_QUESTIONS) * 100;

    // İstatistik kutusu
    document.getElementById('progressText').textContent   = answeredCount + '/' + TOTAL_QUESTIONS;
    document.getElementById('scoreDisplay').textContent   = score;

    // İlerleme çubuğu
    document.getElementById('progressBar').style.width    = percent + '%';
    document.getElementById('progressTextBar').textContent= answeredCount + '/' + TOTAL_QUESTIONS;
    document.getElementById('scoreDisplayBar').textContent= score;

    if (answeredCount === TOTAL_QUESTIONS) {
        setTimeout(finishGame, 800);
    }
}

function finishGame() {
    for (let i = 1; i <= TOTAL_QUESTIONS; i++) {
        if (!answered[i]) {
            const input    = document.getElementById('input' + i);
            const feedback = document.getElementById('fb' + i);
            answered[i]    = true;
            answeredCount++;
            revealLetters(i, ANSWERS[i], 'revealed');
            feedback.className = 'answer-feedback show wrong-fb';
            feedback.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;">cancel</span> Cevap: <strong>' + ANSWERS[i] + '</strong>';
            input.disabled = true;
        }
    }

    document.getElementById('finalScore').textContent = score;

    const percent = (score / 100) * 100;
    let emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.querySelector('.result-emoji').textContent = emoji;
    document.querySelector('.result-card p').textContent = msg;
    document.getElementById('resultOverlay').classList.add('show');

    const saveStatus = document.getElementById('saveStatus');
    saveScore(1, score, 100, function (data) {
        if (data && data.success) {
            saveStatus.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
        } else if (data && data.login_required) {
            saveStatus.innerHTML = '<a href="/genclik-rehberim/login.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
        }
    });
}

function restartGame() {
    window.location.reload();
}
</script>

<?php include '../includes/footer.php'; ?>
