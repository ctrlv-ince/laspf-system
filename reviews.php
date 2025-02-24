<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSeekr - Reviews</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .star-rating {
            color: #ffc107;
        }
        .review-img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
        }
        .verified-badge {
            background-color: #198754;
            color: white;
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
        }
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        .review-card {
            transition: transform 0.2s;
        }
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar (same as landing page) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="./index.php">GoSeekr</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#reviews">Reviews</a></li>
                    <li class="nav-item"><a class="btn btn-primary ms-lg-3" href="#join">Join as Provider</a></li>
                    <li class="nav-item"><a class="btn btn-outline-primary ms-lg-2" href="#login">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Reviews Header -->
    <header class="bg-primary text-white py-5">
        <div class="container">
            <h1 class="display-5 fw-bold">Customer Reviews</h1>
            <p class="lead">See what our community is saying about our service providers</p>
        </div>
    </header>

    <!-- Filter Section -->
    <section class="py-4">
        <div class="container">
            <div class="filter-section p-4 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select">
                            <option selected>All Categories</option>
                            <option>Home Cleaning</option>
                            <option>Plumbing</option>
                            <option>Electrical</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option selected>Rating</option>
                            <option>5 Stars</option>
                            <option>4+ Stars</option>
                            <option>3+ Stars</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option selected>Sort By</option>
                            <option>Most Recent</option>
                            <option>Highest Rated</option>
                            <option>Most Helpful</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="py-4">
        <div class="container">
            <!-- Overall Rating Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <h2 class="display-4 mb-0">4.8</h2>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="text-muted mb-0">Based on 2,456 reviews</p>
                        </div>
                        <div class="col-md-9">
                            <div class="row align-items-center mb-2">
                                <div class="col-3">5 stars</div>
                                <div class="col">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 75%"></div>
                                    </div>
                                </div>
                                <div class="col-2">75%</div>
                            </div>
                            <div class="row align-items-center mb-2">
                                <div class="col-3">4 stars</div>
                                <div class="col">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 15%"></div>
                                    </div>
                                </div>
                                <div class="col-2">15%</div>
                            </div>
                            <div class="row align-items-center mb-2">
                                <div class="col-3">3 stars</div>
                                <div class="col">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 6%"></div>
                                    </div>
                                </div>
                                <div class="col-2">6%</div>
                            </div>
                            <div class="row align-items-center mb-2">
                                <div class="col-3">2 stars</div>
                                <div class="col">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 3%"></div>
                                    </div>
                                </div>
                                <div class="col-2">3%</div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-3">1 star</div>
                                <div class="col">
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 1%"></div>
                                    </div>
                                </div>
                                <div class="col-2">1%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Reviews -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card review-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="/api/placeholder/64/64" class="review-img me-3" alt="User">
                                <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">Home Cleaning Service</small>
                                    <span class="ms-2 verified-badge">Verified Customer</span>
                                </div>
                            </div>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="card-title">Exceptional Cleaning Service!</h5>
                            <p class="card-text">Maria did an amazing job cleaning our home. She was thorough, professional, and paid attention to every detail. Would definitely recommend her services!</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">2 days ago</small>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="far fa-thumbs-up"></i> Helpful (24)
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="far fa-comment"></i> Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card review-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="/api/placeholder/64/64" class="review-img me-3" alt="User">
                                <div>
                                    <h6 class="mb-0">Mike Wilson</h6>
                                    <small class="text-muted">Plumbing Service</small>
                                    <span class="ms-2 verified-badge">Verified Customer</span>
                                </div>
                            </div>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <h5 class="card-title">Quick and Professional Service</h5>
                            <p class="card-text">John arrived on time and fixed our leaking pipe efficiently. He explained everything clearly and cleaned up afterward. Very professional service.</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">1 week ago</small>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="far fa-thumbs-up"></i> Helpful (18)
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="far fa-comment"></i> Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
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
    </section>

    <!-- Footer (same as landing page) -->
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