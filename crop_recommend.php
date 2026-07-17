<?php
include 'db.php';
$results  = [];
$searched = false;

if (isset($_POST['search'])) {
    $searched = true;
    $soil     = clean($conn, $_POST['soil_type']);
    $season   = clean($conn, $_POST['season']);
    $water    = clean($conn, $_POST['water_level']);

    $sql = "SELECT * FROM crops WHERE soil_type='$soil' AND season='$season' AND water_level='$water'";
    $res = safeQuery($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
    }
}

$soils   = ['Black Soil', 'Red Soil', 'Alluvial Soil', 'Sandy Soil', 'Loamy Soil', 'Clay Soil'];
$seasons = ['Kharif', 'Rabi', 'Summer'];
$waters  = ['Low', 'Medium', 'High'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Recommendation - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="crop_recommend.php" class="active">🌱 Crop</a>
        <a href="fertilizer_recommend.php">🧪 Fertilizer</a>
        <a href="weather_advisory.php">🌦️ Weather</a>
        <a href="pest_disease.php">🐛 Pest</a>
        <a href="marketplace.php">🛒 Market</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="btn-nav">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>🌱 Crop Recommendation System</h1>
        <p>Enter your soil type, season and water availability to get the best crop suggestions</p>
    </div>

    <div class="adv-form">
        <form method="post">
            <div class="form-group">
                <label>🏔️ Soil Type</label>
                <select name="soil_type" required>
                    <option value="">-- Select Soil Type --</option>
                    <?php foreach ($soils as $s): ?>
                        <option value="<?= $s ?>" <?= (isset($_POST['soil_type']) && $_POST['soil_type'] == $s) ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>📅 Season</label>
                <select name="season" required>
                    <option value="">-- Select Season --</option>
                    <?php foreach ($seasons as $s): ?>
                        <option value="<?= $s ?>" <?= (isset($_POST['season']) && $_POST['season'] == $s) ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>💧 Water Availability</label>
                <select name="water_level" required>
                    <option value="">-- Select Water Level --</option>
                    <?php foreach ($waters as $w): ?>
                        <option value="<?= $w ?>" <?= (isset($_POST['water_level']) && $_POST['water_level'] == $w) ? 'selected' : '' ?>><?= $w ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="search" class="btn btn-green btn-full">🔍 Get Crop Recommendations</button>
        </form>
    </div>

    <!-- RESULTS -->
    <?php if ($searched): ?>
    <div style="margin-top:30px;">
        <?php if (empty($results)): ?>
            <div class="alert alert-warning">No crop recommendations found for the selected combination. Try different inputs.</div>
        <?php else: ?>
            <div class="result-box">
                <h3>✅ Recommended Crops for your conditions</h3>
                <p style="font-size:0.85rem;color:#555;margin-bottom:15px;">
                    Soil: <strong><?= htmlspecialchars($_POST['soil_type']) ?></strong> &nbsp;|&nbsp;
                    Season: <strong><?= htmlspecialchars($_POST['season']) ?></strong> &nbsp;|&nbsp;
                    Water: <strong><?= htmlspecialchars($_POST['water_level']) ?></strong>
                </p>
                <div class="cards-grid">
                <?php foreach ($results as $r): ?>
                    <div class="card">
                        <img src="<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['crop_name']) ?>">
                        <div class="card-body">
                            <h3>🌾 <?= htmlspecialchars($r['crop_name']) ?></h3>
                            <p><?= htmlspecialchars($r['description']) ?></p>
                            <div style="margin-top:8px;">
                                <span class="badge badge-green"><?= $r['season'] ?></span>
                                <span class="badge badge-blue"><?= $r['water_level'] ?> Water</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ALL CROPS TABLE -->
    <h2 class="section-title" style="margin-top:45px;">📋 All Crop Data</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Crop Name</th>
                    <th>Soil Type</th>
                    <th>Season</th>
                    <th>Water Level</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $all = safeQuery($conn, "SELECT * FROM crops ORDER BY season, soil_type");
            $i = 1;
            while ($row = mysqli_fetch_assoc($all)):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($row['crop_name']) ?></strong></td>
                    <td><?= $row['soil_type'] ?></td>
                    <td><span class="badge badge-green"><?= $row['season'] ?></span></td>
                    <td><span class="badge badge-blue"><?= $row['water_level'] ?></span></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
