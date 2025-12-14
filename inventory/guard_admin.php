<?php
// ============================================
// Admin Guard (Inventory Module)
// Purpose: block non-admin users from add/edit/delete pages
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin is represented by a boolean: $_SESSION['admin'] (true/false)
$isAdmin = isset($_SESSION['id']) && isset($_SESSION['admin']) && $_SESSION['admin'] === true;

if (!$isAdmin) {
    // Redirect back to product list with an error flag
    header("Location: /HobbyMart/inventory/list_products.php?error=unauthorized");
    exit;
}
?>
