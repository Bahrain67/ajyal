<?php
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $payment_id = filter_input(INPUT_GET, 'payment_id', FILTER_SANITIZE_STRING);

    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Deposit cancelled: user_id: $user_id, payment_id: $payment_id\n");

    if ($payment_id) {
        $stmt = $pdo->prepare("DELETE FROM deposits WHERE user_id = ? AND payment_id = ? AND status = 'pending'");
        $stmt->execute([$user_id, $payment_id]);
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Deleted pending deposit: payment_id: $payment_id\n");
    }

    unset($_SESSION['deposit_user_id']);
    unset($_SESSION['deposit_payment_id']);

    $error = "تم إلغاء عملية الإيداع!";
    header("Location: index.php?action=profile&error=" . urlencode($error));
    exit;
}
?>