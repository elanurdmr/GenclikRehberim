/* ===========================================================
   EŞLEŞTİRME + KATEGORİ — İki Bölümlü Oyun
   Bölüm 1: Sürükle-bırak eşleştirme  (activityId=2, max 140)
   Bölüm 2: Kelime kategori             (activityId=3, max 170)
   Toplam: 310 puan
   =========================================================== */

/* ---------- VERİ ---------- */

var CARDS = [
    { id: 1,  text: 'Zorbalığa müdahale etmeden önce kendi güvenliğimi kontrol ederim.',        category: 'dogru'  },
    { id: 2,  text: 'Zorbalık olunca herkesi oradan uzaklaşmaya çağırırım.',                    category: 'dogru'  },
    { id: 3,  text: 'Arkadaşlarımla birlikte zorbalık yapan kişiyi nazikçe uyarırım.',          category: 'dogru'  },
    { id: 4,  text: 'Zorbalık yapan kişiye, bu davranışın başkasını üzdüğünü söylerim.',        category: 'dogru'  },
    { id: 5,  text: 'Böyle bir durumda öğretmenime veya bir yetişkine haber veririm.',          category: 'dogru'  },
    { id: 6,  text: 'Zorbalığa uğrayan arkadaşımın yanında olur, ona destek olurum.',          category: 'dogru'  },
    { id: 7,  text: 'Zorbalık yapan kişiyle konuşup davranışını düzeltmesine yardımcı olurum.',category: 'dogru'  },
    { id: 8,  text: 'Okulda herkesin güvende olma hakkı olduğunu bilirim.',                    category: 'dogru'  },
    { id: 9,  text: 'Zorbalık olurken kendi güvenliğimi düşünmeden müdahale ederim.',          category: 'yanlis' },
    { id: 10, text: 'Zorbalık yapan kişiyi alkışlarım, gülerim ya da desteklerim.',            category: 'yanlis' },
    { id: 11, text: 'Olanları öğretmene söylemem çünkü ispiyonculuk sanırım.',                category: 'yanlis' },
    { id: 12, text: 'Zorbalığa uğrayan kişiyi görmezden gelirim.',                            category: 'yanlis' },
    { id: 13, text: 'Zorbalık yapan kişiyi uyarmaktan korkarım ve hiçbir şey yapmam.',        category: 'yanlis' },
    { id: 14, text: 'Zorbalık sırasında olay yerinde kalıp izlerim.',                         category: 'yanlis' },
];

var KAT_WORDS = [
    { id: 1,  text: 'Yumruk atmak',                 category: 'zorbalik' },
    { id: 2,  text: 'Tehdit etmek',                 category: 'zorbalik' },
    { id: 3,  text: 'Küfretmek',                    category: 'zorbalik' },
    { id: 4,  text: 'Kavga çıkarmak',               category: 'zorbalik' },
    { id: 5,  text: 'Kötü lakap takmak',            category: 'zorbalik' },
    { id: 6,  text: 'Arkadaşını itmek',             category: 'zorbalik' },
    { id: 7,  text: 'Çelme takmak',                 category: 'zorbalik' },
    { id: 8,  text: 'Yardım etmek',                 category: 'not' },
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

/* ---------- DURUM ---------- */

var score1        = 0;
var score2        = 0;
var currentSection = 1;

// Bölüm 1
var placedCards  = {};
var draggedEl    = null;

// Bölüm 2
var selectedWord = null;
var placedWords  = {};

/* ============================================================
   BÖLÜM GEÇİŞİ
   ============================================================ */

function goToSection(n) {
    var from = document.getElementById('section' + currentSection);
    var to   = document.getElementById('section' + n);

    from.classList.add('section-exit');
    setTimeout(function () {
        from.style.display = 'none';
        from.classList.remove('section-exit');

        to.style.display = 'block';
        to.classList.add('section-enter');

        requestAnimationFrame(function () {
            to.classList.add('section-enter-active');
            setTimeout(function () {
                to.classList.remove('section-enter', 'section-enter-active');
            }, 350);
        });

        currentSection = n;

        document.getElementById('stepIndicator1').className = 'step' + (n > 1 ? ' done' : ' active');
        document.getElementById('stepIndicator2').className = 'step' + (n === 2 ? ' active' : '');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 350);
}

/* ============================================================
   BÖLÜM 1 — EŞLEŞTİRME (sürükle-bırak)
   ============================================================ */

function buildCards() {
    var pool = document.getElementById('itemsPool');
    pool.innerHTML = '';
    var shuffled = CARDS.slice().sort(function () { return Math.random() - 0.5; });
    shuffled.forEach(function (card) {
        var el = document.createElement('div');
        el.className   = 'drag-item';
        el.id          = 'card_' + card.id;
        el.draggable   = true;
        el.dataset.id  = card.id;
        el.dataset.cat = card.category;
        el.textContent = card.text;
        el.addEventListener('dragstart', dragStart);
        el.addEventListener('dragend',   dragEnd);
        el.addEventListener('touchstart', touchStart, { passive: true });
        el.addEventListener('touchend',   touchEnd,   { passive: false });
        pool.appendChild(el);
    });
}

function dragStart(e) {
    draggedEl = this;
    this.classList.add('dragging');
    e.dataTransfer.setData('text/plain', this.id);
    e.dataTransfer.effectAllowed = 'move';
}

function dragEnd() {
    this.classList.remove('dragging');
    draggedEl = null;
}

function allowDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function drop(e, targetCat) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    var cardId = parseInt(e.dataTransfer.getData('text/plain').replace('card_', ''));
    var cardEl = document.getElementById('card_' + cardId);
    if (!cardEl) return;
    var targetContainer = document.getElementById(targetCat === 'dogru' ? 'droppedDogru' : 'droppedYanlis');
    if (placedCards[cardId] === targetCat) return;
    targetContainer.appendChild(cardEl);
    placedCards[cardId] = targetCat;
    updatePlacedCount();
}

/* --- Touch sürükle-bırak --- */
var touchCard = null, touchClone = null, touchOffX, touchOffY;

function touchStart(e) {
    touchCard = this;
    var touch = e.touches[0], rect = this.getBoundingClientRect();
    touchOffX = touch.clientX - rect.left;
    touchOffY = touch.clientY - rect.top;
    touchClone = this.cloneNode(true);
    touchClone.style.cssText = 'position:fixed;z-index:9999;opacity:0.85;pointer-events:none;width:' +
        rect.width + 'px;transform:rotate(2deg);box-shadow:0 12px 30px rgba(0,0,0,0.2);';
    document.body.appendChild(touchClone);
    moveTouchClone(touch);
    document.addEventListener('touchmove', onTouchMove, { passive: false });
    document.addEventListener('touchend',  onTouchEndGlobal);
}

function touchEnd() {}

function onTouchMove(e) {
    e.preventDefault();
    moveTouchClone(e.touches[0]);
}

function moveTouchClone(touch) {
    if (!touchClone) return;
    touchClone.style.left = (touch.clientX - touchOffX) + 'px';
    touchClone.style.top  = (touch.clientY - touchOffY) + 'px';
}

function onTouchEndGlobal(e) {
    if (!touchCard || !touchClone) return;
    var touch = e.changedTouches[0];
    var targetZone = null;
    document.elementsFromPoint(touch.clientX, touch.clientY).forEach(function (el) {
        if (el.classList.contains('zone-dogru'))  targetZone = 'dogru';
        if (el.classList.contains('zone-yanlis')) targetZone = 'yanlis';
    });
    if (targetZone) {
        var cardId = parseInt(touchCard.id.replace('card_', ''));
        document.getElementById(targetZone === 'dogru' ? 'droppedDogru' : 'droppedYanlis').appendChild(touchCard);
        placedCards[cardId] = targetZone;
        updatePlacedCount();
    }
    touchClone.remove();
    touchClone = null;
    touchCard  = null;
    document.removeEventListener('touchmove', onTouchMove);
    document.removeEventListener('touchend',  onTouchEndGlobal);
}

function updatePlacedCount() {
    var count = Object.keys(placedCards).length;
    document.getElementById('placedCount').textContent    = count;
    document.getElementById('placedCountBar').textContent = count;
    document.getElementById('progressBar').style.width    = (count / 14 * 100) + '%';
}

function checkAllEsles() {
    score1 = 0;
    var allPlaced = true;

    CARDS.forEach(function (card) {
        var cardEl = document.getElementById('card_' + card.id);
        if (!cardEl) return;
        if (!placedCards[card.id]) { allPlaced = false; return; }
        if (placedCards[card.id] === card.category) {
            score1 += 10;
            cardEl.classList.remove('wrong-placed');
            cardEl.classList.add('correct-placed');
        } else {
            cardEl.classList.remove('correct-placed');
            cardEl.classList.add('wrong-placed');
        }
    });

    document.getElementById('scoreDisplay').textContent    = score1;
    document.getElementById('scoreDisplayBar').textContent = score1;

    if (!allPlaced) {
        alert('Lütfen tüm kartları bir kutuya sürükleyin!');
        return;
    }

    var pct = score1 / 140 * 100;
    var emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (pct === 100)   { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (pct >= 70){ emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (pct >= 40){ emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.getElementById('sectionDoneEmoji').textContent = emoji;
    document.getElementById('sectionDoneMsg').textContent   = msg;
    document.getElementById('sectionDoneScore').textContent = score1;
    document.getElementById('sectionDoneOverlay').classList.add('show');

    var ss = document.getElementById('sectionSaveStatus');
    ss.textContent = '';
    if (typeof saveScore === 'function') {
        saveScore(2, score1, 140, function (data) {
            if (data && data.success) {
                ss.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
            } else if (data && data.login_required) {
                ss.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
            }
        });
    }
}

/* ============================================================
   BÖLÜM 2 — KATEGORİ (tıkla-yerleştir)
   ============================================================ */

function buildWordChips() {
    var container = document.getElementById('wordChips');
    container.innerHTML = '';
    var shuffled = KAT_WORDS.slice().sort(function () { return Math.random() - 0.5; });
    shuffled.forEach(function (word) {
        var chip = document.createElement('div');
        chip.className   = 'word-chip';
        chip.id          = 'chip_' + word.id;
        chip.dataset.id  = word.id;
        chip.textContent = word.text;
        chip.setAttribute('role', 'button');
        chip.setAttribute('tabindex', '0');
        chip.addEventListener('click', function () { selectWord(word.id); });
        chip.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' || e.key === ' ') selectWord(word.id);
        });
        container.appendChild(chip);
    });
}

function selectWord(wordId) {
    if (placedWords[wordId]) return;
    if (selectedWord !== null) {
        var prev = document.getElementById('chip_' + selectedWord);
        if (prev) prev.classList.remove('selected');
    }
    selectedWord = wordId;
    var chip = document.getElementById('chip_' + wordId);
    chip.classList.add('selected');
    var word = KAT_WORDS.filter(function (w) { return w.id === wordId; })[0];
    document.getElementById('selectedHint').innerHTML =
        '<div class="selected-hint-inner">' +
        '<span class="material-symbols-outlined">touch_app</span>' +
        '"' + word.text + '" seçildi → Şimdi bir kutuya tıkla!' +
        '</div>';
}

function placeSelected(targetCat) {
    if (selectedWord === null) {
        document.getElementById('selectedHint').innerHTML =
            '<div class="selected-hint-inner" style="background:var(--tertiary);color:var(--on-tertiary)">' +
            '<span class="material-symbols-outlined">warning</span>Önce bir kelimeye tıkla!</div>';
        return;
    }
    var wordId = selectedWord;
    var word   = KAT_WORDS.filter(function (w) { return w.id === wordId; })[0];
    var chip   = document.getElementById('chip_' + wordId);
    chip.classList.remove('selected');
    chip.classList.add('placed');

    var targetContainer = document.getElementById(targetCat === 'zorbalik' ? 'chipsZorbalik' : 'chipsNot');
    var newChip = document.createElement('div');
    newChip.className   = 'word-chip';
    newChip.id          = 'placed_' + wordId;
    newChip.textContent = word.text;
    if (targetCat === 'zorbalik') {
        newChip.style.cssText = 'background:rgba(186,26,26,0.1);border-color:var(--error);color:var(--on-error-container)';
    } else {
        newChip.style.cssText = 'background:rgba(58,106,0,0.1);border-color:var(--secondary);color:var(--on-secondary-fixed-variant)';
    }
    targetContainer.appendChild(newChip);
    placedWords[wordId] = targetCat;
    selectedWord = null;
    document.getElementById('selectedHint').innerHTML = '';
    updateKategoriProgress();
}

function updateKategoriProgress() {
    var count = Object.keys(placedWords).length;
    document.getElementById('placedCountK').textContent    = count;
    document.getElementById('placedCountBarK').textContent = count;
    document.getElementById('progressBarK').style.width   = (count / 17 * 100) + '%';
    if (count === 17) setTimeout(checkAllKategori, 500);
}

function checkAllKategori() {
    var placed = Object.keys(placedWords).length;
    if (placed < 17) {
        document.getElementById('selectedHint').innerHTML =
            '<div class="selected-hint-inner" style="background:var(--tertiary);color:var(--on-tertiary)">' +
            '<span class="material-symbols-outlined">warning</span>Lütfen tüm kelimeleri yerleştir! (' + placed + '/17)</div>';
        return;
    }
    score2 = 0;
    KAT_WORDS.forEach(function (word) {
        var placedChip = document.getElementById('placed_' + word.id);
        if (!placedChip) return;
        if (placedWords[word.id] === word.category) {
            score2 += 10;
            placedChip.style.background  = 'rgba(58,106,0,0.15)';
            placedChip.style.borderColor = 'var(--secondary)';
            placedChip.style.color       = 'var(--on-secondary-fixed-variant)';
            placedChip.style.fontWeight  = '700';
        } else {
            placedChip.style.background  = 'rgba(186,26,26,0.12)';
            placedChip.style.borderColor = 'var(--error)';
            placedChip.style.color       = 'var(--on-error-container)';
            placedChip.style.fontWeight  = '700';
        }
    });

    document.getElementById('scoreDisplayK').textContent    = score2;
    document.getElementById('scoreDisplayBarK').textContent = score2;

    var total = score1 + score2;
    document.getElementById('finalScore1').textContent     = score1;
    document.getElementById('finalScore2').textContent     = score2;
    document.getElementById('finalScoreTotal').textContent = total;

    var pct = total / 310 * 100;
    var emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (pct === 100)   { emoji = '🏆'; msg = 'Mükemmel! Her iki bölümü de doğru tamamladın!'; }
    else if (pct >= 70){ emoji = '🎉'; msg = 'Harika! Çok iyi bir toplam skor!'; }
    else if (pct >= 40){ emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.getElementById('resultFinalEmoji').textContent = emoji;
    document.getElementById('resultFinalMsg').textContent   = msg;
    document.getElementById('resultOverlay').classList.add('show');

    var ss2 = document.getElementById('saveStatus2');
    ss2.textContent = '';
    if (typeof saveScore === 'function') {
        saveScore(3, score2, 170, function (data) {
            if (data && data.success) {
                ss2.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
            } else if (data && data.login_required) {
                ss2.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
            }
        });
    }
}

/* ============================================================
   ORTAK checkAll — HTML onclick'lerden çağrılır
   ============================================================ */

function checkAll() {
    if (currentSection === 1) checkAllEsles();
    else if (currentSection === 2) checkAllKategori();
}

/* ============================================================
   INIT
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    buildCards();
    buildWordChips();

    document.querySelectorAll('#section1 .drop-zone').forEach(function (zone) {
        zone.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });
    });

    document.getElementById('zoneZorbalik').addEventListener('keypress', function (e) {
        if (e.key === 'Enter' || e.key === ' ') placeSelected('zorbalik');
    });
    document.getElementById('zoneNot').addEventListener('keypress', function (e) {
        if (e.key === 'Enter' || e.key === ' ') placeSelected('not');
    });

    document.getElementById('goToSection2Btn').addEventListener('click', function () {
        document.getElementById('sectionDoneOverlay').classList.remove('show');
        goToSection(2);
    });
});
