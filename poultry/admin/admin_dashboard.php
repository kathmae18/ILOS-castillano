<?php
session_start();
include 'conn.php'; // your DB connection

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['message'] = 'You are not authorized to open this page. Please log in.';
    header("Location: admin_login.php");
    exit();
}

// Fetch order counts dynamically
$statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
$orderCounts = [];

foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $orderCounts[$status] = $count;
    $stmt->close();
}


$salesData = [];

if (isset($_GET['start_date'], $_GET['end_date'])) {
    $start = $_GET['start_date'] . ' 00:00:00';
    $end = $_GET['end_date'] . ' 23:59:59';

    $stmt = $conn->prepare("
        SELECT DATE(o.created_at) AS order_day, SUM(o.quantity * p.price) AS total_sales
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.status = 'Completed' AND o.created_at BETWEEN ? AND ?
        GROUP BY DATE(o.created_at)
        ORDER BY DATE(o.created_at)
    ");

    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $salesData[] = [
            'date' => $row['order_day'],
            'total' => $row['total_sales']
        ];
    }

    $stmt->close();
}

// Total Products
$stmt = $conn->prepare("SELECT COUNT(*) FROM products");
$stmt->execute();
$stmt->bind_result($totalProducts);
$stmt->fetch();
$stmt->close();

// Total Users
$stmt = $conn->prepare("SELECT COUNT(*) FROM customer");
$stmt->execute();
$stmt->bind_result($totalUsers);
$stmt->fetch();
$stmt->close();

// Total Earnings (Completed Orders)
$stmt = $conn->prepare("
    SELECT SUM(o.quantity * p.price) 
    FROM orders o 
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Completed'
");
$stmt->execute();
$stmt->bind_result($totalEarnings);
$stmt->fetch();
$stmt->close();


// success message
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Earnings by Product (Top 10)
$productEarnings = [];
$query = "
    SELECT p.name AS product_name, SUM(o.quantity * p.price) AS total_earnings
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Completed'
    GROUP BY p.id, p.name
    ORDER BY total_earnings DESC
    LIMIT 10
";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productEarnings[] = [
            'product' => $row['product_name'],
            'earnings' => $row['total_earnings']
        ];
    }
}

// Top Products by Units Sold (Top 10)
$topUnitsSold = [];
$query = "
    SELECT p.name AS product_name, SUM(o.quantity) AS total_units
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.status = 'Completed'
    GROUP BY p.id, p.name
    ORDER BY total_units DESC
    LIMIT 10
";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topUnitsSold[] = [
            'product' => $row['product_name'],
            'units' => $row['total_units']
        ];
    }
}

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
    /* Hover effect for forecast cards */
.forecast-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}
.forecast-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 25px rgba(0,0,0,0.15);
}
</style>
</head>
<body>
   <?php include 'header.php'; ?>
<div class="container py-5">
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
                    setTimeout(() => alert.remove(), 500); 
                }
            }, 3000);
        </script>
    <?php endif; ?>


<div class="container py-5">
    <h2 class="mb-4 text-center text-primary">Dashboard Summary</h2>
    <div class="row text-center g-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="fas fa-boxes me-2"></i>Total Products</h5>
                    <h2 class="card-text"><?= $totalProducts; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="fas fa-coins me-2"></i>Total Earnings</h5>
                    <h2 class="card-text">₱ <?= number_format($totalEarnings, 2); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-users me-2"></i>Total Users</h5>
                    <h2 class="card-text"><?= $totalUsers; ?></h2>
                </div>
            </div>
        </div>

    </div>
</div>

    <h2 class="mb-4 text-center text-primary">Orders Summary</h2>
    <div class="row text-center g-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <i class="fas fa-hourglass-half me-2"></i>Pending
                    </h5>
                    <h2 class="card-text" id="pendingOrders"><?= $orderCounts['Pending']; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <h5 class="card-title text-info">
                        <i class="fas fa-spinner me-2"></i>Processing
                    </h5>
                    <h2 class="card-text" id="processingOrders"><?= $orderCounts['Processing']; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-check-circle me-2"></i>Completed
                    </h5>
                    <h2 class="card-text" id="completedOrders"><?= $orderCounts['Completed']; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-times-circle me-2"></i>Cancelled
                    </h5>
                    <h2 class="card-text" id="cancelledOrders"><?= $orderCounts['Cancelled']; ?></h2>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="container py-5 sales-overview">
    <h2 class="mb-4 text-center text-primary">📊 Sales Overview</h2>
    <div class="row g-4">
        <!-- Total Earnings -->
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm p-4 h-100 d-flex flex-column chart-card">
                <h4 class="text-center text-primary mb-3">💰 Total Earnings</h4>
                <form id="salesFilterForm" method="GET" class="mb-3">
                    <div class="mb-2">
                        <label for="startDate" class="form-label small">Start Date</label>
                        <input type="date" id="startDate" name="start_date" class="form-control form-control-sm"
                            value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    </div>
                    <div class="mb-2">
                        <label for="endDate" class="form-label small">End Date</label>
                        <input type="date" id="endDate" name="end_date" class="form-control form-control-sm"
                            value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                        <i class="fas fa-chart-line me-1"></i> Show
                    </button>
                </form>

                <div class="text-center mb-3">
                    <h5 class="fw-bold text-success mb-1">
                        ₱ <?= number_format(array_sum(array_column($salesData, 'total')), 2) ?>
                    </h5>
                    <p class="text-muted small mb-0">
                        From <?= htmlspecialchars($_GET['start_date'] ?? '-') ?> 
                        to <?= htmlspecialchars($_GET['end_date'] ?? '-') ?>
                    </p>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Earnings by Product -->
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm p-4 h-100 d-flex flex-column chart-card">
                <h4 class="text-center text-primary mb-3">📦 Earnings by Product</h4>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <canvas id="productEarningsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products by Units Sold -->
        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm p-4 h-100 d-flex flex-column chart-card">
                <h4 class="text-center text-primary mb-3">📈 Top Products by Units Sold</h4>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <canvas id="unitsSoldChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.sales-overview .chart-card {
    min-height: 500px; 
}

.sales-overview canvas {
    width: 100% !important;
    max-height: 250px !important;
}
</style>

<script>
const productEarnings = <?= json_encode($productEarnings); ?>;
if (productEarnings.length > 0) {
    const ctx = document.getElementById('productEarningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productEarnings.map(item => item.product),
            datasets: [{
                label: 'Earnings (₱)',
                data: productEarnings.map(item => item.earnings),
                backgroundColor: [
                    'rgba(0,123,255,0.7)', 'rgba(40,167,69,0.7)', 'rgba(255,193,7,0.7)',
                    'rgba(220,53,69,0.7)', 'rgba(23,162,184,0.7)', 'rgba(111,66,193,0.7)',
                    'rgba(102,16,242,0.7)', 'rgba(255,159,64,0.7)', 'rgba(13,110,253,0.7)',
                    'rgba(25,135,84,0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y', // 🔹 This makes the bars horizontal
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: context => '₱ ' + context.raw.toLocaleString()
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₱ ' + value.toLocaleString()
                    },
                    grid: { color: '#e0e0e0' }
                },
                y: {
                    ticks: {
                        autoSkip: false
                    },
                    grid: { display: false }
                }
            }
        }
    });
}


const topUnitsSold = <?= json_encode($topUnitsSold); ?>;
if (topUnitsSold.length > 0) {
    const ctx = document.getElementById('unitsSoldChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topUnitsSold.map(item => item.product),
            datasets: [{
                label: 'Units Sold',
                data: topUnitsSold.map(item => item.units),
                backgroundColor: [
                    'rgba(255,99,132,0.7)', 'rgba(255,159,64,0.7)', 'rgba(255,205,86,0.7)',
                    'rgba(75,192,192,0.7)', 'rgba(54,162,235,0.7)', 'rgba(153,102,255,0.7)',
                    'rgba(201,203,207,0.7)', 'rgba(40,167,69,0.7)', 'rgba(220,53,69,0.7)',
                    'rgba(23,162,184,0.7)'
                ],
                borderColor: '#fff', borderWidth: 1, borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } },
                x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 } }
            }
        }
    });
}
</script>


<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold text-primary">📊 Next Best-Selling Products Forecast</h2>
    <div class="row g-3 justify-content-center">
        <?php
        $forecastQuery = "
            SELECT p.id, p.name, p.picture,
                SUM(CASE WHEN MONTH(o.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN o.quantity ELSE 0 END) AS last_month,
                SUM(CASE WHEN MONTH(o.created_at) = MONTH(CURRENT_DATE) THEN o.quantity ELSE 0 END) AS this_month
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.status = 'Completed'
              AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
            GROUP BY p.id, p.name, p.picture
            HAVING this_month > 0 OR last_month > 0
            ORDER BY this_month DESC
            LIMIT 3
        ";
        $forecastResult = mysqli_query($conn, $forecastQuery);

        if ($forecastResult && mysqli_num_rows($forecastResult) > 0) {
            while ($row = mysqli_fetch_assoc($forecastResult)) {
                $product = htmlspecialchars($row['name']);
                $image = !empty($row['picture']) ? htmlspecialchars($row['picture']) : "assets/img/default-product.png";
                $lastMonth = (int)$row['last_month'];
                $thisMonth = (int)$row['this_month'];
                $growth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
                $forecast = $growth != 0 ? round($thisMonth * (1 + $growth / 100)) : $thisMonth;

                if ($growth > 0) {
                    $trendText = "Upward Trend";
                    $trendDesc = "Strong sales momentum.";
                    $trendColor = "success";
                    $trendIcon = "fa-arrow-up";
                } elseif ($growth < 0) {
                    $trendText = "Declining Trend";
                    $trendDesc = "Demand is slowing down.";
                    $trendColor = "danger";
                    $trendIcon = "fa-arrow-down";
                } else {
                    $trendText = "Stable Sales";
                    $trendDesc = "Consistent performance.";
                    $trendColor = "secondary";
                    $trendIcon = "fa-minus";
                }

                echo "
                <div class='col-sm-6 col-md-4'>
                    <div class='card forecast-card shadow-sm border-0 h-100 rounded-3'>
                        <div class='ratio ratio-4x3 rounded-top-3 overflow-hidden'>
                            <img src='uploads/products/{$image}' alt='{$product}' class='w-100 h-100' style='object-fit: contain; background-color:#f8f9fa;'>
                        </div>
                        <div class='card-body p-3'>
                            <h5 class='card-title fw-bold mb-2'>{$product}</h5>
                            <div class='d-flex justify-content-between mb-1'>
                                <span class='text-muted small'>Last Month</span>
                                <span class='fw-semibold'>{$lastMonth}</span>
                            </div>
                            <div class='d-flex justify-content-between mb-1'>
                                <span class='text-muted small'>This Month</span>
                                <span class='fw-semibold'>{$thisMonth}</span>
                            </div>
                            <div class='d-flex justify-content-between mb-2'>
                                <span class='text-muted small'>Growth</span>
                                <span class='fw-semibold'>" . number_format($growth,1) . "%</span>
                            </div>
                            <div class='text-center p-2 rounded-3 bg-{$trendColor} text-white fw-bold mb-2'>
                                📊 Forecast Next Month: {$forecast} units
                            </div>
                            <div class='text-center small text-{$trendColor}'>
                                <i class='fa-solid {$trendIcon} me-1'></i>
                                <span class='fw-semibold'>{$trendText}</span> – {$trendDesc}
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<p class='text-muted text-center mb-0'>No sales data available for forecasting.</p>";
        }
        ?>
    </div>
</div>



<script>
const salesData = <?= json_encode($salesData); ?>;

// Aggregate total per day
const labels = salesData.map(item => item.date);
const data = salesData.map(item => item.total);

// Chart
const ctx = document.getElementById('earningsChart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 200);
gradient.addColorStop(0, 'rgba(0, 123, 255, 0.8)');
gradient.addColorStop(1, 'rgba(0, 123, 255, 0.3)');

new Chart(ctx, {
    type: 'line', // Line chart is cleaner for showing total earnings over days
    data: {
        labels: labels,
        datasets: [{
            label: 'Daily Earnings',
            data: data,
            backgroundColor: gradient,
            borderColor: '#007bff',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#007bff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '₱ ' + context.raw.toLocaleString();
                    }
                }
            },
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱ ' + value.toLocaleString();
                    }
                },
                grid: { color: '#e0e0e0' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>



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