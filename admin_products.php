<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (isset($_POST['create'])) {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $stock = max(0, (int) ($_POST['stock'] ?? 0));

        if ($name === '' || $category === '' || $price <= 0) {
            flash('error', 'Name, category and a valid positive price are required.');
            redirect('admin_products.php');
        }

        $pdo->prepare('INSERT INTO products (name, category, description, price, stock, is_active) VALUES (?, ?, ?, ?, ?, 1)')
            ->execute([$name, $category, $description, $price, $stock]);
        flash('success', 'Product created.');
    }

    if (isset($_POST['update'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $stock = max(0, (int) ($_POST['stock'] ?? 0));
        $state = (int) ($_POST['is_active'] ?? 0);

        if ($name === '' || $category === '' || $price <= 0) {
            flash('error', 'Name, category and valid price are required.');
            redirect('admin_products.php');
        }

        $pdo->prepare('UPDATE products SET name = ?, category = ?, description = ?, price = ?, stock = ?, is_active = ? WHERE id = ?')
            ->execute([$name, $category, $description, $price, $stock, $state, $id]);
        flash('success', 'Product updated.');
    }

    redirect('admin_products.php');
}

$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
include __DIR__ . '/includes/layout.php';
?>
<h1>Manage Products</h1>
<h3>Add Product</h3>
<form method="post">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <input type="hidden" name="create" value="1">
    <label>Name</label><input name="name" required>
    <label>Category</label><input name="category" required>
    <label>Description</label><textarea name="description"></textarea>
    <label>Price</label><input type="number" step="0.01" min="0.01" name="price" required>
    <label>Stock</label><input type="number" min="0" name="stock" required>
    <button class="btn" type="submit">Create Product</button>
</form>

<h3>Existing Products</h3>
<table>
    <thead><tr><th>Product</th><th>Manage</th></tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td>
                <strong><?= e($p['name']) ?></strong><br>
                <span class="muted">Category: <?= e($p['category']) ?> | Price: <?= e(money((float) $p['price'])) ?> | Stock: <?= (int) $p['stock'] ?> | Status: <?= $p['is_active'] ? 'Active' : 'Inactive' ?></span>
            </td>
            <td>
                <form method="post">
                    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <label>Name</label><input name="name" value="<?= e($p['name']) ?>" required>
                    <label>Category</label><input name="category" value="<?= e($p['category']) ?>" required>
                    <label>Description</label><textarea name="description"><?= e($p['description']) ?></textarea>
                    <label>Price</label><input type="number" step="0.01" min="0.01" name="price" value="<?= e((string) $p['price']) ?>" required>
                    <label>Stock</label><input type="number" min="0" name="stock" value="<?= (int) $p['stock'] ?>" required>
                    <label>Status</label>
                    <select name="is_active">
                        <option value="1" <?= $p['is_active'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= !$p['is_active'] ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <button class="btn secondary" type="submit">Save Product</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
