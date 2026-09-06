<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_to_cart') {
        $product_id = intval($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
            exit;
        }
    }

    if ($action === 'remove_from_cart') {
        $product_id = intval($_POST['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        exit;
    }

    if ($action === 'update_cart_qty') {
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        exit;
    }
}
?>