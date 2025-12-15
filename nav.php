<?php
// ============================================
// Global Navigation
// Author: Xinrui Huang (updated)
// Notes:
// - Shows different links for Admin / User / Guest
// - Fixes guest check (use === and isset)
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['id']);
$isAdmin    = $isLoggedIn && isset($_SESSION['admin']) && $_SESSION['admin'] === true;
$isGuest    = isset($_SESSION['guest']) && $_SESSION['guest'] === true;

// Left side nav links
echo "<a href=\"/HobbyMart/shop.php\">Shop</a>";
echo "<a href=\"/HobbyMart/cart/view_cart.php\">Cart</a>";

// Admin shortcut (Back Office)
if ($isAdmin) {
    echo "<a href=\"/HobbyMart/inventory/list_products.php\">Product List (Admin)</a>";
}

// Right side action
if ($isLoggedIn) {
    echo "<a href=\"/HobbyMart/?logout\">Log Out</a>";
} else {
    // Guest or not logged in
    echo "<a href=\"/HobbyMart/\">Return to Login</a>";
}
?>

