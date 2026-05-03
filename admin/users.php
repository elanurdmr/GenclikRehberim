<?php
/**
 * admin/users.php — Admin Kullanıcı Yönetimi
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$db      = getDB();
$message = '';
$error   = '';

/* ------ Kullanıcı Silme İşlemi ------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $targetId = (int)($_POST['user_id'] ?? 0);

    if ($_POST['action'] === 'delete') {
        // Kendini silmeye çalışıyorsa engelle
        if ($targetId === (int)$_SESSION['user_id']) {
            $error = 'Kendinizi silemezsiniz.';
        } else {
            $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            $message = 'Kullanıcı başarıyla silindi.';
        }
    }

    // Rol değiştirme
    if ($_POST['action'] === 'toggle_role') {
        if ($targetId === (int)$_SESSION['user_id']) {
            $error = 'Kendi rolünüzü değiştiremezsiniz.';
        } else {
            $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            $user = $stmt->fetch();
            if ($user) {
                $newRole = ($user['role'] === 'admin') ? 'student' : 'admin';
                $stmt    = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
                $stmt->execute([$newRole, $targetId]);
                $message = 'Kullanıcı rolü "' . $newRole . '" olarak güncellendi.';
            }
        }
    }
}

// Tüm kullanıcıları al
$users = getAllUsers();
?>
<?php include '../includes/header.php'; ?>

<!-- ===== KULLANICI YÖNETİMİ ===== -->
<main>
<div class="admin-wrapper">

    <!-- Sol sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Admin Menü</div>
        <nav aria-label="Admin navigasyon">
            <ul>
                <li><a href="/genclik-rehberim/admin/index.php"><i class="fa-solid fa-gauge"></i> Genel Bakış</a></li>
                <li><a href="/genclik-rehberim/admin/users.php" class="active"><i class="fa-solid fa-users"></i> Kullanıcılar</a></li>
                <li><a href="/genclik-rehberim/admin/scores.php"><i class="fa-solid fa-chart-bar"></i> Skorlar</a></li>
                <li style="margin-top:2rem">
                    <a href="/genclik-rehberim/index.php"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
                </li>
                <li>
                    <a href="/genclik-rehberim/logout.php" style="color:var(--secondary)!important">
                        <i class="fa-solid fa-right-from-bracket"></i> Çıkış
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Ana içerik -->
    <section class="admin-content">

        <h1 class="admin-page-title">
            <i class="fa-solid fa-users"></i>
            Kullanıcı Yönetimi
        </h1>

        <!-- Başarı / Hata mesajları -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Kullanıcı Tablosu -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <i class="fa-solid fa-list"></i>
                    Tüm Kullanıcılar
                    <span style="background:var(--bg-light);padding:0.2rem 0.8rem;border-radius:50px;font-size:0.8rem;margin-left:0.5rem">
                        <?= count($users) ?>
                    </span>
                </h2>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-users"></i>
                    <p>Henüz kullanıcı yok.</p>
                </div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Toplam Puan</th>
                            <th>Oyun Sayısı</th>
                            <th>Kayıt Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td style="color:var(--text-muted)">#<?= $user['id'] ?></td>
                            <td>
                                <strong><?= e($user['username']) ?></strong>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <span style="color:var(--primary);font-size:0.75rem"> (Sen)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($user['email']) ?></td>
                            <td>
                                <span class="badge" style="<?= $user['role']==='admin'
                                    ? 'background:rgba(108,99,255,0.12);color:var(--primary)'
                                    : 'background:rgba(67,233,123,0.12);color:#059669' ?>">
                                    <?= $user['role'] === 'admin' ? '👑 Admin' : '🎓 Öğrenci' ?>
                                </span>
                            </td>
                            <td style="font-weight:800;color:var(--primary)"><?= $user['total_score'] ?></td>
                            <td><?= $user['games_played'] ?></td>
                            <td style="color:var(--text-muted);font-size:0.85rem">
                                <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>

                                        <!-- Rol değiştirme -->
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="action" value="toggle_role">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-outline btn-sm"
                                                    title="Rolü Değiştir">
                                                <i class="fa-solid fa-user-gear"></i>
                                                <?= $user['role'] === 'admin' ? 'Öğrenci Yap' : 'Admin Yap' ?>
                                            </button>
                                        </form>

                                        <!-- Silme -->
                                        <form method="POST" style="display:inline"
                                              onsubmit="return confirm('<?= e($user['username']) ?> kullanıcısını silmek istediğinizden emin misiniz?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    title="Kullanıcıyı Sil">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>

                                    <?php else: ?>
                                        <span style="color:var(--text-muted);font-size:0.85rem">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </section>

</div>
</main>

<?php include '../includes/footer.php'; ?>
