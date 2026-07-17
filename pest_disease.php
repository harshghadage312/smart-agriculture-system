<?php
include 'db.php';
$results  = [];
$searched = false;

if (isset($_POST['search'])) {
    $searched = true;
    $crop     = clean($conn, $_POST['crop_name']);
    $symptom  = clean($conn, $_POST['symptom']);

    // search by crop + partial symptom match
    $sql = "SELECT * FROM pests WHERE crop_name='$crop' AND symptoms LIKE '%$symptom%'";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
    }

    // fallback: just crop
    if (empty($results)) {
        $res2 = safeQuery($conn, "SELECT * FROM pests WHERE crop_name='$crop'");
        while ($row = mysqli_fetch_assoc($res2)) {
            $results[] = $row;
        }
    }
}

$crops = ['Cotton','Rice','Wheat','Maize','Tomato','Potato','Groundnut'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pest & Disease - AgriSmart</title>
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
        <a href="pest_disease.php" class="active">🐛 Pest</a>
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
        <h1>🐛 Pest & Disease Management</h1>
        <p>Select your crop and describe symptoms to identify pests/diseases and get treatments</p>
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
                <label>🔍 Symptoms (keywords)</label>
                <input type="text" name="symptom" placeholder="e.g. yellow leaves, holes, white spots"
                    value="<?= isset($_POST['symptom']) ? htmlspecialchars($_POST['symptom']) : '' ?>">
                <small style="color:#888;font-size:0.8rem;">Leave blank to see all pests for selected crop</small>
            </div>
            <button type="submit" name="search" class="btn btn-green btn-full">🔍 Search Pest & Disease</button>
        </form>
    </div>

    <?php if ($searched): ?>
    <div style="margin-top:30px;">
        <?php if (empty($results)): ?>
            <div class="alert alert-warning">No pest/disease records found. Try a different crop or symptom.</div>
        <?php else: ?>
            <div class="result-box">
                <h3>🦟 Identified Pest/Disease for: <?= htmlspecialchars($_POST['crop_name']) ?></h3>
                <?php foreach ($results as $r): ?>
                <div style="background:#fff;padding:18px;border-radius:10px;margin-bottom:14px;border-left:4px solid #c62828;">
                    <div class="result-item">
                        <span class="label">🦟 Pest / Disease</span>
                        <span class="value"><strong><?= htmlspecialchars($r['pest_name']) ?></strong></span>
                    </div>
                    <div class="result-item">
                        <span class="label">🔬 Symptoms</span>
                        <span class="value"><?= htmlspecialchars($r['symptoms']) ?></span>
                    </div>
                    <div class="result-item">
                        <span class="label">💉 Treatment</span>
                        <span class="value" style="color:#c62828;"><?= htmlspecialchars($r['treatment']) ?></span>
                    </div>
                    <?php if (!empty($r['prevention'])): ?>
                    <div class="result-item">
                        <span class="label">🛡️ Prevention</span>
                        <span class="value"><?= htmlspecialchars($r['prevention']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- All Pest Table -->
    <h2 class="section-title" style="margin-top:45px;">📋 Pest & Disease Database</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Crop</th>
                    <th>Pest / Disease</th>
                    <th>Symptoms</th>
                    <th>Treatment</th>
                    <th>Prevention</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $all = safeQuery($conn, "SELECT * FROM pests ORDER BY crop_name");
            $i = 1;
            while ($row = mysqli_fetch_assoc($all)):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($row['crop_name']) ?></strong></td>
                    <td><span class="badge badge-red"><?= htmlspecialchars($row['pest_name']) ?></span></td>
                    <td><?= htmlspecialchars($row['symptoms']) ?></td>
                    <td><?= htmlspecialchars($row['treatment']) ?></td>
                    <td><?= htmlspecialchars($row['prevention']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
