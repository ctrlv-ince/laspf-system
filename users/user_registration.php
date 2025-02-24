<?php
// Database connection
// $host = 'localhost';
// $db = 'your_database_name';
// $user = 'your_database_user';
// $pass = 'your_database_password';

// $conn = new mysqli($host, $user, $pass, $db);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// // Handle form submission
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get form data
//     $full_name = $_POST['full_name'];
//     $email = $_POST['email'];
//     $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
//     $phone_number = $_POST['phone_number'];
//     $address = $_POST['address'];

//     // Handle file upload
//     $id_file = $_FILES['id_file'];
//     $upload_dir = 'uploads/'; // Directory to store uploaded files
//     $file_name = uniqid() . '_' . basename($id_file['name']); // Unique file name
//     $file_path = $upload_dir . $file_name;

//     // Validate file type and size
//     $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
//     $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
//     $max_size = 5 * 1024 * 1024; // 5MB

//     if (!in_array($file_extension, $allowed_types)) {
//         die("Error: Only PDF, JPG, JPEG, and PNG files are allowed.");
//     }

//     if ($id_file['size'] > $max_size) {
//         die("Error: File size exceeds the maximum limit of 5MB.");
//     }

//     // Move uploaded file to the upload directory
//     if (!move_uploaded_file($id_file['tmp_name'], $file_path)) {
//         die("Error: Failed to upload file.");
//     }

//     // Insert user into the database
//     $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, user_type, full_name, phone_number, address, id_file_path) VALUES (?, ?, ?, 'customer', ?, ?, ?, ?)");
//     $stmt->bind_param("sssssss", $email, $password, $email, $full_name, $phone_number, $address, $file_path);

//     if ($stmt->execute()) {
//         echo "<script>alert('Registration successful!'); window.location.href = 'index.php';</script>";
//     } else {
//         echo "<script>alert('Error: " . $stmt->error . "');</script>";
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
    <title>User Registration - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .registration-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .registration-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h2>User Registration</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Full Name -->
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>

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

            <!-- Phone Number -->
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>

            <!-- ID Upload -->
            <div class="mb-3">
                <label for="id_file" class="form-label">Upload Valid ID (e.g., Passport, Driver's License)</label>
                <input type="file" class="form-control" id="id_file" name="id_file" accept=".pdf,.jpg,.jpeg,.png" required>
                <small class="text-muted">Allowed formats: PDF, JPG, JPEG, PNG (Max 5MB)</small>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>