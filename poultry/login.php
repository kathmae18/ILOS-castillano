<?php
session_start();

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // Redirect to shop page (or dashboard)
    header("Location: /poultry/customer/shop.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Castillano's Backyard - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="img/logo2.png" type="image/png">
</head>
<body class="bg-light">

<?php include 'header.php'; ?>




<div class="container d-flex justify-content-center align-items-center vh-100">
    <!-- Success Message -->

    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <?php if (!empty($message)): ?>
    <div class="alert alert-success text-center fade-alert">
        <?= $message ?>
    </div>
<?php endif; ?>

<!-- Error Message -->

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
        <p class="text-center mt-3">
            Don't have an account? <a href="register.php" class="text-primary">Register here</a>
        </p>
        <p class="text-center mt-2">
            Are you an admin? <a href="admin/admin_login.php?admin=1" class="text-primary">Click here</a>
        </p>

    </div>
</div>
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

<?php include 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
