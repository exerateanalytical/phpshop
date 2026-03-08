<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

$pdo = db();
$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] ?? [] as $id => $qty) {
            $id = (int) $id;
            $qty = (int) $qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = min($qty, 999);
            }
        }
        flash('success', 'Cart updated.');
        redirect('cart.php');
    }
}

$items = [];
if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $row) {
        $row['qty'] = $cart[$row['id']] ?? 0;
        $row['line_total'] = $row['qty'] * (float) $row['price'];
        $items[] = $row;
    }
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Your Cart</h1>
<?php if (!$items): ?>
    <p>Your cart is empty. <a href="products.php">Browse products</a></p>
<?php else: ?>
<form method="post">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <table>
        <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= e(money((float) $item['price'])) ?></td>
                    <td><input type="number" name="qty[<?= (int) $item['id'] ?>]" min="0" max="999" value="<?= (int) $item['qty'] ?>"></td>
                    <td><?= e(money((float) $item['line_total'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Grand Total: <?= e(money(cartTotal($pdo))) ?></strong></p>
    <button class="btn secondary" name="update" value="1" type="submit">Update Cart</button>
    <a class="btn" href="checkout.php">Proceed to Checkout</a>
</form>
<?php endif; ?>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
