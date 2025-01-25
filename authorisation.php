<?php
session_start();

// Helper functions
function loadUsers() {
    if (!file_exists('users.json')) {
        file_put_contents('users.json', json_encode([], JSON_PRETTY_PRINT));
    }
    $data = file_get_contents('users.json');
    return json_decode($data, true);
}

function saveUsers($users) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}

$errorMessages = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = loadUsers();

    if (isset($_POST['register'])) {
        $fullName = trim($_POST['fullName']);
        $email = trim($_POST['registerEmail']);
        $password = trim($_POST['registerPassword']);
        if (empty($fullName)) {
            $errorMessages[] = 'Full name is required.';
        }
        if (empty($email)) {
            $errorMessages[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessages[] = 'Invalid email address.';
        }
        if (empty($password)) {
            $errorMessages[] = 'Password is required.';
        } elseif (strlen($password) < 5) {
            $errorMessages[] = 'Password must be at least 5 characters long.';
        }
        if (array_filter($users, fn($u) => $u['email'] === $email)) {
            $errorMessages[] = 'Email is already registered.';
        }

        if (empty($errorMessages)) {
            $users[] = [
                'fullName' => $fullName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'isAdmin' => false 
            ];
            saveUsers($users);
            $successMessage = 'Registration successful!';
        }
    }

    if (isset($_POST['login'])) {
        $email = trim($_POST['loginEmail']);
        $password = trim($_POST['loginPassword']);

        if (empty($email)) {
            $errorMessages[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessages[] = 'Invalid email address.';
        }
        if (empty($password)) {
            $errorMessages[] = 'Password is required.';
        }

        if (empty($errorMessages)) {
            $user = array_filter($users, fn($u) => $u['email'] === $email);
            $user = $user ? array_shift($user) : null;

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $user['email'];
                $_SESSION['isAdmin'] = $user['isAdmin'];
                header("Location: index.php?login=success");
                exit;
            } else {
                $errorMessages[] = 'Invalid credentials.';
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
    <title>Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .feedback { font-size: 0.9em; color: red; display: none; }
    </style>
</head>
<body class="bg-dark">
<div class="container mt-5 ">
    <h1 class="text-center mb-4 text-light">Authentication</h1>

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

    <ul class="nav nav-tabs mb-4" id="authTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">Login</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">Register</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="login">
            <form method="POST">
                <div class="mb-3">
                    <label for="loginEmail" class="form-label text-light">Email Address</label>
                    <input type="email" name="loginEmail" id="loginEmail" class="form-control" required>
                    <div class="feedback">Invalid email address.</div>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label text-light">Password</label>
                    <input type="password" name="loginPassword" id="loginPassword" class="form-control" required>
                    <div class="feedback">Password cannot be empty.</div>
                </div>
                <button type="submit" name="login" class="btn btn-primary">Login</button>
                <a href="index.php" class="btn btn-secondary">Go to Homepage</a>
            </form>
        </div>

        <div class="tab-pane fade" id="register">
            <form method="POST">
                <div class="mb-3">
                    <label for="fullName" class="form-label text-light">Full Name</label>
                    <input type="text" name="fullName" id="fullName" class="form-control" required>
                    <div class="feedback">Full name is required.</div>
                </div>
                <div class="mb-3">
                    <label for="registerEmail" class="form-label text-light">Email Address</label>
                    <input type="email" name="registerEmail" id="registerEmail" class="form-control" required>
                    <div class="feedback">Invalid email address.</div>
                </div>
                <div class="mb-3">
                    <label for="registerPassword" class="form-label text-light">Password</label>
                    <input type="password" name="registerPassword" id="registerPassword" class="form-control" required>
                    <div class="feedback">Password must be at least 8 characters long.</div>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
                <a href="index.php" class="btn btn-secondary">Go to Homepage</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>