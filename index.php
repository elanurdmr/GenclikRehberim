<?php
/**
 * index.php — Ana Sayfa
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pageTitle = 'Ana Sayfa';

// Liderlik tablosunu al (ana sayfa için top 3)
$leaderboard = getLeaderboard(3);
?>
<?php include 'includes/header.php'; ?>

<!-- ===== ANA SAYFA ===== -->
<main>

    <!-- Hero bölümü -->
    <section class="hero" aria-label="Ana tanıtım">
        <div class="hero-content">

            <!-- Sol: Metin -->
            <div class="hero-text">
                <!-- Gamification rozeti -->
                <div class="hero-badge">
                    <span class="material-symbols-outlined">stars</span>
                    Gençlik Rehberim
                </div>

                <h1>
                    Birlikte Daha Güçlüyüz:<br>
                    <span>Zorbalığa Dur De!</span>
                </h1>

                <p>
                    Okulda ve dijital dünyada güvenli bir alan yaratmak senin elinde.
                    Oyunlar oynayarak öğren ve pozitif enerjini etrafına yay.
                </p>

                <div class="hero-buttons">
                    <?php if (isLoggedIn()): ?>
                        <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-primary btn-lg">
                            <span class="material-symbols-outlined">bar_chart</span> Panele Git
                        </a>
                    <?php else: ?>
                        <a href="/genclik-rehberim/kayitol.php" class="btn btn-primary btn-lg">
                            <span class="material-symbols-outlined">rocket_launch</span> Hemen Başla
                        </a>
                        <a href="/genclik-rehberim/girisyap.php" class="btn btn-surface btn-lg">
                            <span class="material-symbols-outlined">play_circle</span> Nasıl Çalışır?
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sağ: Görsel -->
            <div class="hero-visual" aria-hidden="true">
                <div class="hero-visual-bg"></div>
                <img
                    src="./docs/photos/akranzorbaligi.png"
                    alt="Pozitif enerji dolu öğrenciler"
                    class="hero-image"
                    width="600"
                    height="460"
                    loading="lazy">
                <div class="hero-floating-card">
                    <div class="hero-floating-icon">
                        <span class="material-symbols-outlined">favorite</span>
                    </div>
                    <div>
                        <p class="hero-floating-num">1000+</p>
                        <p class="hero-floating-label">Destekçi</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Oyun kartları bölümü -->
    <section class="games-section" id="oyunlar" aria-label="Etkinlikler">
        <div class="section-title">
            <h2>Eğlenirken <span>Öğren</span></h2>
            <p>Kendini geliştirmek hiç bu kadar keyifli olmamıştı.</p>
        </div>

        <div class="game-grid">

            <!-- Bulmaca Kartı -->
            <article class="game-card card-bulmaca">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined">extension</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Zorbalık Bulmacası</h3>
                    <p>Zorba davranışa karşı koyma yöntemlerini soruları cevaplayarak öğren. 10 soruluk kelime bulmacası!</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 100 Puan
                </div>
                <a href="/genclik-rehberim/games/bulmaca.php" class="btn btn-primary">
                    <span class="material-symbols-outlined">play_arrow</span> Oyna
                </a>
            </article>

            <!-- Çengel bulmaca -->
            <article class="game-card card-bulmaca">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined">grid_on</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Çengel Bulmaca</h3>
                    <p>Aynı 10 soru; solda ipuçları, sağda tahmin. Zorbalıkla başa çıkma ipuçlarını pekiştir.</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 100 Puan
                </div>
                <a href="/genclik-rehberim/games/cengelbulmaca.php" class="btn btn-secondary">
                    <span class="material-symbols-outlined">play_arrow</span> Oyna
                </a>
            </article>

            <!-- Wordle -->
            <article class="game-card card-wordle">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined">spellcheck</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Wordle (5 Harf)</h3>
                    <p>Türkçe harflerle altı denemede kelimeyi bul. Yeşil, sarı ve gri ipuçlarını kullan.</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 100 Puan
                </div>
                <a href="/genclik-rehberim/games/wordle.php" class="btn btn-primary">
                    <span class="material-symbols-outlined">play_arrow</span> Oyna
                </a>
            </article>

            <!-- Eşleştirme + Kategori (İki Bölümlü) -->
            <article class="game-card card-eslestirme">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined">join_inner</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Eşleştirme</h3>
                    <p>İki bölümlü oyun: kartları sürükle-bırak ile eşleştir, ardından kelimeleri doğru kategoriye yerleştir!</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 390 Puan
                </div>
                <a href="/genclik-rehberim/games/eslestirme.php" class="btn btn-secondary">
                    <span class="material-symbols-outlined">play_arrow</span> Başla
                </a>
            </article>

            <!-- Benim Hikayem -->
            <article class="game-card card-hikaye">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined">auto_stories</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Benim Hikayem</h3>
                    <p>15 karar noktasında empati ve cesaret testine gir. Zorbalık karşısında nasıl davranırsın?</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 100 Puan
                </div>
                <a href="/genclik-rehberim/games/benimhikayem.php" class="btn btn-hikaye">
                    <span class="material-symbols-outlined">play_arrow</span> Oyna
                </a>
            </article>

            <!-- Farkındalık Zinciri -->
            <article class="game-card card-zincir">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined">link</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Farkındalık Zinciri</h3>
                    <p>Bilgisayarın verdiği kelimeden başlayarak 120 saniyede empati ve dostluk zinciri kur!</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 100 Puan
                </div>
                <a href="/genclik-rehberim/games/farkindalikzinciri.php" class="btn btn-zincir">
                    <span class="material-symbols-outlined">play_arrow</span> Oyna
                </a>
            </article>

        </div>
    </section>

    <!-- Bilgi bölümü -->
    <section class="info-section" aria-label="Zorbalık hakkında bilgi">
        <div class="info-section-inner">
            <h2>Akran Zorbalığı Nedir?</h2>
            <p>
                Akran zorbalığı; bir kişinin başkalarına tekrarlı olarak fiziksel, sözel veya sosyal zarar vermesidir.
                Doğru davranışları öğrenmek zorbalığı durdurmada en etkili adımdır.
            </p>

            <div class="info-cards">
                <article class="info-card">
                    <span class="info-card-icon" aria-hidden="true">🛡️</span>
                    <h3>Kendini Koru</h3>
                    <p>Zorbalıkla karşılaştığında güvenli bir yere git ve yetişkinlere haber ver.</p>
                </article>
                <article class="info-card">
                    <span class="info-card-icon" aria-hidden="true">🤝</span>
                    <h3>Destekle</h3>
                    <p>Zorbalığa uğrayan arkadaşının yanında ol ve ona destek ver.</p>
                </article>
                <article class="info-card">
                    <span class="info-card-icon" aria-hidden="true">📢</span>
                    <h3>Bildir</h3>
                    <p>Gördüğün zorbalığı öğretmenine veya güvendiğin bir yetişkine söyle.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Liderlik Tablosu (kısa) -->
    <?php if (!empty($leaderboard)): ?>
    <section class="leaderboard-section" aria-label="Liderlik tablosu">
        <div class="section-title">
            <h2>🏆 <span>Liderler</span></h2>
            <p>En yüksek puanı toplayan öğrenciler</p>
        </div>
        <div class="card">
            <div class="leaderboard-list">
                <?php foreach ($leaderboard as $i => $leader): ?>
                <div class="leaderboard-row">
                    <div class="leaderboard-rank <?= $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-other')) ?>">
                        <?= $i===0?'🥇':($i===1?'🥈':($i===2?'🥉':'#'.($i+1))) ?>
                    </div>
                    <div>
                        <div class="leaderboard-name"><?= e($leader['username']) ?></div>
                        <div class="leaderboard-meta"><?= $leader['games_played'] ?> oyun oynandı</div>
                    </div>
                    <div class="leaderboard-score">
                        <?= $leader['total_score'] ?> <span>puan</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>
<!-- ===== ANA SAYFA SONU ===== -->

<?php include 'includes/footer.php'; ?>
