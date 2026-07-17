<?php
include '../db.php';
requireAdmin();
$success = '';

if (isset($_POST['add'])) {
    $season   = clean($conn, $_POST['season']);
    $rainfall = clean($conn, $_POST['rainfall']);
    $advisory = clean($conn, $_POST['advisory']);
    $crops    = clean($conn, $_POST['suitable_crops']);
    mysqli_query($conn, "INSERT INTO weather_advisory(season,rainfall,advisory,suitable_crops) VALUES('$season','$rainfall','$advisory','$crops')");
    $success = "Weather advisory added!";
}
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM weather_advisory WHERE id=" . (int)$_GET['delete']);
    $success = "Deleted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Weather - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_crops.php">🌱 Crops</a>
        <a href="manage_fertilizers.php">🧪 Fertilizers</a>
        <a href="manage_weather.php" class="active">🌦️ Weather</a>
        <a href="manage_pests.php">🐛 Pests</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="page-header"><h1>🌦️ Manage Weather Advisory</h1></div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <div style="background:#fff;padding:25px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:25px;">
        <h3 style="color:#2e7d32;margin-bottom:15px;">➕ Add Advisory</h3>
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
            <div class="form-group"><label>Season</label>
                <select name="season" required>
                    <option value="Kharif">Kharif</option>
                    <option value="Rabi">Rabi</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class="form-group"><label>Rainfall</label>
                <select name="rainfall" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group" style="grid-column:span 2;"><label>Advisory Text</label><textarea name="advisory" required placeholder="Farming advisory for this condition"></textarea></div>
            <div class="form-group" style="grid-column:span 2;"><label>Suitable Crops (comma separated)</label><input type="text" name="suitable_crops" placeholder="e.g. Rice, Cotton, Sugarcane"></div>
            <div><button type="submit" name="add" class="btn btn-green">➕ Add Advisory</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Season</th><th>Rainfall</th><th>Advisory</th><th>Suitable Crops</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT * FROM weather_advisory ORDER BY season");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><span class="badge badge-blue"><?= $row['season'] ?></span></td>
                <td><span class="badge badge-orange"><?= $row['rainfall'] ?></span></td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars(substr($row['advisory'],0,70)) ?>...</td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars($row['suitable_crops']) ?></td>
                <td><a href="manage_weather.php?delete=<?= $row['id'] ?>" class="btn btn-red" style="padding:4px 10px;font-size:0.8rem;" onclick="return confirm('Delete?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<footer><p>&copy; 2024 AgriSmart Admin</p></footer>
</body>
</html>
