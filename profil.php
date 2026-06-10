<?php
/**
 * profil.php — Öğrenci profil sayfası
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Profilim';
requireLogin();

if (isAdmin()) {
    header('Location: /genclik-rehberim/admin/index.php');
    exit;
}

$userId   = (int)$_SESSION['user_id'];
$db       = getDB();
$success  = '';
$error    = '';

// Güncel kullanıcı verisi
$user        = getUserById($userId);
$totalScore  = getUserTotalScore($userId);
$history     = getUserHistory($userId, 100);
$gamesPlayed = count($history);
$earnedBadges = getEarnedBadges($userId);

// POST işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        $newUsername = trim($_POST['username'] ?? '');
        $newEmail    = trim($_POST['email']    ?? '');

        if (mb_strlen($newUsername) < 3 || mb_strlen($newUsername) > 30) {
            $error = 'Kullanıcı adı 3-30 karakter arasında olmalıdır.';
        } elseif (!preg_match('/^[\w\-\.]+$/u', $newUsername)) {
            $error = 'Kullanıcı adı yalnızca harf, rakam, -, _ ve . içerebilir.';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Geçerli bir e-posta adresi giriniz.';
        } elseif (isUsernameOrEmailTaken($newUsername, $newEmail, $userId)) {
            $error = 'Bu kullanıcı adı veya e-posta zaten kullanımda.';
        } else {
            updateUserInfo($userId, $newUsername, $newEmail);
            $_SESSION['username'] = $newUsername;
            $user['username']     = $newUsername;
            $user['email']        = $newEmail;
            $success = 'Bilgileriniz başarıyla güncellendi.';
        }

    } elseif ($action === 'change_password') {
        $currentPw  = $_POST['current_password']  ?? '';
        $newPw      = $_POST['new_password']       ?? '';
        $confirmPw  = $_POST['confirm_password']   ?? '';
        $currentHash = getUserPasswordHash($userId);

        if (!password_verify($currentPw, $currentHash)) {
            $error = 'Mevcut şifreniz hatalı.';
        } elseif (mb_strlen($newPw) < 6) {
            $error = 'Yeni şifre en az 6 karakter olmalıdır.';
        } elseif ($newPw !== $confirmPw) {
            $error = 'Yeni şifreler eşleşmiyor.';
        } else {
            updateUserPassword($userId, password_hash($newPw, PASSWORD_BCRYPT));
            $success = 'Şifreniz başarıyla güncellendi.';
        }
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main style="max-width:960px;margin:0 auto;padding:2rem 1rem 6rem">

    <!-- Sayfa başlığı -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
        <div>
            <h1 style="font-size:clamp(1.6rem,4vw,2.2rem);font-weight:800;margin-bottom:.4rem">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:var(--primary);vertical-align:middle;font-size:2rem">manage_accounts</span>
                Profilim
            </h1>
            <p style="color:var(--on-surface-variant)">Hesap bilgilerini görüntüle ve güncelle.</p>
        </div>
        <a href="/genclik-rehberim/ogrencipanel.php" class="btn btn-surface btn-sm">
            <span class="material-symbols-outlined">arrow_back</span> Panele Dön
        </a>
    </div>

    <!-- Bildirimler -->
    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:1.5rem">
        <span class="material-symbols-outlined">check_circle</span>
        <?= e($success) ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger" style="margin-bottom:1.5rem">
        <span class="material-symbols-outlined">error</span>
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

        <!-- Sol kolon: İstatistikler + Rozetler -->
        <div style="display:flex;flex-direction:column;gap:1.25rem">

            <!-- Profil özeti kartı -->
            <div class="card">
                <div style="padding:2rem;text-align:center;border-bottom:1px solid var(--surface-variant)">
                    <div style="width:80px;height:80px;border-radius:9999px;background:linear-gradient(135deg,var(--primary-fixed),var(--primary));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:40px;color:#fff">person</span>
                    </div>
                    <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:.25rem"><?= e($user['username']) ?></h2>
                    <p style="color:var(--on-surface-variant);font-size:.9rem"><?= e($user['email']) ?></p>
                    <p style="font-size:.8rem;color:var(--on-surface-variant);margin-top:.25rem">
                        Üye: <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                    </p>
                </div>
                <!-- İstatistikler -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;text-align:center;padding:1rem 0">
                    <div style="padding:.75rem;border-right:1px solid var(--surface-variant)">
                        <div style="font-size:1.4rem;font-weight:900;color:var(--primary)"><?= (int)$totalScore ?></div>
                        <div style="font-size:.75rem;color:var(--on-surface-variant);font-weight:600">Toplam XP</div>
                    </div>
                    <div style="padding:.75rem;border-right:1px solid var(--surface-variant)">
                        <div style="font-size:1.4rem;font-weight:900;color:var(--secondary)"><?= (int)$gamesPlayed ?></div>
                        <div style="font-size:.75rem;color:var(--on-surface-variant);font-weight:600">Oyun</div>
                    </div>
                    <div style="padding:.75rem">
                        <div style="font-size:1.4rem;font-weight:900;color:var(--tertiary)"><?= count($earnedBadges) ?></div>
                        <div style="font-size:.75rem;color:var(--on-surface-variant);font-weight:600">Rozet</div>
                    </div>
                </div>
            </div>

            <!-- Kazanılan rozetler -->
            <?php if (!empty($earnedBadges)): ?>
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">military_tech</span>
                        Rozetlerim
                    </h2>
                </div>
                <div style="padding:1rem;display:flex;flex-wrap:wrap;gap:.5rem">
                    <?php foreach ($earnedBadges as $badge): ?>
                    <span class="badge" style="background:var(--tertiary-fixed);color:var(--tertiary);padding:.3rem .75rem;font-size:.8rem">
                        🏅 <?= e($badge) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Sağ kolon: Formlar -->
        <div style="display:flex;flex-direction:column;gap:1.25rem">

            <!-- Bilgileri Güncelle formu -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">edit</span>
                        Bilgileri Güncelle
                    </h2>
                </div>
                <form method="POST" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
                    <input type="hidden" name="action" value="update_info">

                    <div>
                        <label for="prf-username" style="display:block;font-weight:700;font-size:.875rem;margin-bottom:.5rem">
                            Kullanıcı Adı
                        </label>
                        <input
                            type="text"
                            id="prf-username"
                            name="username"
                            value="<?= e($user['username']) ?>"
                            maxlength="30"
                            required
                            style="width:100%;padding:.75rem 1rem;border-radius:10px;border:2px solid var(--outline-variant);font-family:var(--font);font-size:.9rem;outline:none;transition:border-color .2s;box-sizing:border-box"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--outline-variant)'">
                    </div>

                    <div>
                        <label for="prf-email" style="display:block;font-weight:700;font-size:.875rem;margin-bottom:.5rem">
                            E-posta
                        </label>
                        <input
                            type="email"
                            id="prf-email"
                            name="email"
                            value="<?= e($user['email']) ?>"
                            maxlength="100"
                            required
                            style="width:100%;padding:.75rem 1rem;border-radius:10px;border:2px solid var(--outline-variant);font-family:var(--font);font-size:.9rem;outline:none;transition:border-color .2s;box-sizing:border-box"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--outline-variant)'">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span class="material-symbols-outlined">save</span>
                        Kaydet
                    </button>
                </form>
            </div>

            <!-- Şifre Değiştir formu -->
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">lock</span>
                        Şifre Değiştir
                    </h2>
                </div>
                <form method="POST" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
                    <input type="hidden" name="action" value="change_password">

                    <div>
                        <label for="prf-cur-pw" style="display:block;font-weight:700;font-size:.875rem;margin-bottom:.5rem">
                            Mevcut Şifre
                        </label>
                        <input
                            type="password"
                            id="prf-cur-pw"
                            name="current_password"
                            autocomplete="current-password"
                            required
                            style="width:100%;padding:.75rem 1rem;border-radius:10px;border:2px solid var(--outline-variant);font-family:var(--font);font-size:.9rem;outline:none;transition:border-color .2s;box-sizing:border-box"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--outline-variant)'">
                    </div>

                    <div>
                        <label for="prf-new-pw" style="display:block;font-weight:700;font-size:.875rem;margin-bottom:.5rem">
                            Yeni Şifre <span style="font-size:.78rem;font-weight:400;color:var(--on-surface-variant)">(en az 6 karakter)</span>
                        </label>
                        <input
                            type="password"
                            id="prf-new-pw"
                            name="new_password"
                            autocomplete="new-password"
                            minlength="6"
                            required
                            style="width:100%;padding:.75rem 1rem;border-radius:10px;border:2px solid var(--outline-variant);font-family:var(--font);font-size:.9rem;outline:none;transition:border-color .2s;box-sizing:border-box"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--outline-variant)'">
                    </div>

                    <div>
                        <label for="prf-conf-pw" style="display:block;font-weight:700;font-size:.875rem;margin-bottom:.5rem">
                            Yeni Şifre (Tekrar)
                        </label>
                        <input
                            type="password"
                            id="prf-conf-pw"
                            name="confirm_password"
                            autocomplete="new-password"
                            minlength="6"
                            required
                            style="width:100%;padding:.75rem 1rem;border-radius:10px;border:2px solid var(--outline-variant);font-family:var(--font);font-size:.9rem;outline:none;transition:border-color .2s;box-sizing:border-box"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--outline-variant)'">
                    </div>

                    <!-- Şifre eşleşme göstergesi -->
                    <div id="pw-match-hint" style="font-size:.8rem;display:none"></div>

                    <button type="submit" class="btn btn-danger" style="background:var(--error);color:#fff">
                        <span class="material-symbols-outlined">lock_reset</span>
                        Şifreyi Güncelle
                    </button>
                </form>
            </div>

        </div>
    </div>
</main>

<script>
(function () {
    const newPw  = document.getElementById('prf-new-pw');
    const confPw = document.getElementById('prf-conf-pw');
    const hint   = document.getElementById('pw-match-hint');

    function checkMatch() {
        if (!confPw.value) { hint.style.display = 'none'; return; }
        if (newPw.value === confPw.value) {
            hint.textContent = '✓ Şifreler eşleşiyor';
            hint.style.cssText = 'font-size:.8rem;display:block;color:#43a047;font-weight:700';
        } else {
            hint.textContent = '✗ Şifreler eşleşmiyor';
            hint.style.cssText = 'font-size:.8rem;display:block;color:var(--error);font-weight:700';
        }
    }

    if (newPw && confPw) {
        newPw.addEventListener('input', checkMatch);
        confPw.addEventListener('input', checkMatch);
    }
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
