<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo "<script>alert('Please login first'); window.location.href='index.html';</script>";
    exit;
}

$product_id = intval($_GET['product_id'] ?? 0);
$pay_with_coins = isset($_GET['pay_with_coins']);

if ($product_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $price = $product['sale_price'] ?: $product['price'];
        $coins_needed = ceil($price / 100);

        if ($pay_with_coins) {
            // Check user coin balance
            $stmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_coins = $stmt->fetchColumn() ?: 0;

            if ($user_coins >= $coins_needed) {
                // Deduct coins & create order
                $pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ?")->execute([$coins_needed, $user_id]);
                $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, 'Coins', 'delivered')")->execute([$user_id, $price]);

                echo "<script>alert('Purchase successful with QC Coins!'); window.location.href='index.html';</script>";
                exit;
            } else {
                echo "<script>alert('Insufficient QC Coins!'); window.location.href='index.html';</script>";
                exit;
            }
        }
    }
}

echo "<script>alert('Order placed successfully!'); window.location.href='index.html';</script>";
exit;
?>