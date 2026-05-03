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

            <!-- Sol: Metin -->
            <div class="hero-text">
                <!-- Gamification rozeti -->
                <div class="hero-badge">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">stars</span>
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
                        <a href="/genclik-rehberim/dashboard.php" class="btn btn-primary btn-lg">
                            <span class="material-symbols-outlined">bar_chart</span> Panele Git
                        </a>
                    <?php else: ?>
                        <a href="/genclik-rehberim/register.php" class="btn btn-primary btn-lg">
                            <span class="material-symbols-outlined">rocket_launch</span> Hemen Başla
                        </a>
                        <a href="/genclik-rehberim/login.php" class="btn btn-surface btn-lg">
                            <span class="material-symbols-outlined">play_circle</span> Nasıl Çalışır?
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sağ: Görsel -->
            <div class="hero-visual" aria-hidden="true">
                <div class="hero-visual-bg"></div>
                <img
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuA1a12dl0OwJg5B0SZ14DTQlbC8OVnIJn50bWvOZ0H3IBz42j1Ge_qLViey_Fk2XYwRtYQmf7s2nvq4947nod0aO32JM9cS4oIYuUJHcWDYEydT4vcHoeTIJT68CkDGlPE0OZ-0EBGnWxQQ-aTrV6woCV8z2agpWj-VNBymzar-Y2rOyUMsADer6igK8Y8DLON2DWepkBtmyDsLu3MXikvAwgnW0TA-j7jCK25s3Rd7YuaddIn6S5wxVvbb-URo5gnorEwpk4LI3imN"
                    alt="Pozitif enerji dolu öğrenciler"
                    class="hero-image"
                    loading="lazy">
                <div class="hero-floating-card">
                    <div class="hero-floating-icon">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">favorite</span>
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
    <section class="games-section" aria-label="Etkinlikler">
        <div class="section-title">
            <h2>Eğlenirken <span>Öğren</span></h2>
            <p>Kendini geliştirmek hiç bu kadar keyifli olmamıştı.</p>
        </div>

        <div class="game-grid">

            <!-- Bulmaca Kartı -->
            <article class="game-card card-bulmaca">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">extension</span>
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
                <div class="game-card-preview">
                    <img
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCPUGP5lIwsHR4-QzBI2banxpDuTGwWw994X_j9N1zZ4QFygnL05FRcrxItPRFsRzyduQ5T6ciJK-NklP5OPQeSi18Cv_BjDohO-eWdJ7DiSUuj6Wk-wB-FcLPqJ1ZA8RcFEMhUsIE04yxputL_dnwYwZ6OmCHXioPCPpJWP6RZqrCuopUQxgoTWCyvEv86YvASGCl6plNhQqzy37RpREBe2pfsF6qSp2GtMQtA9hcY6wyV1E9kQlpUtrDW0EC6hWnVvcnERhI9A_Px"
                        alt="Bulmaca Önizleme"
                        loading="lazy">
                </div>
                <a href="/genclik-rehberim/games/bulmaca.php" class="btn btn-primary">
                    <span class="material-symbols-outlined">play_arrow</span> Oyna
                </a>
            </article>

            <!-- Eşleştirme Kartı -->
            <article class="game-card card-eslestirme">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">join_inner</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Doğru mu, Yanlış mı?</h3>
                    <p>Davranışları sürükle-bırak ile doğru ve yanlış kutularına yerleştir. 14 kart eşleştirme!</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 140 Puan
                </div>
                <div class="game-card-preview">
                    <img
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDTd_WKaslyxrGXH234c6wW3-u3HsjdJ4ptAcUMyGYC3BAppDPOtGXioPCPpJWP6RZqrCuopUQxgoTWCyvEv86YvASGCl6plNhQqzy37RpREBe2pfsF6qSp2GtMQtA9hcY6wyV1E9kQlpUtrDW0EC6hWnVvcnERhI9A_Px"
                        alt="Eşleştirme Önizleme"
                        loading="lazy">
                </div>
                <a href="/genclik-rehberim/games/eslestirme.php" class="btn btn-secondary">
                    <span class="material-symbols-outlined">play_arrow</span> Başla
                </a>
            </article>

            <!-- Kategori Kartı -->
            <article class="game-card card-kategori">
                <div class="game-card-top">
                    <div class="game-card-icon">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">category</span>
                    </div>
                    <span class="game-card-arrow">
                        <span class="material-symbols-outlined">arrow_outward</span>
                    </span>
                </div>
                <div>
                    <h3>Zorbalık mı, Değil mi?</h3>
                    <p>17 kelimeyi "Zorbalık" ve "Zorbalık Değil" kutularına yerleştir. Farkındalığını artır!</p>
                </div>
                <div class="score-badge">
                    <span class="material-symbols-outlined">star</span> Max 170 Puan
                </div>
                <div class="game-card-preview">
                    <img
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCI3xG9eRCTmh3biwFdqp_mfXC1v40DWsxGWBXFl6s-4EorAWwifYOMsBYHdJjoc2C9lUrVJ5KMs6jowW1ZBrQFUr-LFlHv58-ubjSR4MloUPOfLU-u5pAE9iSbgtJZISx_zKXyqmtSk6WN-gnNb0ErZRXum_Zc6zDS1_WAHaE1I5ADKFF3Xc7D-OX4NRDvX6IXO0CswqffEFkaUann6x_bkEOuyK7se7lWQjRfetsRnyujNTU0N--BE6fwTM-zXE7o3H7afws0flx8"
                        alt="Kategori Önizleme"
                        loading="lazy">
                </div>
                <a href="/genclik-rehberim/games/kategori.php" class="btn btn-success">
                    <span class="material-symbols-outlined">play_arrow</span> Sırala
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
