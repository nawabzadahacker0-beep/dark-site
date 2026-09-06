<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($identifier) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ? OR phone = ?");
            $stmt->execute([$identifier, $identifier, $identifier]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user;
                header("Location: index.php");
                exit;
            } else {
                echo "<script>alert('Invalid Credentials!'); window.location.href='index.php';</script>";
                exit;
            }
        }
    } elseif ($action === 'signup') {
        $username = trim($_POST['username'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($username) && !empty($email) && !empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (name, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $username, $email, $phone, $hashed_password]);
                
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user'] = [
                    'id' => $user_id,
                    'name' => $username,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'coins' => 0
                ];

                header("Location: index.php");
                exit;
            } catch (Exception $e) {
                echo "<script>alert('User with this email or username already exists!'); window.location.href='index.php';</script>";
                exit;
            }
        }
    }
}
header("Location: index.php");
exit;
?>