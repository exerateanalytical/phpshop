<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT id, order_number FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, currentUser()['id']]);
$order = $stmt->fetch();
if (!$order) {
    flash('error', 'Order not found.');
    redirect('my_orders.php');
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Order Success</h1>
<p>Your order <strong><?= e($order['order_number']) ?></strong> has been placed.</p>
<p class="actions">
    <a class="btn" href="order_view.php?id=<?= (int) $order['id'] ?>">View Order</a>
    <a class="btn secondary" href="my_orders.php">All Orders</a>
</p>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
