<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        flash('error', 'Invalid credentials.');
        redirect('login.php');
    }

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    flash('success', 'Welcome back ' . $user['name']);
    redirect('index.php');
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Login</h1>
<form method="post">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button class="btn" type="submit">Login</button>
</form>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
