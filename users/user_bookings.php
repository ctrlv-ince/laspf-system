<?php
session_start();

// Check if the user is logged in and has the role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Initialize variables for search and filter
$search = '';
$filter_category = '';
$where_clause = "WHERE 1"; // Default condition
$params = [];
$param_types = '';
$param_values = [];

// Handle search and filter form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['search'])) {
        $search = trim($_GET['search']);
        if (!empty($search)) {
            $where_clause .= " AND (s.service_name LIKE ? OR p.business_name LIKE ?)";
            $param_types .= 'ss';
            $param_values[] = "%$search%";
            $param_values[] = "%$search%";
        }
    }

    if (isset($_GET['filter_category'])) {
        $filter_category = trim($_GET['filter_category']);
        if (!empty($filter_category)) {
            $where_clause .= " AND p.service_category = ?";
            $param_types .= 's';
            $param_values[] = $filter_category;
        }
    }
}

// Fetch available services with provider details
$query = "
    SELECT s.service_id, s.service_name, s.service_description, s.price, s.duration_minutes,
           p.provider_id, p.business_name, p.service_category
    FROM services s
    JOIN providers p ON s.provider_id = p.provider_id
    $where_clause
    ORDER BY s.service_name ASC
";

$stmt = $conn->prepare($query);
if (!empty($param_types)) {
    $stmt->bind_param($param_types, ...$param_values);
}
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSeekr - Find and Book Services</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Include your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Find and Book Services</h1>

        <!-- Search and Filter Form -->
        <form method="GET" action="user_bookings.php">
            <div class="search-filter">
                <input type="text" name="search" placeholder="Search by service or provider" value="<?php echo htmlspecialchars($search); ?>">
                <select name="filter_category">
                    <option value="">All Categories</option>
                    <option value="plumbing" <?php echo $filter_category === 'plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                    <option value="cleaning" <?php echo $filter_category === 'cleaning' ? 'selected' : ''; ?>>Cleaning</option>
                    <option value="electrician" <?php echo $filter_category === 'electrician' ? 'selected' : ''; ?>>Electrician</option>
                    <option value="carpentry" <?php echo $filter_category === 'carpentry' ? 'selected' : ''; ?>>Carpentry</option>
                    <option value="other" <?php echo $filter_category === 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
                <button type="submit">Apply</button>
            </div>
        </form>

        <!-- Services List -->
        <div class="services-list">
            <?php if (empty($services)): ?>
                <p>No services found.</p>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                        <p><strong>Provider:</strong> <?php echo htmlspecialchars($service['business_name']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($service['service_category']); ?></p>
                        <p><strong>Price:</strong> ₱<?php echo number_format($service['price'], 2); ?></p>
                        <p><strong>Duration:</strong> <?php echo $service['duration_minutes']; ?> minutes</p>
                        <p><?php echo htmlspecialchars($service['service_description']); ?></p>
                        <a href="book_service.php?service_id=<?php echo $service['service_id']; ?>" class="btn-book">Book Now</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>