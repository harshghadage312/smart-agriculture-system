<?php
include '../db.php';
requireAdmin();
$success = '';

if (isset($_POST['add'])) {
    $crop      = clean($conn, $_POST['crop_name']);
    $nutrient  = clean($conn, $_POST['nutrient_status']);
    $fert      = clean($conn, $_POST['fertilizer_name']);
    $dosage    = clean($conn, $_POST['dosage']);
    $instr     = clean($conn, $_POST['instructions']);
    mysqli_query($conn, "INSERT INTO fertilizers(crop_name,nutrient_status,fertilizer_name,dosage,instructions) VALUES('$crop','$nutrient','$fert','$dosage','$instr')");
    $success = "Fertilizer record added!";
}
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM fertilizers WHERE id=" . (int)$_GET['delete']);
    $success = "Deleted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Fertilizers - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_crops.php">🌱 Crops</a>
        <a href="manage_fertilizers.php" class="active">🧪 Fertilizers</a>
        <a href="manage_weather.php">🌦️ Weather</a>
        <a href="manage_pests.php">🐛 Pests</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="page-header"><h1>🧪 Manage Fertilizers</h1></div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <div style="background:#fff;padding:25px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:25px;">
        <h3 style="color:#2e7d32;margin-bottom:15px;">➕ Add Fertilizer Record</h3>
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
            <div class="form-group"><label>Crop Name</label><input type="text" name="crop_name" required placeholder="e.g. Rice"></div>
            <div class="form-group"><label>Nutrient Status</label>
                <select name="nutrient_status" required>
                    <option value="Low Nitrogen">Low Nitrogen</option>
                    <option value="Low Phosphorus">Low Phosphorus</option>
                    <option value="Low Potassium">Low Potassium</option>
                    <option value="Low Calcium">Low Calcium</option>
                    <option value="Balanced">Balanced</option>
                </select>
            </div>
            <div class="form-group"><label>Fertilizer Name</label><input type="text" name="fertilizer_name" required placeholder="e.g. Urea 46% N"></div>
            <div class="form-group"><label>Dosage</label><input type="text" name="dosage" placeholder="e.g. 25 kg/acre"></div>
            <div class="form-group" style="grid-column:span 2;"><label>Instructions</label><textarea name="instructions" placeholder="Application instructions"></textarea></div>
            <div><button type="submit" name="add" class="btn btn-green">➕ Add Fertilizer</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Crop</th><th>Nutrient</th><th>Fertilizer</th><th>Dosage</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT * FROM fertilizers ORDER BY crop_name");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><strong><?= htmlspecialchars($row['crop_name']) ?></strong></td>
                <td><span class="badge badge-orange"><?= htmlspecialchars($row['nutrient_status']) ?></span></td>
                <td><?= htmlspecialchars($row['fertilizer_name']) ?></td>
                <td><?= htmlspecialchars($row['dosage']) ?></td>
                <td><a href="manage_fertilizers.php?delete=<?= $row['id'] ?>" class="btn btn-red" style="padding:4px 10px;font-size:0.8rem;" onclick="return confirm('Delete?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<footer><p>&copy; 2024 AgriSmart Admin</p></footer>
</body>
</html>
