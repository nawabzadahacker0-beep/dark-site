<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'signup') {
        // Form se aaye huwe kisi bhi data ko temporary session me save karna
        $_SESSION['user_id']   = rand(1000, 9999);
        $_SESSION['username']  = trim($_POST['username'] ?? 'Test User');
        $_SESSION['email']     = trim($_POST['email'] ?? 'test@example.com');
        $_SESSION['phone']     = trim($_POST['phone'] ?? '03000000000');
        $_SESSION['coins']     = 100; // Testing bonus coins
        $_SESSION['is_logged'] = true;

        // Direct dashboard/home page par redirect
        header("Location: index.php");
        exit;
    }

    if ($action === 'login') {
        // Testing ke liye koi bhi dummy login
        $identifier = trim($_POST['identifier'] ?? 'User');

        $_SESSION['user_id']   = rand(1000, 9999);
        $_SESSION['username']  = $identifier;
        $_SESSION['email']     = $identifier . "@test.com";
        $_SESSION['phone']     = "03000000000";
        $_SESSION['coins']     = 100;
        $_SESSION['is_logged'] = true;

        header("Location: index.php");
        exit;
    }
} else {
    // Agar direct URL khole toh wapas form par bhej de
    header("Location: index.php");
    exit;
}
?>
