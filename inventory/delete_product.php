<?php
// ============================================
// Delete Product (Admin Only)
// Author: Xinrui Huang
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . "/guard_admin.php";

$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id <= 0) {
    header("Location: /HobbyMart/inventory/list_products.php?error=invalid_id");
    exit;
}

// Optional: get image path before delete (so we can delete file too)
$imgPath = "";
$get = $conn->prepare("SELECT image FROM Products WHERE productID=?");
$get->bind_param("i", $id);
$get->execute();
$row = $get->get_result()->fetch_assoc();
$get->close();
if ($row && !empty($row["image"])) {
    $imgPath = $row["image"];
}

// Delete product
$stmt = $conn->prepare("DELETE FROM Products WHERE productID=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Optional safe file delete: only delete files inside img/products/
    if ($imgPath !== "" && str_starts_with($imgPath, "img/products/")) {
        $absolute = $_SERVER["DOCUMENT_ROOT"] . "/HobbyMart/" . $imgPath;
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    $stmt->close();
    $conn->close();
    header("Location: /HobbyMart/inventory/list_products.php?msg=deleted");
    exit;
}

$stmt->close();
$conn->close();
header("Location: /HobbyMart/inventory/list_products.php?error=delete_failed");
exit;
?>


