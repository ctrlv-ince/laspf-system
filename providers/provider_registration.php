<?php
session_start();

// Include the database configuration
require_once '../database/config.php';

// Define document types at the top so it's available throughout the script
$document_types = ['dti_registration', 'business_permit', 'mayors_permit', 'tin_id'];

// Initialize variables for form data and errors
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $business_name = trim($_POST['business_name']);
    $business_description = trim($_POST['business_description']);
    $service_category = trim($_POST['service_category']);
    $address = trim($_POST['address']);
    $upload_dir = '../uploads/';
    $uploaded_files = [];

    // Validate required fields
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($phone_number)) $errors[] = "Phone number is required.";
    if (empty($business_name)) $errors[] = "Business name is required.";
    if (empty($business_description)) $errors[] = "Business description is required.";
    if (empty($service_category)) $errors[] = "Service category is required.";
    if (empty($address)) $errors[] = "Address is required.";

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
            $file_name = basename($_FILES[$doc_type]['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES[$doc_type]['tmp_name'], $file_path)) {
                $uploaded_files[$doc_type] = $file_path;
            } else {
                $errors[] = "Failed to upload " . str_replace('_', ' ', ucfirst($doc_type));
            }
        } else {
            $errors[] = ucfirst(str_replace('_', ' ', $doc_type)) . " is required.";
        }
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert into `users` table
            $query = "INSERT INTO users (username, password_hash, email, role, full_name, phone_number, address, created_at, updated_at)
                      VALUES (?, ?, ?, 'provider', ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($query);
            $password_hash = password_hash('default_password', PASSWORD_DEFAULT); // Set a default password
            $stmt->bind_param('ssssss', $email, $password_hash, $email, $full_name, $phone_number, $address);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert into `providers` table
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

            // Commit the transaction
            $conn->commit();
            $success = true;
        } catch (Exception $e) {
            // Rollback the transaction on error
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
    <title>GoSeekr - Provider Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Service Provider Registration</h2>

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

        <form method="POST" action="provider_registration.php" enctype="multipart/form-data">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Personal Information</div>
                <div class="card-body">
                    <label class="form-label required-field">Full Name</label>
                    <input type="text" class="form-control" name="full_name" required>
                    <label class="form-label required-field">Email</label>
                    <input type="email" class="form-control" name="email" required>
                    <label class="form-label required-field">Phone Number</label>
                    <input type="tel" class="form-control" name="phone_number" pattern="[0-9]{10}" required>
                    <label class="form-label required-field">Address</label>
                    <input type="text" class="form-control" name="address" required>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Business Information</div>
                <div class="card-body">
                    <label class="form-label required-field">Business Name</label>
                    <input type="text" class="form-control" name="business_name" required>
                    <label class="form-label required-field">Business Description</label>
                    <textarea class="form-control" name="business_description" rows="3" required></textarea>
                    <label class="form-label required-field">Service Category</label>
                    <select class="form-select" name="service_category" required>
                        <option value="">Select Service Category</option>
                        <option value="plumbing">Plumbing</option>
                        <option value="cleaning">Cleaning</option>
                        <option value="electrician">Electrician</option>
                        <option value="carpentry">Carpentry</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Upload Required Documents</div>
                <div class="card-body">
                    <?php foreach ($document_types as $doc_type): ?>
                        <label class="form-label required-field"><?php echo ucfirst(str_replace('_', ' ', $doc_type)); ?></label>
                        <input type="file" class="form-control" name="<?php echo $doc_type; ?>" required>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit Registration</button>
        </form>
    </div>
</body>
</html>
