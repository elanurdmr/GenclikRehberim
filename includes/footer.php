<?php
/**
 * footer.php — Ortak Sayfa Altbilgisi
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */
?>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="footer-inner">

        <!-- Marka ve açıklama -->
        <div class="footer-brand">
            <span class="footer-logo">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">shield_person</span>
                Gençlik Rehberim
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

        <!-- Hukuki bağlantılar -->
        <nav class="footer-nav" aria-label="Hukuki bağlantılar">
            <h4>Bağlantılar</h4>
            <ul>
                <li><a href="#">Hakkımızda</a></li>
                <li><a href="#">Gizlilik Politikası</a></li>
                <li><a href="#">Kullanım Şartları</a></li>
                <li><a href="#">İletişim</a></li>
            </ul>
        </nav>

        <!-- Proje bilgisi -->
        <div class="footer-info">
            <h4>Proje Hakkında</h4>
            <p>
                <span class="material-symbols-outlined">school</span>
                Web Tasarımı Dersi Projesi
            </p>
            <p>
                <span class="material-symbols-outlined">code</span>
                PHP · MySQL · JavaScript
            </p>
            <p>
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">favorite</span>
                Gençlere destek için yapıldı
            </p>
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
