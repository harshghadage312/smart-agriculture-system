<?php
include 'db.php';
$success = '';

// Handle add to cart
if (isset($_GET['add_cart'])) {
    requireLogin();
    $product_id = (int)$_GET['add_cart'];
    $user_id    = $_SESSION['user_id'];

    $check = safeQuery($conn, "SELECT id FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity=quantity+1 WHERE user_id=$user_id AND product_id=$product_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart(user_id,product_id,quantity) VALUES($user_id,$product_id,1)");
    }
    $success = "Product added to cart!";
}

// Search filter
$search = '';
$where  = "p.status='available'";
if (isset($_GET['search']) && $_GET['search'] != '') {
    $search = clean($conn, $_GET['search']);
    $where .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marketplace - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="crop_recommend.php">🌱 Crop</a>
        <a href="fertilizer_recommend.php">🧪 Fertilizer</a>
        <a href="weather_advisory.php">🌦️ Weather</a>
        <a href="pest_disease.php">🐛 Pest</a>
        <a href="marketplace.php" class="active">🛒 Market</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] !== 'buyer'): ?>
                <a href="sell_product.php">📦 Sell</a>
            <?php endif; ?>
            <a href="cart.php">🛍️ Cart</a>
            <a href="orders.php">📋 Orders</a>
            <a href="logout.php" class="btn-nav">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" class="btn-nav">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>🛒 Farm Products Marketplace</h1>
        <p>Buy fresh farm products directly from local farmers</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?> <a href="cart.php">View Cart</a></div>
    <?php endif; ?>

    <!-- Search -->
    <form method="get" style="margin-bottom:25px;display:flex;gap:10px;">
        <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>"
               style="flex:1;padding:11px 14px;border:1.5px solid #c8e6c9;border-radius:8px;font-size:0.95rem;">
        <button type="submit" class="btn btn-green">🔍 Search</button>
        <?php if ($search): ?>
            <a href="marketplace.php" class="btn btn-orange">✕ Clear</a>
        <?php endif; ?>
    </form>

    <div class="cards-grid">
    <?php
    $sql = "SELECT p.*, u.name AS seller FROM products p JOIN users u ON p.user_id=u.id WHERE $where ORDER BY p.created_at DESC";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 0):
    ?>
        <div style="grid-column:1/-1;">
            <div class="alert alert-warning">No products found. <?php if ($search) echo "Try a different search."; ?></div>
        </div>
    <?php else: while ($row = mysqli_fetch_assoc($res)): ?>
        <div class="card">
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
            <div class="card-body">
                <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                <p><?= htmlspecialchars(substr($row['description'], 0, 70)) ?>...</p>
                <span class="price">₹<?= number_format($row['price'], 2) ?>/<?= $row['unit'] ?></span>
                <p style="font-size:0.8rem;color:#888;margin-bottom:5px;">
                    Stock: <?= $row['quantity'] ?> <?= $row['unit'] ?> &nbsp;|&nbsp; By: <?= htmlspecialchars($row['seller']) ?>
                </p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="marketplace.php?add_cart=<?= $row['id'] ?>" class="btn btn-green" style="font-size:0.85rem;padding:8px 14px;">
                        🛍️ Add to Cart
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-orange" style="font-size:0.85rem;padding:8px 14px;">
                        Login to Buy
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; endif; ?>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
