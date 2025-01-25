<?php
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] || !$_SESSION['isAdmin']) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKarRental - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
<div class="container mt-5">
    <h1 class="mb-4 text-light">Manage Cars</h1>

    <div class="mb-4">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="logout.php" class="btn btn-danger">Log Out</a>
        <?php else: ?>
            <a href="authorisation.php" class="btn btn-primary">Authorization</a>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary ">Back to Homepage</a>
    </div>

    <?php
    $errorMessages = [];
    $successMessage = '';

    $jsonData = file_get_contents('cars.json');
    $cars = json_decode($jsonData, true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $carId = $_POST['car_id'];
            $cars = array_filter($cars, function ($car) use ($carId) {
                return $car['id'] != $carId;
            });
            file_put_contents('cars.json', json_encode($cars, JSON_PRETTY_PRINT));
            $successMessage = 'Car deleted successfully!';
        } else {

            $brand = $_POST['brand'] ?? '';
            $model = $_POST['model'] ?? '';
            $year = $_POST['year'] ?? '';
            $transmission = $_POST['transmission'] ?? '';
            $dailyPriceHuf = $_POST['daily_price_huf'] ?? '';
            $image = $_POST['image'] ?? '';

            if (empty($brand)) {
                $errorMessages[] = 'Brand is required.';
            }
            if (empty($model)) {
                $errorMessages[] = 'Model is required.';
            }
            if (empty($year) || !filter_var($year, FILTER_VALIDATE_INT) || $year <= 0) {
                $errorMessages[] = 'Please enter a valid year.';
            }
            if (empty($transmission)) {
                $errorMessages[] = 'Transmission is required.';
            }
            if (empty($dailyPriceHuf) || !filter_var($dailyPriceHuf, FILTER_VALIDATE_INT) || $dailyPriceHuf <= 0) {
                $errorMessages[] = 'Please enter a valid daily price.';
            }
            if (empty($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
                $errorMessages[] = 'Please enter a valid image URL.';
            }

            if (empty($_POST['fuel_type'])) {
                $errorMessages[] = 'Fuel type is required.';
            }
            if (empty($_POST['number_of_passengers']) || !filter_var($_POST['number_of_passengers'], FILTER_VALIDATE_INT) || $_POST['number_of_passengers'] <= 0) {
                $errorMessages[] = 'Please enter a valid number of passengers.';
            }

            if (empty($errorMessages)) {
                $newCarId = end($cars)['id'] + 1;
                $newCar = [
                    'id' => $newCarId,
                    'brand' => $brand,
                    'model' => $model,
                    'year' => (int)$year,
                    'transmission' => $transmission,
                    'fuel_type' => $_POST['fuel_type'],
                    'passengers' => (int)$_POST['number_of_passengers'],
                    'daily_price_huf' => (int)$dailyPriceHuf,
                    'image' => $image,
                ];

                $cars[] = $newCar;

                file_put_contents('cars.json', json_encode($cars, JSON_PRETTY_PRINT));

                $successMessage = 'Car added successfully!';
            }
        }
    }
    ?>

    <?php if (!empty($errorMessages)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errorMessages as $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <h2 class="mb-4 text-light">Add a New Car</h2>
    <form method="POST">
        
        <div class="mb-3">
            <label for="brand" class="form-label text-light">Brand</label>
            <input type="text" name="brand" id="brand" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="model" class="form-label text-light">Model</label>
            <input type="text" name="model" id="model" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label text-light">Year</label>
            <input type="number" name="year" id="year" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="transmission" class="form-label text-light">Transmission</label>
            <select name="transmission" id="transmission" class="form-control" required>
                <option value="">Select Transmission</option>
                <option value="Manual">Manual</option>
                <option value="Automatic">Automatic</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="fuel_type" class="form-label text-light">Fuel Type</label>
            <select name="fuel_type" id="fuel_type" class="form-control" required>
                <option value="">Select Fuel Type</option>
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="number_of_passengers" class="form-label text-light">Number of Passengers</label>
            <input type="number" name="number_of_passengers" id="number_of_passengers" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="daily_price_huf" class="form-label text-light">Daily Price (HUF)</label>
            <input type="number" name="daily_price_huf" id="daily_price_huf" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label text-light">Image URL</label>
            <input type="text" name="image" id="image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Car</button>

    </form>

    <h2 class="mt-5 mb-4 text-light">Existing Cars</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-light">ID</th>
                <th class="text-light">Brand</th>
                <th class="text-light">Model</th>
                <th class="text-light">Year</th>
                <th class="text-light">Transmission</th>
                <th class="text-light">Daily Price (HUF)</th>
                <th class="text-light">Image</th>
                <th class="text-light">Actions</th>
            </tr>
        </thead> class="text-light"
        <tbody>
            <?php foreach ($cars as $car): ?>
                <tr>
                    <td class="text-light"><?= htmlspecialchars($car['id']) ?></td>
                    <td class="text-light"><?= htmlspecialchars($car['brand']) ?></td>
                    <td class="text-light"><?= htmlspecialchars($car['model']) ?></td>
                    <td class="text-light"><?= htmlspecialchars($car['year']) ?></td>
                    <td class="text-light"><?= htmlspecialchars($car['transmission']) ?></td>
                    <td class="text-light"><?= htmlspecialchars($car['daily_price_huf']) ?></td>
                    <td class="text-light"><img src="<?= htmlspecialchars($car['image']) ?>" alt="Car Image" style="width: 100px;"></td>
                    <td class="text-light">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="car_id" value="<?= htmlspecialchars($car['id']) ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>