<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coins = intval($_POST['coins'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');
    $transaction_id = trim($_POST['transaction_id'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($coins <= 0 || empty($transaction_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit;
    }

    $image_path = '';
    if (isset($_FILES['payment_image']) && $_FILES['payment_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['payment_image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['payment_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO coin_requests (user_id, coins, payment_method, transaction_id, payment_image) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $coins, $payment_method, $transaction_id, $image_path])) {
        echo json_encode(['success' => true, 'message' => 'Coin request submitted successfully! Pending admin approval.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit request.']);
    }
    exit;
}
?>