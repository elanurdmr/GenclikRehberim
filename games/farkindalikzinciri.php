<?php
/**
 * farkindalikzinciri.php — Kelime Türetme Zinciri Oyunu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * Bilgisayar rastgele bir kelimeyle başlar (örn. EMPATİ).
 * Oyuncu son harfle başlayan, en az 5 harfli, temaya uygun yeni kelimeler girer.
 * 90 saniye içinde en fazla 10 doğru kelime = 100 puan.
 * normalizeTurkish mantığı bulmaca.js ile aynıdır.
 */

$pageTitle = 'Farkındalık Zinciri';
require_once '../includes/header.php';
requireLogin();
$activityId = getActivityId('farkindalikzinciri');
?>

<style>
/* ── Farkındalık Zinciri — Oyun Özel Stiller ── */

/* Oyun alanı düzeni */
.chain-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 768px) {
    .chain-layout { grid-template-columns: 1fr; }
    .chain-sidebar { order: -1; }
}

/* Zamanlayıcı halkası */
.timer-ring-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .4rem;
    margin-bottom: 1.5rem;
}
.timer-ring {
    position: relative;
    width: 84px; height: 84px;
}
.timer-ring svg { transform: rotate(-90deg); }
.timer-ring .ring-bg  { fill: none; stroke: var(--surface-variant); stroke-width: 7; }
.timer-ring .ring-arc {
    fill: none;
    stroke: var(--primary);
    stroke-width: 7;
    stroke-linecap: round;
    stroke-dasharray: 220.9; /* 2π×35 */
    stroke-dashoffset: 0;
    transition: stroke-dashoffset .9s linear, stroke .5s;
}
.ring-arc.danger { stroke: #e53935; }
.timer-value {
    position: absolute;
    inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: 800; color: var(--on-surface);
}

/* Zincir geçmişi */
.chain-history {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem .75rem;
    padding: 1.25rem;
    background: var(--surface-container);
    border-radius: 14px;
    min-height: 80px;
    align-content: flex-start;
}
.chain-word {
    background: var(--primary-container);
    color: var(--on-primary-container);
    border-radius: 20px;
    padding: .3rem .85rem;
    font-size: .875rem;
    font-weight: 600;
    display: flex; align-items: center; gap: .35rem;
    animation: popIn .25s ease;
}
.chain-word .cw-arrow {
    font-size: 1rem;
    color: var(--primary);
    font-weight: 900;
}
.chain-word.computer {
    background: var(--secondary-container);
    color: var(--on-secondary-container);
}
.chain-word.correct  { background: #e8f5e9; color: #2e7d32; }
.chain-word.wrong    { background: #ffebee; color: #c62828; }

/* Mevcut harf göstergesi */
.current-letter-badge {
    background: var(--primary);
    color: var(--on-primary);
    border-radius: 50%;
    width: 52px; height: 52px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; font-weight: 900;
    margin: 0 auto 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    transition: transform .2s;
}

/* Girdi alanı */
.chain-input-wrap {
    display: flex;
    gap: .75rem;
    margin-bottom: 1rem;
}
.chain-input {
    flex: 1;
    padding: .75rem 1rem;
    border: 2px solid var(--outline-variant);
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    background: var(--surface);
    color: var(--on-surface);
    font-family: inherit;
    transition: border-color .15s;
    outline: none;
}
.chain-input:focus  { border-color: var(--primary); }
.chain-input.error  { border-color: #e53935; animation: shake .3s ease; }
.chain-input.valid  { border-color: #43a047; }

/* Hata mesajı */
.chain-error-msg {
    min-height: 1.5em;
    font-size: .875rem;
    color: #c62828;
    font-weight: 500;
    margin-bottom: .75rem;
    transition: opacity .2s;
}

/* Kenar çubuğu istatistikleri */
.chain-stat-box {
    background: var(--surface-container);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1rem;
}
.chain-stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .9rem;
    padding: .3rem 0;
    border-bottom: 1px solid var(--outline-variant);
}
.chain-stat-row:last-child { border-bottom: none; }
.csr-label { color: var(--on-surface-variant); }
.csr-value { font-weight: 700; color: var(--on-surface); }

/* Puan animasyonu */
.score-popup {
    position: fixed;
    pointer-events: none;
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--primary);
    animation: floatUp .9s ease forwards;
    z-index: 999;
}
@keyframes floatUp {
    from { opacity: 1; transform: translateY(0); }
    to   { opacity: 0; transform: translateY(-60px); }
}

/* Kelime listesi (yardım) */
.word-hint-toggle {
    font-size: .8rem;
    color: var(--on-surface-variant);
    cursor: pointer;
    text-decoration: underline;
    background: none;
    border: none;
    font-family: inherit;
    padding: 0;
    margin-bottom: .5rem;
    display: block;
}
.word-hint-list {
    display: none;
    flex-wrap: wrap;
    gap: .35rem;
    margin-bottom: 1rem;
    max-height: 120px;
    overflow-y: auto;
}
.word-hint-list.open { display: flex; }
.word-hint-pill {
    background: var(--surface-variant);
    color: var(--on-surface-variant);
    border-radius: 99px;
    padding: .2rem .65rem;
    font-size: .75rem;
    font-weight: 600;
}

/* Önceki kelimeler paneli */
.used-words-panel {
    background: var(--surface-container);
    border-radius: 14px;
    padding: 1rem;
}
.used-words-panel h4 { font-size: .875rem; font-weight: 700; margin: 0 0 .5rem; color: var(--on-surface-variant); }
.used-list { list-style: none; margin: 0; padding: 0; font-size: .85rem; max-height: 180px; overflow-y: auto; }
.used-list li { padding: .2rem 0; border-bottom: 1px solid var(--outline-variant); display: flex; justify-content: space-between; }
.used-list li:last-child { border-bottom: none; }

@keyframes popIn {
    from { transform: scale(.7); opacity: 0; }
    to   { transform: scale(1);  opacity: 1; }
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    25%      { transform: translateX(-8px); }
    75%      { transform: translateX(8px); }
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
</style>

<main class="game-page" id="chainPage">
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Başlık -->
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">link</span>
                Kelime Zinciri · Etkinlik 6
            </div>
            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>Farkındalık Zinciri</h1>
                    <p>Verilen kelimenin <strong>son harfiyle</strong> başlayan, en az <strong>5 harfli</strong> yeni bir kelime gir. Süren dolmadan zinciri uzat!</p>
                </div>
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">Kelime</span>
                        <span class="game-stat-value text-primary" id="wordCount">0/10</span>
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

        <!-- ── BAŞLANGIÇ EKRANI ── -->
        <div id="screenIntro" style="text-align:center;padding:2rem 0">
            <div style="font-size:4rem;margin-bottom:1rem">🔗</div>
            <h2 style="margin:0 0 .75rem">Zincire hazır mısın?</h2>
            <p style="color:var(--on-surface-variant);max-width:480px;margin:0 auto 2rem">
                Bilgisayar bir farkındalık kelimesi söyler. Sen de o kelimenin <strong>son harfiyle</strong> başlayan, en az <strong>5 harfli</strong>, zorbalık ve arkadaşlık temalı bir kelime girersin. <strong>120 saniyede</strong> en uzun zinciri kur!
            </p>
            <button class="btn btn-primary btn-lg" onclick="startGame()">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">play_arrow</span>
                Oyunu Başlat
            </button>
        </div>

        <!-- ── OYUN EKRANI ── -->
        <div id="screenGame" style="display:none">
            <div class="chain-layout">

                <!-- Ana oyun alanı -->
                <div>
                    <!-- Mevcut harf -->
                    <div style="text-align:center;margin-bottom:1.5rem">
                        <p style="font-size:.85rem;color:var(--on-surface-variant);margin:0 0 .5rem">Şu an girilmesi gereken harf</p>
                        <div class="current-letter-badge" id="currentLetterBadge">?</div>
                        <p style="font-size:.8rem;color:var(--on-surface-variant);margin:.25rem 0 0">ile başlayan bir kelime gir</p>
                    </div>

                    <!-- Girdi -->
                    <div class="chain-input-wrap">
                        <input
                            type="text"
                            id="wordInput"
                            class="chain-input"
                            placeholder="Kelimeni yaz..."
                            maxlength="20"
                            autocomplete="off"
                            spellcheck="false"
                            aria-label="Kelime giriş alanı"
                            disabled
                        >
                        <button class="btn btn-primary" id="submitBtn" onclick="submitWord()" disabled aria-label="Kelimeyi gönder">
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </div>

                    <!-- Hata / Bilgi mesajı -->
                    <div class="chain-error-msg" id="chainErrorMsg"></div>

                    <!-- Yardım: Kelime listesi -->
                    <button class="word-hint-toggle" onclick="toggleHints()" id="hintToggleBtn">
                        Konu hakkında kelime önerileri gör ▾
                    </button>
                    <div class="word-hint-list" id="hintList"></div>

                    <!-- Zincir geçmişi -->
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--on-surface-variant);margin:1rem 0 .5rem">Zincir</h3>
                    <div class="chain-history" id="chainHistory" aria-live="polite" aria-label="Zincir kelimeler"></div>
                </div>

                <!-- Kenar çubuğu -->
                <div class="chain-sidebar">
                    <!-- Zamanlayıcı -->
                    <div class="timer-ring-wrap">
                        <div class="timer-ring" aria-label="Kalan süre">
                            <svg width="84" height="84" viewBox="0 0 84 84">
                                <circle class="ring-bg"  cx="42" cy="42" r="35"/>
                                <circle class="ring-arc" cx="42" cy="42" r="35" id="timerArc"/>
                            </svg>
                            <div class="timer-value" id="timerVal">120</div>
                        </div>
                        <span style="font-size:.8rem;color:var(--on-surface-variant)">saniye</span>
                    </div>

                    <!-- İstatistikler -->
                    <div class="chain-stat-box">
                        <div class="chain-stat-row">
                            <span class="csr-label">Doğru kelime</span>
                            <span class="csr-value" id="statCorrect">0</span>
                        </div>
                        <div class="chain-stat-row">
                            <span class="csr-label">Puan</span>
                            <span class="csr-value" id="statScore">0</span>
                        </div>
                        <div class="chain-stat-row">
                            <span class="csr-label">Kalan hak</span>
                            <span class="csr-value" id="statRemaining">10</span>
                        </div>
                    </div>

                    <!-- Kullanılan kelimeler -->
                    <div class="used-words-panel">
                        <h4>Kullanılan Kelimeler</h4>
                        <ul class="used-list" id="usedList"></ul>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- /game-wrapper -->
</main>

<!-- Sonuç Modalı -->
<div class="result-overlay" id="resultOverlay" role="dialog" aria-modal="true" style="display:none">
    <div class="result-card">
        <div class="result-emoji" id="resultEmoji">🏆</div>
        <h2>Süre Doldu!</h2>
        <p id="resultMsg"></p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 100 puan</div>
        <div id="saveStatus" style="margin-bottom:1rem;font-size:.85rem;color:var(--on-surface-variant)"></div>
        <div class="result-buttons">
            <a href="/genclik-rehberim/games/farkindalikzinciri.php" class="btn btn-primary">
                <span class="material-symbols-outlined">refresh</span> Tekrar Oyna
            </a>
            <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                <span class="material-symbols-outlined">bar_chart</span> Panele Git
            </a>
        </div>
    </div>
</div>

<script>
/* ============================================================
   FARKINDALIK ZİNCİRİ — Kelime Türetme Oyunu
   ============================================================ */
window.GAME_CONFIG = { activityId: <?= (int)$activityId ?> };

const ACTIVITY_ID  = window.GAME_CONFIG.activityId;
const SAVE_URL     = '/genclik-rehberim/games/save_score.php';
const MAX_WORDS    = 10;
const MAX_SCORE    = 100;
const GAME_TIME    = 120;      // saniye
const MIN_LETTERS  = 5;
const CIRCUMFERENCE = 220.9;  // 2π×35

// Bilgisayar başlangıç kelimeleri (display formu)
const START_WORDS = ['EMPATİ', 'ARKADAŞ', 'DESTEK', 'GÜVEN', 'SEVGİ'];

// Kelime listesi — normalizeTurkish uygulanmış hali (İ→I, ı→I; Ş/Ğ/Ü/Ö/Ç aynen).
// Tüm kelimeler akran zorbalığı / arkadaşlık / duygu temalı, en az 5 harf.
const WORD_LIST = new Set([
    // A
    'ADALET','ANLAYIŞ','ARKADAŞ','AFFETMEK','ANLAŞMA','ALINGANLIK',
    // B
    'BASKICI','BAĞIMSIZ','BAĞLILIK','BARIŞÇIL',
    // C
    'CESARET','CÖMERT',
    // Ç
    'ÇÖZÜM','ÇARESIZ',
    // D
    'DESTEK','DÜRÜST','DAYANIŞMA','DOSTLUK','DEĞER','DUYARSIZ',
    // E
    'EMPATI','ENDIŞE','EŞITLIK',
    // F
    'FARKINDALIK','FEDAKARLIK',
    // G
    'GÜVEN','GÜÇLÜ','GURUR','GÜVENCE','GÖZLEM',
    // H
    'HUZUR','HAYIR','HAKARET',
    // I (İ normalized → I)
    'INSAN','INANÇ','ITAAT','ITIRAZ','IYILIK','ISRAR','IYINIYET',
    // K
    'KORUMA','KABUL','KORKU','KAHRAMAN','KARDEŞLIK','KÖTÜLÜK',
    'KAVGA','KIRIKLIK','KABADAYI','KIŞKIRTMA','KORKUSUZ',
    // L
    'LIDERLIK','LÜTUF',
    // M
    'MERHAMET','MUTLULUK','MERHABA',
    // N
    'NEFES','NEZAKET','NIYET','NEFRET',
    // O
    'ONAYLAMA',
    // Ö
    'ÖZGÜVEN','ÖVÜNMEK',
    // P
    'PAYLAŞIM',
    // R
    'REDDEDMEK','RAHATSIZLIK',
    // S
    'SEVGI','SAYGI','SAVUNMA','SAĞLIK','SALDIRGAN','SORUMLULUK',
    // Ş
    'ŞEFKAT','ŞIDDET','ŞIKAYET','ŞÜPHE',
    // T
    'TEŞEKKÜR','TOLERANS','TEBESSÜM',
    // U
    'UMUTLU','UMUTSUZ','UMURSAMA',
    // Ü
    'ÜZÜNTÜ','ÜZGÜN',
    // Y
    'YARDIM','YALNIZ','YARDIMCI','YASAK',
    // Z
    'ZORBA','ZORBALIK','ZARAR',
]);

// Oyun durumu
let score          = 0;
let wordCount      = 0;
let usedWords      = new Set();
let currentLetter  = '';   // normalleştirilmiş son harf (büyük)
let timerInterval  = null;
let timeLeft       = GAME_TIME;
let gameOver       = false;
let startWordDisplay = '';

// ── normalizeTurkish (bulmaca.js ile aynı mantık) ──
function normalizeTurkish(str) {
    var s = String(str || '');
    s = s.split('i').join('I')
         .split('ı').join('I')
         .split('İ').join('I')
         .split('ş').join('Ş')
         .split('ğ').join('Ğ')
         .split('ü').join('Ü')
         .split('ö').join('Ö')
         .split('ç').join('Ç');
    return s.toUpperCase().trim();
}

// Türkçe büyük harf çevirimi (girdi görüntüleme için)
function trUpper(s) {
    return String(s || '').split('i').join('İ').split('ı').join('I').toUpperCase();
}

// ── Oyun Başlat ──
function startGame() {
    document.getElementById('screenIntro').style.display = 'none';
    document.getElementById('screenGame').style.display  = 'block';

    // Rastgele başlangıç kelimesi seç
    startWordDisplay = START_WORDS[Math.floor(Math.random() * START_WORDS.length)];
    const normStart  = normalizeTurkish(startWordDisplay);
    currentLetter    = normStart[normStart.length - 1];
    usedWords.add(normStart);

    // Zincire ekle
    addToChain(startWordDisplay, 'computer');

    // Mevcut harfi göster
    updateLetterBadge();

    // Yardım listesini doldur (ilk harfe göre)
    buildHintList();

    // Girdiyi etkinleştir
    const inp = document.getElementById('wordInput');
    inp.disabled  = false;
    document.getElementById('submitBtn').disabled = false;
    inp.focus();
    inp.addEventListener('keydown', e => { if (e.key === 'Enter') submitWord(); });
    inp.addEventListener('input',   e => { e.target.value = trUpper(e.target.value); });

    // Sayaç başlat
    startTimer();
}

// ── Kelime Gönder ──
function submitWord() {
    if (gameOver) return;

    const inp     = document.getElementById('wordInput');
    const raw     = inp.value.trim();
    const norm    = normalizeTurkish(raw);
    const errEl   = document.getElementById('chainErrorMsg');

    inp.classList.remove('error','valid');
    errEl.textContent = '';

    // Boş girdi
    if (!norm) return;

    // Minimum uzunluk
    if ([...norm].length < MIN_LETTERS) {
        showError(inp, errEl, `En az ${MIN_LETTERS} harfli bir kelime gir!`);
        return;
    }

    // İlk harf kontrolü
    if (norm[0] !== currentLetter) {
        showError(inp, errEl, `Kelime "${currentLetter}" harfiyle başlamalı!`);
        return;
    }

    // Kelime listesinde mi?
    if (!WORD_LIST.has(norm)) {
        showError(inp, errEl, 'Bu kelime listede yok. Zorbalık veya arkadaşlık temalı bir kelime dene!');
        return;
    }

    // Daha önce kullanıldı mı?
    if (usedWords.has(norm)) {
        showError(inp, errEl, 'Bu kelimeyi zaten kullandın!');
        return;
    }

    // Geçerli kelime ✓
    usedWords.add(norm);
    score     += 10;
    wordCount += 1;

    // UI güncelle
    inp.classList.add('valid');
    inp.value = '';
    errEl.textContent = '';
    addToChain(raw, 'correct');
    addToUsedList(trUpper(raw), score);
    updateStats();
    showScorePopup(inp);

    // Yeni son harf
    currentLetter = norm[norm.length - 1];
    updateLetterBadge();
    buildHintList();

    // Maksimuma ulaşıldı mı?
    if (wordCount >= MAX_WORDS) {
        endGame(true);
        return;
    }

    setTimeout(() => inp.classList.remove('valid'), 600);
    inp.focus();
}

// ── Zincire Kelime Ekle ──
function addToChain(displayWord, type) {
    const history = document.getElementById('chainHistory');
    const span    = document.createElement('span');
    span.className = 'chain-word ' + type;
    if (history.children.length > 0) {
        span.innerHTML = '<span class="cw-arrow">→</span>' + normalizeTurkish(displayWord);
    } else {
        span.textContent = normalizeTurkish(displayWord);
    }
    history.appendChild(span);
    history.scrollLeft = history.scrollWidth;
}

// ── Kullanılan Listesine Ekle ──
function addToUsedList(word, pts) {
    const li   = document.createElement('li');
    li.innerHTML = '<span>' + word + '</span><span style="color:var(--primary);font-weight:700">+10 pt</span>';
    document.getElementById('usedList').prepend(li);
}

// ── Mevcut Harf Rozeti ──
function updateLetterBadge() {
    document.getElementById('currentLetterBadge').textContent = currentLetter;
    document.getElementById('currentLetterBadge').style.transform = 'scale(1.2)';
    setTimeout(() => { document.getElementById('currentLetterBadge').style.transform = ''; }, 200);
}

// ── İstatistik Güncelle ──
function updateStats() {
    document.getElementById('wordCount').textContent    = wordCount + '/' + MAX_WORDS;
    document.getElementById('scoreDisplay').textContent = score;
    document.getElementById('statCorrect').textContent  = wordCount;
    document.getElementById('statScore').textContent    = score;
    document.getElementById('statRemaining').textContent = MAX_WORDS - wordCount;
}

// ── Hata Göster ──
function showError(inp, errEl, msg) {
    inp.classList.add('error');
    errEl.textContent = msg;
    setTimeout(() => inp.classList.remove('error'), 400);
}

// ── Puan Popup ──
function showScorePopup(anchorEl) {
    const rect = anchorEl.getBoundingClientRect();
    const pop  = document.createElement('div');
    pop.className   = 'score-popup';
    pop.textContent = '+10';
    pop.style.left  = rect.left + 'px';
    pop.style.top   = (rect.top + window.scrollY - 10) + 'px';
    document.body.appendChild(pop);
    setTimeout(() => pop.remove(), 1000);
}

// ── Yardım Listesi ──
function buildHintList() {
    const hintList = document.getElementById('hintList');
    hintList.innerHTML = '';
    const words = [...WORD_LIST].filter(w => w[0] === currentLetter && !usedWords.has(w));
    words.slice(0, 8).forEach(w => {
        const pill = document.createElement('span');
        pill.className   = 'word-hint-pill';
        pill.textContent = w;
        hintList.appendChild(pill);
    });
}

function toggleHints() {
    const list = document.getElementById('hintList');
    list.classList.toggle('open');
    document.getElementById('hintToggleBtn').textContent =
        list.classList.contains('open') ? 'Kelimeleri gizle ▴' : 'Konu hakkında kelime önerileri gör ▾';
}

// ── Sayaç ──
function startTimer() {
    const arc   = document.getElementById('timerArc');
    const val   = document.getElementById('timerVal');
    const total = GAME_TIME;

    timerInterval = setInterval(() => {
        timeLeft -= 1;
        val.textContent = timeLeft;

        // Halka güncelle
        const offset = CIRCUMFERENCE * (1 - timeLeft / total);
        arc.style.strokeDashoffset = offset;
        if (timeLeft <= 15) arc.classList.add('danger');

        if (timeLeft <= 0) endGame(false);
    }, 1000);
}

// ── Oyun Bitti ──
function endGame(won) {
    if (gameOver) return;
    gameOver = true;
    clearInterval(timerInterval);

    const inp = document.getElementById('wordInput');
    inp.disabled = true;
    document.getElementById('submitBtn').disabled = true;

    const overlay  = document.getElementById('resultOverlay');
    const emojiEl  = document.getElementById('resultEmoji');
    const msgEl    = document.getElementById('resultMsg');
    const finalEl  = document.getElementById('finalScore');

    finalEl.textContent = score;

    if (score >= 90)       { emojiEl.textContent = '🏆'; msgEl.textContent = 'Mükemmel zincir! Farkındalık kelimelerinde uzman oldun.'; }
    else if (score >= 60)  { emojiEl.textContent = '🌟'; msgEl.textContent = `${wordCount} kelimelik harika bir zincir kurdin!`; }
    else if (score >= 30)  { emojiEl.textContent = '👍'; msgEl.textContent = `${wordCount} kelime! Pratik yaptıkça zincirin uzar.`; }
    else                   { emojiEl.textContent = '💡'; msgEl.textContent = 'İyi başlangıç! Kelime listesine bakıp tekrar dene.'; }

    overlay.style.display = 'flex';

    if (ACTIVITY_ID > 0) {
        document.getElementById('saveStatus').textContent = 'Puan kaydediliyor...';
        fetch(SAVE_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ activity_id: ACTIVITY_ID, score: score, max_score: MAX_SCORE })
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('saveStatus').textContent =
                d.success ? 'Puan kaydedildi ✓' : (d.login_required ? 'Kayıt için giriş yapın.' : 'Kayıt yapılamadı.');
        })
        .catch(() => { document.getElementById('saveStatus').textContent = 'Bağlantı hatası.'; });
    }
}
</script>

<?php include '../includes/footer.php'; ?>
