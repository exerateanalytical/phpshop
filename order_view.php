<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, currentUser()['id']]);
$order = $stmt->fetch();
if (!$order) {
    flash('error', 'Order not found.');
    redirect('my_orders.php');
}

$itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

include __DIR__ . '/includes/layout.php';
?>
<h1>Order Details</h1>
<p><strong>Order:</strong> <?= e($order['order_number']) ?></p>
<p><strong>Status:</strong> <?= e($order['status']) ?></p>
<p><strong>Payment:</strong> <?= e($order['payment_method']) ?></p>
<p><strong>Shipping Address:</strong><br><?= nl2br(e($order['address'])) ?></p>
<table>
    <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= e($item['product_name']) ?></td>
            <td><?= e(money((float) $item['price'])) ?></td>
            <td><?= (int) $item['quantity'] ?></td>
            <td><?= e(money((float) $item['line_total'])) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<p><strong>Grand Total: <?= e(money((float) $order['total_amount'])) ?></strong></p>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
