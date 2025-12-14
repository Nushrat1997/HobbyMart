<?php
// ============================================
// Add New Product (Admin Only) + Image Upload
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

// Helper: upload image and return saved relative path (e.g., img/products/xxx.jpg) or empty string
function uploadProductImage(string $inputName = "image_file"): string
{
    // If user did not choose a file
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]["error"] === UPLOAD_ERR_NO_FILE) {
        return "";
    }

    // Basic upload error check
    if ($_FILES[$inputName]["error"] !== UPLOAD_ERR_OK) {
        return "";
    }

    // Security: validate file size (5MB max)
    $maxBytes = 5 * 1024 * 1024;
    if ($_FILES[$inputName]["size"] > $maxBytes) {
        return "";
    }

    // Security: validate it's an image by reading its metadata
    $tmpPath = $_FILES[$inputName]["tmp_name"];
    $imgInfo = @getimagesize($tmpPath);
    if ($imgInfo === false) {
        return "";
    }

    // Allow only common image extensions
    $allowedExt = ["jpg", "jpeg", "png", "gif", "webp"];
    $originalName = $_FILES[$inputName]["name"];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return "";
    }

    // Save to /HobbyMart/img/products/
    $relativeDir = "img/products/";
    $absoluteDir = $_SERVER["DOCUMENT_ROOT"] . "/HobbyMart/" . $relativeDir;

    // Ensure directory exists
    if (!is_dir($absoluteDir)) {
        @mkdir($absoluteDir, 0777, true);
    }

    // Create unique filename
    $safeName = bin2hex(random_bytes(8)) . "_" . time() . "." . $ext;
    $absoluteTarget = $absoluteDir . $safeName;

    if (move_uploaded_file($tmpPath, $absoluteTarget)) {
        return $relativeDir . $safeName; // store in DB
    }

    return "";
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Basic input
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = floatval($_POST["price"] ?? 0);
    $stock = intval($_POST["stock"] ?? 0);

    // Upload image (optional)
    $imagePath = uploadProductImage("image_file");

    // If no uploaded file, allow manual path as fallback (optional)
    if ($imagePath === "") {
        $manual = trim($_POST["image_path"] ?? "");
        $imagePath = $manual; // e.g. img/products/example.jpg
    }

    // Insert new product
    $stmt = $conn->prepare("INSERT INTO Products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $stock, $imagePath);

    if ($stmt->execute()) {
        // Redirect to list for clean UX
        $stmt->close();
        $conn->close();
        header("Location: /HobbyMart/inventory/list_products.php?msg=added");
        exit;
    } else {
        $message = "<div class='error'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
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

    <?php echo $message; ?>

    <!-- IMPORTANT: enctype is required for file uploads -->
    <form method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" required><br>

        <label>Upload Image:</label><br>
        <input type="file" name="image_file" accept="image/*"><br><br>

        <label>OR Image Path (optional fallback):</label><br>
        <input type="text" name="image_path" placeholder="e.g. img/products/product.jpg"><br><br>

        <input type="submit" value="Add Product" class="btn">
    </form>

    <p><a href="list_products.php" class="btn">Back to Product List</a></p>
</body>
</html>
<?php $conn->close(); ?>


