<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetch()['is_admin']) {
        $bond_values = $_POST['bond_value'];
        $profit_percentages = $_POST['profit_percentage'];

        $pdo->beginTransaction();
        try {
            foreach ($bond_values as $bond_id => $value) {
                $profit = filter_input(INPUT_POST, "profit_percentage.$bond_id", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $value = filter_input(INPUT_POST, "bond_value.$bond_id", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt = $pdo->prepare("UPDATE bonds SET bond_value = ?, profit_percentage = ? WHERE id = ?");
                $stmt->execute([$value, $profit, $bond_id]);
                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Updated bond: id: $bond_id, value: $value, profit: $profit\n");
            }
            $pdo->commit();
            $success = "تم تحديث إعدادات السندات بنجاح!";
            header("Location: index.php?action=admin&success=" . urlencode($success));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Bond settings update failed: " . $e->getMessage() . "\n");
            $error = "فشل تحديث إعدادات السندات: " . $e->getMessage();
            header("Location: index.php?action=admin&error=" . urlencode($error));
            exit;
        }
    } else {
        $error = "غير مصرح لك بالوصول إلى لوحة الأدمن!";
        header("Location: index.php?action=admin&error=" . urlencode($error));
        exit;
    }
}
?>