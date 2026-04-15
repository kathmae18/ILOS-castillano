<?php

session_start(); 

include 'conn.php'; 
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Set an error message to show on login page
    $_SESSION['error_message'] = "Please log in to access.";
    
    // Redirect to login page
    header("Location: /poultry/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Get customer info
$stmt = $conn->prepare("SELECT phone, address FROM customer WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Electro - Electronics Website Template</title>
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
    <link rel="icon" href="../img/pigs.jpg" type="image/jpeg">
    <style>
        .btn-danger:hover {
    background-color: #c82333; /* Darker red on hover */
    transform: scale(1.05);    /* Slightly enlarge */
    transition: all 0.2s ease-in-out;
}
#modalCartMessage {
  
    color: #856404;            /* Bootstrap warning text color */
    font-size: 20px;
    font-weight: bold;
    padding: 30px 20px;
    border-radius: 12px;
    max-width: 1000px;
    margin: 20px auto;  /* centered in modal body */
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
                <div id="warningMessage" class="alert alert-danger d-none text-center mx-auto" style="max-width: 400px; font-size: 18px; padding: 10px 15px;">
                    Please select at least one item to checkout.
                </div>

            <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Cart Page</h1>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="shop.php">Home</a></li>
                <li class="breadcrumb-item active text-white">Cart Page</li>
            </ol>
        </div>
        <!-- Single Page Header End -->

        <!-- Cart Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">

                    <?php
                        $user_id = $_SESSION['user_id'];

                        // Fetch cart items into an array
                        $cart_items = [];
                        $sql = "SELECT uc.id AS cart_id, uc.quantity, p.* 
                                FROM user_cart uc 
                                JOIN products p ON uc.product_id = p.id 
                                WHERE uc.user_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Fill $cart_items array
                        while ($row = $result->fetch_assoc()) {
                            $cart_items[] = $row;
                        }

                        // Calculate subtotal
                        $subtotal = 0;
                        foreach ($cart_items as $item) {
                            $subtotal += $item['price'] * $item['quantity'];
                        }
                    ?>

                    <?php if (empty($cart_items)): ?>
                        <div class="alert alert-warning text-center my-5" style="font-size: 18px;">
                            Your cart is empty. <a href="shop.php" class="text-primary">Go to Shop</a>
                        </div>
                    <?php else: ?>
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Select</th>
                                    <th class="text-start">Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cart_items as $row): ?>
                                <tr data-price="<?= $row['price'] ?>">
                                    <td>
                                        <input type="checkbox" class="item-checkbox" 
                                            data-total="<?= $row['price'] * $row['quantity'] ?>" 
                                            name="selected_items[]" 
                                            value="<?= $row['cart_id'] ?>">
                                    </td>
                                    <td class="text-start"><?= htmlspecialchars($row['name']) ?></td>
                                    <td>₱<?= number_format($row['price'], 2) ?></td>
                                    <td>
                                        <div class="quantity-form d-flex align-items-center justify-content-center">
                                            <button type="button" name="minus_quan" class="btn btn-sm btn-secondary me-1 quantity-btn" 
                                            data-action="minus" data-cart-id="<?= $row['cart_id'] ?>" data-price="<?= $row['price'] ?>">-</button>
                                            <input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="1" class="form-control text-center" style="width: 60px;">
                                            <button type="button" name="add_quan" class="btn btn-sm btn-secondary ms-1 quantity-btn" 
                                            data-action="plus" data-cart-id="<?= $row['cart_id'] ?>" data-price="<?= $row['price'] ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="row-total">₱<?= number_format($row['price'] * $row['quantity'], 2) ?></td>
                                    <td>
                                        <form method="POST" action="process.php" style="display:inline;">
                                            <button type="submit" name="remove_from_cart" value="<?= $row['cart_id'] ?>" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt me-1"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>


                                <!-- Checkout Modal with its own form -->
                    <form method="POST" action="process.php" id="checkoutForm">
                        <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content shadow rounded">
                                    <!-- Modal Header -->
                                    <div class="modal-header bg-primary text-white border-0">
                                        <h5 class="modal-title fs-5" id="addressModalLabel">
                                            <?= empty($customer['phone']) || empty($customer['address']) ? 'Enter Your Details Before Checking Out' : 'Confirm Your Details Below' ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body p-4">
                                        <!-- Selected Items Summary -->
                                        <div class="mb-4 p-3 bg-light rounded shadow-sm">
                                            <h6 class="mb-3 fw-bold text-dark">Selected Items</h6>
                                            <table class="table table-borderless table-hover align-middle mb-0 text-dark">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-start">Name</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-end">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="selectedItemsContainer">
                                                </tbody>
                                                <tfoot class="border-top">
                                                    <tr class="fw-bold">
                                                        <th colspan="2" class="text-end">Total:</th>
                                                        <th id="selectedItemsTotal" class="text-end">₱0.00</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <!-- Delivery Method -->
                                          <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">Delivery Method</label>
                                            <select name="delivery_method" class="form-select" required>
                                                <option value="Pick Up">Pick Up</option>
                                                <option value="Delivery">Delivery</option>
                                            </select>
                                        </div>



                                        <!-- Customer Details -->
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">Phone</label>
                                            <input type="tel" name="phone" class="form-control form-control-lg" 
                                                value="<?= htmlspecialchars($customer['phone']) ?>" 
                                                <?= empty($customer['phone']) ? 'required' : '' ?>>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">Address</label>
                                            <input type="text" name="address" class="form-control form-control-lg" 
                                                value="<?= htmlspecialchars($customer['address']) ?>" 
                                                <?= empty($customer['address']) ? 'required' : '' ?>>
                                        </div>

                                        <input type="hidden" name="checkout_selected" value="1">
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="modal-footer border-0 p-3">
                                        <button type="submit" class="btn btn-success w-100 py-2 fs-6 text-uppercase fw-bold shadow-sm">
                                            Proceed Checkout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Checkout Button -->
                    <div class="text-end mt-3">
                        <button type="button" id="checkoutButton" class="btn btn-success btn-sm px-4 py-2">
                            Checkout Selected Items
                        </button>
                    </div>

                    <script>
                        document.getElementById('checkoutButton').addEventListener('click', function() {
                            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
                            const container = document.getElementById('selectedItemsContainer');
                            const totalEl = document.getElementById('selectedItemsTotal');
                            const warning = document.getElementById('warningMessage');

                            if (checkedItems.length === 0) {
                                warning.classList.remove('d-none');
                                setTimeout(() => warning.classList.add('d-none'), 3000);
                                return;
                            }

                            container.innerHTML = ''; // Clear previous items
                            let total = 0;

                            checkedItems.forEach(cb => {
                                const row = cb.closest('tr');
                                const name = row.querySelector('td.text-start').textContent;
                                const qty = row.querySelector('input[name="quantity"]').value;
                                const price = parseFloat(row.querySelector('.row-total').textContent.replace('₱', '').replace(',', ''));

                                total += price;

                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td class="text-start">${name}</td>
                                    <td class="text-center">${qty}</td>
                                    <td class="text-end">₱${price.toFixed(2)}</td>
                                `;
                                container.appendChild(tr);

                                // Add hidden input for form submission
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'selected_items[]';
                                hiddenInput.value = cb.value;
                                container.appendChild(hiddenInput);
                            });

                            totalEl.textContent = `₱${total.toFixed(2)}`;

                            // Show modal
                            var addressModal = new bootstrap.Modal(document.getElementById('addressModal'));
                            addressModal.show();
                        });

                    </script>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="bg-light shadow-sm rounded p-4 sticky-top" style="margin-top: 50px;">
                    <h4 class="mb-4 text-dark">Cart Summary</h4>
                    <input type="hidden" name="checkout_all" id="checkoutAllInput" value="">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-dark">Subtotal:</span>
                        <span id="cartSubtotal" class="text-dark">₱<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top border-bottom py-2 mb-3">
                        <strong class="text-dark">Total:</strong>
                        <strong id="cartTotal" class="text-dark">₱<?= number_format($subtotal, 2) ?></strong>
                    </div>
                    <form id="checkoutAllForm">
                        <button type="button" id="checkoutAllButton" class="btn btn-primary w-100 rounded-pill py-2 text-uppercase fw-bold">
                            Checkout All Items
                        </button>
                    </form>

                </div>
            </div>
            
                        <!-- Checkout All Modal -->
            <form method="POST" action="process.php" id="checkoutAllFormModal">
                <div class="modal fade" id="checkoutAllModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content shadow rounded">
                            <div id="modalCartMessage" class="d-none text-center mx-auto">
                                Your cart is empty. Add items before checkout.
                            </div>

                            <!-- Modal Header -->
                            <div class="modal-header bg-primary text-white border-0">
                                <h5 class="modal-title fs-5" id="checkoutAllModalLabel">Confirm All Items Checkout and Your Details</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body p-4">
                                <!-- All Items Summary -->
                                <div class="mb-4 p-3 bg-light rounded shadow-sm" id="checkoutSummary">
                                    <h6 class="mb-3 fw-bold text-dark">All Items in Cart</h6>
                                    <table class="table table-borderless table-hover align-middle mb-0 text-dark">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">Name</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="allItemsContainer"></tbody>
                                        <tfoot class="border-top">
                                            <tr class="fw-bold">
                                                <th colspan="2" class="text-end">Total:</th>
                                                <th id="allItemsTotal" class="text-end">₱0.00</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                    <!-- Delivery Method -->
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-dark">Delivery Method</label>
                                            <select name="delivery_method" class="form-select" required>
                                                <option value="Pick Up">Pick Up</option>
                                                <option value="Delivery">Delivery</option>
                                            </select>
                                        </div>


                                <!-- Customer Details -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Phone</label>
                                    <input type="tel" name="phone" class="form-control form-control-lg" 
                                        value="<?= htmlspecialchars($customer['phone']) ?>" 
                                        <?= empty($customer['phone']) ? 'required' : '' ?>>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Address</label>
                                    <input type="text" name="address" class="form-control form-control-lg" 
                                        value="<?= htmlspecialchars($customer['address']) ?>" 
                                        <?= empty($customer['address']) ? 'required' : '' ?>>
                                </div>

                                <input type="hidden" name="checkout_all" value="1">
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer border-0 p-3">
                                <button type="submit" class="btn btn-success w-100 py-2 fs-6 text-uppercase fw-bold shadow-sm">
                                    Proceed Checkout All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
document.getElementById('checkoutAllButton').addEventListener('click', function() {
    const cartRows = document.querySelectorAll('tbody tr');
    const message = document.getElementById('modalCartMessage');
    const checkoutAllModalEl = document.getElementById('checkoutAllModal');
    const checkoutAllModal = new bootstrap.Modal(checkoutAllModalEl);

    if (cartRows.length === 0) {
        // Show only the empty cart message
        message.classList.remove('d-none');
        
        // Hide all other elements
        checkoutAllModalEl.querySelector('.modal-header').style.display = 'none';
        checkoutAllModalEl.querySelector('.modal-footer').style.display = 'none';
        checkoutAllModalEl.querySelector('.modal-body > div.mb-4').style.display = 'none'; // table
        checkoutAllModalEl.querySelectorAll('.modal-body .mb-3').forEach(el => el.style.display = 'none'); // phone & address

        checkoutAllModal.show();
        return;
    }

    // Cart has items: show normal checkout
    message.classList.add('d-none');
    checkoutAllModalEl.querySelector('.modal-header').style.display = 'flex';
    checkoutAllModalEl.querySelector('.modal-footer').style.display = 'flex';
    checkoutAllModalEl.querySelector('.modal-body > div.mb-4').style.display = 'block';
    checkoutAllModalEl.querySelectorAll('.modal-body .mb-3').forEach(el => el.style.display = 'block');

    // Fill the table with cart items (same as before)
    const container = document.getElementById('allItemsContainer');
    const totalEl = document.getElementById('allItemsTotal');
    container.innerHTML = '';
    let total = 0;

    cartRows.forEach(row => {
        const name = row.querySelector('td.text-start').textContent;
        const qty = row.querySelector('input[name="quantity"]').value;
        const price = parseFloat(row.querySelector('.row-total').textContent.replace('₱', '').replace(',', ''));
        total += price;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-start">${name}</td>
            <td class="text-center">${qty}</td>
            <td class="text-end">₱${price.toFixed(2)}</td>
        `;
        container.appendChild(tr);
    });

    totalEl.textContent = `₱${total.toFixed(2)}`;
    checkoutAllModal.show();
});

</script>

<script>
document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        const input = row.querySelector('input[name="quantity"]');
        let value = parseInt(input.value);
        const price = parseFloat(this.dataset.price);
        const cartId = this.dataset.cartId;

        // Use the button's name to determine action
        if (this.name === 'add_quan') {
            value++;
        } else if (this.name === 'minus_quan' && value > 1) {
            value--;
        }

        // Update input and row total
        input.value = value;
        row.querySelector('.row-total').textContent = '₱' + (price * value).toFixed(2);
        row.querySelector('.item-checkbox').setAttribute('data-total', (price * value).toFixed(2));

        // Send AJAX to update DB immediately
        fetch('process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `cart_id=${cartId}&quantity=${value}`
        })
        .then(res => res.text())
        .then(data => console.log(data))
        .catch(err => console.error(err));
    });
});
</script>

    <!-- Cart Page End -->


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