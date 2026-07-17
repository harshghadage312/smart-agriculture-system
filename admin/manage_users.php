<?php
include '../db.php';
requireAdmin();
$success = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role!='admin'");
    $success = "User deleted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_users.php" class="active">👤 Users</a>
        <a href="manage_crops.php">🌱 Crops</a>
        <a href="manage_products.php">📦 Products</a>
        <a href="view_orders.php">📋 Orders</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="page-header"><h1>👤 Manage Users</h1></div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT * FROM users ORDER BY created_at DESC");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>
                    <?php
                    $bc = match($row['role']) { 'admin'=>'badge-red', 'farmer'=>'badge-green', default=>'badge-blue' };
                    ?>
                    <span class="badge <?= $bc ?>"><?= ucfirst($row['role']) ?></span>
                </td>
                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php if ($row['role'] !== 'admin'): ?>
                    <a href="manage_users.php?delete=<?= $row['id'] ?>" class="btn btn-red"
                       style="padding:4px 10px;font-size:0.8rem;" onclick="return confirm('Delete user?')">Delete</a>
                    <?php else: echo '<span style="color:#aaa;font-size:0.85rem;">—</span>'; endif; ?>
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
