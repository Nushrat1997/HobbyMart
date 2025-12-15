<?php
// ============================================
// Add To Cart (Session-based cart, demo version)
// Author: Xinrui Huang
// ============================================
include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/cart/cart_add.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: /HobbyMart/shop.php");
    exit;
}

$id = (int)$_GET["id"];

// Create cart if not exists
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Increase quantity
if (!isset($_SESSION["cart"][$id])) {
    $_SESSION["cart"][$id] = 1;
} else {
    $_SESSION["cart"][$id] += 1;
}

// Redirect back to shop with message
header("Location: /HobbyMart/shop.php?msg=added_to_cart");
exit;
