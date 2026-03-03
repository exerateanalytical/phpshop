<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

$pdo = tryDb();
$isDemoMode = $pdo === null;

if ($isDemoMode) {
    $pages = [
        ['title' => 'Pain Relief Essentials', 'hero_text' => 'Fast and trusted pain relief products', 'slug' => 'pain-relief-essentials'],
        ['title' => 'Cold & Flu Care', 'hero_text' => 'Stay strong through the season', 'slug' => 'cold-flu-care'],
        ['title' => 'Digestive Wellness', 'hero_text' => 'Healthy gut, healthy life', 'slug' => 'digestive-wellness'],
    ];
} else {
    $pages = $pdo->query('SELECT * FROM landing_pages WHERE is_active = 1 ORDER BY id ASC')->fetchAll();
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Pharmacy & Drugstore Storefront</h1>
<?php if ($isDemoMode): ?>
    <div class="alert error">
        Database is not connected. You are viewing a limited preview mode.
        Run <code>mysql -u root -p &lt; database/schema.sql</code> to enable full features.
    </div>
<?php endif; ?>
<p class="muted">Choose one of our active campaign landing pages.</p>
<div class="grid">
    <?php foreach ($pages as $page): ?>
        <div class="card">
            <h3><?= e($page['title']) ?></h3>
            <p><?= e($page['hero_text']) ?></p>
            <a class="btn" href="landing.php?slug=<?= urlencode($page['slug']) ?>">Open Page</a>
        </div>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
