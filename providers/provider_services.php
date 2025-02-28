<?php
session_start();

// Check if the user is logged in and has the role 'provider'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: ../login.php');
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Fetch the provider's ID from the session
$provider_id = $_SESSION['user_id'];

// Handle Add Service Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $service_name = trim($_POST['service_name']);
    $service_description = trim($_POST['service_description']);
    $price = floatval($_POST['price']);
    $duration_minutes = intval($_POST['duration_minutes']);

    // Insert the new service into the database
    $query = "INSERT INTO services (provider_id, service_name, service_description, price, duration_minutes) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issdi", $provider_id, $service_name, $service_description, $price, $duration_minutes);
    $stmt->execute();

    // Redirect to refresh the page and show the updated list
    header('Location: provider_services.php');
    exit();
}

// Handle Edit Service Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $service_id = intval($_POST['service_id']);
    $service_name = trim($_POST['edit_service_name']);
    $service_description = trim($_POST['edit_service_description']);
    $price = floatval($_POST['edit_price']);
    $duration_minutes = intval($_POST['edit_duration_minutes']);

    // Update the service in the database
    $query = "UPDATE services 
              SET service_name = ?, service_description = ?, 
                  price = ?, duration_minutes = ? 
              WHERE service_id = ? AND provider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdiii", $service_name, $service_description, $price, $duration_minutes, $service_id, $provider_id);
    $stmt->execute();

    // Redirect to refresh the page and show the updated list
    header('Location: provider_services.php');
    exit();
}

// Handle Delete Service Request
if (isset($_GET['delete_service'])) {
    $service_id = intval($_GET['delete_service']);

    // Delete the service from the database
    $query = "DELETE FROM services WHERE service_id = ? AND provider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $service_id, $provider_id);
    $stmt->execute();

    // Redirect to refresh the page and show the updated list
    header('Location: provider_services.php');
    exit();
}

// Fetch all services for the logged-in provider
$query = "SELECT * FROM services WHERE provider_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .service-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="provider_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="service-container">
        <h2>Manage Services</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">Add New Service</button>

        <!-- Services Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No services found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($service['service_description']); ?></td>
                            <td>$<?php echo number_format($service['price'], 2); ?></td>
                            <td><?php echo $service['duration_minutes']; ?> mins</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editServiceModal<?php echo $service['service_id']; ?>">Edit</button>
                                <a href="provider_services.php?delete_service=<?php echo $service['service_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                            </td>
                        </tr>

                        <!-- Edit Service Modal for Each Service -->
                        <div class="modal fade" id="editServiceModal<?php echo $service['service_id']; ?>" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="provider_services.php">
                                            <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                            <div class="mb-3">
                                                <label for="editServiceName" class="form-label">Service Name</label>
                                                <input type="text" class="form-control" id="editServiceName" name="edit_service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editServiceDescription" class="form-label">Description</label>
                                                <textarea class="form-control" id="editServiceDescription" name="edit_service_description" rows="3" required><?php echo htmlspecialchars($service['service_description']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editPrice" class="form-label">Price</label>
                                                <input type="number" class="form-control" id="editPrice" name="edit_price" value="<?php echo $service['price']; ?>" step="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="editDuration" class="form-label">Duration (minutes)</label>
                                                <input type="number" class="form-control" id="editDuration" name="edit_duration_minutes" value="<?php echo $service['duration_minutes']; ?>" required>
                                            </div>
                                            <button type="submit" name="edit_service" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="provider_services.php">
                        <div class="mb-3">
                            <label for="serviceName" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="serviceName" name="service_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="serviceDescription" name="service_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="servicePrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="servicePrice" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDuration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="serviceDuration" name="duration_minutes" required>
                        </div>
                        <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>