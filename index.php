<?php
session_start();

// Include the database configuration
require_once 'database/config.php';

// Fetch featured services
$query = "
    SELECT s.service_id, s.service_name, s.service_description, s.price, p.business_name, p.service_category
    FROM services s
    JOIN providers p ON s.provider_id = p.provider_id
    ORDER BY RAND()
    LIMIT 3
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$featured_services = $result->fetch_all(MYSQLI_ASSOC);

// Fetch unique service categories
$query = "SELECT DISTINCT service_category FROM providers";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Fetch latest reviews
$query = "
    SELECT r.rating, r.comment, u.full_name AS customer_name, p.business_name
    FROM reviews r
    JOIN users u ON r.customer_id = u.user_id
    JOIN providers p ON r.provider_id = p.provider_id
    ORDER BY r.created_at DESC
    LIMIT 3
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$testimonials = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSeekr: Smart Local Provider Finder and Agency Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('/images/hero.jpg') center/cover no-repeat;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .category-card {
            transition: transform 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-5px);
        }
        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">GoSeekr</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
                    <li class="nav-item"><a class="nav-link" href="reviews.php">Reviews</a></li>
                    <li class="nav-item"><a class="btn btn-primary ms-lg-3" href="providers/provider_registration.php">Join as Provider</a></li>
                    <li class="nav-item"><a class="btn btn-outline-primary ms-lg-2" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-success ms-lg-2" href="users/user_registration.php">Register as User</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Find Trusted Local Service Providers</h1>
                    <p class="lead mb-4">Connect with verified professionals for all your service needs. Book appointments easily and get the job done right.</p>
                    <form class="row g-3" action="user_bookings.php" method="GET">
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-lg" name="search" placeholder="What service do you need?">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-lg w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Services -->
    <section class="py-5" id="services">
        <div class="container">
            <h2 class="text-center mb-5">Popular Services</h2>
            <div class="row g-4">
                <?php foreach ($featured_services as $service): ?>
                    <div class="col-md-4">
                        <div class="card category-card h-100">
                            <img src="images/service-placeholder.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($service['service_description']); ?></p>
                                <p class="text-muted"><small>Provider: <?php echo htmlspecialchars($service['business_name']); ?></small></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5" id="testimonials">
        <div class="container">
            <h2 class="text-center mb-5">What Our Customers Say</h2>
            <div class="row g-4">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="images/user-placeholder.jpg" class="testimonial-img mb-3" alt="Customer">
                                <p class="card-text">"<?php echo htmlspecialchars($testimonial['comment']); ?>"</p>
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($testimonial['customer_name']); ?></h5>
                                <small class="text-muted">Service Provider: <?php echo htmlspecialchars($testimonial['business_name']); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> GoSeekr. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
