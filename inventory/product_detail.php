<?php
// ============================================
// Product Details Page
// Author: Xinrui Huang
// Update: display product image
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: /HobbyMart/inventory/list_products.php?error=invalid_id");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM Products WHERE productID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: /HobbyMart/inventory/list_products.php?error=not_found");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
    </nav>

    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

    <?php if (!empty($product["image"])): ?>
        <img src="/HobbyMart/<?php echo htmlspecialchars($product["image"]); ?>"
             alt="Product Image"
             style="max-width:420px; display:block; margin:10px 0; border:1px solid #ccc;">
    <?php endif; ?>

    <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
    <p><strong>Stock:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

    <p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>
<?php $conn->close(); ?>

