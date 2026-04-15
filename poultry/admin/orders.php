<?php
session_start();
include 'conn.php'; 

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['message'] = '<div class="alert alert-danger text-center">
        You are not authorized to open this page. Please log in.
    </div>';
    header("Location: admin_login.php");
    exit();
}

// Fetch pending orders
$pending_orders = $conn->query("
    SELECT 
        o.id, 
        c.name AS customer_name, 
        c.email,
        c.phone,
        c.address,
        p.name AS product_name, 
        o.quantity, 
        (o.quantity * p.price) AS total_price, 
        o.status, 
        o.delivery_method,   -- ✅ Added
        o.created_at
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Pending'
    ORDER BY o.created_at DESC
");

// Fetch processing orders
$processing_orders = $conn->query("
    SELECT 
        o.id, 
        c.name AS customer_name, 
        c.email,
        c.phone,
        c.address,
        p.name AS product_name, 
        o.quantity, 
        (o.quantity * p.price) AS total_price, 
        o.status, 
        o.delivery_method,   -- ✅ Added
        o.created_at
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Processing'
    ORDER BY o.created_at DESC
");

// Fetch delivering orders
$delivering_orders = $conn->query("
    SELECT 
        o.id, 
        c.name AS customer_name, 
        c.email,
        c.phone,
        c.address,
        p.name AS product_name, 
        o.quantity, 
        (o.quantity * p.price) AS total_price, 
        o.status, 
        o.delivery_method,   -- ✅ Added
        o.created_at
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Delivering'
    ORDER BY o.created_at DESC
");

// Fetch completed orders
$completed_orders = $conn->query("
    SELECT 
        o.id,
        c.name AS customer_name,
        c.email,
        c.phone,
        c.address,
        p.name AS product_name,
        o.quantity,
        (o.quantity * p.price) AS total_price,
        o.status,
        o.delivery_method,   -- ✅ Added
        o.created_at
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Completed'
    ORDER BY o.created_at DESC
");

// Fetch cancelled orders
$cancelled_orders = $conn->query("
    SELECT 
        o.id, 
        c.name AS customer_name, 
        c.email,
        c.phone,
        c.address,
        p.name AS product_name, 
        o.quantity, 
        (o.quantity * p.price) AS total_price, 
        o.status, 
        o.delivery_method,   -- ✅ Added
        o.created_at
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Cancelled'
    ORDER BY o.created_at DESC
");


$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
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

    <!-- Bootstrap & Custom CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" href="../img/logo2.png" type="image/png">
    <style>
        .tab-pane table tbody {
            min-height: 700px; /* adjust as needed */
            display: block;          /* make tbody block so min-height works */
        }

        .tab-pane table thead,
        .tab-pane table tbody tr {
            display: table;          /* keep rows and header as table */
            width: 100%;
            table-layout: fixed;
        }

        /* Active tab full primary background */
        .nav-tabs .nav-link.active {
            background-color: #ef9c21ff !important;
            color: #fff !important;
            font-weight: 600;
        }
        .nav-tabs .nav-link:hover {
            background-color: #fd790d1a;
            color: #000001ff;
        }

/* Status colors with background */

.completed{
    background-color: #b3f5b3ff ;
}
.status-pending {
    background-color: #ef9c21;  /* Orange */
    color: #fff;                /* White text */
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
}

.status-processing {
    background-color: #6f42c1;  /* Purple */
    color: #fff;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 14px;
}

.status-delivering {
    background-color: #198754;  /* Green */
    color: #fff;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
}

.status-completed {
    background-color: #7de97dff;  /* Gray */
    color: #fff;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
}

.status-cancelled {
    background-color: #dc3545;  /* Red */
    color: #fff;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
}

.table-info th {
    background-color: #d4c7efff !important;
    color: black; 
}
.card{
    margin-top: 80px; 
}
/* Uniform font size for all tables */
.container table th,
.container table td {
    color: black !important;
    font-size: 14px; /* Set your preferred size */
}

</style>
</head>
<body>

<?php include 'header.php'; ?>

<?php if (!empty($message)): ?>
<div class="alert alert-success text-center w-75 mx-auto my-3 fade-alert" role="alert">
    <?= $message ?>
</div>
<?php endif; ?>
<div class="container py-5">
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Orders</h4>
        </div>
        <div class="card-body">

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="ordersTab" role="tablist">
                <li class="nav-item"><button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending</button></li>
                <li class="nav-item"><button class="nav-link" id="processing-tab" data-bs-toggle="tab" data-bs-target="#processing" type="button" role="tab">Processing</button></li>
                <li class="nav-item"><button class="nav-link" id="delivering-tab" data-bs-toggle="tab" data-bs-target="#delivering" type="button" role="tab">Delivering</button></li>
                <li class="nav-item"><button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">Completed</button></li>
                <li class="nav-item"><button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">Cancelled</button></li>
            </ul>

            <!-- Tab Panes -->
        <div class="tab-content" id="ordersTabContent">

            <!-- Pending Orders -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Mode of Delivery</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            if ($pending_orders->num_rows > 0):
                                while($row = $pending_orders->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= (int)$row['quantity'] ?></td>
                                <td>₱<?= number_format($row['total_price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['delivery_method']) ?></td>
                                <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                                <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionsDropdown<?= $row['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">Update</button>
                                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown<?= $row['id'] ?>">
                                            <li><a class="dropdown-item" href="process.php?processing_order=<?= $row['id'] ?>">Processing</a></li>
                                            <li><a class="dropdown-item text-danger" href="process.php?cancel_order=<?= $row['id'] ?>">Cancel</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: echo "<tr><td colspan='12'>No pending orders found.</td></tr>"; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Processing Orders -->
            <div class="tab-pane fade" id="processing" role="tabpanel" aria-labelledby="processing-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-info">
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Mode of Delivery</th> 
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            if ($processing_orders->num_rows > 0):
                                while($row = $processing_orders->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= (int)$row['quantity'] ?></td>
                                <td>₱<?= number_format($row['total_price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['delivery_method']) ?></td> 
                                <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                                <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionsDropdown<?= $row['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">Update</button>
                                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown<?= $row['id'] ?>">
                                            <li><a class="dropdown-item" href="process.php?delivering_order=<?= $row['id'] ?>">Delivering</a></li>
                                            <li><a class="dropdown-item text-danger" href="process.php?cancel_order=<?= $row['id'] ?>">Cancel</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: echo "<tr><td colspan='12'>No processing orders found.</td></tr>"; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivering Orders -->
            <div class="tab-pane fade" id="delivering" role="tabpanel" aria-labelledby="delivering-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Mode of Delivery</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            if ($delivering_orders->num_rows > 0):
                                while($row = $delivering_orders->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= (int)$row['quantity'] ?></td>
                                <td>₱<?= number_format($row['total_price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['delivery_method']) ?></td> 
                                <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                                <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="actionsDropdown<?= $row['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">Update</button>
                                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown<?= $row['id'] ?>">
                                            <li><a class="dropdown-item" href="process.php?completed_order=<?= $row['id'] ?>">Complete</a></li>
                                            <li><a class="dropdown-item text-danger" href="process.php?cancel_order=<?= $row['id'] ?>">Cancel</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: echo "<tr><td colspan='11'>No delivering orders found.</td></tr>"; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="completed">
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Mode of Delivery</th> 
                                <th>Status</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            if ($completed_orders->num_rows > 0):
                                while($row = $completed_orders->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= (int)$row['quantity'] ?></td>
                                <td>₱<?= number_format($row['total_price'], 2) ?></td>
                                <td><?= htmlspecialchars($row['delivery_method']) ?></td>
                                <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                                <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; else: echo "<tr><td colspan='10'>No completed orders found.</td></tr>"; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cancelled Orders -->
            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Mode of Delivery</th> 
                                <th>Status</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if ($cancelled_orders->num_rows > 0):
                                while($row = $cancelled_orders->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= (int)$row['quantity'] ?></td>
                                    <td>₱<?= number_format($row['total_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['delivery_method']) ?></td>
                                    <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                                    <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                                </tr>
                            <?php 
                                endwhile; 
                            else: 
                                echo "<tr><td colspan='9'>No cancelled orders found.</td></tr>"; 
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>


<?php include '../footer.php'; ?>

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

<a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

<!-- JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
