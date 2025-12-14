<?php
// ============================================
// Edit Existing Product (Admin Only) + Image Upload
// Author: Xinrui Huang
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . "/guard_admin.php";

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper upload (same rules as add page)
function uploadProductImage(string $inputName = "image_file"): string
{
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]["error"] === UPLOAD_ERR_NO_FILE) {
        return "";
    }
    if ($_FILES[$inputName]["error"] !== UPLOAD_ERR_OK) {
        return "";
    }

    $maxBytes = 5 * 1024 * 1024;
    if ($_FILES[$inputName]["size"] > $maxBytes) {
        return "";
    }

    $tmpPath = $_FILES[$inputName]["tmp_name"];
    $imgInfo = @getimagesize($tmpPath);
    if ($imgInfo === false) {
        return "";
    }

    $allowedExt = ["jpg", "jpeg", "png", "gif", "webp"];
    $ext = strtolower(pathinfo($_FILES[$inputName]["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return "";
    }

    $relativeDir = "img/products/";
    $absoluteDir = $_SERVER["DOCUMENT_ROOT"] . "/HobbyMart/" . $relativeDir;

    if (!is_dir($absoluteDir)) {
        @mkdir($absoluteDir, 0777, true);
    }

    $safeName = bin2hex(random_bytes(8)) . "_" . time() . "." . $ext;
    $absoluteTarget = $absoluteDir . $safeName;

    if (move_uploaded_file($tmpPath, $absoluteTarget)) {
        return $relativeDir . $safeName;
    }
    return "";
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id <= 0) {
    header("Location: /HobbyMart/inventory/list_products.php?error=invalid_id");
    exit;
}

// Load product
$stmt = $conn->prepare("SELECT * FROM Products WHERE productID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: /HobbyMart/inventory/list_products.php?error=not_found");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = floatval($_POST["price"] ?? 0);
    $stock = intval($_POST["stock"] ?? 0);

    // If admin uploads a new image, use it. Otherwise keep old image.
    $newImage = uploadProductImage("image_file");

    if ($newImage === "") {
        // Optional manual override
        $manual = trim($_POST["image_path"] ?? "");
        $newImage = $manual !== "" ? $manual : $product["image"];
    }

    $update = $conn->prepare("UPDATE Products SET name=?, description=?, price=?, stock=?, image=? WHERE productID=?");
    // Correct types: s s d i s i
    $update->bind_param("ssdisi", $name, $description, $price, $stock, $newImage, $id);

    if ($update->execute()) {
        $update->close();
        $conn->close();
        header("Location: /HobbyMart/inventory/list_products.php?msg=updated");
        exit;
    } else {
        $message = "<div class='error'>❌ Update failed: " . htmlspecialchars($update->error) . "</div>";
    }

    $update->close();
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
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
    </nav>

    <h2>Edit Product</h2>
    <?php echo $message; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>

        <label>Description:</label><br>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required><br>

        <p><strong>Current Image:</strong><br>
            <?php if (!empty($product["image"])): ?>
                <img src="/HobbyMart/<?php echo htmlspecialchars($product["image"]); ?>" alt="Product Image"
                     style="max-width:200px; display:block; margin:10px 0;">
                <small><?php echo htmlspecialchars($product["image"]); ?></small>
            <?php else: ?>
                <em>No image</em>
            <?php endif; ?>
        </p>

        <label>Upload New Image (optional):</label><br>
        <input type="file" name="image_file" accept="image/*"><br><br>

        <label>OR Image Path (optional fallback):</label><br>
        <input type="text" name="image_path" value="<?php echo htmlspecialchars($product['image']); ?>"><br><br>

        <input type="submit" value="Update Product" class="btn">
    </form>

    <p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>
<?php $conn->close(); ?>

