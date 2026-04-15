<?php
session_start();

// Collect messages and add fade-alert class
$alerts = [];
if (isset($_SESSION['error_message'])) {
    $alerts[] = '<div class="alert alert-warning text-center w-75 mx-auto my-3 fade-alert" role="alert">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['message'])) {
    $alerts[] = '<div class="alert alert-success text-center w-75 mx-auto my-3 fade-alert" role="alert">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // Redirect to shop page (or dashboard)
    header("Location: /poultry/customer/shop.php");
    exit();
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: /poultry/admin/admin_dashboard.php"); // redirect to dashboard
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Castillano's Backyard - Home</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
     <link rel="icon" href="img/logo2.png" type="image/png">

    <style>
       .product-item.position-relative {
    position: relative;
}

.out-of-stock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(220, 53, 69, 0.7); /* semi-transparent red */
    z-index: 10;
    border-radius: 0.25rem; /* match card rounding */
    pointer-events: none; /* allow clicking through if needed */
}
.product .product-item:hover .product-item-add {
    margin-bottom: 0; 
    opacity: 1;
    background: var(--bs-white);
    /* Optional: add a subtle box-shadow or border */
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: background 0.3s ease, box-shadow 0.3s ease;
  }

  .product-item .product-item-inner .text-center a.d-block.mb-2 {
    font-size: 1.25rem; /* Adjust the size as needed */
    font-weight: 500; /* Make it bolder */
    color: #343a40; /* Darken the color for better readability */
    margin-bottom: 0.5rem !important; /* Slightly increase spacing */
    transition: color 0.3s ease; /* Smooth transition for hover effect */
}

.product-item .product-item-inner .text-center a.d-block.mb-2:hover {
    color: var(--bs-primary); /* Change color on hover */
    text-decoration: none; /* Remove underline on hover */
}

.product-item .product-item-inner .text-center a.d-block.h4 {
    font-size: 1rem; /* Adjust the size as needed */
    font-weight: normal; /* Keep it regular */
    color: #6c757d; /* Use a muted color */
    margin-bottom: 0; /* Reset default margin */
}
</style>
     </style>

</head>

<body>
<?php include 'header.php'; ?>



<!-- Carousel Start -->
<div class="container-fluid carousel bg-light px-0">
    <div class="row g-0 justify-content-center">
        <div class="col-12 col-lg-7 col-xl-9">
            <div class="header-carousel owl-carousel bg-light py-5">

                <!-- Carousel Item 1: Eggs -->
                <div class="row g-0 header-carousel-item align-items-center">
                    <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                        <img src="img/eggs.jpg" class="img-fluid w-100" alt="Fresh Eggs">
                    </div>
                    <div class="col-xl-6 carousel-content p-4 text-center text-xl-start">
                        <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                            style="letter-spacing: 3px;">Fresh Eggs </h4>
                        <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">
                            High-Quality Eggs Straight From Our Backyard
                        </h1>
                        <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">
                            Fresh eggs for your home or business.
                        </p>
                        <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="login.php">Shop Eggs</a>
                    </div>
                </div>

                <!-- Carousel Item 2: Live Pigs -->
                <div class="row g-0 header-carousel-item align-items-center">
                    <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                        <img src="img/pigs.jpg" class="img-fluid w-100" alt="Live Pigs">
                    </div>
                    <div class="col-xl-6 carousel-content p-4 text-center text-xl-start">
                        <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                            style="letter-spacing: 3px;">Live Pigs</h4>
                        <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">
                            Buy Live Pigs our Backyard
                        </h1>
                        <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">
                            Premium breeds raised for quality and productivity.
                        </p>
                        <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="login.php">Shop Pigs</a>
                    </div>
                </div>

                <!-- Carousel Item 3: AI for Pigs -->
                <div class="row g-0 header-carousel-item align-items-center">
                    <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                        <img src="img/ai-pigs.jpg" class="img-fluid w-100" alt="Artificial Insemination">
                    </div>
                    <div class="col-xl-6 carousel-content p-4 text-center text-xl-start">
                        <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                            style="letter-spacing: 3px;">AI Services for Pigs</h4>
                        <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">
                            Improve Your Pig Breeding Efficiency
                        </h1>
                        <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">
                            Professional artificial insemination for healthy litters.
                        </p>
                        <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="login.php">Learn More</a>
                    </div>
                </div>

                <!-- Carousel Item 4: Flattening Tools -->
                <div class="row g-0 header-carousel-item align-items-center">
                    <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                        <img src="img/c.jpeg" class="img-fluid w-100" alt="chicken">
                    </div>
                    <div class="col-xl-6 carousel-content p-4 text-center text-xl-start">
                        <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                            style="letter-spacing: 3px;">Chicken</h4>
                        <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">
                          Fresh Live Chickens Delivered to Your Door 
                        </h1>
                        <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">
                            
                        </p>
                        <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="login.php">Shop Tools</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Carousel End -->
<!-- Our Products Start -->
<div class="container-fluid product py-5">
    <div class="container py-5">

        <!-- Section Title -->
        <h2 class="text-center mb-5">Our Products</h2>

        <div class="tab-class">
            <div class="row g-4">
                <?php
                include 'conn.php';

                $delay = 0.1;
                $query = "
                    SELECT p.*,
                        IFNULL(SUM(oi.quantity), 0) AS completed_qty
                    FROM products p
                    LEFT JOIN orders oi 
                        ON p.id = oi.product_id 
                        AND oi.status = 'completed'
                    GROUP BY p.id
                    ORDER BY p.id ASC
                ";

                $result = mysqli_query($conn, $query);

                while($product = mysqli_fetch_assoc($result)) {
                    $delay += 0.2;

                    $added_stock    = (int)$product['added_stock'];
                    $original_stock = (int)$product['stock'];
                    $completed_qty  = (int)($product['completed_qty'] ?? 0);
                    $reduced_stock  = (int)($product['reduced_stock'] ?? 0);
                    $available_stock = $original_stock + $added_stock - $completed_qty - $reduced_stock;
                    if ($available_stock < 0) $available_stock = 0;

                    // Check if admin marked the product as unavailable
                    $is_unavailable = isset($product['is_unavailable']) && $product['is_unavailable'] == 1;

                    // Get image path
                    $imageFile = $product['picture'] ?? '';
                    $serverPath = __DIR__ . '/admin/uploads/products/' . $imageFile;
                    $webPath = 'admin/uploads/products/default.png';
                    if (!empty($imageFile) && file_exists($serverPath)) {
                        $webPath = 'admin/uploads/products/' . $imageFile;
                    }
                ?>
           <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="product-item rounded wow fadeInUp position-relative" data-wow-delay="<?= $delay ?>s">

                    <?php if ($available_stock <= 0 || $is_unavailable): ?>
                    <!-- Overlay when out of stock or unavailable -->
                    <div class="out-of-stock-overlay d-flex justify-content-center align-items-center">
                        <span class="text-white fw-bold fs-6 bg-danger px-2 py-1 rounded text-center">
                            <?= $is_unavailable ? 'This product is currently unavailable' : 'Out of Stock' ?>
                        </span>
                    </div>

                    <?php endif; ?>

                    <div class="product-item-inner border rounded <?= ($available_stock <= 0 || $is_unavailable) ? 'opacity-50' : '' ?>">
                        <!-- Image container -->
                        <div class="product-item-inner-item" style="height: 200px; overflow: hidden;">
                            <img src="<?= $webPath ?>" 
                                class="img-fluid w-100 rounded-top" 
                                alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>"
                                style="height: 100%; width: 100%; object-fit: cover;">
                        </div>
                        <div class="text-center rounded-bottom p-4">
                            <a href="#" class="d-block mb-2"><?= htmlspecialchars($product['name'] ?? 'Product') ?></a>
                            <a href="#" class="d-block h4"><?= htmlspecialchars($product['description'] ?? '') ?></a>
                            <span class="text-primary fs-5">₱<?= number_format($product['price'] ?? 0, 2) ?></span>
                            <p class="mb-0 mt-2 text-muted">Available Stock: <?= $available_stock ?></p>
                        </div>
                        <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                            <form method="POST" action="process.php" class="mb-4 d-flex justify-content-center align-items-center">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                <?php if ($available_stock > 0 && !$is_unavailable): ?>
                                    <div class="input-group me-2" style="width: 140px;">
                                        <a href="login.php" class="btn btn-primary shadow-sm w-100 py-2 px-3 text-white text-center">
                                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                        </a>
                                    </div>
                                <?php endif; ?>

                            </form>
                        </div>
                    </div>  
                </div>
            </div>
            <?php } ?>  
            </div>
        </div>
    </div>
</div>
    <!-- Our Products End -->
     <?php include 'footer.php'; ?>


<script>
// Wait 3 seconds, then fade out and remove the alert
setTimeout(() => {
    const alerts = document.querySelectorAll('.fade-alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500); // remove after fade
    });
}, 3000);

</script>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>


    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>