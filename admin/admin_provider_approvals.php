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
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-white p-3">
                    <h5>ServiceHub Admin</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_provider_approvals.php">
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
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending Approvals</h5>
                                <h2 class="mb-0">12</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Approved Today</h5>
                                <h2 class="mb-0">8</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Rejected Today</h5>
                                <h2 class="mb-0">3</h2>
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
                                    <!-- Pending Application -->
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/api/placeholder/48/48" class="rounded-circle me-2" alt="Provider">
                                                <div>
                                                    <div class="fw-bold">Juan Dela Cruz</div>
                                                    <small class="text-muted">Applied: Feb 20, 2025</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>Clean Pro Services</div>
                                            <small class="text-muted">Home Cleaning</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#documentModal">
                                                <i class="fas fa-file-alt"></i> View
                                            </button>
                                            <span class="badge bg-success">Complete</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-pending">Pending</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#approveModal">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Approved Application -->
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/api/placeholder/48/48" class="rounded-circle me-2" alt="Provider">
                                                <div>
                                                    <div class="fw-bold">Maria Santos</div>
                                                    <small class="text-muted">Applied: Feb 19, 2025</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>PlumbRight Solutions</div>
                                            <small class="text-muted">Plumbing</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#documentModal">
                                                <i class="fas fa-file-alt"></i> View
                                            </button>
                                            <span class="badge bg-success">Complete</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-approved">Approved</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                        </td>
                                    </tr>
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

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this service provider?</p>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success">Confirm Approval</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this service provider?</p>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection (Optional)</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>