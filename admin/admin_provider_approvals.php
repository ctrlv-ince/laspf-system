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
    if (isset($_POST['approve_provider'])) {
        $provider_id = intval($_POST['provider_id']);
        $notes = trim($_POST['notes']);

        // Update provider status to 'approved'
        $query = "UPDATE providers SET is_verified = TRUE WHERE provider_id = :provider_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':provider_id' => $provider_id]);

        // Optionally, save notes to a separate table or log
    } elseif (isset($_POST['reject_provider'])) {
        $provider_id = intval($_POST['provider_id']);
        $reason = trim($_POST['reason']);

        // Update provider status to 'rejected'
        $query = "UPDATE providers SET is_verified = FALSE WHERE provider_id = :provider_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':provider_id' => $provider_id]);

        // Optionally, save rejection reason to a separate table or log
    }

    // Redirect to refresh the page
    header("Location: admin_provider_approvals.php");
    exit();
}

// Fetch provider applications
$query = "
    SELECT p.provider_id, p.business_name, p.service_category, p.is_verified, p.created_at,
           u.full_name, u.email, u.phone_number
    FROM providers p
    JOIN users u ON p.user_id = u.user_id
    ORDER BY p.created_at DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$pending_count = 0;
$approved_today_count = 0;
$rejected_today_count = 0;

foreach ($providers as $provider) {
    if ($provider['is_verified'] === null) {
        $pending_count++;
    } elseif ($provider['is_verified'] === true && date('Y-m-d', strtotime($provider['created_at'])) === date('Y-m-d')) {
        $approved_today_count++;
    } elseif ($provider['is_verified'] === false && date('Y-m-d', strtotime($provider['created_at'])) === date('Y-m-d')) {
        $rejected_today_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Approvals - ServiceHub Admin</title>
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
        .badge-pending {
            background-color: #ffc107;
        }
        .badge-approved {
            background-color: #198754;
        }
        .badge-rejected {
            background-color: #dc3545;
        }
        .document-preview {
            max-height: 200px;
            object-fit: cover;
            cursor: pointer;
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
            <?php include 'admin_navbar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-3">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3>Provider Approvals</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Provider Approvals</li>
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

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending Approvals</h5>
                                <h2 class="mb-0"><?php echo $pending_count; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Approved Today</h5>
                                <h2 class="mb-0"><?php echo $approved_today_count; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Rejected Today</h5>
                                <h2 class="mb-0"><?php echo $rejected_today_count; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select class="form-select">
                                    <option selected>All Statuses</option>
                                    <option>Pending</option>
                                    <option>Approved</option>
                                    <option>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select">
                                    <option selected>All Services</option>
                                    <option>Home Cleaning</option>
                                    <option>Plumbing</option>
                                    <option>Electrical</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search providers...">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Provider Applications Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
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
                                                    <div class="d-flex align-items-center">
                                                        <img src="/api/placeholder/48/48" class="rounded-circle me-2" alt="Provider">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($provider['full_name']); ?></div>
                                                            <small class="text-muted">Applied: <?php echo date('M j, Y', strtotime($provider['created_at'])); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($provider['business_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($provider['service_category']); ?></small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#documentModal">
                                                        <i class="fas fa-file-alt"></i> View
                                                    </button>
                                                    <span class="badge bg-success">Complete</span>
                                                </td>
                                                <td>
                                                    <?php if ($provider['is_verified'] === null): ?>
                                                        <span class="badge badge-pending">Pending</span>
                                                    <?php elseif ($provider['is_verified'] === true): ?>
                                                        <span class="badge badge-approved">Approved</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-rejected">Rejected</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($provider['is_verified'] === null): ?>
                                                        <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $provider['provider_id']; ?>">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $provider['provider_id']; ?>">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-eye"></i> View Details
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                            <!-- Approve Modal for Each Provider -->
                                            <div class="modal fade" id="approveModal<?php echo $provider['provider_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Approve Provider</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="admin_provider_approvals.php">
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to approve this service provider?</p>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Additional Notes (Optional)</label>
                                                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="approve_provider" class="btn btn-success">Confirm Approval</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal for Each Provider -->
                                            <div class="modal fade" id="rejectModal<?php echo $provider['provider_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Provider</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="admin_provider_approvals.php">
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to reject this service provider?</p>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Reason for Rejection (Optional)</label>
                                                                    <textarea class="form-control" name="reason" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="reject_provider" class="btn btn-danger">Confirm Rejection</button>
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

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Provider Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>DTI Registration</h6>
                                    <img src="/api/placeholder/400/200" class="img-fluid document-preview" alt="DTI Registration">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Mayor's Permit</h6>
                                    <img src="/api/placeholder/400/200" class="img-fluid document-preview" alt="Mayor's Permit">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>BIR Certificate</h6>
                                    <img src="/api/placeholder/400/200" class="img-fluid document-preview" alt="BIR Certificate">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Valid ID</h6>
                                    <img src="/api/placeholder/400/200" class="img-fluid document-preview" alt="Valid ID">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>