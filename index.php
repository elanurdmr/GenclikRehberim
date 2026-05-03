<?php
/**
 * index.php — Ana Sayfa
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Liderlik tablosunu al (ana sayfa için top 3)
$leaderboard = getLeaderboard(3);
?>
<?php include 'includes/header.php'; ?>

<!-- ===== ANA SAYFA ===== -->
<main>

    <!-- Hero bölümü -->
    <section class="hero" aria-label="Ana tanıtım">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa-solid fa-shield-heart"></i>
                Akran Zorbalığı Farkındalık Projesi
            </div>
            <h1>Zorbalığa Karşı Birlikte,<br>Güç Sende!</h1>
            <p>
                Eğlenceli oyunlar ve etkinliklerle zorbalığa karşı doğru davranışları öğren.
                Her doğru cevap seni daha güçlü kılar!
            </p>
            <div class="hero-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="/genclik-rehberim/dashboard.php" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-chart-line"></i> Panele Git
                    </a>
                <?php else: ?>
                    <a href="/genclik-rehberim/register.php" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-rocket"></i> Hemen Başla
                    </a>
                    <a href="/genclik-rehberim/login.php" class="btn btn-outline btn-lg"
                       style="border-color:rgba(255,255,255,0.5);color:white">
                        <i class="fa-solid fa-right-to-bracket"></i> Giriş Yap
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Özellikler bölümü -->
    <section class="games-section" aria-label="Etkinlikler">
        <div class="section-title">
            <h2>Hangi Etkinliği <span>Oynamak İstersin?</span></h2>
            <p>Her etkinlik zorbalıkla mücadelede farklı bir beceri kazandırır</p>
        </div>

        <div class="game-grid">

            <!-- Bulmaca Kartı -->
            <article class="game-card card-bulmaca">
                <div class="game-card-icon">
                    <i class="fa-solid fa-puzzle-piece"></i>
                </div>
                <h3>Zorbalık Bulmacası</h3>
                <p>
                    Zorba davranışa karşı koyma yöntemlerini soruları cevaplayarak öğren.
                    10 soruluk kelime bulmacası!
                </p>
                <div class="score-badge">
                    <i class="fa-solid fa-star"></i> Max 100 Puan
                </div>
                <a href="/genclik-rehberim/games/bulmaca.php" class="btn btn-primary">
                    <i class="fa-solid fa-play"></i> Oyna
                </a>
            </article>

            <!-- Eşleştirme Kartı -->
            <article class="game-card card-eslestirme">
                <div class="game-card-icon">
                    <i class="fa-solid fa-arrows-left-right"></i>
                </div>
                <h3>Doğru mu, Yanlış mı?</h3>
                <p>
                    Davranışları sürükle-bırak ile doğru ve yanlış kutularına yerleştir.
                    14 kart eşleştirme!
                </p>
                <div class="score-badge">
                    <i class="fa-solid fa-star"></i> Max 140 Puan
                </div>
                <a href="/genclik-rehberim/games/eslestirme.php" class="btn btn-secondary">
                    <i class="fa-solid fa-play"></i> Başla
                </a>
            </article>

            <!-- Kategori Kartı -->
            <article class="game-card card-kategori">
                <div class="game-card-icon">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <h3>Zorbalık mı, Değil mi?</h3>
                <p>
                    17 kelimeyi "Zorbalık" ve "Zorbalık Değil" kutularına yerleştir.
                    Farkındalığını artır!
                </p>
                <div class="score-badge">
                    <i class="fa-solid fa-star"></i> Max 170 Puan
                </div>
                <a href="/genclik-rehberim/games/kategori.php" class="btn btn-success">
                    <i class="fa-solid fa-play"></i> Sırala
                </a>
            </article>

        </div>
    </section>

    <!-- Bilgi bölümü -->
    <section style="background: linear-gradient(135deg, #1a1a2e, #16213e); padding: 5rem 1.5rem; color: white;">
        <div style="max-width:900px;margin:0 auto;text-align:center">
            <h2 style="font-size:2rem;font-weight:900;margin-bottom:1rem">
                Akran Zorbalığı Nedir?
            </h2>
            <p style="color:rgba(255,255,255,0.75);font-size:1.1rem;max-width:650px;margin:0 auto 3rem">
                Akran zorbalığı; bir kişinin başkalarına tekrarlı olarak fiziksel, sözel veya sosyal zarar vermesidir.
                Doğru davranışları öğrenmek zorbalığı durdurmada en etkili adımdır.
            </p>

            <!-- 3 bilgi kartı -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:2rem">
                <div style="background:rgba(255,255,255,0.05);border-radius:16px;padding:2rem;border:1px solid rgba(255,255,255,0.1)">
                    <div style="font-size:2.5rem;margin-bottom:1rem">🛡️</div>
                    <h3 style="font-weight:800;margin-bottom:0.5rem">Kendini Koru</h3>
                    <p style="color:rgba(255,255,255,0.65);font-size:0.95rem">
                        Zorbalıkla karşılaştığında güvenli bir yere git ve yetişkinlere haber ver.
                    </p>
                </div>
                <div style="background:rgba(255,255,255,0.05);border-radius:16px;padding:2rem;border:1px solid rgba(255,255,255,0.1)">
                    <div style="font-size:2.5rem;margin-bottom:1rem">🤝</div>
                    <h3 style="font-weight:800;margin-bottom:0.5rem">Destekle</h3>
                    <p style="color:rgba(255,255,255,0.65);font-size:0.95rem">
                        Zorbalığa uğrayan arkadaşının yanında ol ve ona destek ver.
                    </p>
                </div>
                <div style="background:rgba(255,255,255,0.05);border-radius:16px;padding:2rem;border:1px solid rgba(255,255,255,0.1)">
                    <div style="font-size:2.5rem;margin-bottom:1rem">📢</div>
                    <h3 style="font-weight:800;margin-bottom:0.5rem">Bildir</h3>
                    <p style="color:rgba(255,255,255,0.65);font-size:0.95rem">
                        Gördüğün zorbalığı öğretmenine veya güvendiğin bir yetişkine söyle.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Liderlik Tablosu (kısa) -->
    <?php if (!empty($leaderboard)): ?>
    <section style="padding:5rem 1.5rem;max-width:600px;margin:0 auto;text-align:center">
        <div class="section-title">
            <h2>🏆 <span>Liderler</span></h2>
            <p>En yüksek puanı toplayan öğrenciler</p>
        </div>
        <div class="card">
            <?php foreach ($leaderboard as $i => $leader): ?>
            <div style="display:flex;align-items:center;gap:1rem;padding:1rem;
                        border-bottom:<?= $i < count($leaderboard)-1 ? '1px solid var(--bg-light)' : 'none' ?>">
                <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                    font-weight:900;font-size:1rem;flex-shrink:0;
                    background:<?= $i===0?'linear-gradient(135deg,#FFBE0B,#f59e0b)':($i===1?'linear-gradient(135deg,#a8b0c0,#94a3b8)':'linear-gradient(135deg,#c47722,#b45309)') ?>;
                    color:white;box-shadow:0 2px 8px rgba(0,0,0,0.2)">
                    <?= $i===0?'🥇':($i===1?'🥈':'🥉') ?>
                </div>
                <div style="flex:1;text-align:left">
                    <strong><?= e($leader['username']) ?></strong>
                    <div style="font-size:0.8rem;color:var(--text-muted)"><?= $leader['games_played'] ?> oyun oynandı</div>
                </div>
                <div style="font-size:1.3rem;font-weight:900;color:var(--primary)">
                    <?= $leader['total_score'] ?> <span style="font-size:0.75rem;color:var(--text-muted)">puan</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</main>
<!-- ===== ANA SAYFA SONU ===== -->

<?php include 'includes/footer.php'; ?>
