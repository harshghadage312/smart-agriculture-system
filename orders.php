<?php
include 'db.php';
requireLogin();

$uid = $_SESSION['user_id'];
$success = isset($_GET['success']) ? "Order placed successfully! Thank you for your purchase." : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="marketplace.php">🛒 Market</a>
        <a href="cart.php">🛍️ Cart</a>
        <a href="orders.php" class="active">📋 Orders</a>
        <a href="logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>📋 My Orders</h1>
        <p>Track all your orders</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= $success ?></div>
    <?php endif; ?>

    <?php
    $res = safeQuery($conn, "SELECT * FROM orders WHERE user_id=$uid ORDER BY order_date DESC");
    if (mysqli_num_rows($res) == 0):
    ?>
        <div class="alert alert-info">You have no orders yet. <a href="marketplace.php">Shop now!</a></div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($order = mysqli_fetch_assoc($res)): ?>
                <?php
                // Fetch items
                $oid  = $order['id'];
                $ires = safeQuery($conn, "SELECT oi.*, p.product_name FROM order_items oi
                                             JOIN products p ON oi.product_id=p.id
                                             WHERE oi.order_id=$oid");
                $item_list = [];
                while ($ir = mysqli_fetch_assoc($ires)) {
                    $item_list[] = $ir['product_name'] . ' ×' . $ir['quantity'];
                }
                $badge_class = match($order['status']) {
                    'Placed'     => 'badge-blue',
                    'Processing' => 'badge-orange',
                    'Shipped'    => 'badge-blue',
                    'Delivered'  => 'badge-green',
                    'Cancelled'  => 'badge-red',
                    default      => 'badge-blue'
                };
                ?>
                <tr>
                    <td><strong>#<?= $oid ?></strong></td>
                    <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                    <td><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                    <td><span class="badge <?= $badge_class ?>"><?= $order['status'] ?></span></td>
                    <td><?= implode(', ', $item_list) ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
