<?php
// ============================================
// Edit Existing Product (Admin Only)
// Author: Xinrui Huang
// ============================================

$conn = new mysqli('localhost', 'register', 'register', 'DEMOKIM');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get product to edit
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM Products WHERE productID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $update = $conn->prepare("UPDATE Products SET name=?, description=?, price=?, stock=?, image=? WHERE productID=?");
    $update->bind_param("ssdiss", $name, $description, $price, $stock, $image, $id);
    $update->execute();

    echo "<p>Product updated successfully!</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
<h2>Edit Product</h2>
<form method="post">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>
    <label>Description:</label><br>
    <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br>
    <label>Price:</label><br>
    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required><br>
    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required><br>
    <label>Image Path:</label><br>
    <input type="text" name="image" value="<?php echo $product['image']; ?>"><br><br>
    <input type="submit" value="Update Product">
</form>

<p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>
<?php $conn->close(); ?>
