<?php
include 'db.php';
$result   = null;
$searched = false;

if (isset($_POST['search'])) {
    $searched = true;
    $season   = clean($conn, $_POST['season']);
    $rainfall = clean($conn, $_POST['rainfall']);

    $res = safeQuery($conn, "SELECT * FROM weather_advisory WHERE season='$season' AND rainfall='$rainfall'");
    $result = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather Advisory - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="crop_recommend.php">🌱 Crop</a>
        <a href="fertilizer_recommend.php">🧪 Fertilizer</a>
        <a href="weather_advisory.php" class="active">🌦️ Weather</a>
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
        <h1>🌦️ Weather-Based Crop Advisory</h1>
        <p>Get farming tips and suitable crop suggestions based on season and rainfall</p>
    </div>

    <div class="adv-form">
        <form method="post">
            <div class="form-group">
                <label>📅 Season</label>
                <select name="season" required>
                    <option value="">-- Select Season --</option>
                    <option value="Kharif"  <?= (isset($_POST['season']) && $_POST['season']=='Kharif')  ? 'selected' : '' ?>>Kharif (June–Oct)</option>
                    <option value="Rabi"    <?= (isset($_POST['season']) && $_POST['season']=='Rabi')    ? 'selected' : '' ?>>Rabi (Nov–Mar)</option>
                    <option value="Summer"  <?= (isset($_POST['season']) && $_POST['season']=='Summer')  ? 'selected' : '' ?>>Summer (Apr–Jun)</option>
                </select>
            </div>
            <div class="form-group">
                <label>🌧️ Expected Rainfall</label>
                <select name="rainfall" required>
                    <option value="">-- Select Rainfall Level --</option>
                    <option value="Low"    <?= (isset($_POST['rainfall']) && $_POST['rainfall']=='Low')    ? 'selected' : '' ?>>Low (&lt; 500mm)</option>
                    <option value="Medium" <?= (isset($_POST['rainfall']) && $_POST['rainfall']=='Medium') ? 'selected' : '' ?>>Medium (500–1000mm)</option>
                    <option value="High"   <?= (isset($_POST['rainfall']) && $_POST['rainfall']=='High')   ? 'selected' : '' ?>>High (&gt; 1000mm)</option>
                </select>
            </div>
            <button type="submit" name="search" class="btn btn-green btn-full">🌦️ Get Weather Advisory</button>
        </form>
    </div>

    <?php if ($searched): ?>
    <div style="margin-top:30px;">
        <?php if (!$result): ?>
            <div class="alert alert-warning">No advisory found for this combination. Please try different inputs.</div>
        <?php else: ?>
            <div class="result-box">
                <h3>🌤️ Weather Advisory for <?= htmlspecialchars($_POST['season']) ?> — <?= htmlspecialchars($_POST['rainfall']) ?> Rainfall</h3>

                <div class="result-item">
                    <span class="label">📋 Advisory</span>
                    <span class="value"><?= htmlspecialchars($result['advisory']) ?></span>
                </div>
                <div class="result-item">
                    <span class="label">🌾 Suitable Crops</span>
                    <span class="value">
                        <?php foreach (explode(',', $result['suitable_crops']) as $crop): ?>
                            <span class="badge badge-green"><?= trim($crop) ?></span>&nbsp;
                        <?php endforeach; ?>
                    </span>
                </div>
            </div>

            <div class="alert alert-info" style="margin-top:20px;">
                💡 <strong>Tip:</strong> Visit the <a href="crop_recommend.php" style="color:#1565c0;">Crop Recommendation</a> page for detailed crop info.
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- All Advisory Table -->
    <h2 class="section-title" style="margin-top:45px;">📋 All Weather Advisories</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Season</th>
                    <th>Rainfall</th>
                    <th>Advisory</th>
                    <th>Suitable Crops</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $all = safeQuery($conn, "SELECT * FROM weather_advisory ORDER BY season");
            $i = 1;
            while ($row = mysqli_fetch_assoc($all)):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><span class="badge badge-blue"><?= $row['season'] ?></span></td>
                    <td><span class="badge badge-orange"><?= $row['rainfall'] ?></span></td>
                    <td><?= htmlspecialchars($row['advisory']) ?></td>
                    <td><?= htmlspecialchars($row['suitable_crops']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2024 AgriSmart</p></footer>
</body>
</html>
