<?php

require_once __DIR__ . '/functions.php';

function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('error', 'Please login first.');
        redirect('login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        flash('error', 'Admin access required.');
        redirect('index.php');
    }
}
