<?php
/**
 * footer.php — Ortak Sayfa Altbilgisi
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */
?>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="footer-inner">

        <!-- Logo ve kısa açıklama -->
        <div class="footer-brand">
            <span class="footer-logo">
                <i class="fa-solid fa-shield-heart"></i> Gençlik Rehberim
            </span>
            <p>Akran zorbalığına karşı farkındalık ve doğru davranışları öğretmek amacıyla geliştirilmiştir.</p>
        </div>

        <!-- Hızlı bağlantılar -->
        <nav class="footer-nav" aria-label="Alt navigasyon">
            <h4>Hızlı Bağlantılar</h4>
            <ul>
                <li><a href="/genclik-rehberim/index.php">Ana Sayfa</a></li>
                <li><a href="/genclik-rehberim/games/bulmaca.php">Bulmaca</a></li>
                <li><a href="/genclik-rehberim/games/eslestirme.php">Eşleştirme</a></li>
                <li><a href="/genclik-rehberim/games/kategori.php">Kategori</a></li>
            </ul>
        </nav>

        <!-- İletişim / Bilgi -->
        <div class="footer-info">
            <h4>Proje Hakkında</h4>
            <p><i class="fa-solid fa-graduation-cap"></i> Web Tasarımı Dersi Projesi</p>
            <p><i class="fa-solid fa-code"></i> PHP · MySQL · JavaScript</p>
            <p><i class="fa-solid fa-heart"></i> Gençlere destek için yapıldı</p>
        </div>

    </div>

    <!-- Alt bar -->
    <div class="footer-bar">
        <p>&copy; <?= date('Y') ?> Gençlik Rehberim — Tüm hakları saklıdır.</p>
    </div>
</footer>
<!-- ===== FOOTER SONU ===== -->

<!-- Ana JavaScript dosyası -->
<script src="/genclik-rehberim/assets/js/main.js"></script>
</body>
</html>
