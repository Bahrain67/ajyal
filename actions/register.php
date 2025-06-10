<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $error = "البريد الإلكتروني مسجل مسبقًا!";
    } elseif (mb_strlen($username) < 4 || mb_strlen($username) > 50) {
        $error = "اسم المستخدم يجب أن يكون بين 4 و50 حرفًا!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_subscribed, wallet_balance, is_admin, bonds_wallet) VALUES (?, ?, ?, 0, 0.00, 0, 0.00)");
        $stmt->execute([$username, $email, $password]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header("Location: index.php?action=home");
        exit;
    }
}
?>