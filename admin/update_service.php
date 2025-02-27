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
    $service_id = intval($_POST['service_id']);
    $service_name = trim($_POST['service_name']);
    $service_description = trim($_POST['service_description']);
    $price = floatval($_POST['price']);
    $duration_minutes = intval($_POST['duration_minutes']);

    // Validate required fields
    if (empty($service_name) || empty($service_description) || $price <= 0 || $duration_minutes <= 0) {
        $_SESSION['error'] = "All fields are required, and price/duration must be positive.";
        header("Location: admin_services.php");
        exit();
    }

    // Update the service in the database
    $query = "
        UPDATE services
        SET service_name = :service_name,
            service_description = :service_description,
            price = :price,
            duration_minutes = :duration_minutes
        WHERE service_id = :service_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':service_name' => $service_name,
        ':service_description' => $service_description,
        ':price' => $price,
        ':duration_minutes' => $duration_minutes,
        ':service_id' => $service_id
    ]);

    // Redirect back to the services page with a success message
    $_SESSION['success'] = "Service updated successfully!";
    header("Location: admin_services.php");
    exit();
} else {
    // Redirect if accessed directly
    header("Location: admin_services.php");
    exit();
}