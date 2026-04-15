<?php
session_start();
include 'conn.php'; // adjust path to your DB connection
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


?>


<!DOCTYPE html>
<html lang="en">

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
        body {
            background-color: #f8f9fa;
            font-family: 'Open Sans', sans-serif;
        }

        .profile-card {
            max-width: 500px;
            margin: auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #cb6f04ff;
            transition: transform 0.3s;
        }

        .profile-pic:hover {
            transform: scale(1.05);
        }

        .form-label {
            font-weight: 600;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
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
    <h2 class="mb-4 text-center text-primary">Profile Settings</h2>

    <div class="profile-card">
        <form action="process.php" method="POST" enctype="multipart/form-data">

           <!-- Profile Picture -->
                <div class="mb-4 text-center">
                    <img src="uploads/<?= !empty($admin['pic']) ? htmlspecialchars($admin['pic']) : 'customer.png'; ?>" 
                        alt="Profile Picture" class="rounded-circle profile-pic mb-3">
                    <input type="file" class="form-control mt-2" id="pic" name="pic" accept="image/*">
                </div>

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                        value="<?= htmlspecialchars($admin['name']); ?>" required>
                </div>

                <!-- Email (readonly) -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" 
                        value="<?= htmlspecialchars($admin['email']); ?>" readonly>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" 
                        placeholder="Leave blank to keep current">
                </div>

                <button type="submit" name="update" class="btn btn-primary w-100">Update Profile</button>

        </form>
    </div>
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

</body>
</html>
