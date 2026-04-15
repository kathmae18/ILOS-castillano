

<header class="container-fluid p-0">
    <div class="row gx-0 bg-primary px-5 align-items-center">

        <!-- Main Header -->
        <div class="col-12">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary">

                <!-- Logo for all screens -->
                <a href="admin_dashboard.php" class="navbar-brand">
                    <h1 class="display-5 text-white m-0">
                            <h2 class="fw-semibold text-white m-0" style="font-size: 1.75rem; line-height: 1.2;">
                        <img src="../img/logo2.png"  height="60" style="margin-right: 10px;">
                        <span style="vertical-align: middle; color: white; font-weight: bold; 
                        font-family: 'Calibri';">Integrated livestock Ordering System</span>
                    </h1>
                </a>

                <!-- Toggle button for mobile -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars fa-1x text-white"></span>
                </button>

                <!-- Header Links -->
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 d-flex align-items-center">
                        <a href="admin_dashboard.php" class="nav-item nav-link">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                        <span class="nav-item nav-link text-muted">|</span>
                        <a href="products.php" class="nav-item nav-link">
                            <i class="fas fa-box me-1"></i> Products

                        </a>
                        <span class="nav-item nav-link text-muted">|</span>
                         <a href="orders.php" class="nav-item nav-link">
                            <i class="fas fa-box me-1"></i> Orders

                        </a>
                        <span class="nav-item nav-link text-muted">|</span>
                    <!-- Profile Dropdown -->
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                               <i class="fas fa-user-circle me-1"></i>
                                <?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin' ?>


                                
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="admin_profile.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/poultry/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>


                    </div>
                </div>

            </nav>
        </div>

    </div>
</header>


<!-- Header End -->
