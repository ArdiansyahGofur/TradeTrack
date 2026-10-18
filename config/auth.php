<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrfToken()
{
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token)
{
    return hash_equals(
        $_SESSION['csrf_token'],
        $token ?? ''
    );
}

/**
 * Mengecek apakah user sudah login.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Mendapatkan role user yang sedang login.
 */
function userRole(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Memastikan user sudah login.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /tradetrack/login.php');
        exit;
    }
}

/**
 * Memastikan user memiliki role tertentu.
 */
function requireRole(string $role): void
{
    requireLogin();

    if (userRole() !== $role) {
        header('Location: /tradetrack/index.php');
        exit;
    }
}