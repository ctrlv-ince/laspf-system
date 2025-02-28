<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Handle Delete Action
if (isset($_GET['delete_service'])) {
    $service_id = intval($_GET['delete_service']);

    // Delete the service from the database
    $query = "DELETE FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();

    // Redirect to refresh the page
    header("Location: admin_services.php");
    exit();
}

// Fetch all services from the database
$query = "
    SELECT s.service_id, s.service_name, s.service_description, s.price, s.duration_minutes,
           p.business_name AS provider_name, p.service_category
    FROM services s
    JOIN providers p ON s.provider_id = p.provider_id
    ORDER BY s.service_name ASC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - ServiceHub Admin</title>
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'admin_navbar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-3">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3>Services Management</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Services</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="status-indicator status-online"></span>
                            Admin User
                        </div>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Service ID</th>
                                        <th>Service Name</th>
                                        <th>Provider</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($services)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No services found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($service['service_id']); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                                <td><?php echo htmlspecialchars($service['provider_name']); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_category']); ?></td>
                                                <td>$<?php echo number_format($service['price'], 2); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editServiceModal<?php echo $service['service_id']; ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="admin_services.php?delete_service=<?php echo $service['service_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Edit Service Modal for Each Service -->
                                            <div class="modal fade" id="editServiceModal<?php echo $service['service_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Service</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="update_service.php">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Service Name</label>
                                                                    <input type="text" class="form-control" name="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Description</label>
                                                                    <textarea class="form-control" name="service_description" rows="3" required><?php echo htmlspecialchars($service['service_description']); ?></textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Price</label>
                                                                    <input type="number" class="form-control" name="price" value="<?php echo $service['price']; ?>" step="0.01" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Duration (minutes)</label>
                                                                    <input type="number" class="form-control" name="duration_minutes" value="<?php echo $service['duration_minutes']; ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
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

                        <!-- Pagination -->
                        <nav class="mt-3">
                            <ul class="pagination justify-content-end">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>