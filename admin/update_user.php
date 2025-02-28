<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $user_id = intval($_POST['user_id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Validate required fields
    if (empty($username) || empty($email) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: admin_users.php");
        exit();
    }

    // Update the user in the database using MySQLi
    $query = "
        UPDATE users
        SET username = ?, 
            email = ?, 
            role = ? 
        WHERE user_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $username, $email, $role, $user_id);
    $stmt->execute();

    // Redirect back to the users page with a success message
    $_SESSION['success'] = "User updated successfully!";
    header("Location: admin_users.php");
    exit();
} else {
    // Redirect if accessed directly
    header("Location: admin_users.php");
    exit();
}
?>