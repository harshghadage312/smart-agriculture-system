<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Agriculture Advisory & Marketplace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAV -->
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php" class="active">Home</a>
        <a href="crop_recommend.php">🌱 Crop</a>
        <a href="fertilizer_recommend.php">🧪 Fertilizer</a>
        <a href="weather_advisory.php">🌦️ Weather</a>
        <a href="pest_disease.php">🐛 Pest</a>
        <a href="marketplace.php">🛒 Market</a>
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

<!-- HERO -->
<div class="hero">
    <h1>🌾 Smart Agriculture Advisory System</h1>
    <p>Get crop recommendations, fertilizer advice, weather tips, pest management & buy/sell farm products</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn">Get Started Free</a>
    <?php else: ?>
        <a href="crop_recommend.php" class="btn">Start Advising</a>
    <?php endif; ?>
</div>

<!-- MODULES -->
<div class="container">
    <h2 class="section-title">Our Features</h2>

    <div class="module-grid">
        <a href="crop_recommend.php" class="module-card">
            <span class="icon">🌱</span>
            <h3>Crop Recommendation</h3>
            <p>Get best crops based on soil, season & water</p>
        </a>
        <a href="fertilizer_recommend.php" class="module-card">
            <span class="icon">🧪</span>
            <h3>Fertilizer Advice</h3>
            <p>Recommend fertilizers based on soil nutrients</p>
        </a>
        <a href="weather_advisory.php" class="module-card">
            <span class="icon">🌦️</span>
            <h3>Weather Advisory</h3>
            <p>Farming tips based on season & rainfall</p>
        </a>
        <a href="pest_disease.php" class="module-card">
            <span class="icon">🐛</span>
            <h3>Pest & Disease</h3>
            <p>Identify pests, diseases & get treatments</p>
        </a>
        <a href="marketplace.php" class="module-card">
            <span class="icon">🛒</span>
            <h3>Marketplace</h3>
            <p>Buy and sell fresh farm products</p>
        </a>
    </div>

    <!-- LATEST PRODUCTS -->
    <h2 class="section-title" style="margin-top:50px;">🛒 Latest Products in Market</h2>
    <div class="cards-grid">
    <?php
    $res = safeQuery($conn, "SELECT p.*, u.name AS seller FROM products p JOIN users u ON p.user_id=u.id WHERE p.status='available' ORDER BY p.created_at DESC LIMIT 4");
    while ($row = mysqli_fetch_assoc($res)):
    ?>
        <div class="card">
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
            <div class="card-body">
                <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                <p><?= htmlspecialchars(substr($row['description'], 0, 60)) ?>...</p>
                <span class="price">₹<?= number_format($row['price'], 2) ?>/<?= $row['unit'] ?></span>
                <p style="font-size:0.78rem;color:#888;">by <?= htmlspecialchars($row['seller']) ?></p>
                <a href="marketplace.php" class="btn btn-green" style="margin-top:8px;font-size:0.85rem;padding:8px 14px;">View All</a>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 AgriSmart &mdash; Smart Agriculture Advisory & Marketplace System</p>
</footer>
</body>
</html>
