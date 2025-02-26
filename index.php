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
                        url('/api/placeholder/1920/1080') center/cover no-repeat;
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
                    <li class="nav-item"><a class="nav-link" href="./reviews.php">Reviews</a></li>
                    <li class="nav-item"><a class="btn btn-primary ms-lg-3" href="./providers/provider_registration.php">Join as Provider</a></li>
                    <li class="nav-item"><a class="btn btn-outline-primary ms-lg-2" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-success ms-lg-2" href="./users/user_registration.php">Register as User</a></li>
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
                    <form class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-lg" placeholder="What service do you need?">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary btn-lg w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-5" id="services">
        <div class="container">
            <h2 class="text-center mb-5">Popular Services</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card category-card h-100">
                        <img src="/api/placeholder/400/300" class="card-img-top" alt="Home Cleaning">
                        <div class="card-body">
                            <h5 class="card-title">Home Cleaning</h5>
                            <p class="card-text">Professional cleaning services for your home</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card category-card h-100">
                        <img src="/api/placeholder/400/300" class="card-img-top" alt="Plumbing">
                        <div class="card-body">
                            <h5 class="card-title">Plumbing</h5>
                            <p class="card-text">Expert plumbing repairs and installations</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card category-card h-100">
                        <img src="/api/placeholder/400/300" class="card-img-top" alt="Electrical">
                        <div class="card-body">
                            <h5 class="card-title">Electrical</h5>
                            <p class="card-text">Licensed electricians for all your needs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="bg-light py-5" id="how-it-works">
        <div class="container">
            <h2 class="text-center mb-5">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <i class="fas fa-search feature-icon"></i>
                    <h4>Search</h4>
                    <p>Find the right service provider for your needs</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-calendar-alt feature-icon"></i>
                    <h4>Book</h4>
                    <p>Schedule an appointment at your convenience</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-check-circle feature-icon"></i>
                    <h4>Done</h4>
                    <p>Get your service done by verified professionals</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5" id="testimonials">
        <div class="container">
            <h2 class="text-center mb-5">What Our Customers Say</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="/api/placeholder/80/80" class="testimonial-img mb-3" alt="Customer">
                            <p class="card-text">"Amazing service! Found a great cleaner for my home. Highly recommended!"</p>
                            <h5 class="card-title mb-1">Sarah Johnson</h5>
                            <small class="text-muted">Home Cleaning Customer</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="/api/placeholder/80/80" class="testimonial-img mb-3" alt="Customer">
                            <p class="card-text">"Quick response and professional service. Will use again!"</p>
                            <h5 class="card-title mb-1">Mike Wilson</h5>
                            <small class="text-muted">Plumbing Customer</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="/api/placeholder/80/80" class="testimonial-img mb-3" alt="Customer">
                            <p class="card-text">"Easy booking process and excellent service quality."</p>
                            <h5 class="card-title mb-1">Emily Brown</h5>
                            <small class="text-muted">Electrical Customer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="bg-primary text-white py-5" id="join">
        <div class="container text-center">
            <h2 class="mb-4">Are You a Service Provider?</h2>
            <p class="lead mb-4">Join our platform and grow your business</p>
            <a href="./providers/registrationprovider.php" class="btn btn-light btn-lg px-4">Join Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>GoSeekr</h5>
                    <p>Connecting quality service providers with customers</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Services</a></li>
                        <li><a href="#" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>