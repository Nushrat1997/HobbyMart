<?php
    session_start();
// ============================================
// Product Inventory - List of All Products
// Author: Xinrui Huang
// ============================================

// Connect to the database
// Connect to the database (correct settings)
$host = "localhost";
$user = "root";
$pass = "";
$db = "hobbymart";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch all products from database
$query = "SELECT * FROM Products";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product List - HobbyMart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
<h2>Product List</h2>

<!-- Link to add a new product -->
<p><a href="add_product.php" class="btn">Add New Product</a></p>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>

    <!-- Display each product -->
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['productID']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td>$<?php echo $row['price']; ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td>
                <a href="product_detail.php?id=<?php echo $row['productID']; ?>">View</a> |
                <a href="edit_product.php?id=<?php echo $row['productID']; ?>">Edit</a> |
                <a href="delete_product.php?id=<?php echo $row['productID']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
<?php $conn->close(); ?>
