<?php
session_start();
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Castillano' Backyard</title>
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
</head>

<body class="bg-light">


<header class="container-fluid p-0">
    <div class="row gx-0 bg-primary px-5 align-items-center">

        <!-- Main Header -->
        <div class="col-12">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary">

                <!-- Logo for all screens -->
                <a href="admin_login.php" class="navbar-brand">
                    <h1 class="display-5 text-white m-0">
                      <img src="../img/logo3.png"  height="60" style="margin-right: 10px;">
                        <span style="vertical-align: middle; color: white; font-weight: bold; 
                        font-family: 'Calibri';">Castillano's Backyard</span>
                    </h2>
                </a>
                </a>

                <!-- Toggle button for mobile -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars fa-1x text-white"></span>
                </button>

                <!-- Header Links -->
               

            </nav>
        </div>

    </div>
</header>
    <!-- Success Message -->


<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">

        <?php if (!empty($message)): ?>
            <div class="alert alert-success text-center fade-alert">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <script>
          setTimeout(() => {
            const alert = document.querySelector('.fade-alert');
            if (alert) {
              alert.style.transition = 'opacity 0.5s ease-out';
              alert.style.opacity = '0';
              setTimeout(() => alert.remove(), 500); 
            }
          }, 5000); 
        </script>

        <h3 class="text-center mb-4 text-primary">Login</h3>

        <form action="process.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username or email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="forgot_password.php" class="text-primary">Forgot password?</a>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </button>
        </form>

        <p class="text-center mt-2">
            Are you an User? <a href="/poultry/login.php" class="text-primary">Click here</a>
        </p>

    </div>
</div>



<?php include '../footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
