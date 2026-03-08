<?php
require_once __DIR__ . '/includes/functions.php';
ensureSessionStarted();

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
}
session_destroy();

ensureSessionStarted();
flash('success', 'You are logged out.');
redirect('index.php');
