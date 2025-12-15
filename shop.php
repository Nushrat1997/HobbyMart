<?php
// ============================================
// shop.php - Front Shop Page (Customer/Guest View)
// Author: Xinrui Huang
// Description:
// - Shows products in a "store-like" layout
// - Supports "Add to Cart" (session cart) via /cart/add_to_cart.php
// - Uses Products table from HOBBYMART database
// ============================================

session_start();

// Allow logged-in users and guests
// If not logged in and not a guest, send back to login landing page
if (!isset($_SESSION['id']) && empty($_SESSION['guest'])) {
    header("Location: http://localhost/HobbyMart/");
    exit;
}

// If admin, you may optionally redirect to inventory backend
// Comment this out if you want admins to also view the shop
if (!empty($_SESSION['admin']) && $_SESSION['admin'] === true) {
    // Admin can manage inventory in backend
    // header("Location: http://localhost/HobbyMart/inventory/list_products.php");
    // exit;
}

// Database connection (match your current project setup)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Load products
$sql = "SELECT productID, name, description, price, stock, image FROM Products ORDER BY productID ASC";
$result = $conn->query($sql);

// Optional message display (after add-to-cart etc.)
$msg = $_GET['msg'] ?? '';
function showMessage($msg) {
    if ($msg === 'added') {
        echo "<div class='success'>✅ Added to cart.</div>";
    } elseif ($msg === 'invalid') {
        echo "<div class='error'>❌ Invalid product.</div>";
    } elseif ($msg === 'outofstock') {
        echo "<div class='error'>⚠️ This product is out of stock.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HobbyMart Shop</title>
    <link rel="stylesheet" href="/HobbyMart/css/styles.css">
    <link rel="stylesheet" href="/HobbyMart/css/inventory.css">

    <!-- Small page-specific styling (safe) -->
    <style>
        .shop-header { margin-bottom: 10px; }
        .shop-subtitle { margin-bottom: 18px; color: #333; }

        /* Grid layout */
        .shop-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .product-card{
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* FIX: image size control */
        .product-img-wrap{
            width: 100%;
            height: 180px;            /* fixed height */
            background: #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img{
            width: 100%;
            height: 100%;
            object-fit: cover;        /* crop nicely */
            display: block;
        }

        .product-body{
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
        }

        .product-title{
            font-weight: bold;
            color: #0055aa;
            margin: 0;
        }

        .product-price{
            font-weight: bold;
            margin: 0;
        }

        .product-stock{
            margin: 0;
            color: #333;
        }

        .product-desc{
            margin: 0;
            color: #555;
            font-size: 14px;
            line-height: 1.3;
        }

        .product-actions{
            margin-top: auto;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn.secondary{
            background: #666;
        }
        .btn.secondary:hover{
            background: #444;
        }

        .btn.disabled{
            background: #999;
            cursor: not-allowed;
        }
        .btn.disabled:hover{
            background: #999;
        }
    </style>
</head>

<body>
    <nav>
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
    </nav>

    <div class="shop-header">
        <h2>Shop</h2>
        <p class="shop-subtitle">Browse products below.</p>
        <?php showMessage($msg); ?>
    </div>

    <div class="shop-grid">
        <?php if ($result && $result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <?php
                    $pid   = (int)$row['productID'];
                    $name  = $row['name'] ?? '';
                    $desc  = $row['description'] ?? '';
                    $price = (float)($row['price'] ?? 0);
                    $stock = (int)($row['stock'] ?? 0);
                    $img   = $row['image'] ?? '';

                    // If DB stores "img/products/xxx.jpg", then we can load it directly.
                    // If empty, show a simple placeholder block.
                    $imgSrc = !empty($img) ? "/HobbyMart/" . ltrim($img, "/") : "";
                ?>

                <div class="product-card">
                    <div class="product-img-wrap">
                        <?php if (!empty($imgSrc)) { ?>
                            <img class="product-img" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="product image">
                        <?php } else { ?>
                            <span class="no-image">No image</span>
                        <?php } ?>
                    </div>

                    <div class="product-body">
                        <p class="product-title"><?php echo htmlspecialchars($name); ?></p>
                        <p class="product-price">$<?php echo number_format($price, 2); ?></p>
                        <p class="product-stock">In stock: <?php echo $stock; ?></p>

                        <?php
                            // Short description (demo-friendly)
                            $shortDesc = mb_strlen($desc) > 80 ? mb_substr($desc, 0, 80) . "..." : $desc;
                        ?>
                        <?php if (!empty($shortDesc)) { ?>
                            <p class="product-desc"><?php echo htmlspecialchars($shortDesc); ?></p>
                        <?php } ?>

                        <div class="product-actions">
                        <a class="btn secondary" href="/HobbyMart/inventory/product_detail.php?id=<?php echo $pid; ?>&return=shop">
                            View Details
                        </a>


                            <?php if ($stock > 0) { ?>
                                <form method="post" action="/HobbyMart/cart/add_to_cart.php" style="display:inline;">
                                    <input type="hidden" name="productID" value="<?php echo $pid; ?>">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn">Add to Cart</button>
                                </form>
                            <?php } else { ?>
                                <button class="btn disabled" type="button">Out of Stock</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            <?php } ?>
        <?php } else { ?>
            <div class="warning">No products found.</div>
        <?php } ?>
    </div>

</body>
</html>
<?php $conn->close(); ?>

