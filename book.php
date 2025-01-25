<?php
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: authorisation.php");
    exit;
}

$carId = $_POST['car_id'] ?? null;
$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;

$currentDate = new DateTime();

if ($startDate === null || $endDate === null) {
    $errorMessage = 'Start date and end date are required.';
} else {
    $startDateTime = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);

    if ($endDateTime < $startDateTime) {
        $errorMessage = 'End date cannot be before start date.';
    } else {
        $bookingsData = file_get_contents('bookings.json');
        $bookings = json_decode($bookingsData, true);

        foreach ($bookings as $booking) {
            if ($booking['car_id'] == $carId && (
                ($startDate >= $booking['start_date'] && $startDate <= $booking['end_date']) ||
                ($endDate >= $booking['start_date'] && $endDate <= $booking['end_date']) ||
                ($startDate <= $booking['start_date'] && $endDate >= $booking['end_date'])
            )) {
                $errorMessage = 'The car is already booked for the selected period.';
                break;
            }
        }

        if (!isset($errorMessage)) {
            $jsonData = file_get_contents('cars.json');
            $cars = json_decode($jsonData, true);
            $carDetails = null;

            foreach ($cars as $car) {
                if ($car['id'] == $carId) {
                    $carDetails = $car;
                    break;
                }
            }

            if (!$carDetails) {
                $errorMessage = 'The car you are trying to book does not exist.';
            } else {
                $interval = $startDateTime->diff($endDateTime);
                $days = $interval->days + 1; 
                $totalPrice = $days * $carDetails['daily_price_huf'];

                $bookings[] = [
                    'car_id' => $carId,
                    'user_id' => $_SESSION['email'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_price' => $totalPrice
                ];
                file_put_contents('bookings.json', json_encode($bookings, JSON_PRETTY_PRINT));

                $successMessage = 'Booking successful!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
<div class="container mt-5 mb-5 ">
    <?php if (isset($errorMessage)): ?>
        <div class="card text-center bg-secondary">
            <div class="card-body bg-secondary">
                <h5 class="card-title text-light">Booking Error</h5>
                <p class="card-text text-light"><?= htmlspecialchars($errorMessage) ?></p>
                <a href="index.php" class="btn btn-dark mt-3">Back to Homepage</a>
                <?php if ($carId): ?>
                    <a href="details.php?id=<?= htmlspecialchars($carId) ?>" class="btn btn-primary mt-3">Back to Car Page</a>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif (isset($successMessage)): ?>
        <h1 class="mb-4 text-light">Booking Confirmation: Success</h1>
        <div class="card">
            <img src="<?= htmlspecialchars($carDetails['image']) ?>" class="card-img-top" alt="Car Image">
            <div class="card-body bg-secondary">
                <h5 class="card-title text-light">Brand: <?= htmlspecialchars($carDetails['brand']) ?></h5>
                <p class="card-text text-light">Model: <?= htmlspecialchars($carDetails['model']) ?></p>
                <p class="card-text text-light">Year: <?= htmlspecialchars($carDetails['year']) ?></p>
                <p class="card-text text-light">Transmission: <?= htmlspecialchars($carDetails['transmission']) ?></p>
                <p class="card-text text-light">Fuel Type: <?= htmlspecialchars($carDetails['fuel_type']) ?></p>
                <p class="card-text text-light">Passengers: <?= htmlspecialchars($carDetails['passengers']) ?></p>
                <p class="card-text text-light">Daily Price: <?= htmlspecialchars($carDetails['daily_price_huf']) ?> HUF</p>
                <p class="card-text text-light">Booking Period: <?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?></p>
                <p class="card-text text-light">Total Price: <?= htmlspecialchars($totalPrice) ?> HUF</p>
            </div>
        </div>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Homepage</a>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>