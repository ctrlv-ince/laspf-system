<?php
session_start();
require_once '../database/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    // Validate action
    if (!in_array($action, ['confirmed', 'cancelled'])) {
        die("Invalid action.");
    }

    // Update booking status
    $query = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $action, $booking_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>
                alert('Booking status updated successfully.');
                window.location.href = 'provider_dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to update booking status.');
                window.location.href = 'provider_dashboard.php';
              </script>";
    }

    $stmt->close();
}
?>