<?php
session_start();

$jsonData = file_get_contents('cars.json');
$cars = json_decode($jsonData, true);

$carId = $_GET['id'] ?? null;
$carDetails = null;

if ($carId) {
    foreach ($cars as $car) {
        if ($car['id'] == $carId) {
            $carDetails = $car;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental - <?= htmlspecialchars($carDetails['brand'] . ' ' . $carDetails['model']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
<div class="container mt-5 mb-5 bg-dark">
    <h1 class="mb-4 text-light">Car Details</h1>

    <div class="mb-4">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="logout.php" class="btn btn-danger">Log Out</a>
        <?php else: ?>
            <a href="authorisation.php" class="btn btn-success">Authorization</a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary ">Back to Homepage</a>
    </div>

    <div class="card bg-secondary">
        <img src="<?= htmlspecialchars($carDetails['image']) ?>" class="card-img-top" alt="Car Image">
        <div class="card-body">
            <h5 class="card-title text-light">Brand: <?= htmlspecialchars($carDetails['brand']) ?></h5>
            <?php foreach ($carDetails as $key => $value): ?>
                <?php if ($key != 'image' && $key != 'brand'): ?>
                    <p class="card-text text-light"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?>: <?= htmlspecialchars($value) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
        <form method="POST" action="book.php" class="mt-4">
            <input type="hidden" name="car_id" value="<?= htmlspecialchars($carDetails['id']) ?>">
            <div class="mb-3">
                <label for="start_date" class="form-label text-light">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label text-light">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Book</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning mt-4">Please <a href="authorisation.php">log in</a> to book this car.</div>
    <?php endif; ?>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>