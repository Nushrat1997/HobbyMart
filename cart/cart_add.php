<?php
// ============================================
// Add To Cart (from shop / detail pages)
// Author: Xinrui Huang (final revised version)
// ============================================

session_start();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productID = isset($_POST['productID']) ? (int)$_POST['productID'] : 0;
$qty       = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($qty <= 0) $qty = 1;

if ($productID > 0) {
    if (!isset($_SESSION['cart'][$productID])) {
        $_SESSION['cart'][$productID] = 0;
    }
    $_SESSION['cart'][$productID] += $qty;

    header("Location: /HobbyMart/cart/view_cart.php?msg=" . urlencode("Added to cart."));
    exit;
}

header("Location: /HobbyMart/shop.php?msg=" . urlencode("Invalid product."));
exit;

