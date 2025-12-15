<?php
// ============================================
// Product Details Page (Shop + Inventory shared)
// Author: Xinrui Huang
// Updated: smarter default return + safer checks
// ============================================

session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid product ID.");
}

/*
Return target: shop / inventory
- If return is provided, follow it.
- If not provided, guess by role:
  admin -> inventory
  user/guest -> shop
*/
$return = $_GET['return'] ?? null;

if ($return !== 'shop' && $return !== 'inventory') {
    // Guess based on session
    // admin session is set in login.php: $_SESSION['admin'] = true/false
    if (!empty($_SESSION['admin']) && $_SESSION['admin'] === true) {
        $return = 'inventory';
    } else {
        $return = 'shop';
    }
}

$backUrl = ($return === 'shop')
    ? "/HobbyMart/shop.php"
    : "/HobbyMart/inventory/list_products.php";

// Fetch product
$stmt = $conn->prepare("SELECT * FROM Products WHERE productID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Image path
$img = $product['image'] ?? '';
$imgSrc = !empty($img) ? "/HobbyMart/" . ltrim($img, "/") : "";

$stock = (int)($product['stock'] ?? 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
    <link rel="stylesheet" href="/HobbyMart/css/styles.css">
    <link rel="stylesheet" href="/HobbyMart/css/inventory.css">
    <style>
        .detail-wrap{
            background: white;
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            max-width: 900px;
        }
        .detail-img{
            width: 360px;
            height: 360px;
            object-fit: cover;
            border-radius: 8px;
            background: #f2f2f2;
            display:block;
            margin-bottom: 12px;
        }
        .detail-actions{
            margin-top: 14px;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }
        .detail-actions form{
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .btn.disabled{
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
    </nav>

    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

    <div class="detail-wrap">
        <?php if (!empty($imgSrc)) { ?>
            <img class="detail-img" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="product image">
        <?php } ?>

        <p><strong>Price:</strong> $<?php echo number_format((float)$product['price'], 2); ?></p>
        <p><strong>Stock:</strong> <?php echo $stock; ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>

        <div class="detail-actions">
            <?php if ($return === 'shop') { ?>
                <?php if ($stock > 0) { ?>
                    <form method="post" action="/HobbyMart/cart/add_to_cart.php">
                        <input type="hidden" name="productID" value="<?php echo (int)$product['productID']; ?>">
                        <input type="hidden" name="qty" value="1">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                <?php } else { ?>
                    <button type="button" class="btn disabled">Out of Stock</button>
                <?php } ?>
            <?php } ?>

            <a href="<?php echo $backUrl; ?>" class="btn">Back</a>
        </div>
    </div>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>



