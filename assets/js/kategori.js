/* ===========================================================
   KATEGORİ OYUNU — JavaScript
   Kelime tıkla → kutuya yerleştir mekanizması
   =========================================================== */

const WORDS = [
    { id: 1,  text: 'Yumruk atmak',       category: 'zorbalik' },
    { id: 2,  text: 'Tehdit etmek',       category: 'zorbalik' },
    { id: 3,  text: 'Küfretmek',          category: 'zorbalik' },
    { id: 4,  text: 'Kavga çıkarmak',     category: 'zorbalik' },
    { id: 5,  text: 'Kötü lakap takmak',  category: 'zorbalik' },
    { id: 6,  text: 'Arkadaşını itmek',   category: 'zorbalik' },
    { id: 7,  text: 'Çelme takmak',       category: 'zorbalik' },
    { id: 8,  text: 'Yardım etmek',                category: 'not' },
    { id: 9,  text: 'Sırada beklemek',              category: 'not' },
    { id: 10, text: 'Nazik konuşmak',               category: 'not' },
    { id: 11, text: 'Arkadaşını dinlemek',          category: 'not' },
    { id: 12, text: 'Oyuna davet etmek',            category: 'not' },
    { id: 13, text: 'Paylaşmak',                    category: 'not' },
    { id: 14, text: 'Sorunu bildirmek',             category: 'not' },
    { id: 15, text: 'Arkadaşına gülümsemek',        category: 'not' },
    { id: 16, text: 'Birini düşerse kaldırmak',     category: 'not' },
    { id: 17, text: 'Söz hakkına saygı göstermek',  category: 'not' },
];

const TOTAL  = WORDS.length;
const POINTS = 10;

let selectedWord = null;
let placedWords  = {};
let score        = 0;

document.addEventListener('DOMContentLoaded', function () {
    buildWordChips();
    document.getElementById('zoneZorbalik').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.key === ' ') placeSelected('zorbalik');
    });
    document.getElementById('zoneNot').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.key === ' ') placeSelected('not');
    });
});

function buildWordChips() {
    const container = document.getElementById('wordChips');
    container.innerHTML = '';

    const shuffled = [...WORDS].sort(() => Math.random() - 0.5);

    shuffled.forEach(function (word) {
        const chip = document.createElement('div');
        chip.className   = 'word-chip';
        chip.id          = 'chip_' + word.id;
        chip.dataset.id  = word.id;
        chip.textContent = word.text;
        chip.addEventListener('click', function () { selectWord(word.id); });
        chip.setAttribute('role', 'button');
        chip.setAttribute('tabindex', '0');
        chip.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') selectWord(word.id);
        });
        container.appendChild(chip);
    });
}

function selectWord(wordId) {
    if (placedWords[wordId]) return;

    if (selectedWord !== null) {
        const prevChip = document.getElementById('chip_' + selectedWord);
        if (prevChip) prevChip.classList.remove('selected');
    }

    selectedWord = wordId;
    const chip = document.getElementById('chip_' + wordId);
    chip.classList.add('selected');

    const word = WORDS.find(w => w.id === wordId);
    const hint = document.getElementById('selectedHint');
    hint.innerHTML = '<div class="selected-hint-inner">' +
        '<span class="material-symbols-outlined">touch_app</span>' +
        '"' + word.text + '" seçildi → Şimdi bir kutuya tıkla!' +
        '</div>';
}

function placeSelected(targetCat) {
    if (selectedWord === null) {
        const hint = document.getElementById('selectedHint');
        hint.innerHTML = '<div class="selected-hint-inner" style="background:var(--tertiary);color:var(--on-tertiary)">' +
            '<span class="material-symbols-outlined">warning</span>' +
            'Önce bir kelimeye tıkla!' +
            '</div>';
        return;
    }

    const wordId = selectedWord;
    const word   = WORDS.find(w => w.id === wordId);
    const chip   = document.getElementById('chip_' + wordId);

    chip.classList.remove('selected');
    chip.classList.add('placed');

    const targetContainer = document.getElementById(
        targetCat === 'zorbalik' ? 'chipsZorbalik' : 'chipsNot'
    );

    const newChip = document.createElement('div');
    newChip.className   = 'word-chip';
    newChip.id          = 'placed_' + wordId;
    newChip.textContent = word.text;
    if (targetCat === 'zorbalik') {
        newChip.style.background   = 'rgba(186,26,26,0.1)';
        newChip.style.borderColor  = 'var(--error)';
        newChip.style.color        = 'var(--on-error-container)';
    } else {
        newChip.style.background   = 'rgba(58,106,0,0.1)';
        newChip.style.borderColor  = 'var(--secondary)';
        newChip.style.color        = 'var(--on-secondary-fixed-variant)';
    }
    targetContainer.appendChild(newChip);

    placedWords[wordId] = targetCat;
    selectedWord = null;

    document.getElementById('selectedHint').innerHTML = '';
    updateProgress();
}

function updateProgress() {
    const count = Object.keys(placedWords).length;
    document.getElementById('placedCount').textContent    = count;
    document.getElementById('placedCountBar').textContent = count;
    document.getElementById('progressBar').style.width   = (count / TOTAL * 100) + '%';

    if (count === TOTAL) {
        setTimeout(checkAll, 500);
    }
}

function checkAll() {
    const placed = Object.keys(placedWords).length;

    if (placed < TOTAL) {
        const hint = document.getElementById('selectedHint');
        hint.innerHTML = '<div class="selected-hint-inner" style="background:var(--tertiary);color:var(--on-tertiary)">' +
            '<span class="material-symbols-outlined">warning</span>' +
            'Lütfen tüm kelimeleri yerleştir! (' + placed + '/' + TOTAL + ')' +
            '</div>';
        return;
    }

    score = 0;

    WORDS.forEach(function (word) {
        const placedChip = document.getElementById('placed_' + word.id);
        if (!placedChip) return;

        if (placedWords[word.id] === word.category) {
            score += POINTS;
            placedChip.style.background   = 'rgba(58,106,0,0.15)';
            placedChip.style.borderColor  = 'var(--secondary)';
            placedChip.style.color        = 'var(--on-secondary-fixed-variant)';
            placedChip.style.fontWeight   = '700';
        } else {
            placedChip.style.background   = 'rgba(186,26,26,0.12)';
            placedChip.style.borderColor  = 'var(--error)';
            placedChip.style.color        = 'var(--on-error-container)';
            placedChip.style.fontWeight   = '700';
        }
    });

    document.getElementById('scoreDisplay').textContent    = score;
    document.getElementById('scoreDisplayBar').textContent = score;

    document.getElementById('finalScore').textContent = score;
    const percent = (score / (TOTAL * POINTS)) * 100;
    let emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.querySelector('.result-emoji').textContent  = emoji;
    document.querySelector('.result-card p').textContent = msg;
    document.getElementById('resultOverlay').classList.add('show');

    const saveStatus = document.getElementById('saveStatus');
    saveScore(3, score, TOTAL * POINTS, function (data) {
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
