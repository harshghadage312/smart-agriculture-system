<?php
include 'db.php';
requireLogin();

$uid = $_SESSION['user_id'];

// Handle remove
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id=$id AND user_id=$uid");
    header("Location: cart.php");
    exit;
}

// Handle update qty
if (isset($_POST['update'])) {
    $cart_id = (int)$_POST['cart_id'];
    $qty     = max(1, (int)$_POST['quantity']);
    mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$uid");
    header("Location: cart.php");
    exit;
}

$total = 0;
$items = [];
$res = safeQuery($conn, "SELECT cart.*, products.product_name, products.price, products.unit, products.image
                             FROM cart
                             JOIN products ON cart.product_id = products.id
                             WHERE cart.user_id = $uid");
while ($row = mysqli_fetch_assoc($res)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="marketplace.php">🛒 Market</a>
        <a href="cart.php" class="active">🛍️ Cart</a>
        <a href="orders.php">📋 Orders</a>
        <a href="logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>🛍️ My Cart</h1>
        <p>Review your items before checkout</p>
    </div>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">Your cart is empty. <a href="marketplace.php">Browse products</a></div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <img src="<?= htmlspecialchars($item['image']) ?>" style="width:55px;height:45px;object-fit:cover;border-radius:6px;">
                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                        </div>
                    </td>
                    <td>₹<?= number_format($item['price'], 2) ?>/<?= $item['unit'] ?></td>
                    <td>
                        <form method="post" style="display:flex;align-items:center;gap:6px;">
                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>"
                                   min="1" style="width:60px;padding:5px;border:1px solid #c8e6c9;border-radius:6px;">
                            <button type="submit" name="update" class="btn btn-blue" style="padding:5px 10px;font-size:0.8rem;">Update</button>
                        </form>
                    </td>
                    <td><strong>₹<?= number_format($item['subtotal'], 2) ?></strong></td>
                    <td>
                        <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-red" style="padding:5px 12px;font-size:0.8rem;"
                           onclick="return confirm('Remove item?')">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr style="background:#e8f5e9;">
                    <td colspan="3" style="text-align:right;font-weight:700;padding:14px 16px;">Grand Total:</td>
                    <td colspan="2"><strong style="font-size:1.2rem;color:#2e7d32;">₹<?= number_format($total, 2) ?></strong></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div style="text-align:right;margin-top:20px;">
            <a href="marketplace.php" class="btn btn-orange">← Continue Shopping</a>
            &nbsp;
            <a href="checkout.php" class="btn btn-green">Proceed to Checkout →</a>
        </div>
    <?php endif; ?>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
