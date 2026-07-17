<?php
include '../db.php';
requireAdmin();

$success = '';

// Update status
if (isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = clean($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$oid");
    $success = "Order #$oid status updated to $status.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_crops.php">🌱 Crops</a>
        <a href="manage_products.php">📦 Products</a>
        <a href="view_orders.php" class="active">📋 Orders</a>
        <a href="manage_users.php">👤 Users</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header"><h1>📋 All Orders</h1><p>Manage and update order statuses</p></div>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT o.*, u.name AS buyer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.order_date DESC");
            while ($order = mysqli_fetch_assoc($res)):
                $oid  = $order['id'];
                $ires = safeQuery($conn, "SELECT oi.quantity, p.product_name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$oid");
                $items = [];
                while ($ir = mysqli_fetch_assoc($ires)) {
                    $items[] = $ir['product_name'] . '×' . $ir['quantity'];
                }
            ?>
            <tr>
                <td><strong>#<?= $oid ?></strong></td>
                <td><?= htmlspecialchars($order['buyer']) ?></td>
                <td style="font-size:0.82rem;"><?= implode(', ', $items) ?></td>
                <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars(substr($order['address'], 0, 40)) ?>...</td>
                <td>
                    <?php
                    $badge = match($order['status']) {
                        'Placed'     => 'badge-blue',
                        'Processing' => 'badge-orange',
                        'Shipped'    => 'badge-blue',
                        'Delivered'  => 'badge-green',
                        'Cancelled'  => 'badge-red',
                        default      => 'badge-blue'
                    };
                    ?>
                    <span class="badge <?= $badge ?>"><?= $order['status'] ?></span>
                </td>
                <td>
                    <form method="post" style="display:flex;gap:5px;align-items:center;">
                        <input type="hidden" name="order_id" value="<?= $oid ?>">
                        <select name="status" style="padding:4px 6px;border:1px solid #ccc;border-radius:5px;font-size:0.8rem;">
                            <?php foreach (['Placed','Processing','Shipped','Delivered','Cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $order['status']==$s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-green" style="padding:4px 10px;font-size:0.78rem;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart Admin</p></footer>
</body>
</html>
