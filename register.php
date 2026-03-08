<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        flash('error', 'Valid name, email and password (8+ chars) are required.');
        redirect('register.php');
    }

    $stmt = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        flash('error', 'Email already exists.');
        redirect('register.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    db()->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "customer")')->execute([$name, $email, $hash]);

    flash('success', 'Registration successful. Please login.');
    redirect('login.php');
}

include __DIR__ . '/includes/layout.php';
?>
<h1>Register</h1>
<form method="post">
    <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
    <label>Name</label>
    <input name="name" maxlength="120" required>
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" minlength="8" required>
    <button class="btn" type="submit">Create Account</button>
</form>
<?php include __DIR__ . '/includes/layout_footer.php'; ?>
