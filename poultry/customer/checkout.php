<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /poultry/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for this user
$stmt = $conn->prepare("
    SELECT o.id, p.name AS product_name, o.quantity, o.status, o.created_at
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.customer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Castillano's Backyard</title>
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
    <link rel="icon" href="../img/logo2.png" type="image/png">

<style>
.badge-purple {
    background-color: #6f42c1; /* Bootstrap's purple shade */
    color: #fff;
 
}
#ordersTable td, 
#ordersTable th {
    text-align: center;
    vertical-align: middle;
}
.status-badge {
    font-size: 14px;
    padding: 0.35em 0.6em;
}
.container.my-5 {
    min-height: 800px; /* adjust as needed */
}

/* Optional: vertically center "no orders" message if table is empty */
#ordersTable tbody:empty::before {
    content: "No orders found.";
    display: block;
    text-align: center;
    font-size: 1.2rem;
    color: #555;
    padding: 100px 0; /* adjust vertical spacing */
     
}
.card{
margin-top: 150px; /* adjust the value as needed */
}
</style>



</head>

<body>
 <?php include 'header.php'; ?>

<div class="container my-5">
    <div class="card mb-4">
        <!-- Card Header -->
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #ff7f00; color: white;">
            <h3 class="mb-0">My Orders</h3>
            <input type="text" id="orderSearch" class="form-control w-50" placeholder="Search orders by product or status..." style="border-radius: 0.25rem;">
        </div>

        <!-- Card Body -->
        <div class="card-body">
        <table id="ordersTable" class="table table-bordered table-hover text-center align-middle">

                <thead class="table-primary">
                    <tr>
                        <th>Order #</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
               <tbody>
                <?php foreach ($orders as $index => $order): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                    <td><?= $order['quantity'] ?></td>
                    <td>
                        <?php
                            $status = $order['status'];
                            switch ($status) {
                                case 'Pending':
                                    $badgeClass = 'badge bg-warning text-dark';
                                    break;
                            case 'Processing':
                                $badgeClass = 'badge badge-purple '; // custom purple
                                break;
                                case 'Delivering':
                                    $badgeClass = 'badge bg-info text-dark';
                                    break;
                                case 'Completed':
                                    $badgeClass = 'badge bg-success';
                                    break;
                                case 'Cancelled':
                                    $badgeClass = 'badge bg-danger';
                                    break;
                                default:
                                    $badgeClass = 'badge bg-secondary';
                            }

                        ?>
                        <span class="<?= $badgeClass ?> status-badge"><?= $status ?></span>

                    </td>

                    <td><?= $order['created_at'] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Search Script -->
    <script>
    document.getElementById('orderSearch').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tbody tr');
        
        rows.forEach(row => {
            const product = row.cells[1].textContent.toLowerCase();
            const status = row.cells[3].textContent.toLowerCase();
            row.style.display = (product.includes(searchTerm) || status.includes(searchTerm)) ? '' : 'none';
        });
    });
    </script>
</div>

 <?php include '../footer.php'; ?>

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