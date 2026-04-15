<?php
// header.php
?>
<header class="container-fluid p-0">
    <div class="row gx-0 bg-primary px-5 align-items-center">

        <!-- Main Header -->
        <div class="col-12">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary">
                <!-- Logo for all screens -->
                <a href="index.php" class="navbar-brand">
                    <h1 class="display-5 text-white m-0">
                        <img src="img/logo2.png"  height="60" style="margin-right: 10px;">
                        <span style="vertical-align: middle; color: white; font-weight: bold; 
                        font-family: 'Calibri';">Castillano's Backyard</span>
                    </h1>
                </a>

                <!-- Toggle button for mobile -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars fa-1x text-white"></span>
                </button>

                <!-- Header Links -->
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.php" class="nav-item nav-link">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                         <span class="nav-item nav-link text-muted">|</span>
                        <a href="customer/cart.php" class="nav-item nav-link">
                            <i class="fas fa-shopping-cart me-1"></i> Cart
                        </a>
                         <span class="nav-item nav-link text-muted">|</span>
                        <a href="register.php" class="nav-item nav-link me-2">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                         <span class="nav-item nav-link text-muted">|</span>
                        <a href="login.php" class="nav-item nav-link">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
         
                        </a>
                    </div>
                </div>
            </nav>
        </div>

    </div>
</header>

<!-- Header End -->
