<?php
require_once 'db.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login");
        exit;
    }
}

// Check if user is an admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect to login/dashboard if not an admin
function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: ../login");
        exit;
    }
    if (!isAdmin()) {
        // If logged in but not admin, send to normal covenant page
        header("Location: ../covenant");
        exit;
    }
}
?>
