<?php
session_start();
include 'conn.php';

// For updating profile
if (isset($_POST['update'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Session expired. Please log in again.";
        header("Location: /poultry/login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $pic = null;

    // Handle file upload
    if (isset($_FILES['pic']) && $_FILES['pic']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['pic']['name'];
        $file_tmp = $_FILES['pic']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = "customer_" . $user_id . "." . $file_ext;
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($file_tmp, $upload_dir . $new_name);
            $pic = $new_name;
        } else {
            $_SESSION['message'] = "Invalid image format!";
            header("Location: customer_profile.php");
            exit();
        }
    }

    // Prepare update query
    $params = [];
    $types = "";
    $sql = "UPDATE customer SET name = ?, phone = ?, address = ?";
    $params[] = $name;   $types .= "s";
    $params[] = $phone;  $types .= "s";
    $params[] = $address; $types .= "s";

    $password_changed = false;

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $hashed_password; 
        $types .= "s";
        $password_changed = true;
    }

    if ($pic !== null) {
        $sql .= ", pic = ?";
        $params[] = $pic; 
        $types .= "s";
    }

    $sql .= " WHERE id = ?";
    $params[] = $user_id; 
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($password_changed) {
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['message'] = "Password updated successfully. Please log in again.";
            header("Location: /poultry/login.php");
            exit();
        } else {
            $_SESSION['message'] = "Profile updated successfully!";
            header("Location: customer_profile.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Something went wrong. Please try again.";
        header("Location: customer_profile.php");
        exit();
    }
}

// Removing products from the cart
if(isset($_POST['remove_from_cart'])){
    $cart_id = $_POST['remove_from_cart']; 
    $stmt = $conn->prepare("DELETE FROM user_cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Item removed from cart.";
    header("Location: cart.php");
    exit();
}

// Update cart quantity
if (isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    $stmt = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
}

// Update phone/address if submitted
if (isset($_POST['phone'], $_POST['address']) && !empty($_POST['phone']) && !empty($_POST['address'])) {
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE customer SET phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssi", $phone, $address, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Checkout selected items
if (isset($_POST['checkout_selected'])) {
    $delivery_method = isset($_POST['delivery_method']) ? $_POST['delivery_method'] : 'Pick Up';

    if (!empty($_POST['selected_items'])) {
        $customer_id = $_SESSION['user_id'];
        $selected_items = $_POST['selected_items'];

        foreach ($selected_items as $cart_id) {
            $cart_id = (int)$cart_id;

            // Get product info from cart
            $stmt = $conn->prepare("SELECT product_id, quantity FROM user_cart WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $customer_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result) {
                $product_id = $result['product_id'];
                $quantity = $result['quantity'];

                // Insert into orders with delivery method
                $stmt = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, status, delivery_method) VALUES (?, ?, ?, 'Pending', ?)");
                $stmt->bind_param("iiis", $customer_id, $product_id, $quantity, $delivery_method);
                $stmt->execute();
                $stmt->close();

                // Remove from cart
                $stmt = $conn->prepare("DELETE FROM user_cart WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $cart_id, $customer_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        $_SESSION['message'] = "Selected items checked out successfully!";
    } else {
        $_SESSION['message'] = "Please select at least one item to checkout.";
    }

    header("Location: cart.php");
    exit();
}

// Checkout ALL items
if(isset($_POST['checkout_all'])) {
    $user_id = $_SESSION['user_id'];
    $delivery_method = isset($_POST['delivery_method']) ? $_POST['delivery_method'] : 'Pick Up';

    // Fetch all cart items for this user
    $stmt = $conn->prepare("SELECT * FROM user_cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach($cart_items as $item){
        $stmt2 = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, status, delivery_method) VALUES (?, ?, ?, 'Pending', ?)");
        $stmt2->bind_param("iiis", $user_id, $item['product_id'], $item['quantity'], $delivery_method);
        $stmt2->execute();
    }

    // Clear user's cart
    $stmt = $conn->prepare("DELETE FROM user_cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $_SESSION['message'] = "All items checked out successfully!";
    header("Location: cart.php");
    exit();
}

// Adding products to the cart
if (isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if product is already in user's cart
    $stmt = $conn->prepare("SELECT id, quantity FROM user_cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Product exists, update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE id = ?");
        $update->bind_param("ii", $new_quantity, $row['id']);
        $update->execute();
        $update->close();
    } else {
        // Insert new product into cart
        $insert = $conn->prepare("INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $product_id, $quantity);
        $insert->execute();
        $insert->close();
    }

    $stmt->close();

    $_SESSION['message'] = "Product added to cart!";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
