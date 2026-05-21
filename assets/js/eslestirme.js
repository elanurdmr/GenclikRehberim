/* ===========================================================
   EŞLEŞTİRME — Üç Bölümlü Oyun
   Bölüm 1: Sürükle-bırak eşleştirme  (activityId=2, max 140)
   Bölüm 2: Boşluk doldurma            (activityId=6, max 80)
   Bölüm 3: Kelime kategori             (activityId=3, max 170)
   Toplam: 390 puan
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
    { id: 11, text: 'Olanları öğretmene söylemem çünkü ispiyonculuk sanırım.',                 category: 'yanlis' },
    { id: 12, text: 'Zorbalığa uğrayan kişiyi görmezden gelirim.',                            category: 'yanlis' },
    { id: 13, text: 'Zorbalık yapan kişiyi uyarmaktan korkarım ve hiçbir şey yapmam.',        category: 'yanlis' },
    { id: 14, text: 'Zorbalık sırasında olay yerinde kalıp izlerim.',                         category: 'yanlis' },
];

var FILL_SENTENCES = [
    { id: 1, template: 'Zorbalığa uğradığımda bir ___ yetişkinden yardım isterim.',
      blank: 'güvendiğim', options: ['güvendiğim', 'korktuğum', 'tanımadığım', 'gördüğüm'] },
    { id: 2, template: 'Arkadaşım zorbalığa uğrarsa ona ___ olurum.',
      blank: 'destek', options: ['destek', 'engel', 'rakip', 'yabancı'] },
    { id: 3, template: 'Zorbalık yapmak başkasına ___ zarar verir.',
      blank: 'büyük', options: ['büyük', 'küçük', 'geçici', 'önemsiz'] },
    { id: 4, template: 'Farklılıkları ___ ile karşılamalıyız.',
      blank: 'saygı', options: ['saygı', 'öfke', 'korku', 'nefret'] },
    { id: 5, template: 'Birinin duygularını anlamaya çalışmak ___ olarak adlandırılır.',
      blank: 'empati', options: ['empati', 'zorbalık', 'sempati', 'kibir'] },
    { id: 6, template: 'Zorbalığa sessiz kalmak onu ___ etmek demektir.',
      blank: 'desteklemek', options: ['desteklemek', 'durdurmak', 'azaltmak', 'görmezden gelmek'] },
    { id: 7, template: 'Güvenli bir ortam için herkes ___ göstermelidir.',
      blank: 'sorumluluk', options: ['sorumluluk', 'direnç', 'cesaret', 'yetenek'] },
    { id: 8, template: 'Zorba davranışı durdurmanın en etkili yolu onu ___ bildirmektir.',
      blank: 'yetkililere', options: ['yetkililere', 'kimseye', 'arkadaşa', 'aileye'] },
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

/* Etkinlik ID'leri PHP'den (window.GAME_CONFIG) gelir; sabit kodlanmaz. */
var ESL_CFG      = window.GAME_CONFIG || {};
var ACT_MATCH    = ESL_CFG.activityMatch    || 2;
var ACT_FILL     = ESL_CFG.activityFill     || 6;
var ACT_CATEGORY = ESL_CFG.activityCategory || 3;

var score1         = 0;
var score3         = 0;
var fillScore      = 0;
var currentSection = 1;

// Bölüm 1
var placedCards    = {};
var draggedEl      = null;
var cardQueue      = [];
var currentCardIdx = 0;
var activeCard     = null;
var dogruCount     = 0;
var yanlisCount    = 0;

// Bölüm 2
var fillCurrentIdx     = 0;
var fillSelectedOption = null;
var fillAnsweredCount  = 0;

// Bölüm 3
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
        document.getElementById('stepIndicator2').className = 'step' + (n === 2 ? ' active' : (n > 2 ? ' done' : ''));
        document.getElementById('stepIndicator3').className = 'step' + (n === 3 ? ' active' : '');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 350);
}

/* ============================================================
   BÖLÜM 1 — EŞLEŞTİRME (flashcard tek kart akışı)
   ============================================================ */

function buildCards() {
    cardQueue = CARDS.slice().sort(function () { return Math.random() - 0.5; });
    currentCardIdx = 0;
    placedCards    = {};
    dogruCount     = 0;
    yanlisCount    = 0;
    document.getElementById('dogru-count').textContent  = 0;
    document.getElementById('yanlis-count').textContent = 0;
    document.getElementById('section1Done').style.display = 'none';
    showNextCard();
}

function showNextCard() {
    var wrap = document.getElementById('flashcardWrap');
    wrap.innerHTML = '';

    if (currentCardIdx >= cardQueue.length) {
        document.getElementById('cardCounter').textContent = cardQueue.length + '/' + cardQueue.length;
        document.getElementById('section1Done').style.display = 'block';
        activeCard = null;
        return;
    }

    document.getElementById('cardCounter').textContent = (currentCardIdx + 1) + '/' + cardQueue.length;

    var card = cardQueue[currentCardIdx];
    var el = document.createElement('div');
    el.className   = 'flashcard flashcard-enter';
    el.id          = 'card_' + card.id;
    el.draggable   = true;
    el.dataset.id  = String(card.id);
    el.dataset.cat = card.category;
    el.textContent = card.text;
    el.addEventListener('dragstart', dragStart);
    el.addEventListener('dragend',   dragEnd);
    el.addEventListener('touchstart', touchStart, { passive: true });
    el.addEventListener('touchend',   touchEnd,   { passive: false });
    wrap.appendChild(el);
    activeCard = el;
}

function placeCard(targetCat) {
    if (!activeCard) return;
    var cardId = parseInt(activeCard.dataset.id);
    if (placedCards[cardId]) return;

    var exitClass = targetCat === 'dogru' ? 'flashcard-exit-right' : 'flashcard-exit-left';
    activeCard.classList.remove('flashcard-enter');
    activeCard.classList.add(exitClass);

    placedCards[cardId] = targetCat;
    if (targetCat === 'dogru') {
        dogruCount++;
        document.getElementById('dogru-count').textContent = dogruCount;
    } else {
        yanlisCount++;
        document.getElementById('yanlis-count').textContent = yanlisCount;
    }
    document.getElementById('scoreDisplay').textContent = Object.keys(placedCards).length * 5;

    currentCardIdx++;
    setTimeout(showNextCard, 320);
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
    placeCard(targetCat);
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
    if (targetZone) placeCard(targetZone);
    touchClone.remove();
    touchClone = null;
    touchCard  = null;
    document.removeEventListener('touchmove', onTouchMove);
    document.removeEventListener('touchend',  onTouchEndGlobal);
}

function updatePlacedCount() {} // no-op — counts updated inside placeCard

function checkAllEsles() {
    score1 = 0;
    CARDS.forEach(function (card) {
        if (placedCards[card.id] === card.category) score1 += 10;
    });

    document.getElementById('scoreDisplay').textContent = score1;

    var pct = score1 / 140 * 100;
    var emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (pct === 100)    { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (pct >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (pct >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.getElementById('sectionDoneEmoji').textContent = emoji;
    document.getElementById('sectionDoneMsg').textContent   = msg;
    document.getElementById('sectionDoneScore').textContent = score1;
    document.getElementById('sectionDoneOverlay').classList.add('show');

    var ss = document.getElementById('sectionSaveStatus');
    ss.textContent = '';
    if (typeof saveScore === 'function') {
        saveScore(ACT_MATCH, score1, 140, function (data) {
            if (data && data.success) {
                ss.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
            } else if (data && data.login_required) {
                ss.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
            }
        });
    }
}

/* ============================================================
   BÖLÜM 2 — BOŞLUK DOLDURMA (tek soru akışı, seçenek chipli)
   ============================================================ */

function buildFillGame() {
    fillCurrentIdx     = 0;
    fillScore          = 0;
    fillSelectedOption = null;
    fillAnsweredCount  = 0;
    document.getElementById('fillScoreDisplay').textContent    = 0;
    document.getElementById('fillQuestionCounter').textContent = '1/' + FILL_SENTENCES.length;
    document.getElementById('fillProgressText').textContent    = 0;
    document.getElementById('fillProgressBar').style.width     = '0%';
    document.getElementById('fillScoreBar').textContent        = 0;
    var fb = document.getElementById('fillFeedback');
    fb.textContent = '';
    fb.className   = 'fill-feedback';
    showFillQuestion();
}

function showFillQuestion() {
    var q = FILL_SENTENCES[fillCurrentIdx];
    document.getElementById('fillQuestionCounter').textContent = (fillCurrentIdx + 1) + '/' + FILL_SENTENCES.length;

    var parts = q.template.split('___');
    var card = document.getElementById('fillQuestionCard');
    card.innerHTML =
        '<div class="question-number">Soru ' + (fillCurrentIdx + 1) + ' / ' + FILL_SENTENCES.length + '</div>' +
        '<div class="fill-sentence-display">' +
        '<span class="fill-text-part">' + parts[0] + '</span>' +
        '<span class="fill-blank-slot" id="fillBlankSlot">___</span>' +
        (parts[1] ? '<span class="fill-text-part">' + parts[1] + '</span>' : '') +
        '</div>';
    card.className = 'fill-question-card';

    var optContainer = document.getElementById('fillOptions');
    optContainer.innerHTML = '';
    var opts = q.options.slice().sort(function () { return Math.random() - 0.5; });
    opts.forEach(function (opt) {
        var chip = document.createElement('button');
        chip.className   = 'fill-option-chip';
        chip.textContent = opt;
        chip.addEventListener('click', function () { selectFillOption(opt, chip); });
        optContainer.appendChild(chip);
    });

    var fb = document.getElementById('fillFeedback');
    fb.textContent = '';
    fb.className   = 'fill-feedback';
    fillSelectedOption = null;
}

function selectFillOption(opt, chipEl) {
    document.querySelectorAll('.fill-option-chip').forEach(function (c) {
        c.classList.remove('selected');
    });
    chipEl.classList.add('selected');
    fillSelectedOption = opt;
    var slot = document.getElementById('fillBlankSlot');
    slot.textContent = opt;
    slot.classList.add('has-value');
}

function checkFillAnswer() {
    if (!fillSelectedOption) {
        var fb = document.getElementById('fillFeedback');
        fb.textContent = 'Önce bir seçenek seç!';
        fb.className   = 'fill-feedback wrong-fb';
        return;
    }
    var q = FILL_SENTENCES[fillCurrentIdx];
    var fb = document.getElementById('fillFeedback');
    document.querySelectorAll('.fill-option-chip').forEach(function (c) { c.disabled = true; });

    if (fillSelectedOption === q.blank) {
        fillScore += 10;
        document.querySelectorAll('.fill-option-chip').forEach(function (c) {
            if (c.textContent === q.blank) c.classList.add('correct');
        });
        fb.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;vertical-align:middle">check_circle</span> Doğru! +10 puan';
        fb.className = 'fill-feedback correct-fb';
    } else {
        document.querySelectorAll('.fill-option-chip').forEach(function (c) {
            if (c.textContent === fillSelectedOption) c.classList.add('wrong');
            if (c.textContent === q.blank) c.classList.add('correct');
        });
        fb.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;vertical-align:middle">cancel</span> Yanlış! Doğru cevap: <strong>' + q.blank + '</strong>';
        fb.className = 'fill-feedback wrong-fb';
    }

    fillAnsweredCount++;
    document.getElementById('fillScoreDisplay').textContent    = fillScore;
    document.getElementById('fillProgressText').textContent    = fillAnsweredCount;
    document.getElementById('fillProgressBar').style.width     = (fillAnsweredCount / FILL_SENTENCES.length * 100) + '%';
    document.getElementById('fillScoreBar').textContent        = fillScore;

    setTimeout(function () {
        fillCurrentIdx++;
        fillSelectedOption = null;
        if (fillCurrentIdx >= FILL_SENTENCES.length) {
            finishFillGame();
        } else {
            showFillQuestion();
        }
    }, 1400);
}

function checkFillGame() { checkFillAnswer(); } // HTML onclick alias

function fillHint() {
    var q = FILL_SENTENCES[fillCurrentIdx];
    document.querySelectorAll('.fill-option-chip').forEach(function (c) {
        if (c.textContent === q.blank && !c.disabled) {
            c.style.boxShadow   = '0 0 0 3px var(--tertiary)';
            c.style.borderColor = 'var(--tertiary)';
        }
    });
}

function finishFillGame() {
    var pct = fillScore / 80 * 100;
    var emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (pct === 100)    { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (pct >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (pct >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.getElementById('fillDoneEmoji').textContent = emoji;
    document.getElementById('fillDoneMsg').textContent   = msg;
    document.getElementById('fillDoneScore').textContent = fillScore;
    document.getElementById('fillDoneOverlay').classList.add('show');

    var ss = document.getElementById('fillSaveStatus');
    ss.textContent = '';
    if (typeof saveScore === 'function') {
        saveScore(ACT_FILL, fillScore, 80, function (data) {
            if (data && data.success) {
                ss.innerHTML = '<span class="material-symbols-outlined" style="font-variation-settings:\'FILL\' 1;color:var(--secondary);font-size:16px">check_circle</span> Puan kaydedildi!';
            } else if (data && data.login_required) {
                ss.innerHTML = '<a href="/genclik-rehberim/girisyap.php" style="color:var(--primary);font-weight:700">Puanı kaydetmek için giriş yap</a>';
            }
        });
    }
}

/* ============================================================
   BÖLÜM 3 — KATEGORİ (tıkla-yerleştir)
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
    score3 = 0;
    KAT_WORDS.forEach(function (word) {
        var placedChip = document.getElementById('placed_' + word.id);
        if (!placedChip) return;
        if (placedWords[word.id] === word.category) {
            score3 += 10;
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

    document.getElementById('scoreDisplayK').textContent    = score3;
    document.getElementById('scoreDisplayBarK').textContent = score3;

    var total = score1 + fillScore + score3;
    document.getElementById('finalScore1').textContent     = score1;
    document.getElementById('finalScore2').textContent     = fillScore;
    document.getElementById('finalScore3').textContent     = score3;
    document.getElementById('finalScoreTotal').textContent = total;

    var pct = total / 390 * 100;
    var emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (pct === 100)    { emoji = '🏆'; msg = 'Mükemmel! Her üç bölümü de doğru tamamladın!'; }
    else if (pct >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir toplam skor!'; }
    else if (pct >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.getElementById('resultFinalEmoji').textContent = emoji;
    document.getElementById('resultFinalMsg').textContent   = msg;
    document.getElementById('resultOverlay').classList.add('show');

    var ss2 = document.getElementById('saveStatus2');
    ss2.textContent = '';
    if (typeof saveScore === 'function') {
        saveScore(ACT_CATEGORY, score3, 170, function (data) {
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
    else if (currentSection === 2) checkFillAnswer();
    else if (currentSection === 3) checkAllKategori();
}

/* ============================================================
   INIT
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    buildCards();
    buildWordChips();

    document.querySelectorAll('#section1 .flashcard-zone').forEach(function (zone) {
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

    document.getElementById('checkFillBtn').addEventListener('click', checkFillAnswer);
    document.getElementById('fillHintBtn').addEventListener('click', fillHint);

    document.getElementById('goToSection2Btn').addEventListener('click', function () {
        document.getElementById('sectionDoneOverlay').classList.remove('show');
        buildFillGame();
        goToSection(2);
    });

    document.getElementById('goToSection3Btn').addEventListener('click', function () {
        document.getElementById('fillDoneOverlay').classList.remove('show');
        goToSection(3);
    });
});
