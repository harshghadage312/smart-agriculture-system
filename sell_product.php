<?php
include 'db.php';
requireLogin();

if ($_SESSION['role'] === 'buyer') {
    header("Location: marketplace.php");
    exit;
}

$success = '';
$error   = '';

if (isset($_POST['sell'])) {
    $name  = clean($conn, $_POST['product_name']);
    $desc  = clean($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $qty   = (int)$_POST['quantity'];
    $unit  = clean($conn, $_POST['unit']);
    $image = clean($conn, $_POST['image']);
    $uid   = $_SESSION['user_id'];

    if (empty($image)) {
        $image = "https://via.placeholder.com/300x200?text=" . urlencode($name);
    }

    if ($price <= 0 || $qty <= 0) {
        $error = "Price and quantity must be greater than 0.";
    } else {
        mysqli_query($conn, "INSERT INTO products(user_id,product_name,description,price,quantity,unit,image)
                             VALUES($uid,'$name','$desc',$price,$qty,'$unit','$image')");
        $success = "Product listed successfully! <a href='marketplace.php'>View Marketplace</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Product - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="marketplace.php">🛒 Market</a>
        <a href="sell_product.php" class="active">📦 Sell</a>
        <a href="orders.php">📋 Orders</a>
        <a href="logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="form-box" style="max-width:560px;">
        <h2>📦 List Your Product</h2>
        <p style="text-align:center;color:#666;font-size:0.9rem;margin-bottom:20px;">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" placeholder="e.g. Fresh Wheat" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Describe your product..."></textarea>
            </div>
            <div class="form-group">
                <label>Price (₹)</label>
                <input type="number" name="price" placeholder="Price per unit" min="1" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Quantity Available</label>
                <input type="number" name="quantity" placeholder="Amount you want to sell" min="1" required>
            </div>
            <div class="form-group">
                <label>Unit</label>
                <select name="unit">
                    <option value="kg">kg</option>
                    <option value="quintal">quintal</option>
                    <option value="litre">litre</option>
                    <option value="dozen">dozen</option>
                    <option value="piece">piece</option>
                    <option value="bundle">bundle</option>
                </select>
            </div>
            <div class="form-group">
                <label>Product Image URL (optional)</label>
                <input type="text" name="image" placeholder="https://example.com/image.jpg">
            </div>
            <button type="submit" name="sell" class="btn btn-green btn-full">📦 List Product for Sale</button>
        </form>
    </div>

    <!-- My Listings -->
    <h2 class="section-title" style="margin-top:40px;">📋 My Listings</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Product</th><th>Price</th><th>Qty</th><th>Unit</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php
            $uid = $_SESSION['user_id'];
            $res = safeQuery($conn, "SELECT * FROM products WHERE user_id=$uid ORDER BY created_at DESC");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td>₹<?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['unit'] ?></td>
                    <td>
                        <span class="badge <?= $row['status']=='available' ? 'badge-green' : 'badge-red' ?>">
                            <?= ucfirst(str_replace('_',' ',$row['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <a href="delete_product.php?id=<?= $row['id'] ?>"
                           class="btn btn-red" style="padding:5px 12px;font-size:0.8rem;"
                           onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
