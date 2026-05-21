/* ===========================================================
   BULMACA OYUNU — JavaScript
   10 sorulu kelime tahmin oyunu
   =========================================================== */

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
/* Etkinlik ID'si PHP'den (window.GAME_CONFIG) gelir; sabit kodlanmaz. */
const ACTIVITY_ID     = (window.GAME_CONFIG && window.GAME_CONFIG.activityId) || 1;
let score           = 0;
let answeredCount   = 0;
let answered        = {};

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
    /* Unicode escape'ler: kaynak dosya encoding'inden bağımsız, tüm tarayıcılarda güvenli.
       i/ı/İ/I → hepsi 'I'; diğer Türkçe harfler büyük harfe çevrilir. */
    var s = String(str || '');
    s = s.split('i')            .join('I')          /* i  U+0069 */
         .split('ı').join('I')                  /* ı  U+0131 DOTLESS I */
         .split('İ').join('I')                  /* İ  U+0130 DOTTED CAPITAL I */
         .split('ş').join('Ş')              /* ş → Ş */
         .split('ğ').join('Ğ')              /* ğ → Ğ */
         .split('ü').join('Ü')              /* ü → Ü */
         .split('ö').join('Ö')              /* ö → Ö */
         .split('ç').join('Ç');             /* ç → Ç */
    return s.toUpperCase().trim();
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

    document.getElementById('progressText').textContent    = answeredCount + '/' + TOTAL_QUESTIONS;
    document.getElementById('scoreDisplay').textContent    = score;
    document.getElementById('progressBar').style.width    = percent + '%';
    document.getElementById('progressTextBar').textContent = answeredCount + '/' + TOTAL_QUESTIONS;
    document.getElementById('scoreDisplayBar').textContent = score;

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
    saveScore(ACTIVITY_ID, score, 100, function (data) {
        if (data && data.success) {
            saveStatus.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
        } else if (data && data.login_required) {
            saveStatus.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
        }
    });
}

function restartGame() {
    window.location.reload();
}
