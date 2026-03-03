<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

$slug = trim($_GET['slug'] ?? '');
$pdo = tryDb();
$isDemoMode = $pdo === null;

if ($isDemoMode) {
    $demoPages = [
        'pain-relief-essentials' => [
            'title' => 'Pain Relief Essentials',
            'hero_text' => 'Fast and trusted pain relief products',
            'body_text' => 'Shop tablets, gels, and relief kits selected by pharmacists for quick and reliable everyday support.',
        ],
        'cold-flu-care' => [
            'title' => 'Cold & Flu Care',
            'hero_text' => 'Stay strong through the season',
            'body_text' => 'Explore syrups, vitamins, and immune support bundles designed to help you recover and stay protected.',
        ],
        'digestive-wellness' => [
            'title' => 'Digestive Wellness',
            'hero_text' => 'Healthy gut, healthy life',
            'body_text' => 'Find antacids, probiotics, and digestion support essentials in this limited preview mode.',
        ],
    ];

    $page = $demoPages[$slug] ?? null;
} else {
    $stmt = $pdo->prepare('SELECT * FROM landing_pages WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
}

if (!$page || (!$isDemoMode && !$page['is_active'] && !isAdmin())) {
    flash('error', 'Landing page not available.');
    redirect('index.php');
}

include __DIR__ . '/includes/layout.php';
?>
<h1><?= e($page['title']) ?></h1>
<?php if ($isDemoMode): ?>
    <div class="alert error">Limited preview mode: this landing page uses sample content because database is not connected.</div>
<?php endif; ?>
<p><strong><?= e($page['hero_text']) ?></strong></p>
<p><?= nl2br(e($page['body_text'])) ?></p>
<p><a class="btn" href="products.php">Shop Products</a></p>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
