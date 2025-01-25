<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title >iKarRental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
<div class="container mt-5 bg-dark">
    <h1 class="mb-4 text-light">IKarRental</h1>

    <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
        <?php $_SESSION['loggedin'] = true; ?>
        <?php $_SESSION['login_message'] = 'Login successful! Welcome back!'; ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['login_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['login_message'] ?></div>
        <?php unset($_SESSION['login_message']); ?>
    <?php endif; ?>

    <div class="mb-4">
        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
            <a href="admin.php?status=logedin" class="btn btn-primary">Edit Car Databases</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="logout.php" class="btn btn-danger">Log Out</a>
        <?php else: ?>
            <a href="authorisation.php" class="btn btn-success">Authorization</a>
        <?php endif; ?>
    </div>

    <?php
    $jsonData = file_get_contents('cars.json');
    $cars = json_decode($jsonData, true);

    $brandFilter = $_GET['brand'] ?? '';
    $transmissionFilter = $_GET['transmission'] ?? '';
    $minPrice = $_GET['min_price'] ?? '';
    $maxPrice = $_GET['max_price'] ?? '';
    $passengerFilter = $_GET['passengers'] ?? '';

    $filteredCars = array_filter($cars, function ($car) use ($brandFilter, $transmissionFilter, $minPrice, $maxPrice, $passengerFilter) {
        if ($brandFilter && stripos($car['brand'], $brandFilter) === false) {
            return false;
        }
        if ($transmissionFilter && $car['transmission'] !== $transmissionFilter) {
            return false;
        }
        if ($minPrice && $car['daily_price_huf'] < (int)$minPrice) {
            return false;
        }
        if ($maxPrice && $car['daily_price_huf'] > (int)$maxPrice) {
            return false;
        }
        if ($passengerFilter && $car['passengers'] < (int)$passengerFilter) {
            return false;
        }
        return true;
    });
    ?>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="brand" class="form-control" placeholder="Brand" value="<?= htmlspecialchars($brandFilter) ?>">
            </div>
            <div class="col-md-2">
                <select name="transmission" class="form-control">
                    <option value="">Select Transmission</option>
                    <option value="Manual" <?= $transmissionFilter === 'Manual' ? 'selected' : '' ?>>Manual</option>
                    <option value="Automatic" <?= $transmissionFilter === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="<?= htmlspecialchars($minPrice) ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="<?= htmlspecialchars($maxPrice) ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="passengers" class="form-control" placeholder="Passengers" value="<?= htmlspecialchars($passengerFilter) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php if (count($filteredCars) > 0): ?>
            <?php foreach ($filteredCars as $car): ?>
                <div class="col-md-4">
                    <div class="card mb-4 bg-secondary">
                        <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="Car Image">
                        <div class="card-body ">
                            <h5 class="card-title text-light">Brand: <?= htmlspecialchars($car['brand']) ?></h5>
                            <p class="card-text text-light">Model: <?= htmlspecialchars($car['model']) ?></p>
                            <p class="card-text text-light">Year: <?= htmlspecialchars($car['year']) ?></p>
                            <p class="card-text text-light">Passengers: <?= htmlspecialchars($car['passengers']) ?></p>
                            <p class="card-text text-light">Fuel Type: <?= htmlspecialchars($car['fuel_type']) ?></p>
                            <p class="card-text text-light">Transmission: <?= htmlspecialchars($car['transmission']) ?></p>
                            <p class="card-text text-light">Daily Price: <?= htmlspecialchars($car['daily_price_huf']) ?> HUF</p>
                            <a href="details.php?id=<?= htmlspecialchars($car['id']) ?>" class="btn btn-dark">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No cars match the selected filters.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>