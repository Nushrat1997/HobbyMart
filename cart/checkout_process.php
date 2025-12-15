<?php
// ============================================
// Checkout Process
// Author: Xinrui Huang (final revised version)
// Behavior:
// - Validate cart
// - Check stock for each item
// - Reduce stock in Products table
// - Clear cart on success
// - Show a simple receipt page (demo purpose)
// ============================================

session_start();

// ---------- DB connection ----------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "HOBBYMART";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: /HobbyMart/cart/view_cart.php?msg=" . urlencode("Cart is empty."));
    exit;
}

$cart = $_SESSION['cart'];

$errors = [];
$receipt = [];
$total = 0.0;

$conn->begin_transaction();

try {
    // Prepare statements once
    $selectStmt = $conn->prepare("SELECT productID, name, price, stock FROM Products WHERE productID = ?");
    $updateStmt = $conn->prepare("UPDATE Products SET stock = stock - ? WHERE productID = ? AND stock >= ?");

    foreach ($cart as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;

        if ($pid <= 0 || $qty <= 0) continue;

        // 1) Read product info
        $selectStmt->bind_param("i", $pid);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            $errors[] = "Product ID $pid not found.";
            continue;
        }

        $name  = $product['name'];
        $price = (float)$product['price'];
        $stock = (int)$product['stock'];

        // 2) Stock check
        if ($qty > $stock) {
            $errors[] = "Not enough stock for: $name (requested $qty, available $stock).";
            continue;
        }

        // 3) Reduce stock (extra safety: WHERE stock >= qty)
        // bind: (qty, pid, qty)
        $updateStmt->bind_param("iii", $qty, $pid, $qty);
        $updateStmt->execute();

        if ($updateStmt->affected_rows <= 0) {
            $errors[] = "Stock update failed for: $name.";
            continue;
        }

        $lineTotal = $price * $qty;
        $total += $lineTotal;

        $receipt[] = [
            'name' => $name,
            'qty'  => $qty,
            'price'=> $price,
            'line' => $lineTotal
        ];
    }

    // If any errors → rollback
    if (!empty($errors)) {
        $conn->rollback();
    } else {
        $conn->commit();
        // Clear cart after successful payment
        $_SESSION['cart'] = [];
    }

    $selectStmt->close();
    $updateStmt->close();

} catch (Exception $e) {
    $conn->rollback();
    $errors[] = "Checkout failed: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - HobbyMart</title>
    <link rel="stylesheet" href="/HobbyMart/css/styles.css">
    <link rel="stylesheet" href="/HobbyMart/css/inventory.css">
    <style>
        .box {
            background: #fff;
            border: 1px solid #ddd;
            padding: 14px;
            border-radius: 6px;
            margin-top: 14px;
        }
        .error { color: #b00020; }
        .success { color: #1b5e20; }
    </style>
</head>
<body>
<nav>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/HobbyMart/nav.php"; ?>
</nav>

<h2>Checkout</h2>

<?php if (!empty($errors)) { ?>
    <div class="box">
        <h3 class="error">Payment Not Completed</h3>
        <ul>
            <?php foreach ($errors as $err) { ?>
                <li class="error"><?php echo htmlspecialchars($err); ?></li>
            <?php } ?>
        </ul>
        <p><a class="btn" href="/HobbyMart/cart/view_cart.php">Back to Cart</a></p>
    </div>
<?php } else { ?>
    <div class="box">
        <h3 class="success">✅ Payment Successful!</h3>
        <p>Your order is confirmed (demo).</p>

        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Line Total</th>
            </tr>
            <?php foreach ($receipt as $r) { ?>
            <tr>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td><?php echo (int)$r['qty']; ?></td>
                <td>$<?php echo number_format($r['price'], 2); ?></td>
                <td>$<?php echo number_format($r['line'], 2); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
            </tr>
        </table>

        <p style="margin-top:12px;">
            <a class="btn" href="/HobbyMart/shop.php">Continue Shopping</a>
        </p>
    </div>
<?php } ?>

</body>
</html>

