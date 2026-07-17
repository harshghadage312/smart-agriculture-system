<?php
include '../db.php';
requireAdmin();

// Stats
$total_users    = mysqli_fetch_row(safeQuery($conn, "SELECT COUNT(*) FROM users WHERE role!='admin'"))[0];
$total_products = mysqli_fetch_row(safeQuery($conn, "SELECT COUNT(*) FROM products"))[0];
$total_orders   = mysqli_fetch_row(safeQuery($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_revenue  = mysqli_fetch_row(safeQuery($conn, "SELECT SUM(total_amount) FROM orders"))[0] ?? 0;
$total_crops    = mysqli_fetch_row(safeQuery($conn, "SELECT COUNT(*) FROM crops"))[0];
$total_pests    = mysqli_fetch_row(safeQuery($conn, "SELECT COUNT(*) FROM pests"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - AgriSmart</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .stat-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:18px; margin:20px 0; }
        .stat-card { background:#fff; border-radius:12px; padding:22px 18px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.08); border-top:4px solid #2e7d32; }
        .stat-card .num { font-size:2rem; font-weight:700; color:#2e7d32; }
        .stat-card .lbl { font-size:0.85rem; color:#666; margin-top:5px; }
        .admin-nav { display:flex; flex-wrap:wrap; gap:10px; margin:20px 0; }
        .admin-nav a { background:#2e7d32; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; font-size:0.9rem; transition:background 0.2s; }
        .admin-nav a:hover { background:#1b5e20; }
    </style>
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="manage_users.php">👤 Users</a>
        <a href="manage_crops.php">🌱 Crops</a>
        <a href="manage_fertilizers.php">🧪 Fertilizers</a>
        <a href="manage_weather.php">🌦️ Weather</a>
        <a href="manage_pests.php">🐛 Pests</a>
        <a href="manage_products.php">📦 Products</a>
        <a href="view_orders.php">📋 Orders</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>🛠️ Admin Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! Manage your AgriSmart platform.</p>
    </div>

    <!-- Stats -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="num"><?= $total_users ?></div>
            <div class="lbl">👤 Registered Users</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $total_products ?></div>
            <div class="lbl">📦 Products Listed</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $total_orders ?></div>
            <div class="lbl">📋 Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="num">₹<?= number_format($total_revenue, 0) ?></div>
            <div class="lbl">💰 Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $total_crops ?></div>
            <div class="lbl">🌱 Crop Records</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $total_pests ?></div>
            <div class="lbl">🐛 Pest Records</div>
        </div>
    </div>

    <!-- Quick Links -->
    <h2 class="section-title">⚡ Quick Actions</h2>
    <div class="admin-nav">
        <a href="manage_users.php">👤 Manage Users</a>
        <a href="manage_crops.php">🌱 Manage Crops</a>
        <a href="manage_fertilizers.php">🧪 Manage Fertilizers</a>
        <a href="manage_weather.php">🌦️ Manage Weather</a>
        <a href="manage_pests.php">🐛 Manage Pests</a>
        <a href="manage_products.php">📦 Manage Products</a>
        <a href="view_orders.php">📋 View All Orders</a>
    </div>

    <!-- Recent Orders -->
    <h2 class="section-title" style="margin-top:35px;">🕐 Recent Orders</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order ID</th><th>Buyer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT o.*, u.name AS buyer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.order_date DESC LIMIT 10");
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><strong>#<?= $row['id'] ?></strong></td>
                <td><?= htmlspecialchars($row['buyer']) ?></td>
                <td>₹<?= number_format($row['total_amount'], 2) ?></td>
                <td><span class="badge badge-blue"><?= $row['status'] ?></span></td>
                <td><?= date('d M Y', strtotime($row['order_date'])) ?></td>
                <td><a href="view_orders.php" class="btn btn-blue" style="padding:4px 10px;font-size:0.8rem;">Manage</a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart Admin Panel</p></footer>
</body>
</html>
