<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();
$pdo = db();
$stats = [
    'users' => (int) $pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'],
    'products' => (int) $pdo->query('SELECT COUNT(*) c FROM products')->fetch()['c'],
    'orders' => (int) $pdo->query('SELECT COUNT(*) c FROM orders')->fetch()['c'],
    'pages' => (int) $pdo->query('SELECT COUNT(*) c FROM landing_pages')->fetch()['c'],
];
$recentOrders = $pdo->query('SELECT o.id, o.order_number, u.name, o.total_amount, o.status, o.created_at FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 10')->fetchAll();

include __DIR__ . '/includes/layout.php';
?>
<h1>Admin Dashboard</h1>
<div class="grid">
    <div class="card"><h3>Users</h3><p><?= $stats['users'] ?></p></div>
    <div class="card"><h3>Products</h3><p><?= $stats['products'] ?></p></div>
    <div class="card"><h3>Orders</h3><p><?= $stats['orders'] ?></p></div>
    <div class="card"><h3>Landing Pages</h3><p><?= $stats['pages'] ?></p></div>
</div>
<p class="actions">
    <a class="btn" href="admin_products.php">Manage Products</a>
    <a class="btn secondary" href="admin_pages.php">Manage Landing Pages</a>
    <a class="btn secondary" href="admin_orders.php">Manage Orders</a>
</p>
<h2>Recent Orders</h2>
<table>
    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($recentOrders as $o): ?>
        <tr>
            <td><?= e($o['order_number']) ?></td>
            <td><?= e($o['name']) ?></td>
            <td><?= e(money((float) $o['total_amount'])) ?></td>
            <td><?= e($o['status']) ?></td>
            <td><?= e($o['created_at']) ?></td>
            <td><a class="btn secondary" href="admin_order_view.php?id=<?= (int) $o['id'] ?>">View</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
