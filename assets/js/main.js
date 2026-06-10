/**
 * main.js — Genel JavaScript Yardımcıları
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

/* ============================================================
   NAVİGASYON — Hamburger Menü
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    /* Hamburger butonu ve nav listesi class seçicisiyle hedeflenir;
       #id bağımlılığı olmadan herhangi bir sayfada çalışır. */
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks  = document.querySelector('.nav-links');

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

    // Sayfanın herhangi bir yerine tıklanınca mobil menüyü ve dropdownları kapat
    document.addEventListener('click', function (e) {
        if (navLinks && navToggle) {
            if (!navLinks.contains(e.target) && !navToggle.contains(e.target)) {
                navLinks.classList.remove('open');
                const icon = navToggle.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = 'menu';
                navToggle.setAttribute('aria-expanded', 'false');
            }
        }
        // Dropdown dışına tıklanınca kapat
        document.querySelectorAll('.nav-dropdown').forEach(function (dd) {
            if (!dd.contains(e.target)) {
                dd.classList.remove('open');
                const btn = dd.querySelector('.nav-dropdown-btn');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // Dropdown butonları
    document.querySelectorAll('.nav-dropdown-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const dd = btn.closest('.nav-dropdown');
            const isOpen = dd.classList.contains('open');
            // Diğer açık dropdown'ları kapat
            document.querySelectorAll('.nav-dropdown').forEach(function (other) {
                other.classList.remove('open');
                const ob = other.querySelector('.nav-dropdown-btn');
                if (ob) ob.setAttribute('aria-expanded', 'false');
            });
            if (!isOpen) {
                dd.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
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

/* ============================================================
   SONUÇ MODALI — Puan sayaç animasyonu + konfeti
   ------------------------------------------------------------
   Tamamen eklemeli: her .result-overlay'i izler; 'show' sınıfı
   eklendiğinde içindeki puanı 0'dan hedefe sayar ve konfeti atar.
   Böylece oyun JS dosyalarını değiştirmeye gerek kalmaz.
   ============================================================ */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Bir sayı elemanını 0'dan hedefe doğru animasyonla artırır. */
    function countUp(el, target, duration) {
        if (reduceMotion || target <= 0) {
            el.textContent = String(target);
            return;
        }
        var startTs = null;
        function step(ts) {
            if (startTs === null) startTs = ts;
            var p = Math.min((ts - startTs) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            el.textContent = String(Math.round(eased * target));
            if (p < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = String(target);
            }
        }
        requestAnimationFrame(step);
    }

    var CONFETTI_COLORS = ['#005da7', '#3a6a00', '#f7e61a', '#a1fa49', '#ba1a1a', '#a4c9ff'];

    /* Ekranın üstünden düşen konfeti parçaları oluşturur. */
    function confettiBurst(count) {
        if (reduceMotion) return;
        var layer = document.createElement('div');
        layer.className = 'confetti-layer';
        for (var i = 0; i < count; i++) {
            var piece = document.createElement('span');
            piece.className = 'confetti-piece';
            piece.style.left              = (Math.random() * 100) + 'vw';
            piece.style.background        = CONFETTI_COLORS[i % CONFETTI_COLORS.length];
            piece.style.animationDuration = (2.4 + Math.random() * 1.8) + 's';
            piece.style.animationDelay    = (Math.random() * 0.5) + 's';
            piece.style.width             = (6 + Math.random() * 8) + 'px';
            piece.style.height            = (10 + Math.random() * 8) + 'px';
            if (Math.random() < 0.5) piece.style.borderRadius = '50%';
            layer.appendChild(piece);
        }
        document.body.appendChild(layer);
        setTimeout(function () { layer.remove(); }, 5200);
    }

    /* Bir .result-overlay göründüğünde puanı say + konfeti at. */
    function celebrate(overlay) {
        var scoreEl = overlay.querySelector('.result-score-big');
        var target  = scoreEl ? parseInt(scoreEl.textContent, 10) : NaN;
        if (scoreEl && !isNaN(target)) {
            countUp(scoreEl, target, 900);
        }
        // Konfeti yalnızca puan kazanıldıysa (kayıp ekranında değil)
        if (isNaN(target) || target > 0) {
            confettiBurst(48);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.result-overlay').forEach(function (overlay) {
            var obs = new MutationObserver(function () {
                var shown = overlay.classList.contains('show');
                if (shown && !overlay.dataset.celebrated) {
                    overlay.dataset.celebrated = '1';
                    celebrate(overlay);
                } else if (!shown) {
                    overlay.dataset.celebrated = '';
                }
            });
            obs.observe(overlay, { attributes: true, attributeFilter: ['class'] });
        });
    });
})();
