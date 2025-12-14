<?php
    session_start();
// ============================================
// Add New Product (Admin Only)
// Author: Xinrui Huang
// ============================================

// Database connection
// Correct Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize form input
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    // Prepare SQL statement
    // NOTE: 5 columns → therefore 5 placeholders (?) → matching 5 variables
    $stmt = $conn->prepare("INSERT INTO Products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");

    // ✅ FIXED: Corrected type definition string
    // s = string, s = string, d = double (decimal), i = integer, s = string
    $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image);

    // Execute and check result
    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Product added successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
    </nav>
    <h2>Add New Product</h2>
    <form method="post">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" required><br>

        <label>Image Path:</label><br>
        <input type="text" name="image" placeholder="e.g. img/product.jpg"><br><br>

        <input type="submit" value="Add Product" class="btn">
    </form>

    <p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>
<?php 
$conn->close(); 
?>

