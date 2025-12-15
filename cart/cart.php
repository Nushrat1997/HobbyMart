<?php
// ============================================
// Cart Actions Handler (Update / Clear)
// Author: Xinrui Huang (updated)
// Notes:
// - This file MUST NOT output any HTML.
// - It only processes POST requests then redirects back to view_cart.php
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure cart session exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // productID => qty
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /HobbyMart/cart/view_cart.php");
    exit;
}

// -------- Clear Cart --------
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: /HobbyMart/cart/view_cart.php?msg=Cart+cleared");
    exit;
}

// -------- Update Cart --------
// Expect: qty[productID] = number
if (isset($_POST['update_cart']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;

        // Remove if qty <= 0
        if ($pid > 0) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$pid]);
            } else {
                // Optional: cap qty to avoid silly numbers
                if ($qty > 99) $qty = 99;
                $_SESSION['cart'][$pid] = $qty;
            }
        }
    }

    header("Location: /HobbyMart/cart/view_cart.php?msg=Cart+updated");
    exit;
}

// Fallback
header("Location: /HobbyMart/cart/view_cart.php");
exit;

