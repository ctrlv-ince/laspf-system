<?php
session_start();

// Database connection
// $host = 'localhost';
// $db = 'your_database_name';
// $user = 'your_database_user';
// $pass = 'your_database_password';

// $conn = new mysqli($host, $user, $pass, $db);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// // Handle login form submission
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $email = $_POST['email'];
//     $password = $_POST['password'];

//     // Fetch user from the database
//     $stmt = $conn->prepare("SELECT user_id, password_hash, role FROM users WHERE email = ?");
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $stmt->store_result();
//     $stmt->bind_result($user_id, $password_hash, $role);

//     if ($stmt->fetch() && password_verify($password, $password_hash)) {
//         // Login successful
//         $_SESSION['user_id'] = $user_id;
//         $_SESSION['role'] = $role;

//         // Redirect based on role
//         switch ($role) {
//             case 'admin':
//                 header("Location: ./admin/admin_dashboard.php");
//                 break;
//             case 'provider':
//                 header("Location: ./providers/provider_dashboard.php");
//                 break;
//             case 'user':
//                 header("Location: ./users/user_dashboard.php");
//                 break;
//             default:
//                 echo "<script>alert('Invalid role');</script>";
//                 break;
//         }
//         exit();
//     } else {
//         echo "<script>alert('Invalid email or password');</script>";
//     }

//     $stmt->close();
//     $conn->close();
// }
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
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
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

            <!-- Register Link -->
            <div class="mt-3 text-center">
                <p>Don't have an account? <a href="./userregistration.php">Register as User</a></p>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>