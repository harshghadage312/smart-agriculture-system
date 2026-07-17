<?php
include '../db.php';
requireAdmin();

$success = $error = '';

// Add
if (isset($_POST['add'])) {
    $soil   = clean($conn, $_POST['soil_type']);
    $season = clean($conn, $_POST['season']);
    $water  = clean($conn, $_POST['water_level']);
    $crop   = clean($conn, $_POST['crop_name']);
    $desc   = clean($conn, $_POST['description']);
    $img    = clean($conn, $_POST['image']);
    if (empty($img)) $img = "https://via.placeholder.com/200?text=" . urlencode($crop);

    mysqli_query($conn, "INSERT INTO crops(soil_type,season,water_level,crop_name,description,image)
                         VALUES('$soil','$season','$water','$crop','$desc','$img')");
    $success = "Crop added successfully!";
}

// Delete
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM crops WHERE id=" . (int)$_GET['delete']);
    $success = "Crop deleted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Crops - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_crops.php" class="active">🌱 Crops</a>
        <a href="manage_fertilizers.php">🧪 Fertilizers</a>
        <a href="manage_weather.php">🌦️ Weather</a>
        <a href="manage_pests.php">🐛 Pests</a>
        <a href="manage_products.php">📦 Products</a>
        <a href="view_orders.php">📋 Orders</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header"><h1>🌱 Manage Crops</h1></div>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <!-- Add Form -->
    <div style="background:#fff;padding:25px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:30px;">
        <h3 style="color:#2e7d32;margin-bottom:18px;">➕ Add New Crop</h3>
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;">
            <div class="form-group">
                <label>Crop Name</label>
                <input type="text" name="crop_name" placeholder="e.g. Cotton" required>
            </div>
            <div class="form-group">
                <label>Soil Type</label>
                <select name="soil_type" required>
                    <option value="Black Soil">Black Soil</option>
                    <option value="Red Soil">Red Soil</option>
                    <option value="Alluvial Soil">Alluvial Soil</option>
                    <option value="Sandy Soil">Sandy Soil</option>
                    <option value="Loamy Soil">Loamy Soil</option>
                    <option value="Clay Soil">Clay Soil</option>
                </select>
            </div>
            <div class="form-group">
                <label>Season</label>
                <select name="season" required>
                    <option value="Kharif">Kharif</option>
                    <option value="Rabi">Rabi</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class="form-group">
                <label>Water Level</label>
                <select name="water_level" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label>Description</label>
                <input type="text" name="description" placeholder="Brief description">
            </div>
            <div class="form-group" style="grid-column:span 3;">
                <label>Image URL (optional)</label>
                <input type="text" name="image" placeholder="https://...">
            </div>
            <div style="grid-column:span 3;">
                <button type="submit" name="add" class="btn btn-green">➕ Add Crop</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Crop</th><th>Soil</th><th>Season</th><th>Water</th><th>Description</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT * FROM crops ORDER BY crop_name");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><strong><?= htmlspecialchars($row['crop_name']) ?></strong></td>
                <td><?= $row['soil_type'] ?></td>
                <td><span class="badge badge-green"><?= $row['season'] ?></span></td>
                <td><span class="badge badge-blue"><?= $row['water_level'] ?></span></td>
                <td><?= htmlspecialchars(substr($row['description'], 0, 50)) ?>...</td>
                <td>
                    <a href="manage_crops.php?delete=<?= $row['id'] ?>" class="btn btn-red"
                       style="padding:4px 10px;font-size:0.8rem;" onclick="return confirm('Delete?')">Delete</a>
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
