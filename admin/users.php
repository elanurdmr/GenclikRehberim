<?php
/**
 * admin/users.php — Admin Kullanıcı Yönetimi
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

$pageTitle = 'Kullanıcı Yönetimi';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

$db      = getDB();
$message = '';
$error   = '';

/* ------ Kullanıcı Silme İşlemi ------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $targetId = (int)($_POST['user_id'] ?? 0);

    if ($_POST['action'] === 'delete') {
        if ($targetId === (int)$_SESSION['user_id']) {
            $error = 'Kendinizi silemezsiniz.';
        } else {
            $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            $message = 'Kullanıcı başarıyla silindi.';
        }
    }

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
    <aside class="admin-sidebar" aria-label="Admin menü">
        <div class="admin-sidebar-title">Yönetim Paneli</div>
        <nav aria-label="Admin navigasyon">
            <ul>
                <li>
                    <a href="/genclik-rehberim/admin/index.php">
                        <span class="material-symbols-outlined">dashboard</span> Genel Bakış
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/users.php" class="active">
                        <span class="material-symbols-outlined">groups</span> Kullanıcılar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/scores.php">
                        <span class="material-symbols-outlined">bar_chart</span> Skorlar
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/admin/feedback.php">
                        <span class="material-symbols-outlined">feedback</span> Geri Bildirimler
                    </a>
                </li>
                <li class="admin-sidebar__nav-item--spaced">
                    <a href="/genclik-rehberim/index.php">
                        <span class="material-symbols-outlined">home</span> Ana Sayfa
                    </a>
                </li>
                <li>
                    <a href="/genclik-rehberim/logout.php" class="admin-sidebar__link--danger">
                        <span class="material-symbols-outlined">logout</span> Çıkış
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Ana içerik -->
    <section class="admin-content">

        <h1 class="admin-page-title">
            <span class="material-symbols-outlined icon-fill">groups</span>
            Kullanıcı Yönetimi
        </h1>

        <!-- Başarı / Hata mesajları -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <span class="material-symbols-outlined icon-fill icon-sm">check_circle</span>
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <span class="material-symbols-outlined icon-fill icon-sm">error</span>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Kullanıcı Tablosu -->
        <div class="card">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-outlined icon-fill">format_list_bulleted</span>
                    Tüm Kullanıcılar
                </h2>
                <span class="badge badge-active"><?= count($users) ?> kullanıcı</span>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">groups</span>
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
                            <th>Oyun</th>
                            <th>Kayıt Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="admin-table__meta--small">#<?= $user['id'] ?></td>
                            <td>
                                <strong><?= e($user['username']) ?></strong>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <span class="admin-table__badge--self">(Sen)</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-table__subtle"><?= e($user['email']) ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge badge-bulmaca">👑 Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-success">🎓 Öğrenci</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-table__value--primary"><?= (int)$user['total_score'] ?></td>
                            <td><?= (int)$user['games_played'] ?></td>
                            <td class="admin-table__meta--small">
                                <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td>
                                <div class="admin-table__actions">
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>

                                        <!-- Rol değiştirme -->
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="action" value="toggle_role">
                                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                            <button type="submit" class="btn btn-outline btn-sm" title="Rolü Değiştir">
                                                <span class="material-symbols-outlined icon-sm">manage_accounts</span>
                                                <?= $user['role'] === 'admin' ? 'Öğrenci Yap' : 'Admin Yap' ?>
                                            </button>
                                        </form>

                                        <!-- Silme -->
                                        <?php
                                        /* json_encode → güvenli JS string literali (tüm tehlikeli
                                           karakterler \uXXXX kaçışlı); e() → HTML attribute kaçışı.
                                           İki bağlamlı kodlama, kullanıcı adı yoluyla XSS'i kapatır. */
                                        $confirmMsg = json_encode(
                                            $user['username'] . ' kullanıcısını silmek istediğinizden emin misiniz?',
                                            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                                        );
                                        ?>
                                        <form method="POST" style="display:inline"
                                              onsubmit="return confirm(<?= e($confirmMsg) ?>)">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Kullanıcıyı Sil">
                                                <span class="material-symbols-outlined icon-sm">delete</span>
                                            </button>
                                        </form>

                                    <?php else: ?>
                                        <span class="admin-table__meta--small">—</span>
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
