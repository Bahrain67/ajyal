<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() == 0) {
        $error = "البريد الإلكتروني غير مسجل!";
    } else {
        $token = bin2hex(random_bytes(50));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);

        if (sendResetEmail($email, $token, $debug_log)) {
            $success = "تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني!";
        } else {
            $error = "فشل إرسال البريد الإلكتروني. حاول لاحقًا.";
        }
    }
}
?>