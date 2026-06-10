<?php
/**
 * benimhikayem.php — İnteraktif Karar Ağacı Oyunu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * Hikaye: Okul Kantini ve Dijital Dedikodu (Dışlama & Siber Zorbalık)
 * Puanlama: 10 karar × seçim puanı + bitiş bonusu = max 100 pt
 * Negatif ham puan mümkün; kaydedilirken [0, 100] aralığına sıkıştırılır.
 */

$pageTitle = 'Benim Hikayem';
require_once '../includes/header.php';
requireLogin();
$activityId = getActivityId('benimhikayem');
?>

<style>
/* ═══════════════════════════════════════════════
   BENİM HİKAYEM — Özel Stiller
═══════════════════════════════════════════════ */

/* ── Giriş ekranı ── */
.bh-intro {
    max-width: 620px;
    margin: 0 auto;
    text-align: center;
    padding: 2.5rem 1rem;
}
.bh-intro .bh-cover-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
    display: block;
    animation: floatIcon 3s ease-in-out infinite;
}
@keyframes floatIcon {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-8px); }
}
.bh-intro h2 { font-size: clamp(1.5rem,4vw,2.2rem); font-weight:800; margin:.5rem 0; }
.bh-intro p  { color: var(--on-surface-variant); font-size: 1rem; line-height: 1.7; margin-bottom: 2rem; }

.bh-path-legend {
    display: flex;
    gap: .75rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}
.bh-path-pill {
    display: flex;
    align-items: center;
    gap: .4rem;
    border-radius: 999px;
    padding: .35rem 1rem;
    font-size: .78rem;
    font-weight: 700;
}
.bh-path-pill.hero    { background: #e8f5e9; color: #2e7d32; }
.bh-path-pill.good    { background: #e3f2fd; color: #1565c0; }
.bh-path-pill.passive { background: #fff8e1; color: #f57f17; }
.bh-path-pill.bad     { background: #fce4ec; color: #b71c1c; }

/* ── Oyun alanı ── */
.bh-game { display: none; max-width: 760px; margin: 0 auto; }
.bh-game.active { display: block; }

/* Puan göstergesi (üstte sabit şerit) */
.bh-score-strip {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--surface-container);
    border-radius: 14px;
    padding: .75rem 1.25rem;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
}
.bh-score-label {
    font-size: .8rem;
    font-weight: 700;
    color: var(--on-surface-variant);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
}
.bh-score-value {
    font-size: 1.6rem;
    font-weight: 900;
    min-width: 3.5rem;
    text-align: right;
    transition: color .4s;
}
.bh-score-value.positive { color: #2e7d32; }
.bh-score-value.zero     { color: var(--on-surface); }
.bh-score-value.negative { color: #c62828; }
.bh-score-max  { font-size: .8rem; color: var(--on-surface-variant); }
.bh-meter-wrap { flex: 1; min-width: 120px; }
.bh-meter-track {
    height: 8px;
    background: var(--surface-variant);
    border-radius: 99px;
    overflow: visible;
    position: relative;
}
.bh-meter-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .5s ease, background .5s;
}
.bh-node-badge {
    background: var(--primary-container);
    color: var(--on-primary-container);
    border-radius: 99px;
    padding: .25rem .75rem;
    font-size: .75rem;
    font-weight: 700;
    white-space: nowrap;
}

/* ── Hikaye balonu ── */
.bh-bubble {
    background: var(--surface-container);
    border-radius: 4px 18px 18px 18px;
    padding: 1.5rem 1.75rem;
    font-size: 1.05rem;
    line-height: 1.8;
    margin-bottom: 1.5rem;
    position: relative;
    animation: bubbleIn .35s cubic-bezier(.34,1.56,.64,1);
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.bh-bubble::before {
    content: '';
    position: absolute;
    top: -1px; left: -1px;
    right: -1px; bottom: -1px;
    border-radius: inherit;
    border: 2px solid var(--outline-variant);
    pointer-events: none;
}
@keyframes bubbleIn {
    from { opacity:0; transform: scale(.96) translateY(8px); }
    to   { opacity:1; transform: scale(1)  translateY(0); }
}

/* ── Geri bildirim ── */
.bh-feedback {
    display: none;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
    font-size: .9rem;
    font-weight: 500;
    line-height: 1.6;
    gap: .65rem;
    align-items: flex-start;
    animation: bubbleIn .3s ease;
}
.bh-feedback.show { display: flex; }
.bh-feedback.positive { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #43a047; }
.bh-feedback.neutral  { background: #fff8e1; color: #f57f17; border-left: 4px solid #ffa726; }
.bh-feedback.negative { background: #fce4ec; color: #b71c1c; border-left: 4px solid #ef5350; }
.bh-feedback .material-symbols-outlined { flex-shrink: 0; font-size: 1.3rem; }

/* ── Seçimler ── */
.bh-choices {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    animation: bubbleIn .35s ease;
}
.bh-choice {
    background: var(--surface);
    border: 2px solid var(--outline-variant);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    cursor: pointer;
    font-size: .95rem;
    font-weight: 500;
    line-height: 1.55;
    text-align: left;
    font-family: inherit;
    color: var(--on-surface);
    transition: border-color .15s, background .15s, transform .12s;
    display: flex;
    align-items: flex-start;
    gap: .85rem;
}
.bh-choice .choice-letter {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    min-width: 28px;
    border-radius: 50%;
    background: var(--primary-container);
    color: var(--on-primary-container);
    font-size: .8rem;
    font-weight: 800;
    margin-top: .05rem;
}
.bh-choice:hover  { border-color: var(--primary); background: var(--primary-container); transform: translateX(4px); }
.bh-choice:disabled { opacity: .5; cursor: not-allowed; transform: none; }

/* ── Yükleme ── */
.bh-loader {
    text-align: center;
    padding: 2rem;
    display: none;
}
.bh-loader .material-symbols-outlined {
    font-size: 2.5rem;
    color: var(--primary);
    animation: spin 1s linear infinite;
}
@keyframes spin { from{transform:rotate(0)} to{transform:rotate(360deg)} }

/* ── Bitiş ekranı ── */
.bh-ending {
    display: none;
    max-width: 640px;
    margin: 0 auto;
}
.bh-ending.active { display: block; }
.bh-ending-card {
    border-radius: 24px;
    padding: 2.5rem;
    text-align: center;
    animation: bubbleIn .5s ease;
    position: relative;
    overflow: hidden;
}
.bh-ending-card.legendary { background: linear-gradient(135deg, #fff9c4, #fffde7); border: 2px solid #f9a825; }
.bh-ending-card.good      { background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border: 2px solid #66bb6a; }
.bh-ending-card.redemption{ background: linear-gradient(135deg, #e3f2fd, #e8eaf6); border: 2px solid #42a5f5; }
.bh-ending-card.passive   { background: linear-gradient(135deg, #fff8e1, #fffde7); border: 2px solid #ffa726; }
.bh-ending-card.bad       { background: linear-gradient(135deg, #fce4ec, #fce4ec); border: 2px solid #ef9a9a; }

.bh-ending-emoji  { font-size: 4.5rem; margin-bottom: .5rem; display: block; }
.bh-ending-title  { font-size: 1.5rem; font-weight: 800; margin: .5rem 0; }
.bh-ending-subtitle { font-size: .9rem; font-weight: 600; opacity: .8; margin-bottom: 1.25rem; }
.bh-ending-text   { font-size: .95rem; line-height: 1.7; margin-bottom: 1.5rem; }
.bh-ending-wisdom {
    background: rgba(255,255,255,.7);
    border-radius: 12px;
    padding: .9rem 1rem;
    font-size: .875rem;
    font-style: italic;
    line-height: 1.6;
    margin-bottom: 1.75rem;
    color: var(--on-surface-variant);
}
.bh-ending-score {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: .25rem;
}
.bh-ending-score-label { font-size: .875rem; color: var(--on-surface-variant); margin-bottom: 1.75rem; }
.bh-path-summary {
    display: flex;
    gap: .5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.bh-path-step {
    background: rgba(0,0,0,.06);
    border-radius: 99px;
    padding: .25rem .75rem;
    font-size: .75rem;
    font-weight: 600;
}
.bh-path-step.positive { background: rgba(46,125,50,.1); color: #2e7d32; }
.bh-path-step.negative { background: rgba(198,40,40,.1); color: #c62828; }
.bh-path-step.zero     { background: rgba(0,0,0,.06); color: var(--on-surface-variant); }

/* ── Puan popup animasyonu ── */
.bh-points-popup {
    position: fixed;
    pointer-events: none;
    font-size: 1.6rem;
    font-weight: 900;
    animation: ptsFly .9s ease forwards;
    z-index: 9999;
}
.bh-points-popup.positive { color: #2e7d32; }
.bh-points-popup.negative { color: #c62828; }
@keyframes ptsFly {
    from { opacity:1; transform: translateY(0) scale(1); }
    to   { opacity:0; transform: translateY(-70px) scale(.8); }
}
</style>

<main class="game-page" id="benimHikayemPage">
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Başlık -->
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">auto_stories</span>
                İnteraktif Karar Oyunu · Etkinlik 5
            </div>
            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>Benim Hikayem</h1>
                    <p>Okul hayatında karşılaşılan zorluklarda ne yaparsın? Her seçimin bir sonucu var.</p>
                </div>
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">Karar</span>
                        <span class="game-stat-value text-primary" id="choiceCounter">0/15</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Puan</span>
                        <span class="game-stat-value text-secondary" id="headerScore">0</span>
                    </div>
                    <div class="game-stat-item">
                        <span class="game-stat-label">Max</span>
                        <span class="game-stat-value text-tertiary">100</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- ══ GİRİŞ EKRANI ══ -->
        <div id="screenIntro">
            <div class="bh-intro">
                <span class="bh-cover-icon">📚</span>
                <h2>Okul Kantini ve Dijital Dedikodu</h2>
                <p>
                    Yeni bir öğrenci hakkında dedikodu yapılırken nasıl davranırsın?
                    Doğru kararlar empati göstergesi, yanlış kararlar ise beklenmedik sonuçlar doğurabilir.
                    Hikaye boyunca toplayacağın puanlar, empati profilini belirleyecek.
                </p>

                <div class="bh-path-legend" aria-label="Olası finaller">
                    <span class="bh-path-pill hero"><span>🏆</span> Empati Ustası</span>
                    <span class="bh-path-pill good"><span>🎉</span> Duyarlı Genç</span>
                    <span class="bh-path-pill passive"><span>😔</span> Sessiz Şahit</span>
                    <span class="bh-path-pill bad"><span>❌</span> Zorba Ortağı</span>
                </div>

                <button class="btn btn-primary btn-lg" onclick="startGame()" style="gap:.6rem">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">play_arrow</span>
                    Hikayeye Başla
                </button>
            </div>
        </div>

        <!-- ══ OYUN ALANI ══ -->
        <div class="bh-game" id="screenGame">

            <!-- Puan şeridi -->
            <div class="bh-score-strip" aria-live="polite" aria-label="Puan durumu">
                <span class="bh-score-label">Ham Puan</span>
                <span class="bh-score-value zero" id="rawScoreDisplay">0</span>
                <span class="bh-score-max">/ 100</span>
                <div class="bh-meter-wrap">
                    <div class="bh-meter-track">
                        <div class="bh-meter-fill" id="scoreMeterFill" style="width:50%;background:var(--primary)"></div>
                    </div>
                </div>
                <span class="bh-node-badge" id="nodeBadge">Düğüm 1</span>
            </div>

            <!-- Geri bildirim kutusu -->
            <div class="bh-feedback" id="bhFeedback" role="status">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;" id="fbIcon">lightbulb</span>
                <span id="fbText"></span>
            </div>

            <!-- Hikaye metni -->
            <div class="bh-bubble" id="bhBubble" aria-live="polite">Yükleniyor...</div>

            <!-- Yükleme göstergesi -->
            <div class="bh-loader" id="bhLoader">
                <span class="material-symbols-outlined">progress_activity</span>
            </div>

            <!-- Seçimler -->
            <div class="bh-choices" id="bhChoices" role="group" aria-label="Seçenekler"></div>

        </div>

        <!-- ══ BİTİŞ EKRANI ══ -->
        <div class="bh-ending" id="screenEnding">
            <div class="bh-ending-card" id="endingCard">
                <span class="bh-ending-emoji" id="endEmoji"></span>
                <div class="bh-ending-title"   id="endTitle"></div>
                <div class="bh-ending-subtitle" id="endSubtitle"></div>
                <div class="bh-ending-text"     id="endText"></div>
                <div class="bh-ending-wisdom"   id="endWisdom"></div>

                <div class="bh-ending-score" id="endScore">0</div>
                <div class="bh-ending-score-label">/ 100 puan</div>

                <div class="bh-path-summary" id="pathSummary" aria-label="Karar geçmişi"></div>

                <div id="saveStatus" style="font-size:.85rem;color:var(--on-surface-variant);margin-bottom:1rem"></div>
                <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap">
                    <a href="/genclik-rehberim/games/benimhikayem.php" class="btn btn-primary">
                        <span class="material-symbols-outlined">refresh</span> Tekrar Oyna
                    </a>
                    <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-outline">
                        <span class="material-symbols-outlined">bar_chart</span> Panele Git
                    </a>
                </div>
            </div>
        </div>

    </div><!-- /game-wrapper -->
</main>

<script>
/* ============================================================
   BENİM HİKAYEM — Karar Ağacı Oyunu
   ============================================================ */
window.GAME_CONFIG = { activityId: <?= (int)$activityId ?> };

const ACTIVITY_ID = window.GAME_CONFIG.activityId;
const STORY_URL   = '/genclik-rehberim/games/story_node.php';
const SAVE_URL    = '/genclik-rehberim/games/save_score.php';
const MAX_SCORE   = 100;

// Oyun durumu
let rawScore    = 0;   // Negatif olabilir
let choicesMade = 0;
let choiceLog   = [];  // { text, pts } dizisi

// ── Oyunu Başlat ──
function startGame() {
    document.getElementById('screenIntro').style.display = 'none';
    document.getElementById('screenGame').classList.add('active');
    setLoader(true);
    postStory({ scenario: 1 }).then(d => renderNode(d, null));
}

// ── Node Render ──
function renderNode(data, choicePts) {
    if (!data || !data.success) {
        alert('Bir hata oluştu. Lütfen sayfayı yenileyin.');
        return;
    }

    const { node, choices, points_earned } = data;

    // Seçim puanını uygula
    if (choicePts !== null) {
        rawScore += points_earned;
        updateScoreUI(points_earned);
    }

    // Bitiş bonusunu uygula
    if (node.type === 'end' && node.bonus_points !== 0) {
        setTimeout(() => {
            rawScore += node.bonus_points;
            updateScoreUI(node.bonus_points);
        }, 400);
    }

    // Geri bildirim (bir önceki seçim için)
    renderFeedback(node.feedback, points_earned, node.bonus_points);

    // Düğüm rozeti
    document.getElementById('nodeBadge').textContent = 'Düğüm ' + node.id;

    // Hikaye metni
    setLoader(false);
    const bubble = document.getElementById('bhBubble');
    bubble.textContent = node.text;
    bubble.style.animation = 'none';
    bubble.offsetHeight;
    bubble.style.animation = '';

    // Seçimler
    const choicesEl = document.getElementById('bhChoices');
    choicesEl.innerHTML = '';

    if (node.type === 'end') {
        setTimeout(() => showEnding(node), node.bonus_points !== 0 ? 900 : 600);
        return;
    }

    const letters = ['A', 'B', 'C', 'D'];
    choices.forEach((c, i) => {
        const btn = document.createElement('button');
        btn.className = 'bh-choice';
        btn.innerHTML = `<span class="choice-letter">${letters[i]}</span><span>${escHtml(c.choice_text)}</span>`;
        btn.setAttribute('aria-label', `Seçenek ${letters[i]}: ${c.choice_text}`);
        btn.addEventListener('click', () => makeChoice(c.id, c.choice_text, c.points, choicesEl));
        choicesEl.appendChild(btn);
    });
}

// ── Seçim Yap ──
function makeChoice(choiceId, choiceText, choicePts, choicesEl) {
    choicesEl.querySelectorAll('.bh-choice').forEach(b => b.disabled = true);
    choicesMade++;
    choiceLog.push({ text: choiceText, pts: parseInt(choicePts) || 0 });
    document.getElementById('choiceCounter').textContent = choicesMade + '/15';
    document.getElementById('headerScore').textContent   = Math.max(0, rawScore + (parseInt(choicePts)||0));
    setLoader(true);
    postStory({ choice_id: choiceId }).then(d => renderNode(d, choicePts));
}

// ── Geri Bildirim ──
function renderFeedback(text, choicePts, bonusPts) {
    const fb     = document.getElementById('bhFeedback');
    const fbText = document.getElementById('fbText');
    const fbIcon = document.getElementById('fbIcon');
    const pts    = (parseInt(choicePts) || 0) + (parseInt(bonusPts) || 0);

    if (!text) { fb.classList.remove('show'); return; }

    if (pts > 0)       { fb.className = 'bh-feedback show positive'; fbIcon.textContent = 'check_circle'; }
    else if (pts < 0)  { fb.className = 'bh-feedback show negative'; fbIcon.textContent = 'warning'; }
    else               { fb.className = 'bh-feedback show neutral';  fbIcon.textContent = 'info'; }

    fbText.textContent = text;
}

// ── Puan UI Güncelle ──
function updateScoreUI(delta) {
    const el = document.getElementById('rawScoreDisplay');
    el.textContent = rawScore;
    el.className   = 'bh-score-value ' + (rawScore > 0 ? 'positive' : rawScore < 0 ? 'negative' : 'zero');

    // Metre: sıfır noktası yakın sol (rawScore -30..100 → %0..%100)
    const pct = Math.round(((rawScore + 30) / 130) * 100);
    const fill = document.getElementById('scoreMeterFill');
    fill.style.width      = Math.max(2, Math.min(100, pct)) + '%';
    fill.style.background = rawScore >= 60 ? '#43a047' : rawScore >= 20 ? 'var(--primary)' : '#ef5350';

    document.getElementById('headerScore').textContent = Math.max(0, rawScore);

    // Puan popup
    if (delta !== 0) showPointsPopup(delta);
}

// ── Bitiş Ekranı ──
function showEnding(node) {
    document.getElementById('screenGame').classList.remove('active');
    const ending = document.getElementById('screenEnding');
    ending.classList.add('active');

    const card = document.getElementById('endingCard');
    const finalSaved = Math.max(0, Math.min(MAX_SCORE, rawScore));

    // Node ID'ye göre final tipi; node 18 için skor bazlı passive/bad ayrımı
    const profiles = {
        16: { emoji:'🏆', title:'Empati Ustası!', subtitle:'Efsanevi Final', cls:'legendary' },
        17: { emoji:'🎉', title:'Duyarlı Genç',   subtitle:'İyi Final',      cls:'good'      },
        18: finalSaved >= 15
              ? { emoji:'😔', title:'Sessiz Şahit', subtitle:'Pasif Final', cls:'passive' }
              : { emoji:'❌', title:'Zorba Ortağı', subtitle:'Kötü Final',  cls:'bad'     },
    };
    const prof = profiles[node.id] || { emoji:'🎭', title:'Son', subtitle:'Final', cls:'good' };

    card.className      = 'bh-ending-card ' + prof.cls;
    document.getElementById('endEmoji').textContent    = prof.emoji;
    document.getElementById('endTitle').textContent    = prof.title;
    document.getElementById('endSubtitle').textContent = prof.subtitle;
    document.getElementById('endText').textContent     = node.text;
    document.getElementById('endWisdom').textContent   = node.feedback || '';
    document.getElementById('endScore').textContent    = finalSaved;

    // Karar özeti
    const summary = document.getElementById('pathSummary');
    summary.innerHTML = '';
    choiceLog.forEach((c, i) => {
        const span = document.createElement('span');
        span.className = 'bh-path-step ' + (c.pts > 0 ? 'positive' : c.pts < 0 ? 'negative' : 'zero');
        span.textContent = `Karar ${i+1}: ${c.pts > 0 ? '+' : ''}${c.pts}`;
        span.title = c.text;
        summary.appendChild(span);
    });

    // Puan kaydet
    if (ACTIVITY_ID > 0) {
        document.getElementById('saveStatus').textContent = 'Puan kaydediliyor...';
        fetch(SAVE_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ activity_id: ACTIVITY_ID, score: finalSaved, max_score: MAX_SCORE })
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('saveStatus').textContent =
                d.success ? '✓ Puan kaydedildi' : (d.login_required ? 'Kayıt için giriş yapın.' : 'Puan kaydedilemedi.');
        })
        .catch(() => { document.getElementById('saveStatus').textContent = 'Bağlantı hatası.'; });
    }
}

// ── Yardımcılar ──
function setLoader(on) {
    document.getElementById('bhLoader').style.display  = on ? 'block' : 'none';
    document.getElementById('bhChoices').style.display = on ? 'none'  : 'flex';
    if (on) document.getElementById('bhBubble').textContent = '';
}

function postStory(payload) {
    return fetch(STORY_URL, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(payload)
    }).then(r => r.json());
}

function showPointsPopup(delta) {
    const el  = document.createElement('div');
    el.className = 'bh-points-popup ' + (delta > 0 ? 'positive' : 'negative');
    el.textContent = (delta > 0 ? '+' : '') + delta;
    const strip = document.getElementById('rawScoreDisplay');
    const rect  = strip.getBoundingClientRect();
    el.style.left = rect.left + 'px';
    el.style.top  = (rect.top + window.scrollY) + 'px';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 950);
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php include '../includes/footer.php'; ?>
