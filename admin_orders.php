<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id = (int) ($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');
    $allowed = ['pending', 'paid', 'shipped', 'cancelled'];
    if (in_array($status, $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash('success', 'Order status updated.');
    }
    redirect('admin_orders.php');
}

$orders = $pdo->query('SELECT o.id, o.order_number, o.customer_name, o.total_amount, o.status, o.created_at, u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();
include __DIR__ . '/includes/layout.php';
?>
<h1>Manage Orders</h1>
<table>
    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Update</th><th>View</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e($o['order_number']) ?><br><span class="muted"><?= e($o['created_at']) ?></span></td>
            <td><?= e($o['customer_name']) ?><br><span class="muted"><?= e($o['email']) ?></span></td>
            <td><?= e(money((float) $o['total_amount'])) ?></td>
            <td><?= e($o['status']) ?></td>
            <td>
                <form method="post" style="display:flex; gap:6px; align-items:center;">
                    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                    <select name="status">
                        <?php foreach (['pending', 'paid', 'shipped', 'cancelled'] as $status): ?>
                            <option value="<?= $status ?>" <?= $o['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn secondary" type="submit">Save</button>
                </form>
            </td>
            <td><a class="btn" href="admin_order_view.php?id=<?= (int) $o['id'] ?>">Open</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
