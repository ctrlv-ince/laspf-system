<?php
session_start();

// Redirect if not logged in or not a provider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header("Location: ../login.php");
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Fetch provider details
$provider_id = $_SESSION['user_id'];
$query = "SELECT * FROM providers WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$provider = $result->fetch_assoc();

// Fetch recent bookings
$query = "
    SELECT b.booking_id, b.start_time, b.end_time, b.status, 
           s.service_name, u.full_name AS customer_name
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    JOIN users u ON b.customer_id = u.user_id
    WHERE b.provider_id = ?
    ORDER BY b.start_time DESC
    LIMIT 5
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_bookings = $result->fetch_all(MYSQLI_ASSOC);

// Fetch total earnings
$query = "
    SELECT SUM(provider_earnings) AS total_earnings
    FROM payments
    WHERE booking_id IN (
        SELECT booking_id FROM bookings WHERE provider_id = ?
    )
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$total_earnings = $result->fetch_assoc()['total_earnings'] ?? 0;

// Fetch total reviews
$query = "
    SELECT COUNT(*) AS total_reviews
    FROM reviews
    WHERE provider_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$total_reviews = $result->fetch_assoc()['total_reviews'] ?? 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .dashboard-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">GoSeekr Provider</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="provider_services.php">Manage Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($provider['business_name']); ?>!</h1>
            <p class="lead">Manage your services, bookings, and earnings.</p>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Earnings</h5>
                        <p class="card-text display-6">₱<?php echo number_format($total_earnings, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Reviews</h5>
                        <p class="card-text display-6"><?php echo $total_reviews; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Recent Bookings</h5>
                        <p class="card-text display-6"><?php echo count($recent_bookings); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <p class="text-muted">No recent bookings found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Service</th>
                                    <th>Customer</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                        <td><?php echo date('M j, Y h:i A', strtotime($booking['start_time'])); ?></td>
                                        <td><?php echo date('M j, Y h:i A', strtotime($booking['end_time'])); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php echo $booking['status'] === 'pending' ? 'bg-warning' : 
                                                      ($booking['status'] === 'confirmed' ? 'bg-success' : 
                                                      ($booking['status'] === 'completed' ? 'bg-primary' : 'bg-danger')); ?>">
                                                <?php echo htmlspecialchars($booking['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Manage Services</h5>
                        <p class="card-text">Add, edit, or remove your services.</p>
                        <a href="provider_services.php" class="btn btn-primary">Go to Services</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">View Earnings</h5>
                        <p class="card-text">Check your earnings and payment history.</p>
                        <a href="provider_earnings.php" class="btn btn-primary">View Earnings</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Update Profile</h5>
                        <p class="card-text">Edit your business information.</p>
                        <a href="provider_profile.php" class="btn btn-primary">Update Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>