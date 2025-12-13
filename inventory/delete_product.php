<?php
    session_start();
// ============================================
// Delete Product (Admin Only)
// Author: Xinrui Huang
// ============================================

// Database connection
// Correct Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "hobbymart";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute deletion
    $stmt = $conn->prepare("DELETE FROM Products WHERE productID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "<div class='success'>✅ Product deleted successfully!</div>";
    } else {
        $message = "<div class='error'>❌ Failed to delete product. Please try again.</div>";
    }

    $stmt->close();
} else {
    $message = "<div class='error'>⚠️ Invalid product ID.</div>";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/hobbymart/nav.php"; ?>
    </nav>
    <h2>Delete Product</h2>
    <?php echo $message; ?>
    <p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>

