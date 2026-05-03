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
<main>
<div class="game-wrapper">

    <!-- Oyun başlığı -->
    <header class="game-header">
        <h1>
            <i class="fa-solid fa-puzzle-piece" style="color:var(--primary)"></i>
            Zorbalık Bulmacası
        </h1>
        <p>Soruları okuyup boş kutulara doğru harfleri yaz. Her doğru cevap 10 puan!</p>
    </header>

    <!-- İlerleme çubuğu -->
    <div class="game-progress">
        <span class="progress-info">İlerleme: <span id="progressText">0/10</span></span>
        <div class="progress-bar-outer">
            <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
        </div>
        <span class="progress-score">Puan: <span id="scoreDisplay">0</span>/100</span>
    </div>

    <!-- Sorular (PHP ile oluşturulan HTML, JS ile kontrol edilir) -->
    <div id="quizContainer">

        <!-- Soru 1 -->
        <article class="quiz-card" id="q1">
            <div class="question-number">Soru 1 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Zorbalık olunca ne istemeliyiz?
            </p>
            <div class="letter-boxes" data-answer="YARDIM" id="boxes1"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input1" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(1)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb1"></div>
        </article>

        <!-- Soru 2 -->
        <article class="quiz-card" id="q2">
            <div class="question-number">Soru 2 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Yaşadığımız olayı kime anlatırız?
            </p>
            <div class="letter-boxes" data-answer="YETİŞKİN" id="boxes2"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input2" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(2)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb2"></div>
        </article>

        <!-- Soru 3 -->
        <article class="quiz-card" id="q3">
            <div class="question-number">Soru 3 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Yardım bulamazsak ne yapmaya devam etmeliyiz?
            </p>
            <div class="letter-boxes" data-answer="ARAMAK" id="boxes3"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input3" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(3)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb3"></div>
        </article>

        <!-- Soru 4 -->
        <article class="quiz-card" id="q4">
            <div class="question-number">Soru 4 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Zorba karşısında nasıl durmalıyız?
            </p>
            <div class="letter-boxes" data-answer="DİK" id="boxes4"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input4" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(4)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb4"></div>
        </article>

        <!-- Soru 5 -->
        <article class="quiz-card" id="q5">
            <div class="question-number">Soru 5 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Korktuğumuzu göstermemek zorbaya ne yapar?
            </p>
            <div class="letter-boxes" data-answer="UZAKLAŞTIRIR" id="boxes5"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input5" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(5)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb5"></div>
        </article>

        <!-- Soru 6 -->
        <article class="quiz-card" id="q6">
            <div class="question-number">Soru 6 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Korkunca sakinleşmek için ne alıp veririz?
            </p>
            <div class="letter-boxes" data-answer="NEFES" id="boxes6"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input6" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(6)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb6"></div>
        </article>

        <!-- Soru 7 -->
        <article class="quiz-card" id="q7">
            <div class="question-number">Soru 7 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                "Başarabilirim" gibi sözlere ne denir?
            </p>
            <div class="letter-boxes" data-answer="OLUMLU SÖZ" id="boxes7"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input7" placeholder="Cevabınızı yazın..." maxlength="25" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(7)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb7"></div>
        </article>

        <!-- Soru 8 -->
        <article class="quiz-card" id="q8">
            <div class="question-number">Soru 8 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Sözel zorbalıkta cevap vermeden ne yaparız?
            </p>
            <div class="letter-boxes" data-answer="UZAKLAŞIRIZ" id="boxes8"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input8" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(8)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb8"></div>
        </article>

        <!-- Soru 9 -->
        <article class="quiz-card" id="q9">
            <div class="question-number">Soru 9 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Fiziksel zorbalıkta nereye gideriz?
            </p>
            <div class="letter-boxes" data-answer="GÜVENLİ YER" id="boxes9"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input9" placeholder="Cevabınızı yazın..." maxlength="25" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(9)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb9"></div>
        </article>

        <!-- Soru 10 -->
        <article class="quiz-card" id="q10">
            <div class="question-number">Soru 10 / 10</div>
            <p class="question-text">
                <i class="fa-solid fa-circle-question" style="color:var(--primary)"></i>
                Daha güvende olmak için nerede bulunuruz?
            </p>
            <div class="letter-boxes" data-answer="KALABALIK" id="boxes10"></div>
            <div class="quiz-input-wrap">
                <input type="text" class="quiz-input" id="input10" placeholder="Cevabınızı yazın..." maxlength="20" autocomplete="off">
                <button class="btn btn-primary" onclick="checkAnswer(10)">
                    <i class="fa-solid fa-check"></i> Kontrol
                </button>
            </div>
            <div class="answer-feedback" id="fb10"></div>
        </article>

    </div>

    <!-- Oyunu bitir butonu -->
    <div class="text-center mt-4">
        <button class="btn btn-secondary btn-lg" onclick="finishGame()">
            <i class="fa-solid fa-flag-checkered"></i> Oyunu Bitir
        </button>
    </div>

</div>
</main>

<!-- ===== SONUÇ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay">
    <div class="result-card">
        <div class="result-emoji">🏆</div>
        <h2>Oyun Bitti!</h2>
        <p>Tebrikler! İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 100 puan</div>
        <div id="saveStatus" style="margin-bottom:1rem;font-size:0.85rem;color:var(--text-muted)"></div>
        <div class="result-buttons">
            <button class="btn btn-primary" onclick="restartGame()">
                <i class="fa-solid fa-rotate-right"></i> Tekrar Oyna
            </button>
            <a href="/genclik-rehberim/dashboard.php" class="btn btn-outline">
                <i class="fa-solid fa-chart-line"></i> Panele Git
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

const TOTAL_QUESTIONS = 10;    // Toplam soru sayısı
const POINTS_PER_Q    = 10;    // Soru başına puan
let score           = 0;       // Mevcut puan
let answeredCount   = 0;       // Yanıtlanan soru sayısı
let answered        = {};      // Hangi sorular yanıtlandı

/* ------ Sayfa yüklenince harf kutularını oluştur ------ */
document.addEventListener('DOMContentLoaded', function () {
    // Her soru için harf kutularını dinamik oluştur
    for (let i = 1; i <= TOTAL_QUESTIONS; i++) {
        buildLetterBoxes(i, ANSWERS[i]);
        // Enter tuşuyla kontrol
        document.getElementById('input' + i).addEventListener('keydown', function (e) {
            if (e.key === 'Enter') checkAnswer(i);
        });
    }
});

/**
 * Belirli bir sorunun harf kutularını oluşturur.
 * Boşluklar için ayırıcı eklenir.
 */
function buildLetterBoxes(qNum, answer) {
    const container = document.getElementById('boxes' + qNum);
    container.innerHTML = ''; // Temizle

    for (let i = 0; i < answer.length; i++) {
        if (answer[i] === ' ') {
            // Boşluk için görsel ayırıcı
            const spacer = document.createElement('div');
            spacer.className = 'letter-space';
            container.appendChild(spacer);
        } else {
            // Normal harf kutusu (başlangıçta boş '_')
            const box = document.createElement('div');
            box.className = 'letter-box';
            box.id = 'box_' + qNum + '_' + i;
            box.textContent = '_';
            container.appendChild(box);
        }
    }
}

/**
 * Türkçe karakterleri normalize eder (büyük harfe çevirir).
 * Karşılaştırma için kullanılır.
 */
function normalizeTurkish(str) {
    return str
        .toUpperCase()
        .replace(/i/g, 'İ')
        .replace(/ı/g, 'I')
        .trim();
}

/**
 * Kullanıcının cevabını kontrol eder.
 * @param {number} qNum - Soru numarası
 */
function checkAnswer(qNum) {
    // Daha önce yanıtlandıysa işleme devam etme
    if (answered[qNum]) return;

    const input     = document.getElementById('input' + qNum);
    const feedback  = document.getElementById('fb' + qNum);
    const userAnswer= normalizeTurkish(input.value);
    const correct   = normalizeTurkish(ANSWERS[qNum]);

    if (!userAnswer) {
        // Boş cevap uyarısı
        input.classList.add('shake');
        setTimeout(() => input.classList.remove('shake'), 500);
        return;
    }

    if (userAnswer === correct) {
        // DOĞRU CEVAP
        score += POINTS_PER_Q;
        answered[qNum] = true;
        answeredCount++;

        // Harf kutularını doldur (animasyonlu)
        revealLetters(qNum, ANSWERS[qNum], 'revealed');

        // Geri bildirim göster
        feedback.className  = 'answer-feedback show correct-fb';
        feedback.innerHTML  = '<i class="fa-solid fa-circle-check"></i> Harika! Doğru cevap: <strong>' + ANSWERS[qNum] + '</strong>';

        // Inputu devre dışı bırak
        input.disabled = true;
        input.parentElement.querySelector('button').disabled = true;

        // Skoru güncelle
        updateProgress();

    } else {
        // YANLIŞ CEVAP
        // Harf kutularını yanıltıcı şekilde göster
        revealLetters(qNum, ANSWERS[qNum], 'wrong');

        feedback.className = 'answer-feedback show wrong-fb';
        feedback.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Yanlış! Doğru cevap: <strong>' + ANSWERS[qNum] + '</strong>';

        // Yanlış cevap sonrası da soruyu kilitle (her soru 1 deneme hakkı)
        answered[qNum] = true;
        answeredCount++;
        input.disabled = true;
        input.parentElement.querySelector('button').disabled = true;

        // Skoru güncelle (yanlış cevapta puan yok)
        updateProgress();
    }
}

/**
 * Harf kutularını cevapla doldurur.
 * @param {string} cssClass - 'revealed' | 'wrong'
 */
function revealLetters(qNum, answer, cssClass) {
    let boxIdx = 0;
    for (let i = 0; i < answer.length; i++) {
        if (answer[i] === ' ') continue;
        const box = document.getElementById('box_' + qNum + '_' + i);
        if (box) {
            // Animasyon için kısa gecikme ekle
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

/**
 * İlerleme çubuğunu ve skor sayacını günceller.
 */
function updateProgress() {
    const progressBar  = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const scoreDisplay = document.getElementById('scoreDisplay');

    const percent = (answeredCount / TOTAL_QUESTIONS) * 100;
    progressBar.style.width  = percent + '%';
    progressText.textContent = answeredCount + '/' + TOTAL_QUESTIONS;
    scoreDisplay.textContent = score;

    // Tüm sorular yanıtlandıysa otomatik bitir
    if (answeredCount === TOTAL_QUESTIONS) {
        setTimeout(finishGame, 800);
    }
}

/**
 * Oyunu bitirir ve sonuç modalını gösterir.
 */
function finishGame() {
    // Henüz cevaplanmamış soruları cevapla
    for (let i = 1; i <= TOTAL_QUESTIONS; i++) {
        if (!answered[i]) {
            const input   = document.getElementById('input' + i);
            const feedback= document.getElementById('fb' + i);
            answered[i]   = true;
            answeredCount++;
            revealLetters(i, ANSWERS[i], 'revealed');
            feedback.className = 'answer-feedback show wrong-fb';
            feedback.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Cevap: <strong>' + ANSWERS[i] + '</strong>';
            input.disabled = true;
        }
    }

    // Final skorunu göster
    document.getElementById('finalScore').textContent = score;

    // Sonuç mesajını ve emojiyi ayarla
    const percent = (score / 100) * 100;
    let emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.querySelector('.result-emoji').textContent = emoji;
    document.querySelector('.result-card p').textContent = msg;

    // Modalı göster
    document.getElementById('resultOverlay').classList.add('show');

    // Skoru AJAX ile kaydet (kullanıcı giriş yapmışsa)
    const saveStatus = document.getElementById('saveStatus');
    saveScore(1, score, 100, function (data) {
        if (data && data.success) {
            saveStatus.innerHTML = '<i class="fa-solid fa-circle-check" style="color:#059669"></i> Puan kaydedildi!';
        } else if (data && data.login_required) {
            saveStatus.innerHTML = '<a href="/genclik-rehberim/login.php" style="color:var(--primary)">Puanı kaydetmek için giriş yap</a>';
        }
    });
}

/**
 * Sayfayı yeniler (oyunu yeniden başlatır).
 */
function restartGame() {
    window.location.reload();
}
</script>

<?php include '../includes/footer.php'; ?>
