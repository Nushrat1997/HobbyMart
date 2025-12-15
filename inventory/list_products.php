<?php
// ============================================
// Product Inventory - Admin List View (Back Office)
// Author: Xinrui Huang
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
    die("Database connection failed: " . $conn->connect_error);
}

// Role flag (admin only features)
$isAdmin = isset($_SESSION['id']) && isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Fetch products (ordered for a cleaner list)
$result = $conn->query("SELECT * FROM Products ORDER BY productID ASC");

// Messages (optional)
$msg = "";
if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "added")   $msg = "<div class='success'>✅ Product added successfully!</div>";
    if ($_GET["msg"] === "updated") $msg = "<div class='success'>✅ Product updated successfully!</div>";
    if ($_GET["msg"] === "deleted") $msg = "<div class='success'>✅ Product deleted successfully!</div>";
}

$err = "";
if (isset($_GET["error"])) {
    if ($_GET["error"] === "unauthorized") $err = "<div class='error'>⚠️ Access denied. Admin permission required.</div>";
    if ($_GET["error"] === "invalid_id")   $err = "<div class='error'>⚠️ Invalid product ID.</div>";
    if ($_GET["error"] === "not_found")    $err = "<div class='error'>⚠️ Product not found.</div>";
    if ($_GET["error"] === "delete_failed")$err = "<div class='error'>❌ Delete failed. Please try again.</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product List - HobbyMart</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
    </nav>

    <h2>Product List</h2>

    <?php echo $msg; ?>
    <?php echo $err; ?>

    <!-- Admin-only Add -->
    <?php if ($isAdmin): ?>
        <p><a href="add_product.php" class="btn">Add New Product</a></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo (int)$row['productID']; ?></td>

                <td>
                    <?php if (!empty($row["image"])): ?>
                        <img src="/HobbyMart/<?php echo htmlspecialchars($row["image"]); ?>"
                             alt="Product Image"
                             style="width:60px; height:60px; object-fit:cover; border:1px solid #ccc;">
                    <?php else: ?>
                        <span style="color:#888;">No image</span>
                    <?php endif; ?>
                </td>

                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>

                <td>
                    <!-- ✅ Important: add return=inventory so Back always returns here -->
                    <a href="product_detail.php?id=<?php echo (int)$row['productID']; ?>&return=inventory">View</a>

                    <?php if ($isAdmin): ?>
                        | <a href="edit_product.php?id=<?php echo (int)$row['productID']; ?>">Edit</a>
                        | <a href="delete_product.php?id=<?php echo (int)$row['productID']; ?>"
                             onclick="return confirm('Delete this product?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>
<?php $conn->close(); ?>

