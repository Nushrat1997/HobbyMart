<?php
// ============================================
// Product Details Page
// Author: Xinrui Huang
// ============================================

// Connect to database
$conn = new mysqli('localhost', 'register', 'register', 'DEMOKIM');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Products WHERE productID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="inventory.css">
</head>
<body>
<h2><?php echo htmlspecialchars($product['name']); ?></h2>

<p><strong>Price:</strong> $<?php echo $product['price']; ?></p>
<p><strong>Stock:</strong> <?php echo $product['stock']; ?></p>
<p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

<p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>
<?php $conn->close(); ?>
