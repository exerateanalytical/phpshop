<?php

function appConfig(): array
{
    static $config;
    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }

    return $config;
}

function bootstrapErrorHandling(): void
{
    static $initialized = false;
    if ($initialized) {
        return;
    }
    $initialized = true;

    set_exception_handler(function (Throwable $e): void {
        http_response_code(500);
        $config = appConfig();
        $isProduction = ($config['app_env'] ?? 'production') === 'production';

        $title = 'Application error';
        $message = 'Something went wrong. Please try again later.';

        if ($e instanceof RuntimeException && str_contains($e->getMessage(), 'Database connection failed')) {
            $title = 'Database is not connected';
            $message = 'Update your DB settings and import database/schema.sql, then refresh.';
        }

        if (!$isProduction) {
            $message .= ' [' . $e->getMessage() . ']';
        }

        echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
        echo '<style>body{font-family:Arial,sans-serif;background:#f5f7fb;padding:30px}.box{max-width:760px;margin:auto;background:#fff;border-radius:10px;padding:20px;border:1px solid #ddd}code{background:#eee;padding:2px 4px}</style>';
        echo '</head><body><div class="box"><h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Setup command: <code>mysql -u root -p &lt; database/schema.sql</code></p></div></body></html>';
    });
}

bootstrapErrorHandling();

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $config = appConfig();
    if (session_name() !== $config['session_name']) {
        session_name($config['session_name']);
    }

    session_set_cookie_params([
        'lifetime' => $config['session_lifetime'],
        'path' => '/',
        'secure' => (bool) $config['session_secure'],
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    $config = appConfig();
    $location = $config['base_url'] !== '' && !str_starts_with($path, 'http')
        ? $config['base_url'] . '/' . ltrim($path, '/')
        : $path;

    header('Location: ' . $location);
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    ensureSessionStarted();

    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $message;
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
    return isLoggedIn() && (currentUser()['role'] ?? '') === 'admin';
}

function csrfToken(): string
{
    ensureSessionStarted();

    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

function verifyCsrf(): void
{
    ensureSessionStarted();

    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        flash('error', 'Invalid request token. Please try again.');
        redirect($_SERVER['PHP_SELF']);
    }
}

function money(float $amount): string
{
    $config = appConfig();
    return $config['currency_symbol'] . number_format($amount, 2);
}

function randomOrderNumber(): string
{
    return 'ORD-' . strtoupper(date('Ymd')) . '-' . strtoupper(bin2hex(random_bytes(4)));
}

function cartItemsCount(): int
{
    return array_sum($_SESSION['cart'] ?? []);
}

function cartTotal(PDO $pdo): float
{
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) {
        return 0.0;
    }

    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    $total = 0.0;
    foreach ($stmt->fetchAll() as $product) {
        $qty = $cart[$product['id']] ?? 0;
        $total += ((float) $product['price']) * $qty;
    }

    return $total;
}
