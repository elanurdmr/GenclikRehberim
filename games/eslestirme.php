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
<main>
<div class="game-wrapper">

    <!-- Oyun başlığı -->
    <header class="game-header">
        <h1>
            <i class="fa-solid fa-arrows-left-right" style="color:var(--secondary)"></i>
            Doğru mu, Yanlış mı?
        </h1>
        <p>Kartları sürükle ve doğru sütuna bırak. Tüm kartları doğru yerleştir!</p>
    </header>

    <!-- İlerleme -->
    <div class="game-progress">
        <span class="progress-info">Yerleştirilen: <span id="placedCount">0</span>/14</span>
        <div class="progress-bar-outer">
            <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
        </div>
        <span class="progress-score">Puan: <span id="scoreDisplay">0</span>/140</span>
    </div>

    <!-- Sürüklenebilir kartlar (karıştırılmış) -->
    <div class="matching-items-column" style="margin-bottom:2rem">
        <h3><i class="fa-solid fa-hand-pointer"></i> Kartları Sürükle</h3>
        <div id="itemsPool">
            <!-- Kartlar JavaScript tarafından oluşturulur (karıştırılmış) -->
        </div>
    </div>

    <!-- Bırakma alanları -->
    <div class="drop-zones">

        <!-- Doğru Davranışlar Kutusu -->
        <div class="drop-zone zone-dogru" id="zoneDogru"
             ondragover="allowDrop(event)" ondrop="drop(event, 'dogru')">
            <div class="drop-zone-header">
                <h3>
                    <i class="fa-solid fa-circle-check" style="color:#059669"></i>
                    Doğru Davranışlar
                </h3>
                <small style="color:var(--text-muted);font-size:0.78rem">(8 kart)</small>
            </div>
            <div id="droppedDogru" style="min-height:60px"></div>
        </div>

        <!-- Yanlış Davranışlar Kutusu -->
        <div class="drop-zone zone-yanlis" id="zoneYanlis"
             ondragover="allowDrop(event)" ondrop="drop(event, 'yanlis')">
            <div class="drop-zone-header">
                <h3>
                    <i class="fa-solid fa-circle-xmark" style="color:var(--danger)"></i>
                    Yanlış Davranışlar
                </h3>
                <small style="color:var(--text-muted);font-size:0.78rem">(6 kart)</small>
            </div>
            <div id="droppedYanlis" style="min-height:60px"></div>
        </div>

    </div>

    <!-- Kontrol ve Tekrar Butonları -->
    <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
        <button class="btn btn-secondary btn-lg" onclick="checkAll()">
            <i class="fa-solid fa-spell-check"></i> Kontrol Et
        </button>
        <button class="btn btn-outline" onclick="restartGame()">
            <i class="fa-solid fa-rotate-right"></i> Yeniden Başla
        </button>
    </div>

</div>
</main>

<!-- ===== SONUÇ MODALİ ===== -->
<div class="result-overlay" id="resultOverlay">
    <div class="result-card">
        <div class="result-emoji">🎯</div>
        <h2>Eşleştirme Tamamlandı!</h2>
        <p>İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 140 puan</div>
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

<!-- Eşleştirme Oyunu JavaScript -->
<script>
/* ===========================================================
   EŞLEŞTİRME OYUNU — JavaScript
   Sürükle-bırak ile davranış eşleştirme
   =========================================================== */

// Tüm kartlar ve doğru kategorileri
// category: 'dogru' = Doğru Davranışlar, 'yanlis' = Yanlış Davranışlar
const CARDS = [
    /* DOĞRU DAVRANIŞLAR (8 adet) */
    { id: 1, text: 'Zorbalığa müdahale etmeden önce kendi güvenliğimi kontrol ederim.',        category: 'dogru'  },
    { id: 2, text: 'Zorbalık olunca herkesi oradan uzaklaşmaya çağırırım.',                    category: 'dogru'  },
    { id: 3, text: 'Arkadaşlarımla birlikte zorbalık yapan kişiyi nazikçe uyarırım.',          category: 'dogru'  },
    { id: 4, text: 'Zorbalık yapan kişiye, bu davranışın başkasını üzdüğünü söylerim.',        category: 'dogru'  },
    { id: 5, text: 'Böyle bir durumda öğretmenime veya bir yetişkine haber veririm.',          category: 'dogru'  },
    { id: 6, text: 'Zorbalığa uğrayan arkadaşımın yanında olur, ona destek olurum.',          category: 'dogru'  },
    { id: 7, text: 'Zorbalık yapan kişiyle konuşup davranışını düzeltmesine yardımcı olurum.',category: 'dogru'  },
    { id: 8, text: 'Okulda herkesin güvende olma hakkı olduğunu bilirim.',                    category: 'dogru'  },

    /* YANLIŞ DAVRANIŞLAR (6 adet) */
    { id: 9,  text: 'Zorbalık olurken kendi güvenliğimi düşünmeden müdahale ederim.',         category: 'yanlis' },
    { id: 10, text: 'Zorbalık yapan kişiyi alkışlarım, gülerim ya da desteklerim.',           category: 'yanlis' },
    { id: 11, text: 'Olanları öğretmene söylemem çünkü ispiyonculuk sanırım.',               category: 'yanlis' },
    { id: 12, text: 'Zorbalığa uğrayan kişiyi görmezden gelirim.',                           category: 'yanlis' },
    { id: 13, text: 'Zorbalık yapan kişiyi uyarmaktan korkarım ve hiçbir şey yapmam.',       category: 'yanlis' },
    { id: 14, text: 'Zorbalık sırasında olay yerinde kalıp izlerim.',                        category: 'yanlis' },
];

const POINTS_PER_CARD = 10;  // Her doğru yerleştirme için puan
let placedCards = {};        // { cardId: category } — yerleştirilen kartlar
let checkedCards = {};       // Kontrol edilmiş kartlar
let score = 0;

/* ------ Sayfa yüklenince kartları oluştur (karıştırılmış) ------ */
document.addEventListener('DOMContentLoaded', function () {
    buildCards();
});

/**
 * Kartları karıştırıp DOM'a ekler.
 */
function buildCards() {
    const pool = document.getElementById('itemsPool');
    pool.innerHTML = '';

    // Fisher-Yates algoritması ile kartları karıştır
    const shuffled = [...CARDS].sort(() => Math.random() - 0.5);

    shuffled.forEach(function (card) {
        const el = document.createElement('div');
        el.className   = 'drag-item';
        el.id          = 'card_' + card.id;
        el.draggable   = true;
        el.dataset.id  = card.id;
        el.dataset.cat = card.category;
        el.textContent = card.text;

        // Sürükleme olayları
        el.addEventListener('dragstart', dragStart);
        el.addEventListener('dragend',   dragEnd);

        // Dokunmatik destek (mobil)
        el.addEventListener('touchstart', touchStart, { passive: true });
        el.addEventListener('touchend',   touchEnd,   { passive: false });

        pool.appendChild(el);
    });
}

/* ------ Drag & Drop Olayları ------ */
let draggedElement = null; // Sürüklenen eleman

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

// dragover'dan çıkınca görsel kaldır
document.querySelectorAll('.drop-zone').forEach(function (zone) {
    zone.addEventListener('dragleave', function () {
        this.classList.remove('drag-over');
    });
});

function drop(e, targetCat) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');

    // Sürüklenen kartı al
    const cardId = parseInt(e.dataTransfer.getData('text/plain').replace('card_', ''));
    const cardEl = document.getElementById('card_' + cardId);
    if (!cardEl) return;

    // Hedef zone'a taşı
    const targetContainer = document.getElementById(targetCat === 'dogru' ? 'droppedDogru' : 'droppedYanlis');

    // Eğer daha önce bir zone'a bırakıldıysa, eski kaydı sil
    if (placedCards[cardId]) {
        // Aynı zona bırakıldıysa işlem yapma
        if (placedCards[cardId] === targetCat) return;
    }

    targetContainer.appendChild(cardEl);
    placedCards[cardId] = targetCat;

    // Toplam yerleştirilen sayısını güncelle
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

    // Klon oluştur (görsel taşıma için)
    touchClone = this.cloneNode(true);
    touchClone.style.cssText = `
        position:fixed; z-index:9999; opacity:0.8; pointer-events:none;
        width:${rect.width}px; transform:rotate(3deg);
    `;
    document.body.appendChild(touchClone);
    positionTouchClone(touch);

    document.addEventListener('touchmove', touchMove, { passive: false });
    document.addEventListener('touchend',  touchEnd);
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

function touchEnd(e) {
    if (!touchCard || !touchClone) return;

    const touch    = e.changedTouches[0];
    const elements = document.elementsFromPoint(touch.clientX, touch.clientY);

    // Hangi zone'un üzerine bırakıldı?
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

    // Klonu kaldır
    touchClone.remove();
    touchClone = null;
    touchCard  = null;

    document.removeEventListener('touchmove', touchMove);
    document.removeEventListener('touchend',  touchEnd);
}

/**
 * Yerleştirilen kart sayısını ve ilerlemeyi günceller.
 */
function updatePlacedCount() {
    const count = Object.keys(placedCards).length;
    document.getElementById('placedCount').textContent = count;
    document.getElementById('progressBar').style.width = (count / 14 * 100) + '%';
}

/**
 * Tüm yerleştirilen kartları kontrol eder.
 */
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

        // Doğru mu yanlış mı?
        if (placedCards[card.id] === card.category) {
            score += POINTS_PER_CARD;
            cardEl.classList.remove('wrong-placed');
            cardEl.classList.add('correct-placed');
        } else {
            cardEl.classList.remove('correct-placed');
            cardEl.classList.add('wrong-placed');
        }
    });

    // Skoru güncelle
    document.getElementById('scoreDisplay').textContent = score;

    // Herkes yerleştirilmediyse uyar
    if (!allPlaced) {
        alert('Lütfen tüm kartları bir kutuya sürükleyin!');
        return;
    }

    // Sonuç modalını göster
    document.getElementById('finalScore').textContent = score;
    const percent = (score / 140) * 100;
    let emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.querySelector('.result-emoji').textContent = emoji;
    document.querySelector('.result-card p').textContent = msg;
    document.getElementById('resultOverlay').classList.add('show');

    // Skoru kaydet
    const saveStatus = document.getElementById('saveStatus');
    saveScore(2, score, 140, function (data) {
        if (data && data.success) {
            saveStatus.innerHTML = '<i class="fa-solid fa-circle-check" style="color:#059669"></i> Puan kaydedildi!';
        } else if (data && data.login_required) {
            saveStatus.innerHTML = '<a href="/genclik-rehberim/login.php" style="color:var(--primary)">Puanı kaydetmek için giriş yap</a>';
        }
    });
}

function restartGame() {
    window.location.reload();
}
</script>

<?php include '../includes/footer.php'; ?>
