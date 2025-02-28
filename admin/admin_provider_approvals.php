<?php
session_start();

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include the database configuration
require_once '../database/config.php';

// Handle Approve/Reject Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider_id = intval($_POST['provider_id']);

    if (isset($_POST['approve_provider'])) {
        // Approve provider
        $query = "UPDATE providers SET is_verified = 1, updated_at = NOW() WHERE provider_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $provider_id);
        $stmt->execute();
    } elseif (isset($_POST['reject_provider'])) {
        // Reject provider
        $query = "UPDATE providers SET is_verified = -1, updated_at = NOW() WHERE provider_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $provider_id);
        $stmt->execute();
    }

    // Redirect to refresh the page
    header("Location: admin_provider_approvals.php");
    exit();
}

// Fetch provider applications
$query = "
    SELECT p.provider_id, p.business_name, p.service_category, p.is_verified, p.created_at, p.updated_at,
           u.full_name, u.email, u.phone_number, u.id_file_path
    FROM providers p
    JOIN users u ON p.user_id = u.user_id
    ORDER BY p.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$providers = $result->fetch_all(MYSQLI_ASSOC);

// Calculate stats
$pending_count = 0;
$approved_today_count = 0;
$rejected_today_count = 0;
$today = date('Y-m-d');

foreach ($providers as $provider) {
    if ($provider['is_verified'] == 0) {
        $pending_count++;
    } elseif ($provider['is_verified'] == 1 && date('Y-m-d', strtotime($provider['updated_at'])) === $today) {
        $approved_today_count++;
    } elseif ($provider['is_verified'] == -1 && date('Y-m-d', strtotime($provider['updated_at'])) === $today) {
        $rejected_today_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Approvals - GoSeekr Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Admin Navbar -->
    <?php include "admin_navbar.php"; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Provider Approvals</h2>

        <!-- Stats -->
        <div class="row mb-4">
            
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Pending Approvals</h5>
                        <h2><?php echo $pending_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Approved Today</h5>
                        <h2><?php echo $approved_today_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Rejected Today</h5>
                        <h2><?php echo $rejected_today_count; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Provider Details</th>
                            <th>Business Info</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($providers)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No provider applications found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($providers as $provider): ?>
                                <tr>
                                    <td>
                                        <div><?php echo htmlspecialchars($provider['full_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($provider['email']); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($provider['business_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($provider['service_category']); ?></small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#documentsModal<?php echo $provider['provider_id']; ?>">
                                            View Documents
                                        </button>
                                    </td>
                                    <td>
                                        <?php if ($provider['is_verified'] == 0): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($provider['is_verified'] == 1): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($provider['is_verified'] == 0): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                <button type="submit" name="approve_provider" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                <button type="submit" name="reject_provider" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>View Only</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Modal for Viewing Documents -->
                                <div class="modal fade" id="documentsModal<?php echo $provider['provider_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Provider Documents</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    // Fetch documents for the provider
                                                    $doc_query = "SELECT document_type, file_path FROM provider_documents WHERE provider_id = ?";
                                                    $doc_stmt = $conn->prepare($doc_query);
                                                    $doc_stmt->bind_param("i", $provider['provider_id']);
                                                    $doc_stmt->execute();
                                                    $doc_result = $doc_stmt->get_result();

                                                    // Display ID file from users table
                                                    if (!empty($provider['id_file_path'])) {
                                                        $id_file_path = '../providers/uploads/' . basename($provider['id_file_path']);
                                                        echo '<p><strong>Valid ID:</strong></p>';
                                                        echo '<a href="' . htmlspecialchars($id_file_path) . '" target="_blank">';
                                                        echo '<img src="' . htmlspecialchars($id_file_path) . '" class="img-fluid">';
                                                        echo '</a>';
                                                        echo '<hr>';
                                                    }

                                                    // Display other documents from provider_documents table
                                                    while ($doc = $doc_result->fetch_assoc()):
                                                        $doc_path = '../providers/uploads/' . basename($doc['file_path']);
                                                ?>
                                                    <p><strong><?php echo ucfirst(str_replace('_', ' ', $doc['document_type'])); ?>:</strong></p>
                                                    <a href="<?php echo htmlspecialchars($doc_path); ?>" target="_blank">
                                                        <img src="<?php echo htmlspecialchars($doc_path); ?>" class="img-fluid">
                                                    </a>
                                                    <hr>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>