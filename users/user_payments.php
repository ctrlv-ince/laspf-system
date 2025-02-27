<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Fetch payment history for the logged-in user
$query = "
    SELECT p.payment_id, p.booking_id, p.amount, p.payment_method, p.payment_status, p.created_at,
           s.service_id, s.service_name, p.provider_id
    FROM payments p
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN services s ON b.service_id = s.service_id
    WHERE b.customer_id = :user_id
    ORDER BY p.created_at DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $booking_id = intval($_POST['booking_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // Validate rating (1 to 5)
    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Rating must be between 1 and 5.";
        header("Location: user_payments.php");
        exit();
    }

    // Insert the review into the database
    $query = "
        INSERT INTO reviews (booking_id, customer_id, provider_id, rating, comment, created_at)
        VALUES (:booking_id, :customer_id, :provider_id, :rating, :comment, NOW())
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':booking_id' => $booking_id,
        ':customer_id' => $_SESSION['user_id'],
        ':provider_id' => $_POST['provider_id'],
        ':rating' => $rating,
        ':comment' => $comment
    ]);

    // Redirect with success message
    $_SESSION['success'] = "Review submitted successfully!";
    header("Location: user_payments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .payment-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .review-form {
            margin-top: 20px;
        }
        .star-rating {
            font-size: 24px;
            color: #ffc107;
            cursor: pointer;
        }
        .star-rating .star {
            display: inline-block;
        }
        .star-rating .star:hover,
        .star-rating .star.active {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">GoSeekr User</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Payment History -->
    <div class="payment-container">
        <h2>Payment History</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Service</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No payment history found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($payment['service_name']); ?></td>
                            <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td>
                                <span class="badge 
                                    <?php echo $payment['payment_status'] === 'completed' ? 'bg-success' : 
                                          ($payment['payment_status'] === 'pending' ? 'bg-warning' : 'bg-danger'); ?>">
                                    <?php echo htmlspecialchars($payment['payment_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($payment['created_at'])); ?></td>
                            <td>
                                <?php if ($payment['payment_status'] === 'completed'): ?>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $payment['booking_id']; ?>">
                                        <i class="fas fa-star"></i> Rate
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">Not available</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Review Modal for Each Booking -->
                        <div class="modal fade" id="reviewModal<?php echo $payment['booking_id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Rate Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="user_payments.php">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Rating</label>
                                                <div class="star-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <span class="star" data-rating="<?php echo $i; ?>">&#9733;</span>
                                                    <?php endfor; ?>
                                                </div>
                                                <input type="hidden" name="rating" id="ratingInput<?php echo $payment['booking_id']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Comment</label>
                                                <textarea class="form-control" name="comment" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="booking_id" value="<?php echo $payment['booking_id']; ?>">
                                            <input type="hidden" name="provider_id" value="<?php echo $payment['provider_id']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star Rating Functionality
        document.querySelectorAll('.star-rating').forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            const ratingInput = document.getElementById(`ratingInput${rating.closest('.modal').id.replace('reviewModal', '')}`);

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const selectedRating = star.getAttribute('data-rating');
                    ratingInput.value = selectedRating;

                    // Highlight selected stars
                    stars.forEach((s, index) => {
                        if (index < selectedRating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>