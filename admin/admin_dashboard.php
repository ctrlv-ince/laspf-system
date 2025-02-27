<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Fetch key metrics
$query = "
    SELECT 
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM providers) AS total_providers,
        (SELECT COUNT(*) FROM bookings) AS total_bookings,
        (SELECT COUNT(*) FROM reviews) AS total_reviews
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$metrics = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent bookings
$query = "
    SELECT b.booking_id, b.start_time, b.end_time, b.status,
           u.full_name AS customer_name, p.business_name AS provider_name
    FROM bookings b
    JOIN users u ON b.customer_id = u.user_id
    JOIN providers p ON b.provider_id = p.provider_id
    ORDER BY b.start_time DESC
    LIMIT 5
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch data for charts
$query = "
    SELECT 
        DATE_FORMAT(b.start_time, '%Y-%m') AS month,
        COUNT(*) AS bookings_count,
        SUM(p.amount) AS total_revenue
    FROM bookings b
    JOIN payments p ON b.booking_id = p.booking_id
    GROUP BY DATE_FORMAT(b.start_time, '%Y-%m')
    ORDER BY month ASC
    LIMIT 6
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$chart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$labels = [];
$bookings_data = [];
$revenue_data = [];

foreach ($chart_data as $row) {
    $labels[] = date('M Y', strtotime($row['month']));
    $bookings_data[] = $row['bookings_count'];
    $revenue_data[] = $row['total_revenue'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ServiceHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 1rem;
        }
        .sidebar .nav-link:hover {
            color: rgba(255,255,255,1);
            background-color: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #0d6efd;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-online {
            background-color: #198754;
        }
        .status-offline {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-white p-3">
                    <h5>GoSeekr Admin</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_provider_approvals.php">
                            <i class="fas fa-user-check"></i> Provider Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_services.php">
                            <i class="fas fa-tools"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reviews.php">
                            <i class="fas fa-star"></i> Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_settings.php">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-3">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3>Dashboard</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="status-indicator status-online"></span>
                            Admin User
                        </div>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="mb-0"><?php echo $metrics['total_users']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Providers</h5>
                                <h2 class="mb-0"><?php echo $metrics['total_providers']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Bookings</h5>
                                <h2 class="mb-0"><?php echo $metrics['total_bookings']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Reviews</h5>
                                <h2 class="mb-0"><?php echo $metrics['total_reviews']; ?></h2>
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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer</th>
                                        <th>Provider</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_bookings)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No recent bookings found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_bookings as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['provider_name']); ?></td>
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
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Bookings Overview</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="bookingsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Revenue Overview</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        const bookingsChart = new Chart(bookingsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?php echo json_encode($bookings_data); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($revenue_data); ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>