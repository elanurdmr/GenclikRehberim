<?php
/**
 * geri-bildirim.php — Öğrenci geri bildirim formu
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Geri Bildirim';
requireLogin();

if (isAdmin()) {
    header('Location: /genclik-rehberim/admin/feedback.php');
    exit;
}

$userId  = (int)$_SESSION['user_id'];
$success = false;
$error   = '';

// Daha önce gönderilmiş bildirimleri getir
$prevFeedback = getUserFeedback($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $message  = trim($_POST['message'] ?? '');

    $allowed = ['konu', 'platform', 'oyun', 'diger'];
    if (!in_array($category, $allowed, true)) {
        $error = 'Lütfen bir kategori seçiniz.';
    } elseif (mb_strlen($message) < 10) {
        $error = 'Mesajınız en az 10 karakter olmalıdır.';
    } elseif (mb_strlen($message) > 2000) {
        $error = 'Mesajınız en fazla 2000 karakter olabilir.';
    } else {
        if (saveFeedback($userId, $category, $message)) {
            $success = true;
            $prevFeedback = getUserFeedback($userId);
        } else {
            $error = 'Geri bildirim kaydedilirken bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

$categoryLabels = [
    'konu'     => 'Konu Hakkında',
    'platform' => 'Platform / Site',
    'oyun'     => 'Oyunlar',
    'diger'    => 'Diğer',
];
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main style="max-width:860px;margin:0 auto;padding:2rem 1rem 6rem">

    <!-- Sayfa başlığı -->
    <div style="margin-bottom:2rem">
        <h1 style="font-size:clamp(1.6rem,4vw,2.2rem);font-weight:800;margin-bottom:.4rem">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;color:var(--secondary);vertical-align:middle;font-size:2rem">feedback</span>
            Geri Bildirim
        </h1>
        <p style="color:var(--on-surface-variant);line-height:1.6">
            Akran zorbalığı konusu hakkındaki düşüncelerini, platform önerileri ve oyunlarla ilgili görüşlerini paylaşabilirsin.
        </p>
    </div>

    <!-- Başarı mesajı -->
    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:1.5rem">
        <span class="material-symbols-outlined">check_circle</span>
        Geri bildiriminiz başarıyla gönderildi. Teşekkürler!
    </div>
    <?php endif; ?>

    <!-- Hata mesajı -->
    <?php if ($error): ?>
    <div class="alert alert-danger" style="margin-bottom:1.5rem">
        <span class="material-symbols-outlined">error</span>
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

        <!-- Form -->
        <div class="card" style="height:fit-content">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">edit_note</span>
                    Yeni Geri Bildirim
                </h2>
            </div>
            <form method="POST" style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem">

                <!-- Kategori -->
                <div>
                    <label style="display:block;font-weight:700;font-size:.9rem;margin-bottom:.6rem;color:var(--on-surface)">
                        Kategori <span style="color:var(--error)">*</span>
                    </label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem">
                        <?php foreach ($categoryLabels as $val => $label): ?>
                        <label style="display:flex;align-items:center;gap:.5rem;padding:.7rem .9rem;border-radius:10px;border:2px solid var(--outline-variant);cursor:pointer;font-size:.875rem;font-weight:600;transition:var(--transition)"
                               onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--outline-variant)'">
                            <input type="radio" name="category" value="<?= e($val) ?>"
                                   <?= (($_POST['category'] ?? '') === $val) ? 'checked' : '' ?>
                                   style="accent-color:var(--primary)">
                            <?= e($label) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Mesaj -->
                <div>
                    <label for="fb-message" style="display:block;font-weight:700;font-size:.9rem;margin-bottom:.6rem;color:var(--on-surface)">
                        Düşüncelerini Yaz <span style="color:var(--error)">*</span>
                    </label>
                    <textarea
                        id="fb-message"
                        name="message"
                        rows="6"
                        maxlength="2000"
                        placeholder="Akran zorbalığı, platform veya oyunlar hakkında düşüncelerini buraya yazabilirsin..."
                        style="width:100%;padding:.85rem 1rem;border-radius:12px;border:2px solid var(--outline-variant);font-family:var(--font);font-size:.9rem;resize:vertical;outline:none;transition:border-color .2s;box-sizing:border-box"
                        onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--outline-variant)'"><?= e($_POST['message'] ?? '') ?></textarea>
                    <div style="font-size:.78rem;color:var(--on-surface-variant);margin-top:.3rem;text-align:right">
                        <span id="char-count">0</span>/2000
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <span class="material-symbols-outlined">send</span>
                    Gönder
                </button>

            </form>
        </div>

        <!-- Önceki bildirimler -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">history</span>
                        Önceki Bildirimlerim
                    </h2>
                    <span style="font-size:.8rem;color:var(--on-surface-variant)"><?= count($prevFeedback) ?> adet</span>
                </div>
                <?php if (empty($prevFeedback)): ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">mail</span>
                    <p>Henüz geri bildirim göndermediniz.</p>
                </div>
                <?php else: ?>
                <div style="padding:.5rem">
                    <?php foreach ($prevFeedback as $fb): ?>
                    <div style="padding:1rem;border-radius:12px;background:var(--surface-container-low);margin-bottom:.5rem">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
                            <span class="badge badge-<?= e($fb['category']) ?>" style="background:var(--surface-container);color:var(--on-surface-variant)">
                                <?= e($categoryLabels[$fb['category']] ?? $fb['category']) ?>
                            </span>
                            <span style="font-size:.78rem;color:var(--on-surface-variant)">
                                <?= date('d.m.Y', strtotime($fb['created_at'])) ?>
                            </span>
                        </div>
                        <p style="font-size:.875rem;color:var(--on-surface);margin:0;line-height:1.6;word-break:break-word">
                            <?= nl2br(e($fb['message'])) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bilgilendirme -->
            <div style="margin-top:1rem;padding:1rem 1.25rem;background:var(--primary-fixed);border-radius:14px;border-left:4px solid var(--primary)">
                <div style="font-weight:700;font-size:.875rem;color:var(--primary);margin-bottom:.3rem">
                    <span class="material-symbols-outlined" style="vertical-align:middle;font-size:16px">info</span>
                    Geri Bildirimler Hakkında
                </div>
                <p style="font-size:.82rem;color:var(--on-primary-fixed);margin:0;line-height:1.6">
                    Gönderdiğin geri bildirimler yöneticiler tarafından incelenir. Kişisel bilgilerini paylaşmaktan kaçın.
                </p>
            </div>
        </div>

    </div>

</main>

<script>
const ta = document.getElementById('fb-message');
const counter = document.getElementById('char-count');
if (ta && counter) {
    counter.textContent = ta.value.length;
    ta.addEventListener('input', function() {
        counter.textContent = this.value.length;
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
