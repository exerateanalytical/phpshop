<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    flash('error', 'Product not found.');
    redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $qty = max(1, (int) ($_POST['qty'] ?? 1));
    if ($qty > (int) $product['stock']) {
        flash('error', 'Quantity exceeds stock.');
        redirect('product.php?id=' . $id);
    }

    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    flash('success', 'Added to cart.');
    redirect('cart.php');
}

include __DIR__ . '/includes/layout.php';
?>
<h1><?= e($product['name']) ?></h1>
<p class="muted">Category: <?= e($product['category']) ?></p>
<p><?= e($product['description']) ?></p>
<p><strong><?= e(money((float) $product['price'])) ?></strong></p>
<form method="post">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <label>Quantity</label>
    <input type="number" name="qty" min="1" max="<?= (int) $product['stock'] ?>" value="1" required>
    <button class="btn" type="submit">Add To Cart</button>
</form>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
