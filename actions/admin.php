<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetch()['is_admin']) {
        $subscription_fee = filter_input(INPUT_POST, 'subscription_fee', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $g1_reward = filter_input(INPUT_POST, 'g1_reward', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $g2_reward = filter_input(INPUT_POST, 'g2_reward', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $g3_reward = filter_input(INPUT_POST, 'g3_reward', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $g4_reward = filter_input(INPUT_POST, 'g4_reward', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $g5_reward = filter_input(INPUT_POST, 'g5_reward', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $max_referrals_bonus = filter_input(INPUT_POST, 'max_referrals_bonus', FILTER_SANITIZE_NUMBER_INT);
        $max_bonus_amount = filter_input(INPUT_POST, 'max_bonus_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $stmt = $pdo->prepare("UPDATE settings SET subscription_fee = ?, g1_reward = ?, g2_reward = ?, g3_reward = ?, g4_reward = ?, g5_reward = ?, max_referrals_bonus = ?, max_bonus_amount = ? WHERE id = 1");
        $stmt->execute([$subscription_fee, $g1_reward, $g2_reward, $g3_reward, $g4_reward, $g5_reward, $max_referrals_bonus, $max_bonus_amount]);
        $success = "تم تحديث الإعدادات بنجاح!";
        header("Location: index.php?action=admin");
        exit;
    } else {
        $error = "غير مصرح لك بالوصول إلى لوحة الأدمن!";
    }
}
?>