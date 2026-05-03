<?php
/**
 * kategori.php — Zorbalık / Zorbalık Değil Kategori Oyunu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * PDF kaynağı: 3. Etkinlik — Kelimeleri doğru kutuya yerleştirme
 */

require_once '../includes/header.php';
?>

<!-- ===== KATEGORİ OYUNU ===== -->
<main>
<div class="game-wrapper">

    <!-- Oyun başlığı -->
    <header class="game-header">
        <h1>
            <i class="fa-solid fa-tags" style="color:#10b981"></i>
            Zorbalık mı, Değil mi?
        </h1>
        <p>Bir kelimeye tıkla, sonra doğru kutuya tıkla! Her doğru yerleştirme 10 puan.</p>
    </header>

    <!-- İlerleme -->
    <div class="game-progress">
        <span class="progress-info">Yerleştirilen: <span id="placedCount">0</span>/17</span>
        <div class="progress-bar-outer">
            <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
        </div>
        <span class="progress-score">Puan: <span id="scoreDisplay">0</span>/170</span>
    </div>

    <!-- Seçilecek kelimeler -->
    <div class="category-words">
        <h3><i class="fa-solid fa-hand-pointer"></i> Bir Kelimeye Tıkla, Sonra Kutuya Yerleştir</h3>
        <div class="word-chips" id="wordChips">
            <!-- Kelimeler JavaScript tarafından dinamik oluşturulur -->
        </div>
    </div>

    <!-- Seçili kelime göstergesi -->
    <div id="selectedHint" style="text-align:center;margin-bottom:1.5rem;min-height:36px">
        <!-- Seçilen kelime burada gösterilir -->
    </div>

    <!-- Kategori kutuları -->
    <div class="category-zones">

        <!-- Zorbalık Kutusu -->
        <div class="category-zone zone-zorbalik" id="zoneZorbalik" onclick="placeSelected('zorbalik')">
            <div class="category-zone-header">
                <h3>
                    <i class="fa-solid fa-hand-fist" style="color:var(--danger)"></i>
                    ZORBALIK
                </h3>
            </div>
            <div class="zone-chips" id="chipsZorbalik">
                <!-- Yerleştirilen kelimeler buraya eklenir -->
            </div>
        </div>

        <!-- Zorbalık Değil Kutusu -->
        <div class="category-zone zone-zorbalik-degil" id="zoneNot" onclick="placeSelected('not')">
            <div class="category-zone-header">
                <h3>
                    <i class="fa-solid fa-heart" style="color:#059669"></i>
                    ZORBALIK DEĞİL
                </h3>
            </div>
            <div class="zone-chips" id="chipsNot">
                <!-- Yerleştirilen kelimeler buraya eklenir -->
            </div>
        </div>

    </div>

    <!-- Kontrol butonu -->
    <div class="text-center mt-4" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap">
        <button class="btn btn-success btn-lg" onclick="checkAll()">
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
        <div class="result-emoji">🏷️</div>
        <h2>Kategori Tamamlandı!</h2>
        <p>İşte sonucun:</p>
        <div class="result-score-big" id="finalScore">0</div>
        <div class="result-score-label">/ 170 puan</div>
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

<!-- Kategori Oyunu JavaScript -->
<script>
/* ===========================================================
   KATEGORİ OYUNU — JavaScript
   Kelime tıkla → kutuya yerleştir mekanizması
   =========================================================== */

// Tüm kelimeler ve doğru kategorileri
// PDF'den alınan: 7 zorbalık, 10 zorbalık değil = 17 kelime
const WORDS = [
    /* ZORBALIK (7 adet) */
    { id: 1,  text: 'Yumruk atmak',      category: 'zorbalik'  },
    { id: 2,  text: 'Tehdit etmek',      category: 'zorbalik'  },
    { id: 3,  text: 'Küfretmek',         category: 'zorbalik'  },
    { id: 4,  text: 'Kavga çıkarmak',    category: 'zorbalik'  },
    { id: 5,  text: 'Kötü lakap takmak', category: 'zorbalik'  },
    { id: 6,  text: 'Arkadaşını itmek',  category: 'zorbalik'  },
    { id: 7,  text: 'Çelme takmak',      category: 'zorbalik'  },

    /* ZORBALIK DEĞİL (10 adet) */
    { id: 8,  text: 'Yardım etmek',               category: 'not' },
    { id: 9,  text: 'Sırada beklemek',             category: 'not' },
    { id: 10, text: 'Nazik konuşmak',              category: 'not' },
    { id: 11, text: 'Arkadaşını dinlemek',         category: 'not' },
    { id: 12, text: 'Oyuna davet etmek',           category: 'not' },
    { id: 13, text: 'Paylaşmak',                   category: 'not' },
    { id: 14, text: 'Sorunu bildirmek',            category: 'not' },
    { id: 15, text: 'Arkadaşına gülümsemek',      category: 'not' },
    { id: 16, text: 'Birini düşerse kaldırmak',   category: 'not' },
    { id: 17, text: 'Söz hakkına saygı göstermek',category: 'not' },
];

const TOTAL  = WORDS.length;    // 17
const POINTS = 10;              // Her doğru için 10 puan

let selectedWord = null;        // Şu an seçili kelime ID'si
let placedWords  = {};          // { wordId: category } — yerleştirilen kelimeler
let score        = 0;

/* ------ Sayfa yüklenince kelimeleri oluştur ------ */
document.addEventListener('DOMContentLoaded', function () {
    buildWordChips();
});

/**
 * Kelime chiplerini karıştırıp DOM'a ekler.
 */
function buildWordChips() {
    const container = document.getElementById('wordChips');
    container.innerHTML = '';

    // Karıştır
    const shuffled = [...WORDS].sort(() => Math.random() - 0.5);

    shuffled.forEach(function (word) {
        const chip = document.createElement('div');
        chip.className   = 'word-chip';
        chip.id          = 'chip_' + word.id;
        chip.dataset.id  = word.id;
        chip.textContent = word.text;
        chip.addEventListener('click', function () { selectWord(word.id); });
        container.appendChild(chip);
    });
}

/**
 * Kelime seçme işlemi.
 * @param {number} wordId
 */
function selectWord(wordId) {
    // Daha önce yerleştirildiyse tıklamayı işleme
    if (placedWords[wordId]) return;

    // Önceki seçimi kaldır
    if (selectedWord !== null) {
        const prevChip = document.getElementById('chip_' + selectedWord);
        if (prevChip) {
            prevChip.classList.remove('selected-zorbalik', 'selected-not-zorbalik');
        }
    }

    // Yeni seçimi işaretle
    selectedWord = wordId;
    const chip = document.getElementById('chip_' + wordId);

    // Hangi kategori olduğuna göre renk ver
    const word = WORDS.find(w => w.id === wordId);
    if (word.category === 'zorbalik') {
        chip.classList.add('selected-zorbalik');
    } else {
        chip.classList.add('selected-not-zorbalik');
    }

    // İpucu göster
    document.getElementById('selectedHint').innerHTML =
        '<span style="font-weight:700;color:var(--primary)">"' + word.text + '"</span>' +
        ' seçildi → Şimdi bir kutuya tıkla!';
}

/**
 * Seçili kelimeyi hedef kategoriye yerleştirir.
 * @param {string} targetCat - 'zorbalik' veya 'not'
 */
function placeSelected(targetCat) {
    if (selectedWord === null) {
        // Önce kelime seç uyarısı
        document.getElementById('selectedHint').innerHTML =
            '<span style="color:var(--secondary);font-weight:700">⚠️ Önce bir kelimeye tıkla!</span>';
        return;
    }

    const wordId = selectedWord;
    const word   = WORDS.find(w => w.id === wordId);
    const chip   = document.getElementById('chip_' + wordId);

    // Orijinal chipı gizle (yerleştirildi)
    chip.classList.add('placed');

    // Hedef zone'a yeni chip ekle
    const targetContainer = document.getElementById(
        targetCat === 'zorbalik' ? 'chipsZorbalik' : 'chipsNot'
    );

    const newChip = document.createElement('div');
    newChip.className   = 'word-chip';
    newChip.id          = 'placed_' + wordId;
    newChip.textContent = word.text;
    // Hangi kutuya konulduğuna göre stil
    newChip.style.cssText = targetCat === 'zorbalik'
        ? 'background:rgba(230,57,70,0.08);border-color:var(--danger);color:var(--danger)'
        : 'background:rgba(67,233,123,0.08);border-color:var(--accent);color:#059669';

    targetContainer.appendChild(newChip);

    // Kayıt et
    placedWords[wordId] = targetCat;
    selectedWord = null;

    // İpucu temizle
    document.getElementById('selectedHint').innerHTML = '';

    // İlerlemeyi güncelle
    updateProgress();
}

/**
 * İlerleme çubuğunu günceller.
 */
function updateProgress() {
    const count = Object.keys(placedWords).length;
    document.getElementById('placedCount').textContent = count;
    document.getElementById('progressBar').style.width = (count / TOTAL * 100) + '%';

    // Tümü yerleştirilince otomatik kontrol et
    if (count === TOTAL) {
        setTimeout(checkAll, 500);
    }
}

/**
 * Tüm yerleştirmeleri kontrol eder ve skoru hesaplar.
 */
function checkAll() {
    const placed = Object.keys(placedWords).length;

    if (placed < TOTAL) {
        document.getElementById('selectedHint').innerHTML =
            '<span style="color:var(--secondary);font-weight:700">⚠️ Lütfen tüm kelimeleri yerleştir! (' + placed + '/' + TOTAL + ')</span>';
        return;
    }

    score = 0;

    WORDS.forEach(function (word) {
        const placedChip = document.getElementById('placed_' + word.id);
        if (!placedChip) return;

        if (placedWords[word.id] === word.category) {
            // Doğru yerleştirme
            score += POINTS;
            placedChip.style.cssText = 'background:rgba(67,233,123,0.15);border:2px solid var(--accent);color:#059669;font-weight:700';
        } else {
            // Yanlış yerleştirme — kırmızı göster
            placedChip.style.cssText = 'background:rgba(230,57,70,0.12);border:2px solid var(--danger);color:var(--danger);font-weight:700';
        }
    });

    // Skoru göster
    document.getElementById('scoreDisplay').textContent = score;

    // Sonuç modalı
    document.getElementById('finalScore').textContent = score;
    const percent = (score / (TOTAL * POINTS)) * 100;
    let emoji = '😔', msg = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; msg = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; msg = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; msg = 'İyi iş! Biraz daha çalışabilirsin.'; }

    document.querySelector('.result-emoji').textContent  = emoji;
    document.querySelector('.result-card p').textContent = msg;
    document.getElementById('resultOverlay').classList.add('show');

    // Skoru kaydet
    const saveStatus = document.getElementById('saveStatus');
    saveScore(3, score, TOTAL * POINTS, function (data) {
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
