<?php
session_start();

// Include the database configuration
require_once '../database/config.php';

// Fetch all services from the database
$query = "
    SELECT s.service_id, s.service_name, s.service_description, s.price, s.duration_minutes,
           p.business_name, p.service_category
    FROM services s
    JOIN providers p ON s.provider_id = p.provider_id
    ORDER BY s.service_name ASC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);

// Fetch unique service categories for filtering
$query = "SELECT DISTINCT service_category FROM providers";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Services - GoSeekr</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .service-card {
            transition: transform 0.2s;
        }
        .service-card:hover {
            transform: translateY(-5px);
        }
        .category-filter {
            cursor: pointer;
        }
        .category-filter.active {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="user_index.php">GoSeekr</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_bookings.php">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Find Services</h1>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <form method="GET" action="user_index.php">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search for services...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h5>Filter by Category:</h5>
                <div class="btn-group flex-wrap">
                    <button type="button" class="btn btn-outline-secondary category-filter" data-category="all">
                        All
                    </button>
                    <?php foreach ($categories as $category): ?>
                        <button type="button" class="btn btn-outline-secondary category-filter" data-category="<?php echo htmlspecialchars($category['service_category']); ?>">
                            <?php echo htmlspecialchars($category['service_category']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Service Listings -->
        <div class="row" id="service-listings">
            <?php if (empty($services)): ?>
                <div class="col-12 text-center">
                    <p>No services found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="col-md-4 mb-4 service-card" data-category="<?php echo htmlspecialchars($service['service_category']); ?>">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?php echo htmlspecialchars($service['business_name']); ?>
                                </h6>
                                <p class="card-text"><?php echo htmlspecialchars($service['service_description']); ?></p>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Price:</strong> ₱<?php echo number_format($service['price'], 2); ?>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Duration:</strong> <?php echo $service['duration_minutes']; ?> mins
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Category:</strong> <?php echo htmlspecialchars($service['service_category']); ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <a href="book_service.php?service_id=<?php echo $service['service_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-calendar-check"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category Filter Functionality
        const categoryFilters = document.querySelectorAll('.category-filter');
        const serviceCards = document.querySelectorAll('.service-card');

        categoryFilters.forEach(filter => {
            filter.addEventListener('click', () => {
                // Remove active class from all filters
                categoryFilters.forEach(f => f.classList.remove('active'));
                // Add active class to the clicked filter
                filter.classList.add('active');

                const selectedCategory = filter.getAttribute('data-category');

                // Show/hide services based on the selected category
                serviceCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    if (selectedCategory === 'all' || cardCategory === selectedCategory) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
