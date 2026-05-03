/**
 * main.js — Genel JavaScript Yardımcıları
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

/* ============================================================
   NAVİGASYON — Hamburger Menü
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    // Hamburger butonu ve nav linkleri elementleri
    const navToggle = document.getElementById('navToggle');
    const navLinks  = document.getElementById('navLinks');

    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            navLinks.classList.toggle('open');
            const isOpen = navLinks.classList.contains('open');
            // Material Symbols: metin içeriği değiştir
            const icon = navToggle.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = isOpen ? 'close' : 'menu';
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    // Sayfanın herhangi bir yerine tıklanınca mobil menüyü kapat
    document.addEventListener('click', function (e) {
        if (navLinks && navToggle) {
            if (!navLinks.contains(e.target) && !navToggle.contains(e.target)) {
                navLinks.classList.remove('open');
                const icon = navToggle.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = 'menu';
                navToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
});

/* ============================================================
   PUAN ÇUBUKLARI — Animate on load
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    // Tüm .score-bar-fill elementlerini bul ve genişliklerini animasyonlu yükle
    const bars = document.querySelectorAll('.score-bar-fill');
    bars.forEach(function (bar) {
        const targetWidth = bar.dataset.width || '0%';
        // Animasyonun görünür olması için kısa gecikme
        setTimeout(function () {
            bar.style.width = targetWidth;
        }, 300);
    });
});

/* ============================================================
   ALERT OTO-KAPANMA
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        // 4 saniye sonra alert'i gizle
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });
});

/* ============================================================
   AJAX PUAN KAYDETME
   Oyunlar tamamlandığında bu fonksiyon çağrılır
   ============================================================ */
/**
 * Oyun sonucunu sunucuya kaydeder.
 * @param {number} activityId - Etkinlik ID'si
 * @param {number} score      - Kazanılan puan
 * @param {number} maxScore   - Maksimum puan
 * @param {Function} callback - İşlem tamamlandığında çağrılır
 */
function saveScore(activityId, score, maxScore, callback) {
    // Fetch API ile AJAX isteği
    fetch('/genclik-rehberim/games/save_score.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            activity_id: activityId,
            score:       score,
            max_score:   maxScore
        })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (callback) callback(data);
    })
    .catch(function (err) {
        console.error('Puan kaydedilemedi:', err);
        if (callback) callback({ success: false });
    });
}

/* ============================================================
   SONUÇ MODALI
   ============================================================ */
/**
 * Oyun bitti modalını gösterir.
 * @param {number} score    - Kazanılan puan
 * @param {number} maxScore - Maksimum puan
 * @param {string} gameTitle - Oyun adı
 */
function showResult(score, maxScore, gameTitle) {
    const overlay = document.querySelector('.result-overlay');
    if (!overlay) return;

    // Emoji seç (puana göre)
    const percent = (score / maxScore) * 100;
    let emoji = '😔';
    let message = 'Daha iyi yapabilirsin!';
    if (percent === 100) { emoji = '🏆'; message = 'Mükemmel! Hepsini doğru yaptın!'; }
    else if (percent >= 70) { emoji = '🎉'; message = 'Harika! Çok iyi bir skor!'; }
    else if (percent >= 40) { emoji = '👍'; message = 'İyi iş! Biraz daha çalışabilirsin.'; }

    // Modal içeriğini doldur
    const el = overlay.querySelector('.result-emoji');
    const scoreEl = overlay.querySelector('.result-score-big');
    const msgEl   = overlay.querySelector('.result-card p');
    if (el)      el.textContent = emoji;
    if (scoreEl) scoreEl.textContent = score + '/' + maxScore;
    if (msgEl)   msgEl.textContent = message;

    // Modalı göster
    overlay.classList.add('show');
}

/**
 * Sonuç modalını kapatır.
 */
function closeResult() {
    const overlay = document.querySelector('.result-overlay');
    if (overlay) overlay.classList.remove('show');
}

/* ============================================================
   KLAVYE KISAYOLLARI
   ============================================================ */
document.addEventListener('keydown', function (e) {
    // ESC tuşuyla sonuç modalını kapat
    if (e.key === 'Escape') closeResult();
});
