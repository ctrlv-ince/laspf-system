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
                <form>
                    <!-- Step 1: Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required-field">First Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Middle Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required-field">Last Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Email Address</label>
                                    <input type="email" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+63</span>
                                        <input type="tel" class="form-control" pattern="[0-9]{10}" required>
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
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Business Type</label>
                                    <select class="form-select" required>
                                        <option value="">Select Business Type</option>
                                        <option>Sole Proprietorship</option>
                                        <option>Partnership</option>
                                        <option>Corporation</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">DTI/SEC Registration Number</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Tax Identification Number (TIN)</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required-field">Business Address</label>
                                    <input type="text" class="form-control mb-2" placeholder="Street Address" required>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <select class="form-select" required>
                                                <option value="">Select Region</option>
                                                <option>NCR</option>
                                                <option>Region I</option>
                                                <option>Region II</option>
                                                <!-- Add more regions -->
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select" required>
                                                <option value="">Select Province</option>
                                                <!-- Dynamically populated based on region -->
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select" required>
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
                                        <input type="file" class="d-none" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Mayor's Permit</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">BIR Certificate of Registration</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" accept=".pdf,.jpg,.png" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required-field">Valid Government ID</label>
                                    <div class="upload-box">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Click to upload or drag and drop</p>
                                        <input type="file" class="d-none" accept=".pdf,.jpg,.png" required>
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
                                                <input class="form-check-input" type="checkbox" id="service1">
                                                <label class="form-check-label" for="service1">Home Cleaning</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="service2">
                                                <label class="form-check-label" for="service2">Plumbing</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="service3">
                                                <label class="form-check-label" for="service3">Electrical</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required-field">Service Area</label>
                                    <select class="form-select" multiple required>
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