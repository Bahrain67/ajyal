<?php
require_once __DIR__ . '/../config/config.php';

fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Starting daily profits calculation\n");
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("SELECT ub.id, ub.user_id, ub.daily_profit, ub.expiry_date 
                           FROM user_bonds ub 
                           WHERE ub.status = 'active' AND ub.expiry_date > NOW()");
    $stmt->execute();
    $active_bonds = $stmt->fetchAll();

    foreach ($active_bonds as $bond) {
        $user_id = $bond['user_id'];
        $daily_profit = $bond['daily_profit'];

        $stmt = $pdo->prepare("UPDATE users SET bonds_wallet = bonds_wallet + ? WHERE id = ?");
        $stmt->execute([$daily_profit, $user_id]);

        $stmt = $pdo->prepare("UPDATE user_bonds SET total_profit_earned = total_profit_earned + ? WHERE id = ?");
        $stmt->execute([$daily_profit, $bond['id']]);

        $stmt = $pdo->prepare("INSERT INTO pays (wallet_id, amount, type, description, created_at) VALUES (?, ?, 'credit', ?, NOW())");
        $stmt->execute([$user_id, $daily_profit, "ربح يومي من السند ID: {$bond['id']}"]);

        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Daily profit added: user_id: $user_id, amount: $daily_profit\n");
    }

    $stmt = $pdo->prepare("UPDATE user_bonds SET status = 'expired' WHERE expiry_date <= NOW() AND status = 'active'");
    $stmt->execute();

    $pdo->commit();
    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Daily profits calculation completed\n");
    $success = "تم حساب الأرباح اليومية بنجاح!";
    header("Location: index.php?action=admin&success=" . urlencode($success));
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Daily profits calculation failed: " . $e->getMessage() . "\n");
    $error = "فشل حساب الأرباح اليومية: " . $e->getMessage();
    header("Location: index.php?action=admin&error=" . urlencode($error));
    exit;
}
?>