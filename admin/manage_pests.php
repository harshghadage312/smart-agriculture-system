<?php
include '../db.php';
requireAdmin();
$success = '';

if (isset($_POST['add'])) {
    $crop     = clean($conn, $_POST['crop_name']);
    $symptoms = clean($conn, $_POST['symptoms']);
    $pest     = clean($conn, $_POST['pest_name']);
    $treatment = clean($conn, $_POST['treatment']);
    $prevention = clean($conn, $_POST['prevention']);
    mysqli_query($conn, "INSERT INTO pests(crop_name,symptoms,pest_name,treatment,prevention) VALUES('$crop','$symptoms','$pest','$treatment','$prevention')");
    $success = "Pest record added!";
}
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM pests WHERE id=" . (int)$_GET['delete']);
    $success = "Deleted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pests - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<nav>
    <a href="../index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_crops.php">🌱 Crops</a>
        <a href="manage_fertilizers.php">🧪 Fertilizers</a>
        <a href="manage_weather.php">🌦️ Weather</a>
        <a href="manage_pests.php" class="active">🐛 Pests</a>
        <a href="../logout.php" class="btn-nav">Logout</a>
    </div>
</nav>
<div class="container">
    <div class="page-header"><h1>🐛 Manage Pests & Diseases</h1></div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <div style="background:#fff;padding:25px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);margin-bottom:25px;">
        <h3 style="color:#2e7d32;margin-bottom:15px;">➕ Add Pest/Disease Record</h3>
        <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
            <div class="form-group"><label>Crop Name</label><input type="text" name="crop_name" required placeholder="e.g. Cotton"></div>
            <div class="form-group"><label>Pest/Disease Name</label><input type="text" name="pest_name" required placeholder="e.g. Bollworm"></div>
            <div class="form-group"><label>Symptoms</label><textarea name="symptoms" required placeholder="Describe visible symptoms"></textarea></div>
            <div class="form-group"><label>Treatment</label><textarea name="treatment" required placeholder="Chemical/biological treatment"></textarea></div>
            <div class="form-group" style="grid-column:span 2;"><label>Prevention</label><input type="text" name="prevention" placeholder="Preventive measures"></div>
            <div><button type="submit" name="add" class="btn btn-green">➕ Add Record</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Crop</th><th>Pest/Disease</th><th>Symptoms</th><th>Treatment</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = safeQuery($conn, "SELECT * FROM pests ORDER BY crop_name");
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><strong><?= htmlspecialchars($row['crop_name']) ?></strong></td>
                <td><span class="badge badge-red"><?= htmlspecialchars($row['pest_name']) ?></span></td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars(substr($row['symptoms'],0,60)) ?>...</td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars(substr($row['treatment'],0,60)) ?>...</td>
                <td><a href="manage_pests.php?delete=<?= $row['id'] ?>" class="btn btn-red" style="padding:4px 10px;font-size:0.8rem;" onclick="return confirm('Delete?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<footer><p>&copy; 2024 AgriSmart Admin</p></footer>
</body>
</html>
