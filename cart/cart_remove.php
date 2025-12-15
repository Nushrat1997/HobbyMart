<?php
// ============================================
// Remove One Item From Cart
// Author: Xinrui Huang (final revised version)
// ============================================

session_start();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

header("Location: /HobbyMart/cart/view_cart.php?msg=" . urlencode("Item removed."));
exit;

