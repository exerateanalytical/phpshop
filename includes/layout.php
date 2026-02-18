<?php
require_once __DIR__ . '/functions.php';
$config = appConfig();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($config['app_name']) ?></title>
    <style>
        body {font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #222;}
        nav {background: #0c4a6e; color: #fff; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; gap: 12px;}
        nav a {color: #fff; text-decoration: none; margin-right: 12px;}
        .container {max-width: 1100px; margin: 24px auto; background: #fff; padding: 20px; border-radius: 10px;}
        .grid {display: grid; gap: 14px; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));}
        .card {border: 1px solid #ddd; border-radius: 8px; padding: 14px;}
        .btn {display: inline-block; padding: 8px 12px; background: #0c4a6e; color: #fff; text-decoration: none; border: none; border-radius: 5px; cursor: pointer;}
        .btn.secondary {background: #475569;}
        .btn.danger {background: #b91c1c;}
        .btn.success {background: #166534;}
        input, select, textarea {width: 100%; padding: 8px; margin: 6px 0 12px; border: 1px solid #bbb; border-radius: 6px; box-sizing: border-box;}
        table {width: 100%; border-collapse: collapse; margin-top: 10px;}
        th, td {border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top;}
        .alert {padding: 10px; border-radius: 6px; margin-bottom: 10px;}
        .alert.error {background: #fee2e2; color: #991b1b;}
        .alert.success {background: #dcfce7; color: #166534;}
        .muted {color: #666; font-size: 14px;}
        .actions {display: flex; gap: 8px; flex-wrap: wrap;}
    </style>
</head>
<body>
<nav>
    <div>
        <a href="index.php"><strong><?= e($config['app_name']) ?></strong></a>
        <a href="products.php">Products</a>
        <a href="cart.php">Cart (<?= cartItemsCount() ?>)</a>
    </div>
    <div>
        <?php if (isLoggedIn()): ?>
            <span>Hi, <?= e(currentUser()['name']) ?></span>
            <a href="my_orders.php">My Orders</a>
            <?php if (isAdmin()): ?><a href="admin.php">Admin</a><?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
