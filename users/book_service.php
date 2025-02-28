<?php
session_start();

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Include database configuration
require_once '../database/config.php';

// Check if `service_id` is provided
if (!isset($_GET['service_id']) || empty($_GET['service_id'])) {
    header("Location: user_bookings.php"); // Redirect if no service ID
    exit();
}

$service_id = intval($_GET['service_id']);

// Fetch service details
$query = "
    SELECT s.service_id, s.service_name, s.service_description, s.price, s.duration_minutes,
           p.provider_id, p.business_name, p.service_category
    FROM services s
    JOIN providers p ON s.provider_id = p.provider_id
    WHERE s.service_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$stmt->close();

// If service not found, redirect back
if (!$service) {
    header("Location: user_bookings.php");
    exit();
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['user_id'];
    $provider_id = $service['provider_id'];
    $start_time = $_POST['start_time'];
    
    // Calculate end_time based on service duration
    $duration = $service['duration_minutes'];
    $end_time = date('Y-m-d H:i:s', strtotime("$start_time + $duration minutes"));
    
    // Insert booking
    $query = "INSERT INTO bookings (customer_id, service_id, provider_id, start_time, end_time, status, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiss", $customer_id, $service_id, $provider_id, $start_time, $end_time);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Booking request submitted successfully! The provider will confirm your booking.');
                window.location.href = 'user_bookings.php';
              </script>";
    } else {
        $error = "Failed to book the service. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-4">Book Service</h2>

        <!-- Service Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                <p><strong>Provider:</strong> <?php echo htmlspecialchars($service['business_name']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($service['service_category']); ?></p>
                <p><strong>Price:</strong> ₱<?php echo number_format($service['price'], 2); ?></p>
                <p><strong>Duration:</strong> <?php echo $service['duration_minutes']; ?> minutes</p>
                <p><?php echo htmlspecialchars($service['service_description']); ?></p>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="card">
            <div class="card-body">
                <h4>Schedule Your Service</h4>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Select Date & Time</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                    <a href="user_bookings.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
