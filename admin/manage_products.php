<?php
include '../db.php';
requireAdmin();
$success = '';

if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM products WHERE id=" . (int)$_GET['delete']);
    $success = "Product deleted.";
}

if (isset($_GET['toggle'])) {
    $pid = (int)$_GET['toggle'];
    $cur = mysqli_fetch_assoc(safeQuery($conn, "SELECT status FROM products WHERE id=$pid"));
    $new = $cur['status'] === 'available' ? 'sold_out' : 'available';
    mysqli_query($conn, "UPDATE products SET status='$new' WHERE id=$pid");
    $success = "Product status updated.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_users.php">👤 Users</a>
        <a href="manage_products.php" class="active">📦 Products</a>
        <a href="view_orders.php">📋 Orders</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="page-header"><h1>📦 Manage Marketplace Products</h1></div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Product</th><th>Seller</th><th>Price</th><th>Qty</th><th>Unit</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT p.*, u.name AS seller FROM products p JOIN users u ON p.user_id=u.id ORDER BY p.created_at DESC");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <img src="<?= $row['image'] ?>" style="width:40px;height:35px;object-fit:cover;border-radius:5px;">
                        <strong><?= htmlspecialchars($row['product_name']) ?></strong>
                    </div>
                </td>
                <td><?= htmlspecialchars($row['seller']) ?></td>
                <td>₹<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['unit'] ?></td>
                <td>
                    <span class="badge <?= $row['status']=='available' ? 'badge-green' : 'badge-red' ?>">
                        <?= ucfirst(str_replace('_',' ',$row['status'])) ?>
                    </span>
                </td>
                <td style="display:flex;gap:5px;flex-wrap:wrap;">
                    <a href="manage_products.php?toggle=<?= $row['id'] ?>" class="btn btn-orange" style="padding:4px 8px;font-size:0.78rem;">Toggle</a>
                    <a href="manage_products.php?delete=<?= $row['id'] ?>" class="btn btn-red" style="padding:4px 8px;font-size:0.78rem;"
                       onclick="return confirm('Delete?')">Delete</a>
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
