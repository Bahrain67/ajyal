<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Starting deposit: user_id: $user_id, amount: $amount\n");

    if ($amount <= 0) {
        $error = "يرجى إدخال مبلغ صالح أكبر من 0!";
        header("Location: index.php?action=deposit&error=" . urlencode($error));
        exit;
    }

    try {
        $payment_id = uniqid('ORDER_');
        $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, payment_id, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->execute([$user_id, $amount, $payment_id]);
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Created pending deposit: payment_id: $payment_id\n");

        $_SESSION['deposit_user_id'] = $user_id;
        $_SESSION['deposit_payment_id'] = $payment_id;

        include __DIR__ . '/../templates/deposit_payment.php';
        exit;
    } catch (Exception $e) {
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Deposit error: " . $e->getMessage() . "\n");
        $error = "خطأ أثناء إنشاء الطلب: " . $e->getMessage();
        header("Location: index.php?action=deposit&error=" . urlencode($error));
        exit;
    }
}
?>