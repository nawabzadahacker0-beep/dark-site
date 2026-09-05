<?php
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');

error_reporting(0);
ini_set('display_errors', 0);

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dark_site';

$conn = @new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed!']);
    exit;
}

// Table Auto-create
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    coins INT DEFAULT 0,
    blocked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, username, email, phone, password, coins, blocked FROM users WHERE username = ? OR email = ? OR phone = ? LIMIT 1");
    $stmt->bind_param("sss", $identifier, $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (!empty($user['blocked'])) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Account is blocked by admin.']);
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['phone']     = $user['phone'];
            $_SESSION['coins']     = (int)$user['coins'];
            $_SESSION['is_logged'] = true;

            ob_clean();
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Successful!',
                'user' => [
                    'username' => $user['username'],
                    'email'    => $user['email'],
                    'phone'    => $user['phone'],
                    'coins'    => (int)$user['coins']
                ]
            ]);
            exit;
        } else {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Invalid Password.']);
            exit;
        }
    } else {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'No account found with this identifier.']);
        exit;
    }
}

if ($action === 'signup') {
    $username = trim($_POST['username'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($phone) || empty($email) || empty($password)) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Please fill all signup fields.']);
        exit;
    }

    if (strlen($password) < 8) {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
        exit;
    }

    $chk = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
    $chk->bind_param("ss", $email, $username);
    $chk->execute();
    $chk->store_result();

    if ($chk->num_rows > 0) {
        $chk->close();
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Username or Email already exists!']);
        exit;
    }
    $chk->close();

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (name, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $username, $email, $phone, $hashed);

    if ($stmt->execute()) {
        $_SESSION['user_id']   = $stmt->insert_id;
        $_SESSION['username']  = $username;
        $_SESSION['email']     = $email;
        $_SESSION['phone']     = $phone;
        $_SESSION['coins']     = 0;
        $_SESSION['is_logged'] = true;

        ob_clean();
        echo json_encode([
            'status' => 'success',
            'message' => 'Account created successfully!',
            'user' => [
                'username' => $username,
                'email'    => $email,
                'phone'    => $phone,
                'coins'    => 0
            ]
        ]);
        exit;
    } else {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database error while saving.']);
        exit;
    }
}

$conn->close();
ob_end_flush();
?>
