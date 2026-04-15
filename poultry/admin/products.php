
<?php
session_start();
include 'conn.php'; 

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['message'] = '<div class="alert alert-danger text-center">
        You are not authorized to open this page. Please log in.
    </div>';
    header("Location: admin_login.php");
    exit();
}

// Fetch products
$product_result = $conn->query("
    SELECT 
        p.*, 
        COALESCE(SUM(o.quantity), 0) AS completed_qty
    FROM products p
    LEFT JOIN orders o ON o.product_id = p.id AND o.status = 'Completed'
    GROUP BY p.id
");


// Fetch pending orders
$order_result = $conn->query("
    SELECT 
        o.id, 
        c.name AS customer_name, 
        p.name AS product_name, 
        o.quantity, 
        (o.quantity * p.price) AS total_price, 
        o.status, 
        o.created_at
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Pending'
    ORDER BY o.created_at DESC
");

if (!$product_result || !$order_result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Castillano Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Bootstrap & Custom CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" href="../img/logo3.png" type="image/png">
    <style>
        /* Action buttons hover effect */
/* Action Buttons */
/* Action buttons hover effect */
/* Action Buttons */
td .btn {
    font-size: 14px; /* Reduced font size */
    padding: 0.25rem 0.5rem; /* Adjusted padding */
    border-radius: 0.25rem; /* Adjusted border radius */
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
     margin-right: 0.5rem; /* Add spacing between buttons */
}
td .btn:last-child {
    margin-right: 0; /* Remove right margin from the last button */
}

td .btn-warning {
    background-color: #fbba59ff;
    border-color: #ffb84d;
    color: black;
}

td .btn-warning:hover {
    background-color: #ffa31a;
    border-color: #ffa31a;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    color:  white;
}

td .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color:black;
}

td .btn-success:hover {
    background-color: #218838;
    border-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

td .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: black;
}

td .btn-danger:hover {
    background-color: #c82333;
    border-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card{
    min-height: 650px;
}
td .btn-light {
    background-color: #fc7860ff; /* light gray */
    color: #212529; /* dark text */
    border: 1px solid #fc7860ff;
}

td .btn-light:hover {
    background-color: #f03616ff; /* slightly darker */
    color: #f6f7f8ff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#productsTable td:nth-child(2) {
    width: 20%; /* Adjust as needed */
    font-weight: bold;
}

#productsTable td:nth-child(3) {
    width: 30%; /* Adjust as needed */
    font-size: 0.9em;
}  </style>
</head>
<body>

<?php include 'header.php'; ?>

<?php if (!empty($message)): ?>
<div class="alert alert-success text-center w-75 mx-auto my-3 fade-alert" role="alert">
    <?= $message ?>
</div>
<?php endif; ?>

<div class="container py-5">

    <!-- Add Product Button -->
    <div class="d-flex justify-content-end mb-4">
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus me-1"></i> Add Product
        </button>
    </div>

    <!-- Search Input -->

   <div class="card shadow-sm mb-5">
    <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-center">
        <h4 class="mb-2 mb-md-0">Products</h4>
        <input type="text" id="productSearch" class="form-control w-50 w-md-50" placeholder="Search products by name or description...">
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-bordered table-hover text-center align-middle" id="productsTable">
            <thead class="table-primary">
                <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Original Stock</th>
                        <th>Added Stock</th>
                        <th>Available Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $count = 1;
                    if ($product_result->num_rows > 0):
                        while($row = $product_result->fetch_assoc()):
                            $added_stock     = (int)$row['added_stock'];
                            $original_stock  = (int)$row['stock'];
                            $completed_qty   = (int)$row['completed_qty'];
                            $reduced_stock   = (int)$row['reduced_stock'];
                            $available_stock = $original_stock + $added_stock - $completed_qty - $reduced_stock;
                            if ($available_stock < 0) $available_stock = 0;



                    ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td><?= (int)$row['stock'] ?></td>
                        <td><?= $added_stock ?></td>
                        <td><?= $available_stock ?></td>
                        <td>
                            <img src="uploads/products/<?= $row['picture'] ?>" width="50" class="img-thumbnail clickable-image" data-bs-toggle="modal" data-bs-target="#imageModal<?= $row['id'] ?>" style="cursor:pointer;" alt="Product Image">
                        </td>
                        <td>
                            <div class="text-center mb-1">
                                <?php if ($available_stock <= 0 && $row['is_unavailable'] != 1): ?>
                                    <span class="badge bg-danger w-100">Out of Stock</span>
                                <?php elseif ($row['is_unavailable'] == 1): ?>
                                    <span class="badge bg-warning text-dark w-100">Currently Unavailable</span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-wrap justify-content-center align-items-center gap-1">
                                <!-- Update button always available -->
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['id'] ?>">Update</button>

                                <?php if ($row['is_unavailable'] != 1): ?>
                                    <!-- Add Stock button always visible if not unavailable -->
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addStockModal<?= $row['id'] ?>">+ Add</button>

                                    <!-- Reduce Stock only if stock > 0 -->
                                    <?php if ($available_stock > 0): ?>
                                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#reduceStockModal<?= $row['id'] ?>">- Reduce</button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Remove button always available -->
                                <a href="process.php?delete_product=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Remove</a>
                            </div>
                        </td>

         <!-- reduce Product Modal -->
                    <div class="modal fade" id="reduceStockModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="reduceStockModalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #fc7860ff; color: #eb707aff;">
                                    <h5 class="modal-title" id="reduceStockModalLabel<?= $row['id'] ?>">Reduce Stock - <?= htmlspecialchars($row['name']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form action="process.php" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Current Available Stock: <?= $available_stock ?></label>
                                            <input type="number" class="form-control" name="reduce_stock" placeholder="Enter quantity to reduce" min="1" max="<?= $available_stock ?>" required>
                                            <small class="text-muted">You cannot reduce more than the available stock.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="reduce_stock_submit" class="btn btn-primary">Reduce Stock</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
  <!-- Edit Product Modal -->
                    <div class="modal fade" id="editProductModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editProductModalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="editProductModalLabel<?= $row['id'] ?>">Edit Product</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="process.php" method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">

                                        <?php
                                            $added_stock = (int)$row['added_stock'];
                                            $original_stock = (int)$row['stock'];
                                            $completed_qty = (int)($row['completed_qty'] ?? 0);
                                            $available_stock = $original_stock + $added_stock - $completed_qty;
                                            if ($available_stock < 0) $available_stock = 0;
                                        ?>

                                        <!-- Out of Stock Alert -->
                                        <?php if ($available_stock <= 0): ?>
                                            <div class="alert alert-danger text-center">
                                                ⚠ This product is currently unavailable (Out of Stock)
                                            </div>
                                        <?php endif; ?>

                                        <!-- Manual Unavailable Checkbox -->
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" name="is_unavailable" id="isUnavailable<?= $row['id'] ?>"
                                                value="1" <?= ($row['is_unavailable'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="isUnavailable<?= $row['id'] ?>">Mark as Unavailable</label>
                                        </div>

                                        <?php if (($row['is_unavailable'] ?? 0) == 1): ?>
                                            <div class="alert alert-warning text-center">
                                                ⚠ This product is currently marked as unavailable
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Price</label>
                                            <input type="number" step="0.01" class="form-control" name="price" value="<?= $row['price'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stock Quantity</label>
                                            <input type="number" class="form-control" name="stock" value="<?= $row['stock'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Product Image</label>
                                            <input type="file" class="form-control" name="picture" accept="image/*">
                                            <small class="text-muted">Current: <?= $row['picture'] ?></small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="edit_product" class="btn btn-warning">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Add Stock Modal -->
                    <div class="modal fade" id="addStockModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="addStockModalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="addStockModalLabel<?= $row['id'] ?>">Add Stock - <?= htmlspecialchars($row['name']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="process.php" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Current Available Stock: <?= $available_stock ?></label>
                                            <input type="number" class="form-control" name="added_stock" placeholder="Enter stock to add" min="1" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="add_stock" class="btn btn-success">Add Stock</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php
                        endwhile;
                    else:
                        echo "<tr><td colspan='9'>No products found.</td></tr>";
                    endif;
                ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="process.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter product name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" placeholder="Enter product price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Enter product description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock" placeholder="Enter product stock" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="picture" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_product" class="btn btn-warning">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Reduce Stock Modal -->


</div> <!-- container -->
<script>
  setTimeout(() => {
    const alert = document.querySelector('.fade-alert');
    if (alert) {
      alert.style.transition = 'opacity 0.5s ease-out';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500); 
    }
  }, 3000); 
</script>

<script>
    // Fade alert
    setTimeout(() => {
        const alert = document.querySelector('.fade-alert');
        if (alert) {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500); 
        }
    }, 3000);

    // Product search filter
    const searchInput = document.getElementById('productSearch');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const desc = row.cells[2].textContent.toLowerCase();
            row.style.display = (name.includes(filter) || desc.includes(filter)) ? '' : 'none';
        });
    });
</script>

<?php include '../footer.php'; ?>


<a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

<!-- JS Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>
