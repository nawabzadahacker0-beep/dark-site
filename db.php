<?php
// db.php - Database Connection & Table Setup
$db_file = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create Tables if not exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            username TEXT UNIQUE,
            email TEXT UNIQUE,
            phone TEXT,
            password TEXT,
            address TEXT DEFAULT '',
            coins INTEGER DEFAULT 0,
            blocked INTEGER DEFAULT 0,
            is_admin INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            price REAL DEFAULT 0,
            sale_price REAL DEFAULT 0,
            category TEXT,
            images TEXT,
            video TEXT DEFAULT '',
            description TEXT,
            specifications TEXT DEFAULT '',
            stock INTEGER DEFAULT 999,
            is_free INTEGER DEFAULT 0,
            download_url TEXT DEFAULT '',
            offer_price REAL DEFAULT 0,
            offer_end TEXT DEFAULT '',
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            total_amount REAL,
            payment_method TEXT,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS coin_requests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            coins INTEGER,
            payment_method TEXT,
            transaction_id TEXT,
            payment_image TEXT,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS chat_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            sender TEXT,
            message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            message TEXT,
            type TEXT DEFAULT 'info',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Insert Default Sample Product if empty
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM products");
    if ($stmt->fetch()['cnt'] == 0) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, sale_price, category, images, description, is_free, download_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'DARK WI-FI HACKING TOOL',
            0,
            2000,
            'Penetration Testing',
            json_encode(["data/uploads/img_20260902191516_236fdc224b84.png"]),
            'WIFI HACKING TOOL WITH BRUT FORCE THROUGH WIFI CRACKING SYSTEM',
            1,
            'https://www.mediafire.com/file/8cb6duecx4f3dwj/WIFI_PASSWORD_TOOL_BY_THE_DARK_OF_MASTER.zip/file'
        ]);
    }

    // Insert Sample Notification if empty
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM notifications");
    if ($stmt->fetch()['cnt'] == 0) {
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message, type) VALUES (?, ?, ?)");
        $stmt->execute(['MATER AND I'M', 'How to get details master', 'info']);
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>