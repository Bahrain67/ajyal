<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $bond_id = filter_input(INPUT_POST, 'bond_id', FILTER_SANITIZE_NUMBER_INT);

    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Starting buy_bond: user_id: $user_id, bond_id: $bond_id\n");

    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT bond_value, profit_percentage FROM bonds WHERE id = ?");
    $stmt->execute([$bond_id]);
    $bond = $stmt->fetch();

    if (!$bond) {
        $error = "السند غير موجود!";
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Bond not found: bond_id: $bond_id\n");
    } elseif ($user['wallet_balance'] < $bond['bond_value']) {
        $error = "رصيد المحفظة غير كافٍ لشراء السند!";
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Insufficient wallet balance: user_id: $user_id, wallet: {$user['wallet_balance']}, bond_value: {$bond['bond_value']}\n");
    } else {
        $pdo->beginTransaction();
        try {
            $daily_profit = ($bond['bond_value'] * ($bond['profit_percentage'] / 100)) / 365;
            $expiry_date = date('Y-m-d H:i:s', strtotime('+1 year'));

            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
            $stmt->execute([$bond['bond_value'], $user_id]);

            $stmt = $pdo->prepare("INSERT INTO user_bonds (user_id, bond_id, purchase_date, expiry_date, bond_value, daily_profit, status) VALUES (?, ?, NOW(), ?, ?, ?, 'active')");
            $stmt->execute([$user_id, $bond_id, $expiry_date, $bond['bond_value'], $daily_profit]);

            $stmt = $pdo->prepare("INSERT INTO pays (wallet_id, amount, type, description, created_at) VALUES (?, ?, 'debit', ?, NOW())");
            $stmt->execute([$user_id, $bond['bond_value'], "شراء سند استثماري بقيمة {$bond['bond_value']} دولار"]);

            $pdo->commit();
            fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Bond purchase successful: user_id: $user_id, bond_id: $bond_id, bond_value: {$bond['bond_value']}\n");
            $success = "تم شراء السند بنجاح! سيتم إضافة الأرباح اليومية إلى محفظة السندات.";
            header("Location: index.php?action=bonds&success=" . urlencode($success));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "فشل شراء السند: " . $e->getMessage();
            fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Bond purchase failed: " . $e->getMessage() . "\n");
            header("Location: index.php?action=bonds&error=" . urlencode($error));
            exit;
        }
    }
    header("Location: index.php?action=bonds&error=" . urlencode($error));
    exit;
}
?>