<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$stmt = db()->prepare('SELECT id, order_number, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([currentUser()['id']]);
$orders = $stmt->fetchAll();

include __DIR__ . '/includes/layout.php';
?>
<h1>My Orders</h1>
<table>
    <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e($o['order_number']) ?></td>
            <td><?= e(money((float) $o['total_amount'])) ?></td>
            <td><?= e($o['status']) ?></td>
            <td><?= e($o['created_at']) ?></td>
            <td><a class="btn secondary" href="order_view.php?id=<?= (int) $o['id'] ?>">View</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
