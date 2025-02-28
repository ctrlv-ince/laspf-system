<?php
session_start();

// Include database configuration
require_once '../database/config.php';

// Define required document types
$document_types = ['dti_registration', 'mayors_permit', 'bir_certificate', 'valid_id'];

// Initialize variables
$errors = [];
$success = false;
$upload_dir = './uploads/';

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);  // ✅ Address added
    $business_name = trim($_POST['business_name']);
    $business_description = trim($_POST['business_description']);
    $service_category = trim($_POST['service_category']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $uploaded_files = [];

    // Validate required fields
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($phone_number)) $errors[] = "Phone number is required.";
    if (empty($address)) $errors[] = "Address is required."; // ✅ Address validation
    if (empty($business_name)) $errors[] = "Business name is required.";
    if (empty($business_description)) $errors[] = "Business description is required.";
    if (empty($service_category)) $errors[] = "Service category is required.";
    if (empty($password) || empty($confirm_password)) $errors[] = "Password and confirmation are required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate phone number format
    if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
        $errors[] = "Invalid phone number format.";
    }

    // Handle file uploads for required documents
    foreach ($document_types as $doc_type) {
        if (isset($_FILES[$doc_type]) && $_FILES[$doc_type]['error'] === UPLOAD_ERR_OK) {
            $file_extension = pathinfo($_FILES[$doc_type]['name'], PATHINFO_EXTENSION);
            $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                $errors[] = ucfirst(str_replace('_', ' ', $doc_type)) . " must be a PDF, JPG, JPEG, or PNG file.";
            } else {
                $unique_filename = $doc_type . "_" . time() . "_" . uniqid() . "." . $file_extension;
                $file_path = $upload_dir . $unique_filename;

                if (move_uploaded_file($_FILES[$doc_type]['tmp_name'], $file_path)) {
                    $uploaded_files[$doc_type] = $file_path;
                } else {
                    $errors[] = "Failed to upload " . ucfirst(str_replace('_', ' ', $doc_type));
                }
            }
        } else {
            $errors[] = ucfirst(str_replace('_', ' ', $doc_type)) . " is required.";
        }
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            // Insert into `users` table (Including Address)
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, password_hash, email, role, full_name, phone_number, address, created_at, updated_at)
                      VALUES (?, ?, ?, 'provider', ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssss', $email, $password_hash, $email, $full_name, $phone_number, $address);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert into `providers` table (Pending Approval)
            $query = "INSERT INTO providers (user_id, business_name, business_description, service_category, is_verified, created_at, updated_at)
                      VALUES (?, ?, ?, ?, FALSE, NOW(), NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('isss', $user_id, $business_name, $business_description, $service_category);
            $stmt->execute();
            $provider_id = $stmt->insert_id;
            $stmt->close();

            // Insert uploaded documents into `provider_documents` table
            $query = "INSERT INTO provider_documents (provider_id, document_type, file_path, uploaded_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            foreach ($uploaded_files as $doc_type => $file_path) {
                $stmt->bind_param('iss', $provider_id, $doc_type, $file_path);
                $stmt->execute();
            }
            $stmt->close();

            // Commit transaction
            $conn->commit();
            $success = true;
        } catch (Exception $e) {
            $conn->rollback();
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
    <title>Provider Registration - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Service Provider Registration</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">Registration successful! Your account is pending admin approval.</div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="provider_registration.php" enctype="multipart/form-data">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Personal Information</div>
                <div class="card-body">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" required>

                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>

                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="phone_number" pattern="[0-9]{10}" required>

                    <label class="form-label">Address</label>  
                    <input type="text" class="form-control" name="address" required>

                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>

                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                    </div>
            <div class="mb-3">
                <label class="form-label">Business Name</label>
                <input type="text" class="form-control" name="business_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Business Description</label>
                <textarea class="form-control" name="business_description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Service Category</label>
                <select class="form-select" name="service_category" required>
                    <option value="">Select Service Category</option>
                    <option value="plumbing">Plumbing</option>
                    <option value="cleaning">Cleaning</option>
                    <option value="electrician">Electrician</option>
                    <option value="carpentry">Carpentry</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Upload Documents</div>
                <div class="card-body">
                    <?php foreach ($document_types as $doc_type): ?>
                        <label class="form-label"><?php echo ucfirst(str_replace('_', ' ', $doc_type)); ?></label>
                        <input type="file" class="form-control" name="<?php echo $doc_type; ?>" required>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit Registration</button>
        </form>
    </div>
</body>
</html>
