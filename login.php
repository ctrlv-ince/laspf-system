<?php
// Start the session
session_start();

// Include the database configuration file
require_once './database/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $conn->prepare("SELECT user_id, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $password_hash, $role);

    if ($stmt->fetch() && password_verify($password, $password_hash)) {
        // Login successful
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;

        // Fetch provider_id if the user is a provider
        if ($role === 'provider') {
            $stmt_provider = $conn->prepare("SELECT provider_id FROM providers WHERE user_id = ?");
            $stmt_provider->bind_param("i", $user_id);
            $stmt_provider->execute();
            $stmt_provider->bind_result($provider_id);
            $stmt_provider->fetch();
            $stmt_provider->close();

            // Store provider_id in the session
            $_SESSION['provider_id'] = $provider_id;
        }

        // Redirect based on role
        switch ($role) {
            case 'admin':
                header("Location: ./admin/admin_dashboard.php");
                break;
            case 'provider':
                header("Location: ./providers/provider_dashboard.php");
                break;
            case 'user':
                header("Location: ./users/user_index.php");
                break;
            default:
                echo "<script>alert('Invalid role');</script>";
                break;
        }
        exit();
    } else {
        echo "<script>alert('Invalid email or password');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Login</h2>
        <form action="" method="POST">
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>

        <!-- Register Links -->
        <div class="mt-4 text-center">
            <p>Don't have an account?</p>
            <div class="d-grid gap-2">
                <a href="users/user_registration.php" class="btn btn-success">Register as User</a>
                <a href="providers/provider_registration.php" class="btn btn-outline-primary">Register as Provider</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>