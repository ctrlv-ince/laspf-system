<?php
session_start();

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Include the database configuration file
require_once '../database/config.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, phone_number, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone_number, $address);
$stmt->fetch();
$stmt->close();

// Fetch user bookings
$stmt = $conn->prepare("
    SELECT b.booking_id, s.service_name, p.business_name, b.start_time, b.end_time, b.status
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    JOIN providers p ON b.provider_id = p.provider_id
    WHERE b.customer_id = ?
    ORDER BY b.start_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch user reviews
$stmt = $conn->prepare("
    SELECT r.review_id, s.service_name, p.business_name, r.rating, r.comment
    FROM reviews r
    JOIN bookings b ON r.booking_id = b.booking_id
    JOIN services s ON b.service_id = s.service_id
    JOIN providers p ON b.provider_id = p.provider_id
    WHERE r.customer_id = ?
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch user payments
$stmt = $conn->prepare("
    SELECT p.payment_id, s.service_name, p.amount, p.payment_status, p.created_at
    FROM payments p
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN services s ON b.service_id = s.service_id
    WHERE b.customer_id = ?
    ORDER BY p.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="user_dashboard.php">GoSeekr User</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Welcome, <?php echo $full_name; ?>!</h1>
        <p>This is your dashboard. Manage your bookings, reviews, and profile here.</p>

        <!-- Tabs for Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#bookings">Bookings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#payments">Payments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#profile">Profile</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Bookings Tab -->
            <div class="tab-pane fade show active" id="bookings">
                <h2>Your Bookings</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Provider</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['service_name']; ?></td>
                                <td><?php echo $booking['business_name']; ?></td>
                                <td><?php echo $booking['start_time']; ?></td>
                                <td><?php echo $booking['end_time']; ?></td>
                                <td><?php echo $booking['status']; ?></td>
                                <td>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <form action="cancel_booking.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews">
                <h2>Your Reviews</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Provider</th>
                            <th>Rating</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['service_name']; ?></td>
                                <td><?php echo $review['business_name']; ?></td>
                                <td><?php echo $review['rating']; ?> / 5</td>
                                <td><?php echo $review['comment']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payments Tab -->
            <div class="tab-pane fade" id="payments">
                <h2>Your Payments</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo $payment['service_name']; ?></td>
                                <td>$<?php echo $payment['amount']; ?></td>
                                <td><?php echo $payment['payment_status']; ?></td>
                                <td><?php echo $payment['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Profile Tab -->
            <div class="tab-pane fade" id="profile">
                <h2>Your Profile</h2>
                <form action="update_profile.php" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $full_name; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $phone_number; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $address; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>