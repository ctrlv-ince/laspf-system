<?php
session_start();

// Include the database configuration
require_once '../database/config.php';

// Initialize variables for form data and errors
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile_number = trim($_POST['mobile_number']);
    $business_name = trim($_POST['business_name']);
    $business_type = trim($_POST['business_type']);
    $dti_sec_number = trim($_POST['dti_sec_number']);
    $tin_number = trim($_POST['tin_number']);
    $business_address = trim($_POST['business_address']);
    $region = trim($_POST['region']);
    $province = trim($_POST['province']);
    $city = trim($_POST['city']);
    $service_categories = isset($_POST['service_categories']) ? $_POST['service_categories'] : [];
    $service_areas = isset($_POST['service_areas']) ? $_POST['service_areas'] : [];

    // Validate required fields
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($mobile_number)) $errors[] = "Mobile number is required.";
    if (empty($business_name)) $errors[] = "Business name is required.";
    if (empty($business_type)) $errors[] = "Business type is required.";
    if (empty($dti_sec_number)) $errors[] = "DTI/SEC registration number is required.";
    if (empty($tin_number)) $errors[] = "Tax Identification Number (TIN) is required.";
    if (empty($business_address)) $errors[] = "Business address is required.";
    if (empty($region)) $errors[] = "Region is required.";
    if (empty($province)) $errors[] = "Province is required.";
    if (empty($city)) $errors[] = "City/Municipality is required.";
    if (empty($service_categories)) $errors[] = "At least one service category is required.";
    if (empty($service_areas)) $errors[] = "At least one service area is required.";

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate mobile number format
    if (!preg_match('/^[0-9]{10}$/', $mobile_number)) {
        $errors[] = "Invalid mobile number format.";
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            // Start a transaction
            $pdo->beginTransaction();

            // Insert into `users` table
            $query = "INSERT INTO users (username, password_hash, email, role, full_name, phone_number, address, created_at, updated_at)
                      VALUES (:username, :password_hash, :email, 'provider', :full_name, :phone_number, :address, NOW(), NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':username' => $email, // Use email as username
                ':password_hash' => password_hash('default_password', PASSWORD_DEFAULT), // Set a default password
                ':email' => $email,
                ':full_name' => "$first_name $middle_name $last_name",
                ':phone_number' => $mobile_number,
                ':address' => $business_address
            ]);
            $user_id = $pdo->lastInsertId();

            // Insert into `providers` table
            $query = "INSERT INTO providers (user_id, business_name, business_description, service_category, is_verified, created_at, updated_at)
                      VALUES (:user_id, :business_name, :business_description, :service_category, FALSE, NOW(), NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':business_name' => $business_name,
                ':business_description' => "$business_type, DTI/SEC: $dti_sec_number, TIN: $tin_number",
                ':service_category' => implode(',', $service_categories)
            ]);
            $provider_id = $pdo->lastInsertId();

            // Insert into `provider_documents` table
            $document_types = ['dti_registration', 'mayors_permit', 'bir_certificate', 'valid_id'];
            foreach ($document_types as $type) {
                if (!empty($_FILES[$type]['name'])) {
                    $file_path = '../users/uploads/' . basename($_FILES[$type]['name']);
                    move_uploaded_file($_FILES[$type]['tmp_name'], $file_path);

                    $query = "INSERT INTO provider_documents (provider_id, document_type, file_path, uploaded_at)
                              VALUES (:provider_id, :document_type, :file_path, NOW())";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([
                        ':provider_id' => $provider_id,
                        ':document_type' => $type,
                        ':file_path' => $file_path
                    ]);
                }
            }

            // Insert into `services` table
            foreach ($service_categories as $category) {
                $query = "INSERT INTO services (provider_id, service_name, service_description, price, duration_minutes, created_at, updated_at)
                          VALUES (:provider_id, :service_name, :service_description, 0, 0, NOW(), NOW())";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':provider_id' => $provider_id,
                    ':service_name' => $category,
                    ':service_description' => "Service category: $category"
                ]);
            }

            // Commit the transaction
            $pdo->commit();
            $success = true;
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSeekr - Provider Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .step-wizard {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 20px;
            margin-bottom: 40px;
        }
        .step-wizard .step {
            position: relative;
            padding-bottom: 30px;
        }
        .step-wizard .step.active .step-icon {
            background-color: #0d6efd;
            color: white;
        }
        .step-wizard .step.completed .step-icon {
            background-color: #198754;
            color: white;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }
        .upload-box {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }
        .upload-box:hover {
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">GoSeekr</a>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="text-center mb-4">Service Provider Registration</h2>

                <!-- Display Success or Error Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success">Registration successful!</div>
                <?php elseif (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Step Wizard -->
                <div class="step-wizard d-flex justify-content-between mb-5">
                    <div class="step active text-center">
                        <div class="step-icon">1</div>
                        <small>Basic Info</small>
                    </div>
                    <div class="step text-center">
                        <div class="step-icon">2</div>
                        <small>Business Details</small>
                    </div>
                    <div class="step text-center">
                        <div class="step-icon">3</div>
                        <small>Documents</small>
                    </div>
                    <div class="step text-center">
                        <div class="step-icon">4</div>
                        <small>Services</small>
                    </div>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="provider_registration.php" enctype="multipart/form-data">
                    <!-- Step 1: Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required-field">First Name</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Middle Name</label>
                                    <input type="text" class="form-control" name="middle_name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Email Address</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+63</span>
                                        <input type="tel" class="form-control" name="mobile_number" pattern="[0-9]{10}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Business Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Business Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required-field">Business Name</label>
                                    <input type="text" class="form-control" name="business_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Business Type</label>
                                    <select class="form-select" name="business_type" required>
                                        <option value="">Select Business Type</option>
                                        <option>Sole Proprietorship</option>
                                        <option>Partnership</option>
                                        <option>Corporation</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">DTI/SEC Registration Number</label>
                                    <input type="text" class="form-control" name="dti_sec_number" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Tax Identification Number (TIN)</label>
                                    <input type="text" class="form-control" name="tin_number" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required-field">Business Address</label>
                                    <input type="text" class="form-control mb-2" name="business_address" placeholder="Street Address" required>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <select class="form-select" name="region" required>
                                                <option value="">Select Region</option>
                                                <option>NCR</option>
                                                <option>Region I</option>
                                                <option>Region II</option>
                                                <!-- Add more regions -->
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select" name="province" required>
                                                <option value="">Select Province</option>
                                                <!-- Dynamically populated based on region -->
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select" name="city" required>
                                                <option value="">Select City/Municipality</option>
                                                <!-- Dynamically populated based on province -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Required Documents -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Required Documents</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> All documents must be clear, legible, and in PDF or image format.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required-field">DTI/SEC Registration Certificate</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" name="dti_registration" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Mayor's Permit</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" name="mayors_permit" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">BIR Certificate of Registration</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" name="bir_certificate" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Valid Government ID</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" name="valid_id" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Services -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Services Offered</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label required-field">Service Categories</label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_categories[]" value="Home Cleaning" id="service1">
                                                <label class="form-check-label" for="service1">Home Cleaning</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_categories[]" value="Plumbing" id="service2">
                                                <label class="form-check-label" for="service2">Plumbing</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_categories[]" value="Electrical" id="service3">
                                                <label class="form-check-label" for="service3">Electrical</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required-field">Service Area</label>
                                    <select class="form-select" name="service_areas[]" multiple required>
                                        <option>Metro Manila</option>
                                        <option>Cavite</option>
                                        <option>Laguna</option>
                                        <option>Rizal</option>
                                        <option>Bulacan</option>
                                    </select>
                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple areas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Navigation -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-primary">Previous</button>
                        <button type="submit" class="btn btn-primary">Submit Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>