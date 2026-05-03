<?php
/**
 * eslestirme.php — Doğru/Yanlış Davranış Eşleştirme Oyunu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * PDF kaynağı: 2. Etkinlik — Sürükle-bırak eşleştirme
 */

require_once '../includes/header.php';
?>

<!-- ===== EŞLEŞTİRME OYUNU ===== -->
<main class="game-page" id="eslestirmePage">

    <!-- Dekoratif arka plan lekeleri -->
    <div class="game-page-blob blob-primary" aria-hidden="true"></div>
    <div class="game-page-blob blob-secondary" aria-hidden="true"></div>

    <div class="game-wrapper">

        <!-- Oyun Başlık Alanı -->
        <header class="game-header-area">
            <div class="game-page-label">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">local_fire_department</span>
                Zorbalık Farkındalığı · Etkinlik 2
            </div>

            <div class="game-header-row">
                <div class="game-header-text">
                    <h1>Doğru mu, Yanlış mı?</h1>
                    <p>Kartları sürükle ve doğru sütuna bırak. Her doğru yerleştirme <strong>10 puan</strong>!</p>
                </div>

                <!-- İstatistik kutusu -->
                <div class="game-stats-box" aria-label="Oyun istatistikleri">
                    <div class="game-stat-item">
                        <span class="game-stat-label">Yerleştirilen</span>
                        <span class="game-stat-value text-primary"><span id="placedCount">0</span>/14</span>
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

        <!-- İlerleme çubuğu -->
        <div class="game-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <span class="progress-info">Yerleştirilen: <span id="placedCountBar">0</span>/14</span>
            <div class="progress-bar-outer">
                <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
            </div>
            <span class="progress-score">Puan: <span id="scoreDisplayBar">0</span>/140</span>
        </div>

        <!-- Sürüklenebilir kartlar havuzu -->
        <div class="matching-items-column" aria-label="Sürüklenebilir kartlar">
            <h3>
                <span class="material-symbols-outlined">touch_app</span>
                Kartları Sürükle
            </h3>
            <div id="itemsPool" class="items-pool-wrap">
                <!-- Kartlar JavaScript tarafından oluşturulur (karıştırılmış) -->
            </div>
        </div>

        <!-- Bırakma alanları -->
        <div class="drop-zones">

            <!-- Doğru Davranışlar Kutusu -->
            <div class="drop-zone zone-dogru" id="zoneDogru"
                 ondragover="allowDrop(event)" ondrop="drop(event, 'dogru')"
                 role="region" aria-label="Doğru Davranışlar bölgesi">
                <div class="drop-zone-bg-icon" aria-hidden="true">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">verified</span>
                </div>
                <div class="drop-zone-header">
                    <h3>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
                        Doğru Davranışlar
                    </h3>
                    <small>8 kart</small>
                </div>
                <div id="droppedDogru" class="drop-zone-content" style="min-height:60px"></div>
            </div>

            <!-- Yanlış Davranışlar Kutusu -->
            <div class="drop-zone zone-yanlis" id="zoneYanlis"
                 ondragover="allowDrop(event)" ondrop="drop(event, 'yanlis')"
                 role="region" aria-label="Yanlış Davranışlar bölgesi">
                <div class="drop-zone-bg-icon" aria-hidden="true">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">sentiment_very_dissatisfied</span>
                </div>
                <div class="drop-zone-header">
                    <h3>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">cancel</span>
                        Yanlış Davranışlar
                    </h3>
                    <small>6 kart</small>
                </div>
                <div id="droppedYanlis" class="drop-zone-content" style="min-height:60px"></div>
            </div>

        </div>

        <!-- Kontrol ve Tekrar Butonları -->
        <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
            <button class="btn btn-secondary btn-lg" onclick="checkAll()">
                <span class="material-symbols-outlined">spellcheck</span> Kontrol Et
            </button>
            <button class="btn btn-outline" onclick="restartGame()">
                <span class="material-symbols-outlined">refresh</span> Yeniden Başla
            </button>
        </div>

    </div>
</main>

<!-- ===== SONUÇ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay" role="dialog" aria-modal="true" aria-label="Oyun sonucu">
    <div class="result-card">
        <div class="result-emoji">🎯</div>
        <h2>Eşleştirme Tamamlandı!</h2>
        <p>İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 140 puan</div>
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

<!-- Eşleştirme Oyunu JavaScript -->
<script>
/* ===========================================================
   EŞLEŞTİRME OYUNU — JavaScript
   Sürükle-bırak ile davranış eşleştirme
   =========================================================== */

const CARDS = [
    /* DOĞRU DAVRANIŞLAR (8 adet) */
    { id: 1,  text: 'Zorbalığa müdahale etmeden önce kendi güvenliğimi kontrol ederim.',        category: 'dogru'  },
    { id: 2,  text: 'Zorbalık olunca herkesi oradan uzaklaşmaya çağırırım.',                    category: 'dogru'  },
    { id: 3,  text: 'Arkadaşlarımla birlikte zorbalık yapan kişiyi nazikçe uyarırım.',          category: 'dogru'  },
    { id: 4,  text: 'Zorbalık yapan kişiye, bu davranışın başkasını üzdüğünü söylerim.',        category: 'dogru'  },
    { id: 5,  text: 'Böyle bir durumda öğretmenime veya bir yetişkine haber veririm.',          category: 'dogru'  },
    { id: 6,  text: 'Zorbalığa uğrayan arkadaşımın yanında olur, ona destek olurum.',          category: 'dogru'  },
    { id: 7,  text: 'Zorbalık yapan kişiyle konuşup davranışını düzeltmesine yardımcı olurum.',category: 'dogru'  },
    { id: 8,  text: 'Okulda herkesin güvende olma hakkı olduğunu bilirim.',                    category: 'dogru'  },
    /* YANLIŞ DAVRANIŞLAR (6 adet) */
    { id: 9,  text: 'Zorbalık olurken kendi güvenliğimi düşünmeden müdahale ederim.',          category: 'yanlis' },
    { id: 10, text: 'Zorbalık yapan kişiyi alkışlarım, gülerim ya da desteklerim.',            category: 'yanlis' },
    { id: 11, text: 'Olanları öğretmene söylemem çünkü ispiyonculuk sanırım.',                category: 'yanlis' },
    { id: 12, text: 'Zorbalığa uğrayan kişiyi görmezden gelirim.',                            category: 'yanlis' },
    { id: 13, text: 'Zorbalık yapan kişiyi uyarmaktan korkarım ve hiçbir şey yapmam.',        category: 'yanlis' },
    { id: 14, text: 'Zorbalık sırasında olay yerinde kalıp izlerim.',                         category: 'yanlis' },
];

const POINTS_PER_CARD = 10;
let placedCards  = {};
let checkedCards = {};
let score        = 0;

document.addEventListener('DOMContentLoaded', function () {
    buildCards();
    // dragover sınıfını temizle
    document.querySelectorAll('.drop-zone').forEach(function (zone) {
        zone.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });
    });
});

function buildCards() {
    const pool = document.getElementById('itemsPool');
    pool.innerHTML = '';

    const shuffled = [...CARDS].sort(() => Math.random() - 0.5);

    shuffled.forEach(function (card) {
        const el = document.createElement('div');
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

let draggedElement = null;

function dragStart(e) {
    draggedElement = this;
    this.classList.add('dragging');
    e.dataTransfer.setData('text/plain', this.id);
    e.dataTransfer.effectAllowed = 'move';
}

function dragEnd() {
    this.classList.remove('dragging');
    draggedElement = null;
}

function allowDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function drop(e, targetCat) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');

    const cardId = parseInt(e.dataTransfer.getData('text/plain').replace('card_', ''));
    const cardEl = document.getElementById('card_' + cardId);
    if (!cardEl) return;

    const targetContainer = document.getElementById(targetCat === 'dogru' ? 'droppedDogru' : 'droppedYanlis');
    if (placedCards[cardId] && placedCards[cardId] === targetCat) return;

    targetContainer.appendChild(cardEl);
    placedCards[cardId] = targetCat;
    updatePlacedCount();
}

/* ------ Dokunmatik Sürükleme (Mobil) ------ */
let touchCard = null;
let touchClone = null;
let touchOffsetX, touchOffsetY;

function touchStart(e) {
    touchCard = this;
    const touch = e.touches[0];
    const rect  = this.getBoundingClientRect();
    touchOffsetX = touch.clientX - rect.left;
    touchOffsetY = touch.clientY - rect.top;

    touchClone = this.cloneNode(true);
    touchClone.style.cssText = 'position:fixed;z-index:9999;opacity:0.85;pointer-events:none;width:' + rect.width + 'px;transform:rotate(2deg);box-shadow:0 12px 30px rgba(0,0,0,0.2);';
    document.body.appendChild(touchClone);
    positionTouchClone(touch);

    document.addEventListener('touchmove', touchMove, { passive: false });
    document.addEventListener('touchend',  touchEndGlobal);
}

function touchMove(e) {
    e.preventDefault();
    positionTouchClone(e.touches[0]);
}

function positionTouchClone(touch) {
    if (!touchClone) return;
    touchClone.style.left = (touch.clientX - touchOffsetX) + 'px';
    touchClone.style.top  = (touch.clientY - touchOffsetY) + 'px';
}

function touchEnd(e) {}

function touchEndGlobal(e) {
    if (!touchCard || !touchClone) return;

    const touch    = e.changedTouches[0];
    const elements = document.elementsFromPoint(touch.clientX, touch.clientY);

    let targetZone = null;
    elements.forEach(function (el) {
        if (el.classList.contains('zone-dogru'))  targetZone = 'dogru';
        if (el.classList.contains('zone-yanlis')) targetZone = 'yanlis';
    });

    if (targetZone) {
        const cardId = parseInt(touchCard.id.replace('card_', ''));
        const targetContainer = document.getElementById(targetZone === 'dogru' ? 'droppedDogru' : 'droppedYanlis');
        targetContainer.appendChild(touchCard);
        placedCards[cardId] = targetZone;
        updatePlacedCount();
    }

    touchClone.remove();
    touchClone = null;
    touchCard  = null;

    document.removeEventListener('touchmove', touchMove);
    document.removeEventListener('touchend',  touchEndGlobal);
}

function updatePlacedCount() {
    const count = Object.keys(placedCards).length;
    document.getElementById('placedCount').textContent    = count;
    document.getElementById('placedCountBar').textContent = count;
    document.getElementById('progressBar').style.width    = (count / 14 * 100) + '%';
}

function checkAll() {
    score = 0;
    let allPlaced = true;

    CARDS.forEach(function (card) {
        const cardEl = document.getElementById('card_' + card.id);
        if (!cardEl) return;

        if (!placedCards[card.id]) {
            allPlaced = false;
            return;
        }

        if (placedCards[card.id] === card.category) {
            score += POINTS_PER_CARD;
            cardEl.classList.remove('wrong-placed');
            cardEl.classList.add('correct-placed');
        } else {
            cardEl.classList.remove('correct-placed');
            cardEl.classList.add('wrong-placed');
        }
    });

    document.getElementById('scoreDisplay').textContent    = score;
    document.getElementById('scoreDisplayBar').textContent = score;

    if (!allPlaced) {
        alert('Lütfen tüm kartları bir kutuya sürükleyin!');
        return;
    }

    document.getElementById('finalScore').textContent = score;
    const percent = (score / 140) * 100;
    let emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.querySelector('.result-emoji').textContent  = emoji;
    document.querySelector('.result-card p').textContent = msg;
    document.getElementById('resultOverlay').classList.add('show');

    const saveStatus = document.getElementById('saveStatus');
    saveScore(2, score, 140, function (data) {
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
</script>

<?php include '../includes/footer.php'; ?>
