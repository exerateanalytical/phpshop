<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

$slug = trim($_GET['slug'] ?? '');
$stmt = db()->prepare('SELECT * FROM landing_pages WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page || (!$page['is_active'] && !isAdmin())) {
    flash('error', 'Landing page not available.');
    redirect('index.php');
}

include __DIR__ . '/includes/layout.php';
?>
<h1><?= e($page['title']) ?></h1>
<p><strong><?= e($page['hero_text']) ?></strong></p>
<p><?= nl2br(e($page['body_text'])) ?></p>
<p><a class="btn" href="products.php">Shop Products</a></p>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
