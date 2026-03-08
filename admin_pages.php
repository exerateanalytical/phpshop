<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (isset($_POST['toggle'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $isActive = (int) ($_POST['is_active'] ?? 0);
        $pdo->prepare('UPDATE landing_pages SET is_active = ? WHERE id = ?')->execute([$isActive, $id]);
        flash('success', 'Landing page status updated.');
    }

    if (isset($_POST['update_content'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $hero = trim($_POST['hero_text'] ?? '');
        $body = trim($_POST['body_text'] ?? '');

        if ($title === '' || $hero === '' || $body === '') {
            flash('error', 'Title, hero text and body text are required.');
            redirect('admin_pages.php');
        }

        $pdo->prepare('UPDATE landing_pages SET title = ?, hero_text = ?, body_text = ? WHERE id = ?')->execute([$title, $hero, $body, $id]);
        flash('success', 'Landing page content updated.');
    }

    redirect('admin_pages.php');
}

$pages = $pdo->query('SELECT * FROM landing_pages ORDER BY id ASC')->fetchAll();
include __DIR__ . '/includes/layout.php';
?>
<h1>Manage Landing Pages (10)</h1>
<table>
    <thead><tr><th>Title & Content</th><th>Slug</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($pages as $p): ?>
        <tr>
            <td>
                <form method="post">
                    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="update_content" value="1">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <label>Title</label>
                    <input name="title" value="<?= e($p['title']) ?>" required>
                    <label>Hero Text</label>
                    <input name="hero_text" value="<?= e($p['hero_text']) ?>" required>
                    <label>Body</label>
                    <textarea name="body_text" required><?= e($p['body_text']) ?></textarea>
                    <button class="btn secondary" type="submit">Save Content</button>
                </form>
            </td>
            <td><?= e($p['slug']) ?></td>
            <td><?= $p['is_active'] ? 'Active' : 'Inactive' ?></td>
            <td>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="toggle" value="1">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <input type="hidden" name="is_active" value="<?= $p['is_active'] ? 0 : 1 ?>">
                    <button class="btn <?= $p['is_active'] ? 'danger' : 'success' ?>" type="submit">
                        <?= $p['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
