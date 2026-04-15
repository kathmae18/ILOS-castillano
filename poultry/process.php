<?php
session_start();
include 'conn.php';


// ==========================
// Login
// ==========================
if (isset($_POST['login'])) {
    $email = trim($_POST['username']); // using email
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM customer WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['message'] = 'Welcome back, ' . htmlspecialchars($name) ;

            header("Location: customer/shop.php");
            exit();
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger text-center">Incorrect password. Please try again.</div>';
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = '<div class="alert alert-warning text-center">No account found with that email.</div>';
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
    exit();
}

// ==========================
// Registration
// ==========================
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['message'] = '<div class="alert alert-danger text-center">Passwords do not match!</div>';
        header("Location: register.php");
        exit();
    }

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM customer WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['message'] = '<div class="alert alert-warning text-center">Email already exists. Please use a different email.</div>';
        header("Location: register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO customer (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['message'] = '<div class="alert alert-success text-center">Account created successfully! You can now login.</div>';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger text-center">Error: ' . $stmt->error . '</div>';
        header("Location: register.php");
        exit();
    }

    $stmt->close();
    $check->close();
    $conn->close();
    exit();
}


// ==========================
// Direct access
// ==========================
header("Location: login.php");
exit();
?>
