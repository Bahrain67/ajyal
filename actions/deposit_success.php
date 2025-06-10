<?php
require_once __DIR__ . '/../config/config.php';

if (isset($_GET['token']) && (isset($_SESSION['deposit_user_id']) || isset($_SESSION['user_id']))) {
    $user_id = isset($_SESSION['deposit_user_id']) ? $_SESSION['deposit_user_id'] : $_SESSION['user_id'];
    $paymentId = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

    if (!$user_id || !$paymentId) {
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] deposit_success failed: Missing user_id or payment_id\n");
        $error = "خطأ: جلسة المستخدم أو معرف الدفع غير موجود!";
        header("Location: index.php?action=home&error=" . urlencode($error));
        exit;
    }

    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Starting deposit_success: user_id: $user_id, payment_id: $paymentId\n");

    try {
        $clientId = 'AWJDpSZPqwZKyaZfPwouCi_WSNtIfs4TvFTwCFldWXvii1iHkcpZnVEEpZrSh6-bL-WN89GxReEGfGfM';
        $clientSecret = 'EFrwr3IgSe5GyPeNp-xY7haRO_5gvYWLxIAiQbqEnEHeGr8YK9bh2qYBJ5mHMC37V6oHFlBQVPMu6Bc9';
        $baseUrl = 'https://api-m.paypal.com';

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Attempting to get PayPal Access Token\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$baseUrl/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,, CURLOPT_USERPWD, "$clientId:$clientSecret");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Access Token response: HTTP Code: $httpCode, Response: $response\n");
        if ($curlError || $httpCode != 200) {
            throw new Exception("فشل الحصول على Access Token! HTTP Code: $httpCode, cURL Error: $curlError, Response: $response");
        }

        $tokenData = json_decode($response, true);
        if (empty($tokenData['access_token'])) {
            throw new Exception("رمز الوصول غير موجود! Response: " . json_encode($tokenData));
        }
        $accessToken = $tokenData['access_token'];
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Access Token obtained\n");

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Checking order status for payment_id: $paymentId\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$baseUrl/v2/checkout/orders/$paymentId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        $orderResponse = curl_exec($ch);
        $orderHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Order status response: HTTP Code: $orderHttpCode, Response: $orderResponse\n");
        if ($curlError || $orderHttpCode != 200) {
            throw new Exception("فشل التحقق من حالة الطلب! HTTP Code: $orderHttpCode, cURL Error: $curlError, Response: $orderResponse");
        }

        $order = json_decode($orderResponse, true);
        if ($order['status'] !== 'APPROVED') {
            throw new Exception("الطلب لم يُوافق عليه! Status: " . ($order['status'] ?? 'Unknown'));
        }

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Attempting to capture payment for payment_id: $paymentId\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$baseUrl/v2/checkout/orders/$paymentId/capture");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        $captureResponse = curl_exec($ch);
        $captureHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Capture response: HTTP Code: $captureHttpCode, Response: $captureResponse\n");
        if ($curlError || $captureHttpCode != 201) {
            throw new Exception("فشل Capture الدفعة! HTTP Code: $captureHttpCode, cURL Error: $curlError, Response: $captureResponse");
        }

        $capture = json_decode($captureResponse, true);
        if (empty($capture['status']) || $capture['status'] !== 'COMPLETED') {
            throw new Exception("حالة الدفعة غير COMPLETED! Status: " . ($capture['status'] ?? 'Unknown'));
        }

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Fetching deposit for user_id: $user_id\n");
        $stmt = $pdo->prepare("SELECT amount, payment_id FROM deposits WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $deposit = $stmt->fetch();

        if (!$deposit) {
            throw new Exception("لا يوجد إيداع معلق! user_id: $user_id, payment_id: $paymentId");
        }

        $amount = $deposit['amount'];
        $original_payment_id = $deposit['payment_id'];
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Deposit found: Amount: $amount, Original payment_id: $original_payment_id\n");

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Updating wallet and deposit status\n");
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);

            $stmt = $pdo->prepare("UPDATE deposits SET status = 'completed', payment_id = ? WHERE user_id = ? AND payment_id = ? AND status = 'pending'");
            $stmt->execute([$paymentId, $user_id, $original_payment_id]);

            $stmt = $pdo->prepare("INSERT INTO pays (wallet_id, amount, type, description, created_at) VALUES (?, ?, 'credit', ?, NOW())");
            $stmt->execute([$user_id, $amount, "إيداع عبر PayPal: $paymentId"]);

            $pdo->commit();
            fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Deposit completed: user_id: $user_id, Amount: $amount, payment_id: $paymentId\n");
            $success = "تم الإيداع بنجاح! تم إضافة $amount دولار إلى محفظتك.";

            unset($_SESSION['deposit_user_id']);
            unset($_SESSION['deposit_payment_id']);
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("فشل تحديث قاعدة البيانات: " . $e->getMessage());
        }

        header("Location: index.php?action=profile&success=" . urlencode($success));
        exit;
    } catch (Exception $e) {
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Error in deposit_success: " . $e->getMessage() . "\n");
        $stmt = $pdo->prepare("DELETE FROM deposits WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$user_id]);
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Deleted pending deposit: user_id: $user_id\n");

        $error = "فشل الإيداع: " . $e->getMessage();
        header("Location: index.php?action=profile&error=" . urlencode($error));
        exit;
    }
}
?>