<?php
include 'db.php';
$results  = [];
$searched = false;

if (isset($_POST['search'])) {
    $searched = true;
    $crop     = clean($conn, $_POST['crop_name']);
    $nutrient = clean($conn, $_POST['nutrient_status']);

    $sql = "SELECT * FROM fertilizers WHERE crop_name='$crop' AND nutrient_status='$nutrient'";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
    }

    // fallback: just by crop
    if (empty($results)) {
        $res2 = safeQuery($conn, "SELECT * FROM fertilizers WHERE crop_name='$crop'");
        while ($row = mysqli_fetch_assoc($res2)) {
            $results[] = $row;
        }
    }
}

$crops = ['Cotton','Wheat','Rice','Maize','Groundnut','Sugarcane','Potato'];
$nutrients = ['Low Nitrogen','Low Phosphorus','Low Potassium','Low Calcium','Balanced'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fertilizer Recommendation - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="crop_recommend.php">🌱 Crop</a>
        <a href="fertilizer_recommend.php" class="active">🧪 Fertilizer</a>
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
        <h1>🧪 Fertilizer Recommendation</h1>
        <p>Select your crop and soil nutrient deficiency to get fertilizer suggestions</p>
    </div>

    <div class="adv-form">
        <form method="post">
            <div class="form-group">
                <label>🌾 Crop Name</label>
                <select name="crop_name" required>
                    <option value="">-- Select Crop --</option>
                    <?php foreach ($crops as $c): ?>
                        <option value="<?= $c ?>" <?= (isset($_POST['crop_name']) && $_POST['crop_name'] == $c) ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>🌿 Soil Nutrient Status</label>
                <select name="nutrient_status" required>
                    <option value="">-- Select Deficiency --</option>
                    <?php foreach ($nutrients as $n): ?>
                        <option value="<?= $n ?>" <?= (isset($_POST['nutrient_status']) && $_POST['nutrient_status'] == $n) ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="search" class="btn btn-green btn-full">🔍 Get Fertilizer Recommendation</button>
        </form>
    </div>

    <?php if ($searched): ?>
    <div style="margin-top:30px;">
        <?php if (empty($results)): ?>
            <div class="alert alert-warning">No fertilizer data found. Try a different crop or nutrient status.</div>
        <?php else: ?>
            <div class="result-box">
                <h3>✅ Fertilizer Recommendations</h3>
                <?php foreach ($results as $r): ?>
                <div style="background:#fff;padding:18px;border-radius:10px;margin-bottom:14px;border-left:4px solid #2e7d32;">
                    <div class="result-item">
                        <span class="label">💊 Fertilizer</span>
                        <span class="value"><strong><?= htmlspecialchars($r['fertilizer_name']) ?></strong></span>
                    </div>
                    <div class="result-item">
                        <span class="label">⚖️ Dosage</span>
                        <span class="value"><?= htmlspecialchars($r['dosage']) ?></span>
                    </div>
                    <div class="result-item">
                        <span class="label">📋 Instructions</span>
                        <span class="value"><?= htmlspecialchars($r['instructions']) ?></span>
                    </div>
                    <div class="result-item">
                        <span class="label">🌾 Crop</span>
                        <span class="value"><?= htmlspecialchars($r['crop_name']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- All Fertilizer Table -->
    <h2 class="section-title" style="margin-top:45px;">📋 Fertilizer Database</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Crop</th>
                    <th>Nutrient Status</th>
                    <th>Fertilizer</th>
                    <th>Dosage</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $all = safeQuery($conn, "SELECT * FROM fertilizers ORDER BY crop_name");
            $i = 1;
            while ($row = mysqli_fetch_assoc($all)):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($row['crop_name']) ?></strong></td>
                    <td><span class="badge badge-orange"><?= htmlspecialchars($row['nutrient_status']) ?></span></td>
                    <td><?= htmlspecialchars($row['fertilizer_name']) ?></td>
                    <td><?= htmlspecialchars($row['dosage']) ?></td>
                    <td><?= htmlspecialchars($row['instructions']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
