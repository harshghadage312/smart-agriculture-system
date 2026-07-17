<?php
include 'db.php';
requireLogin();

$uid   = $_SESSION['user_id'];
$error = '';

// Load cart
$total = 0;
$items = [];
$res = mysqli_query($conn, "SELECT cart.*, products.product_name, products.price, products.unit
                             FROM cart JOIN products ON cart.product_id=products.id
                             WHERE cart.user_id=$uid");
while ($row = mysqli_fetch_assoc($res)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}

if (empty($items)) {
    header("Location: cart.php");
    exit;
}

// Process order
if (isset($_POST['place_order'])) {
    $address = clean($conn, $_POST['address']);
    if (empty($address)) {
        $error = "Please enter a delivery address.";
    } else {
        mysqli_query($conn, "INSERT INTO orders(user_id,total_amount,address) VALUES($uid,$total,'$address')");
        $order_id = mysqli_insert_id($conn);

        foreach ($items as $item) {
            mysqli_query($conn, "INSERT INTO order_items(order_id,product_id,quantity,price)
                                 VALUES($order_id,{$item['product_id']},{$item['quantity']},{$item['price']})");
            // reduce stock
            mysqli_query($conn, "UPDATE products SET quantity=quantity-{$item['quantity']} WHERE id={$item['product_id']}");
        }

        // Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id=$uid");
        header("Location: orders.php?success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="marketplace.php">🛒 Market</a>
        <a href="cart.php">🛍️ Cart</a>
        <a href="logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>🧾 Checkout</h1>
        <p>Review your order and provide delivery details</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:25px;align-items:start;">
        <!-- Order Summary -->
        <div>
            <h2 style="color:#2e7d32;margin-bottom:15px;">📋 Order Summary</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= $item['quantity'] ?> <?= $item['unit'] ?></td>
                            <td>₹<?= number_format($item['price'], 2) ?></td>
                            <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background:#e8f5e9;">
                        <td colspan="3" style="text-align:right;font-weight:700;padding:12px 16px;">Total:</td>
                        <td><strong style="color:#2e7d32;">₹<?= number_format($total, 2) ?></strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delivery Form -->
        <div>
            <div style="background:#fff;padding:25px;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,0.09);">
                <h2 style="color:#2e7d32;margin-bottom:20px;">🚚 Delivery Details</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="4" placeholder="Enter your full delivery address..." required></textarea>
                    </div>
                    <div style="background:#fff8e1;padding:13px;border-radius:8px;margin-bottom:18px;font-size:0.85rem;">
                        <strong>💳 Payment:</strong> Cash on Delivery (COD)<br>
                        <strong>Total:</strong> ₹<?= number_format($total, 2) ?>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-green btn-full">
                        ✅ Place Order
                    </button>
                    <a href="cart.php" style="display:block;text-align:center;margin-top:12px;color:#666;font-size:0.9rem;">← Back to Cart</a>
                </form>
            </div>
        </div>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
