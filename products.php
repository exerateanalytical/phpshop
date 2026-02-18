<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

$pdo = tryDb();
$isDemoMode = $pdo === null;

if ($isDemoMode) {
    $products = [
        ['name' => 'Paracetamol 500mg', 'category' => 'Pain Relief', 'description' => 'General pain and fever reducer.', 'price' => 4.50, 'stock' => 200],
        ['name' => 'Vitamin C 1000mg', 'category' => 'Vitamins', 'description' => 'Immune system support.', 'price' => 12.00, 'stock' => 100],
    ];
} else {
    $products = $pdo->query('SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC')->fetchAll();
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Products</h1>
<?php if ($isDemoMode): ?>
    <div class="alert error">Database is not connected. Product interaction is disabled in preview mode.</div>
<?php endif; ?>
<div class="grid">
<?php foreach ($products as $product): ?>
    <div class="card">
        <h3><?= e($product['name']) ?></h3>
        <p class="muted"><?= e($product['category']) ?></p>
        <p><?= e($product['description']) ?></p>
        <p><strong><?= e(money((float) $product['price'])) ?></strong> | Stock: <?= (int) $product['stock'] ?></p>
        <?php if ($isDemoMode): ?>
            <button class="btn secondary" type="button" disabled>View</button>
        <?php else: ?>
            <a class="btn" href="product.php?id=<?= (int) $product['id'] ?>">View</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
