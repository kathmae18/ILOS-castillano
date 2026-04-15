<?php
session_start();
include 'conn.php'; 

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in to access the shop page.";
    header("Location: /poultry/login.php");
    exit();
}

// Pagination
$perPage = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

$search = '';
$where = '';
$params = [];
$param_types = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $where = "WHERE p.name LIKE ?";
    $params[] = "%{$search}%";
    $param_types .= "s";
}

// Get total products count
$count_sql = "SELECT COUNT(*) FROM products p $where";
$count_stmt = $conn->prepare($count_sql);
if (!empty($where)) $count_stmt->bind_param($param_types, ...$params);
$count_stmt->execute();
$count_stmt->bind_result($totalProducts);
$count_stmt->fetch();
$count_stmt->close();

// Fetch products with completed_qty
$sql = "
    SELECT p.*, COALESCE(SUM(o.quantity), 0) AS completed_qty
FROM products p
LEFT JOIN orders o ON o.product_id = p.id AND o.status = 'Completed'
GROUP BY p.id
ORDER BY p.id DESC
LIMIT ?, ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start, $perPage);
$stmt->execute();
$result = $stmt->get_result();

$totalPages = ceil($totalProducts / $perPage);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Castillanos Backyard</title>
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
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="../img/logo2.png" type="image/jpeg">

    <style>
        body {
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* full viewport height */
}
    .input-group-text {
        cursor: pointer;
        transition: background 0.3s, color 0.3s;
    }
    .input-group-text:hover {
        background:  #cb6f04ff;
        color: #fff;
    }
    /* Optional: add subtle hover on input */
    .form-control:focus {
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        border-color: #cb6f04ff;
    }
    .product-item {
    position: relative;
    width: 100%;
    max-width: 300px;
    overflow: hidden; /* prevent children from overflowing */
}

.product-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    display: block; /* remove any inline spacing */
}

.out-of-stock-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; 
    height: 100%;
    background: rgba(220, 53, 69, 0.7);
    z-index: 10;
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none; /* allow clicks to pass through */
    border-radius: 0.25rem; /* same as card corners */
}

/* Remove margin-bottom shift on hover */
.product .product-item:hover .product-item-add {
    margin-bottom: 0 !important;
    opacity: 1;
    background: var(--bs-white);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* subtle shadow */
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

/* Make the add-to-cart container fixed height, so it doesn’t cause jump */
.product-item-add {
    border-top: none !important;
    border-radius: 0 0 0.25rem 0.25rem !important;
    padding: 1rem 1.5rem;
    background: white;
    opacity: 0;
    transition: opacity 0.3s ease;
    height: 80px; /* fixed height */
    overflow: hidden;
}

/* Show add-to-cart container on hover */
.product .product-item:hover .product-item-add {
    opacity: 1;
}

/* Dim product content if out of stock */
.product-item-inner.opacity-50 {
    pointer-events: none; /* disable interaction */
    user-select: none;
}

/* Optional: make sure form buttons stay inside container */
.product-item-add form {
    margin-bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

/* Description sizing and truncation
   Target the description anchor which in the template uses class "h4".
   Adjust font-size, color and clamp to two lines with ellipsis. */
.product-item .text-center a.h4 {
    font-size: 0.95rem;          /* smaller than default h4 */
    font-weight: 500;
    color: #6c757d;              /* muted description color */
    margin: 0.25rem 0;
    line-height: 1.2rem;
    /* multi-line truncate to 2 lines */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* If you want the product name (first anchor) to remain larger, ensure selector below won't affect it */
.product-item .text-center a.d-block.mb-2 {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
}


</style>
</head>



<body>

   <?php include 'header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
            <?php if (!empty($message)): ?>
                <div id="successMessage" 
                    class="alert alert-success alert-dismissible fade show text-center mx-auto" 
                    role="alert" 
                    style="max-width: 400px; font-size: 18px; padding: 10px 15px;">
                    <?= htmlspecialchars($message) ?>
                </div>

                <script>
                    setTimeout(() => {
                        const alert = document.getElementById('successMessage');
                        if (alert) {
                            alert.classList.remove('show');
                            alert.classList.add('fade');
                            setTimeout(() => alert.remove(), 500); // remove completely after fade
                        }
                    }, 3000); // 3 seconds
                </script>
            <?php endif; ?>
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Shop Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active text-white">Shop</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <div class="container-fluid shop py-5">
        <div class="container py-5">
            <div class="row justify-content-center g-4">
                <div class="col-12 col-lg-10">
                        <?php
                            $params = [];
                            $param_types = '';
                            $where = '';

                            if (!empty($search)) {
                                $where = "WHERE p.name LIKE ?";
                                $params[] = "%{$search}%";
                                $param_types .= "s";
                            }

                            // Pagination params
                            $params[] = $start;
                            $params[] = $perPage;
                            $param_types .= "ii";

                            $sql = "
                                SELECT p.*, COALESCE(SUM(o.quantity),0) AS completed_qty
                                FROM products p
                                LEFT JOIN orders o ON o.product_id = p.id AND o.status = 'Completed'
                                $where
                                GROUP BY p.id
                                ORDER BY p.id DESC
                                LIMIT ?, ?
                            ";

                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param($param_types, ...$params);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>

                    <!-- Search bar -->
                        <div class="row mb-5">
                            <div class="col-md-8 mx-auto">
                                <form id="searchForm" method="GET" action="shop.php">
                                    <div class="input-group shadow-lg rounded-pill overflow-hidden">
                                        <input type="search" name="search" 
                                            class="form-control form-control-lg border-0 px-4 py-3" 
                                            placeholder="Search products..." 
                                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                            style="font-size: 1.2rem;">
                                        <span class="input-group-text bg-primary text-white border-0 px-4">
                                            <i class="fa fa-search fa-lg"></i>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <script>
                            const searchInput = document.querySelector('#searchForm input[name="search"]');
                            const searchForm = document.getElementById('searchForm');

                            let timeout = null;
                            searchInput.addEventListener('input', function() {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => {
                                    searchForm.submit();
                                }, 500); 
                            });
                        </script>

            

                    <!-- Products Tab -->
                    <div class="tab-content">
                        <div id="tab-5" class="tab-pane fade show p-0 active">
                            <div class="row g-4 justify-content-center product">
                                <?php while ($product = $result->fetch_assoc()): 
                                $added_stock     = (int)$product['added_stock'];
                                $original_stock  = (int)$product['stock'];
                                $completed_qty   = (int)$product['completed_qty'];
                                $reduced_stock   = (int)$product['reduced_stock'];

                                // Calculate available stock including reduced stock
                                $available_stock = $original_stock + $added_stock - $completed_qty - $reduced_stock;
                                if ($available_stock < 0) $available_stock = 0;

                                // Check if admin marked the product unavailable
                                $is_unavailable = isset($product['is_unavailable']) && $product['is_unavailable'] == 1;
                            ?>

                            <!-- Inside your products loop -->
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="product-item rounded wow fadeInUp position-relative" data-wow-delay="<?= $delay ?>s">

                                    <?php if ($available_stock <= 0 || $is_unavailable): ?>
                                    <div class="out-of-stock-overlay d-flex justify-content-center align-items-center">
                                        <span class="text-white fw-bold fs-6 bg-danger px-2 py-1 rounded text-center">
                                            <?= $is_unavailable ? 'This product is currently unavailable' : 'Out of Stock' ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="product-item-inner border rounded <?= ($available_stock <= 0 || $is_unavailable) ? 'opacity-50' : '' ?>">
                                                <!-- Image container -->
                                            <div class="product-item-inner-item" style="height: 200px; overflow: hidden;">
                                                <?php 
                                                    $img_path = !empty($product['picture']) 
                                                                ? '../admin/uploads/products/' . $product['picture'] 
                                                                : '../admin/uploads/products/default.png'; 
                                                ?>
                                                <img src="<?= $img_path ?>" 
                                                    alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>" 
                                                    class="img-fluid w-100 rounded-top" 
                                                    style="height: 100%; width: 100%; object-fit: cover;">
                                            </div>

                                        <div class="text-center rounded-bottom p-4">
                                            <a href="#" class="d-block mb-2"><?= htmlspecialchars($product['name']) ?></a>
                                            <a href="#" class="d-block h4"><?= htmlspecialchars($product['description']) ?></a>
                                            <span class="text-primary fs-5">₱<?= number_format($product['price'], 2) ?></span>
                                            <p class="mb-0 mt-2 text-muted">Available Stock: <?= $available_stock ?></p>
                                        </div>

                                        <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                            <form method="POST" action="process.php" class="w-100 mb-0">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary shadow-sm w-100 py-2 px-3 text-white">
                                                    <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php endwhile; ?>

                            </div>

                            <!-- Pagination -->
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="pagination d-flex justify-content-center mt-5">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">&laquo;</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                                        class="<?php echo $i === $page ? 'active' : ''; ?> rounded"><?php echo $i; ?></a>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">&raquo;</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


<!--this is for the quantity of the products -->
<script>
    document.querySelectorAll('.btn-plus').forEach(button => {
    button.addEventListener('click', () => {
        let input = button.closest('.input-group').querySelector('input[name="quantity"]');
        input.value = parseInt(input.value) + 1;
    });
});

document.querySelectorAll('.btn-minus').forEach(button => {
    button.addEventListener('click', () => {
        let input = button.closest('.input-group').querySelector('input[name="quantity"]');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    });
});

</script>


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>


  <!-- JavaScript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../lib/wow/wow.min.js"></script>
<script src="../lib/owlcarousel/owl.carousel.min.js"></script>


   <?php include '../footer.php'; ?>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>