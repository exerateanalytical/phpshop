<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$pdo = db();
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    flash('error', 'Cart is empty.');
    redirect('cart.php');
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
$stmt->execute($ids);
$products = $stmt->fetchAll();

if (count($products) !== count($cart)) {
    flash('error', 'One or more cart items are unavailable. Please review your cart.');
    redirect('cart.php');
}

$productMap = [];
$total = 0.0;
foreach ($products as $p) {
    $qty = max(1, (int) ($cart[$p['id']] ?? 0));
    if ($qty > (int) $p['stock']) {
        flash('error', 'Not enough stock for ' . $p['name']);
        redirect('cart.php');
    }

    $cart[$p['id']] = $qty;
    $productMap[$p['id']] = $p;
    $total += $qty * (float) $p['price'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment = trim($_POST['payment_method'] ?? 'COD');

    if ($name === '' || strlen($name) > 150 || $phone === '' || strlen($phone) > 60 || $address === '' || strlen($address) > 1000) {
        flash('error', 'Please provide valid checkout data.');
        redirect('checkout.php');
    }

    $allowedPayments = ['COD', 'Card', 'Bank Transfer'];
    if (!in_array($payment, $allowedPayments, true)) {
        flash('error', 'Invalid payment method.');
        redirect('checkout.php');
    }

    $pdo->beginTransaction();
    try {
        $orderNumber = randomOrderNumber();
        $pdo->prepare('INSERT INTO orders (order_number, user_id, customer_name, phone, address, payment_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, "pending")')
            ->execute([$orderNumber, currentUser()['id'], $name, $phone, $address, $payment, $total]);
        $orderId = (int) $pdo->lastInsertId();

        $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity, line_total) VALUES (?, ?, ?, ?, ?, ?)');
        $updateStock = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');

        foreach ($cart as $pid => $qty) {
            $prod = $productMap[$pid];
            $lineTotal = $qty * (float) $prod['price'];

            $insertItem->execute([$orderId, $pid, $prod['name'], $prod['price'], $qty, $lineTotal]);
            $updateStock->execute([$qty, $pid, $qty]);

            if ($updateStock->rowCount() !== 1) {
                throw new RuntimeException('Stock conflict on product ' . $pid);
            }
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        flash('success', 'Order placed successfully.');
        redirect('order_success.php?id=' . $orderId);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        flash('error', 'Checkout failed. Try again.');
        redirect('checkout.php');
    }
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Checkout</h1>
<p>Total: <strong><?= e(money($total)) ?></strong></p>
<form method="post">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <label>Customer Name</label>
    <input name="customer_name" maxlength="150" value="<?= e(currentUser()['name']) ?>" required>
    <label>Phone</label>
    <input name="phone" maxlength="60" required>
    <label>Address</label>
    <textarea name="address" maxlength="1000" required></textarea>
    <label>Payment Method</label>
    <select name="payment_method">
        <option value="COD">Cash On Delivery</option>
        <option value="Card">Card</option>
        <option value="Bank Transfer">Bank Transfer</option>
    </select>
    <button class="btn" type="submit">Place Order</button>
</form>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
