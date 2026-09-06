<?php
session_start();
require_once 'db.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found!");
}
$images = json_decode($product['images'], true) ?: ['https://i.ibb.co/fz7R0tTG/hacking-dark-web-threat-png.jpg'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - DARK SITE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6 font-sans">
    <a href="index.html" class="inline-block mb-4 text-red-500 font-bold">&larr; Back to Home</a>
    <div class="max-w-2xl mx-auto bg-gray-900 border border-red-900/30 rounded-2xl p-6">
        <img src="<?= htmlspecialchars($images[0]) ?>" class="w-full h-64 object-cover rounded-xl mb-4" alt="Product Image">
        <h1 class="text-2xl font-bold text-red-500 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="text-gray-400 text-sm mb-4"><?= htmlspecialchars($product['category']) ?></p>
        <div class="text-xl font-black mb-4">
            <?= $product['is_free'] ? '<span class="text-green-500">FREE</span>' : 'Rs. ' . number_format($product['sale_price'] ?: $product['price']) ?>
        </div>
        <p class="text-gray-300 text-sm mb-6"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <?php if ($product['is_free'] && !empty($product['download_url'])): ?>
            <a href="<?= htmlspecialchars($product['download_url']) ?>" target="_blank" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl transition">
                <i class="fa-solid fa-download mr-2"></i> Download Tool
            </a>
        <?php else: ?>
            <button onclick="alert('Order Feature Ready!')" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition">
                Buy Now
            </button>
        <?php endif; ?>
    </div>
</body>
</html>