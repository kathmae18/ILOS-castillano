<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();



include 'conn.php'; // Your database connection
if (isset($_POST['login'])) {
    $email = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $db_pass = $admin['password'];

        // ✅ Check hashed OR plain password
        if (password_verify($password, $db_pass) || $password === $db_pass) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name'];

            $_SESSION['message'] = "Successfully logged in. Welcome back Admin, " . $admin['name'] . "!";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "Incorrect password.";
        }
    } else {
        $_SESSION['message'] = "Admin not found.";
    }

    header("Location: admin_login.php");
    exit();
}



// ✅ For updating admin profile
if (isset($_POST['update'])) {
    if (!isset($_SESSION['admin_id'])) {
        $_SESSION['error_message'] = "Session expired. Please log in again.";
        header("Location: admin_login.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $pic = null;

    // ✅ Handle file upload
    if (isset($_FILES['pic']) && $_FILES['pic']['error'] === 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['pic']['name'];
        $file_tmp = $_FILES['pic']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            $new_name = "admin_" . $admin_id . "." . $file_ext;
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($file_tmp, $upload_dir . $new_name);
            $pic = $new_name;
        } else {
            $_SESSION['message'] = "Invalid image format!";
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    // ✅ Prepare update query
    $params = [];
    $types = "";
    $sql = "UPDATE admins SET name = ?";
    $params[] = $name; 
    $types .= "s";

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
    $params[] = $admin_id; 
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        if ($password_changed) {
            // ✅ Force re-login if password was changed
            session_unset();
            session_destroy();

            session_start();
            $_SESSION['message'] = "Password updated successfully. Please log in again.";
            header("Location: admin_login.php");
            exit();
        } else {
            $_SESSION['message'] = "Profile updated successfully!";
            header("Location: admin_profile.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Something went wrong. Please try again.";
        header("Location: admin_profile.php");
        exit();
    }
}

// Adding stock
if(isset($_POST['add_stock'])){
    $product_id = $_POST['product_id'];
    $added_stock = (int)$_POST['added_stock'];

    // Update added_stock only
    $conn->query("UPDATE products SET added_stock = added_stock + $added_stock WHERE id = $product_id");

    $_SESSION['message'] = 'Stock added successfully!';
    header('Location: products.php');
    exit();
}


//adding products
if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    // Handle image upload
    $picture = '';
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $picture = 'product_' . time() . '.' . $ext;
        $upload_dir = 'uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($_FILES['picture']['tmp_name'], $upload_dir . $picture);
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, stock, picture) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsis", $name, $price, $description, $stock, $picture);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
    } else {
        $_SESSION['message'] = "Failed to add product.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to dashboard
    header("Location: products.php");
    exit();
}

if (isset($_POST['edit_product'])) {

    // Get form data
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $is_unavailable = isset($_POST['is_unavailable']) ? 1 : 0; // new field

    // Handle image upload
    $picture = '';
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $picture = 'product_' . time() . '.' . $ext;
        $upload_dir = 'uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($_FILES['picture']['tmp_name'], $upload_dir . $picture);

        // Update all fields including picture
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, stock=?, picture=?, is_unavailable=? WHERE id=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sdsisii", $name, $price, $description, $stock, $picture, $is_unavailable, $product_id);

    } else {
        // Update without changing picture
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, stock=?, is_unavailable=? WHERE id=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sdsiii", $name, $price, $description, $stock, $is_unavailable, $product_id);
    }

    // Execute
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    } else {
        $_SESSION['message'] = "Product updated successfully!";
    }

    $stmt->close();
    $conn->close();

    header("Location: products.php");
    exit();
}


//deleting or removing products
if (isset($_GET['delete_product'])) {

    $product_id = $_GET['delete_product'];


    $stmt = $conn->prepare("SELECT picture FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($picture);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    if (!$stmt->execute()) {
        die("Delete failed: " . $stmt->error);
    }

    if (!empty($picture) && file_exists("uploads/products/" . $picture)) {
        unlink("uploads/products/" . $picture);
    }

    $stmt->close();
    $conn->close();

    $_SESSION['message'] = "Product deleted successfully!";
    header("Location: products.php");
    exit();
}


//reducing stock
if (isset($_POST['reduce_stock_submit'])) {
    $product_id = (int)$_POST['product_id'];
    $reduce_qty = (int)$_POST['reduce_stock'];

    // Fetch current stock
    $stmt = $conn->prepare("SELECT stock, added_stock, reduced_stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        $_SESSION['message'] = "<div class='alert alert-danger text-center'>Product not found.</div>";
        header("Location: products.php");
        exit();
    }

    $original_stock = (int)$product['stock'];
    $added_stock    = (int)$product['added_stock'];
    $reduced_stock  = (int)$product['reduced_stock'];

    // Calculate completed orders
    $completed_qty = 0;
    $res = $conn->query("SELECT COALESCE(SUM(quantity),0) AS completed_qty FROM orders WHERE product_id = $product_id AND status = 'Completed'");
    if ($res) {
        $row = $res->fetch_assoc();
        $completed_qty = (int)$row['completed_qty'];
    }

    // Calculate available stock
    $available_stock = $original_stock + $added_stock - $completed_qty - $reduced_stock;
    if ($available_stock < 0) $available_stock = 0;

    // Prevent reducing more than available
    if ($reduce_qty > $available_stock) {
        $_SESSION['message'] = "<div class='alert alert-danger text-center'>Cannot reduce more than available stock ($available_stock).</div>";
        header("Location: products.php");
        exit();
    }

    // Increment reduced_stock
    $reduced_stock += $reduce_qty;
    $stmt = $conn->prepare("UPDATE products SET reduced_stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $reduced_stock, $product_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success text-center'>Stock successfully reduced.</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger text-center'>Failed to reduce stock. Please try again.</div>";
    }

    header("Location: products.php");
    exit();
}

// this code is for update the status into processing
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['processing_order']) || isset($_GET['cancel_order'])) {

    $order_id = isset($_GET['processing_order']) ? (int) $_GET['processing_order'] : (int) $_GET['cancel_order'];
    $new_status = isset($_GET['processing_order']) ? 'Processing' : 'Cancelled';
    $email_subject = $new_status === 'Processing' 
                        ? "Your Order Has Been Confirmed" 
                        : "Your Order Has Been Cancelled";
    $email_body_template = $new_status === 'Processing' 
        ? "Hello <strong>%s</strong>,<br><br>Your order for <strong>%s</strong>, <strong>%d Piece/s.</strong> With price of <strong>₱%.2f</strong> has been confirmed and is now being processed and will be delivered soon.<br><br>Thank you for shopping with us!" 
        : "Hello <strong>%s</strong>,<br><br>Your order for <strong>%s</strong>, <strong>%d Piece/s.</strong> With price of <strong>₱%.2f</strong> has been cancelled.<br><br>We apologize for any inconvenience.";

    // Update order status
    $sql = "UPDATE orders SET status='$new_status' WHERE id=$order_id";
    if ($conn->query($sql) === TRUE) {

        // Get customer email, name, product name, quantity, and price
        $result = $conn->query("
            SELECT c.email, c.name AS customer_name, p.name AS product_name, o.quantity, (o.quantity * p.price) AS total_price
            FROM orders o
            INNER JOIN customer c ON o.customer_id = c.id
            INNER JOIN products p ON o.product_id = p.id
            WHERE o.id = $order_id
        ");

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $customerEmail = $row['email'];
            $customer_name = $row['customer_name'];
            $product_name  = $row['product_name'];
            $quantity      = (int)$row['quantity'];
            $total_price   = (float)$row['total_price'];

            // Send email via PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['GMAIL_USER'];
                $mail->Password   = $_ENV['GMAIL_APP_PASSWORD'];
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom($_ENV['GMAIL_USER'], 'CastillanoPoultry');
                $mail->addAddress($customerEmail);
                $mail->isHTML(true);
                $mail->Subject = $email_subject;
                $mail->Body = sprintf($email_body_template, $customer_name, $product_name, $quantity, $total_price);

                $mail->send();
                $_SESSION['message'] = "Order #$order_id moved to $new_status, customer notified via email!";
            } catch (Exception $e) {
                $_SESSION['message'] = "Error sending email: {$mail->ErrorInfo}";
            }
        }

    } else {
        $_SESSION['message'] = "Error updating order: " . $conn->error;
    }

    header("Location: orders.php");
    exit();
}

//this code is for updating the status into delivering
if (isset($_GET['delivering_order'])) {
    $order_id = (int) $_GET['delivering_order'];

    // Update order status to Delivering
    $sql = "UPDATE orders SET status='Delivering' WHERE id=$order_id";
    if ($conn->query($sql) === TRUE) {

        $result = $conn->query("
            SELECT c.email, c.name AS customer_name, p.name AS product_name, o.quantity, (o.quantity * p.price) AS total_price
            FROM orders o
            INNER JOIN customer c ON o.customer_id = c.id
            INNER JOIN products p ON o.product_id = p.id
            WHERE o.id = $order_id
        ");

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $customerEmail = $row['email'];
            $customerName = $row['customer_name'];
            $productName = $row['product_name'];
            $quantity = $row['quantity'];
            $totalPrice = $row['total_price'];

            // Send email notification
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['GMAIL_USER'];
                $mail->Password   = $_ENV['GMAIL_APP_PASSWORD'];
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom($_ENV['GMAIL_USER'], 'CastillanoPoultry');
                $mail->addAddress($customerEmail);
                $mail->isHTML(true);
                $mail->Subject = "Your Order is Now Delivering";

                $mail->Body = "Hello <strong>$customerName</strong>,<br><br>"
                    . "Your order for <strong>$productName</strong>$quantity, Piece/s  "
                    . "is now being transport and on its way. Please prepare ₱$totalPrice.<br><br>Thank you for shopping with us!";

                $mail->send();
                $_SESSION['message'] = "Order #$order_id updated to Delivering and customer notified!";
            } catch (Exception $e) {
                $_SESSION['message'] = "Order updated but email failed: {$mail->ErrorInfo}";
            }
        }

    } else {
        $_SESSION['message'] = "Error updating order: " . $conn->error;
    }

    header("Location: orders.php");
    exit();
}


//updating products status into complete
// Updating product status to complete
if (isset($_GET['completed_order'])) {
    $order_id = (int) $_GET['completed_order'];

    // Fetch order details first (including product_id)
    $result = $conn->query("
        SELECT o.product_id, o.quantity, (o.quantity*p.price) AS total_price, 
               p.name AS product_name, c.email, c.name AS customer_name
        FROM orders o
        JOIN customer c ON o.customer_id = c.id
        JOIN products p ON o.product_id = p.id
        WHERE o.id = $order_id
    ");

    if ($result && $row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity   = $row['quantity'];

        // Update order status
        $sql = "UPDATE orders SET status='Completed' WHERE id=$order_id";
        if ($conn->query($sql) === TRUE) {
            // Order marked as completed
            $_SESSION['message'] = "Order #$order_id marked as Completed.";

            // Send email notification
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['GMAIL_USER'];
                $mail->Password   = $_ENV['GMAIL_APP_PASSWORD'];
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom($_ENV['GMAIL_USER'], 'CastillanoPoultry');
                $mail->addAddress($row['email']);
                $mail->isHTML(true);
                $mail->Subject = "Your Order is Completed";
                $mail->Body = "Hello <strong>{$row['customer_name']}</strong>,<br><br>"
                            . "We would like to inform you that your order for <strong>{$row['product_name']}</strong> "
                            . "({$quantity} Piece/s) has been <strong>completed</strong>.<br><br>"
                            . "<strong>Thank you for trusting and buying our products!</strong><br><br>"
                            . "Thank you for shopping with us!";

                $mail->send();
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
            }

        } else {
            $_SESSION['message'] = "Error updating order: " . $conn->error;
        }

    } else {
        $_SESSION['message'] = "Order not found.";
    }

    header("Location: orders.php");
    exit();
}


?>
