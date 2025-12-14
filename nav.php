<?php
// ============================================
// Shared Navigation
// Fix: avoid assignment bug and undefined session warnings
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['id']);
$isGuest = isset($_SESSION['guest']) && $_SESSION['guest'] === true;

if ($isLoggedIn) {
    echo '<a href="/HobbyMart/?logout">Log Out</a>';
} elseif ($isGuest) {
    echo '<a href="/HobbyMart">Return to Login</a>';
} else {
    echo '<a href="/HobbyMart">Login</a>';
}
?>