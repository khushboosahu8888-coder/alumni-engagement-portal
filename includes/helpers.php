<?php
// includes/helpers.php

// Always start session before output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Creating CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

// Check if user is logged in
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

// Redirect to login if not logged in
function require_login() {
    if (!is_logged_in()) {
        // BASE_URL comes from config.php
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}
