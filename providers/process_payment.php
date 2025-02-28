<?php
session_start();
require_once '../database/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'];
    $action = $_POST['action'];

    // Validate action
    if ($action !== 'paid') {
        die("Invalid action.");
    }

    // Update payment status
    $query = "UPDATE payments SET payment_status = 'paid', payment_date = NOW() WHERE payment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>
                alert('Payment confirmed successfully.');
                window.location.href = 'confirm_payment.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to confirm payment.');
                window.location.href = 'confirm_payment.php';
              </script>";
    }

    $stmt->close();
}
?>