<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT o.*, u.email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ? LIMIT 1');
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) {
    flash('error', 'Order not found.');
    redirect('admin_orders.php');
}

$itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

include __DIR__ . '/includes/layout.php';
?>
<h1>Admin Order View</h1>
<p><strong>Order:</strong> <?= e($order['order_number']) ?></p>
<p><strong>Customer:</strong> <?= e($order['customer_name']) ?> (<?= e($order['email']) ?>)</p>
<p><strong>Phone:</strong> <?= e($order['phone']) ?></p>
<p><strong>Status:</strong> <?= e($order['status']) ?></p>
<p><strong>Address:</strong><br><?= nl2br(e($order['address'])) ?></p>
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
