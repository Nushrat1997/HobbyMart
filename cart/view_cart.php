<?php
// ============================================
// View Cart Page (UI)
// Author: Xinrui Huang (updated)
// FIX: Removed nested <form> (Checkout uses formaction now)
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB config
$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure cart exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart']; // productID => qty

// Load cart product rows
$items = [];
$total = 0.0;

if (count($cart) > 0) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $sql = "SELECT productID, name, price, stock, image FROM Products WHERE productID IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $pid = (int)$row['productID'];
        $qty = isset($cart[$pid]) ? (int)$cart[$pid] : 0;

        if ($qty <= 0) continue;

        $price = (float)$row['price'];
        $lineTotal = $price * $qty;
        $total += $lineTotal;

        $row['qty'] = $qty;
        $row['lineTotal'] = $lineTotal;
        $items[] = $row;
    }

    $stmt->close();
}

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart - HobbyMart</title>
    <link rel="stylesheet" href="/HobbyMart/css/styles.css">
    <link rel="stylesheet" href="/HobbyMart/css/inventory.css">

    <style>
        .qty-input{
            width: 70px !important;
            padding: 6px 8px !important;
        }

        .cart-footer{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkout-wrap{
            margin-left: auto;
        }

        .cart-img{
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #f2f2f2;
        }

        /* make form not look like a big white block */
        .inline-form{
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
    </style>

    <script>
        function confirmCheckout(){
            return confirm("Confirm payment?\nThis will reduce product stock and clear your cart.");
        }
    </script>
</head>

<body>
<nav>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
</nav>

<h2>Your Cart</h2>

<?php if ($msg !== ""): ?>
    <div class="success"><?php echo $msg; ?></div>
<?php endif; ?>

<?php if (count($items) === 0): ?>
    <div class="card">
        <p>Your cart is empty.</p>
        <p><a class="btn" href="/HobbyMart/shop.php">Continue Shopping</a></p>
    </div>
<?php else: ?>

<!-- ONE form only (no nested forms) -->
<form class="inline-form" method="post" action="/HobbyMart/cart/cart.php">
    <table>
        <tr>
            <th style="width:90px;">Image</th>
            <th>Product</th>
            <th style="width:110px;">Price</th>
            <th style="width:140px;">Qty</th>
            <th style="width:140px;">Line Total</th>
            <th style="width:90px;">Action</th>
        </tr>

        <?php foreach ($items as $p): ?>
            <?php
                $pid = (int)$p['productID'];
                $img = isset($p['image']) ? trim($p['image']) : "";
                $imgSrc = $img !== "" ? ("/HobbyMart/" . ltrim($img, "/")) : "";
                $stock = (int)$p['stock'];
            ?>
            <tr>
                <td>
                    <?php if ($imgSrc !== ""): ?>
                        <img class="cart-img" src="<?php echo htmlspecialchars($imgSrc); ?>" alt="product">
                    <?php else: ?>
                        <span style="color:#888;">No image</span>
                    <?php endif; ?>
                </td>

                <td><?php echo htmlspecialchars($p['name']); ?></td>

                <td>$<?php echo number_format((float)$p['price'], 2); ?></td>

                <td>
                    <input class="qty-input" type="number" min="0" max="99"
                           name="qty[<?php echo $pid; ?>]"
                           value="<?php echo (int)$p['qty']; ?>">
                    <div style="font-size:11px; color:#666; margin-top:4px;">
                        In stock: <?php echo $stock; ?>
                    </div>
                </td>

                <td>$<?php echo number_format((float)$p['lineTotal'], 2); ?></td>

                <td>
                    <a href="/HobbyMart/cart/cart_remove.php?id=<?php echo $pid; ?>" onclick="return confirm('Remove this item?');">
                        Remove
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="4" style="text-align:right;"><strong>Total</strong></td>
            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
            <td></td>
        </tr>
    </table>

    <div class="cart-footer">
        <div>
            <button type="submit" name="update_cart" class="btn">Update Cart</button>
            <button type="submit" name="clear_cart" class="btn" onclick="return confirm('Clear the entire cart?');">Clear Cart</button>
            <a class="btn" href="/HobbyMart/shop.php">Continue Shopping</a>
        </div>

        <div class="checkout-wrap">
            <!-- Checkout is NOT a new form. It's the same form, but different target. -->
            <button
                type="submit"
                class="btn"
                formaction="/HobbyMart/cart/checkout_process.php"
                formmethod="post"
                onclick="return confirmCheckout();"
            >
                Checkout
            </button>
        </div>
    </div>
</form>

<?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>




