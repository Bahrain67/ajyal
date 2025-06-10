<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT is_subscribed, wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] User ID: $user_id, Subscribed: {$user['is_subscribed']}, Wallet: {$user['wallet_balance']}\n");

    if ($user['is_subscribed']) {
        $error = "لقد اشتركت بالفعل!";
    } else {
        $referral_code = filter_input(INPUT_POST, 'referral_code', FILTER_SANITIZE_STRING);
        $fee = $settings['subscription_fee'];
        if ($user['wallet_balance'] < $fee) {
            $error = "رصيد المحفظة غير كافٍ!";
        } else {
            $new_referral_code = substr(str_shuffle('0123456789'), 0, 8);
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ?, is_subscribed = 1, referral_code = ? WHERE id = ?");
                $stmt->execute([$fee, $new_referral_code, $user_id]);

                $stmt = $pdo->prepare("UPDATE platform_wallet SET balance = balance + ? WHERE id = 1");
                $stmt->execute([$fee]);
                $stmt = $pdo->prepare("INSERT INTO pays (wallet_id, amount, type, description, created_at) VALUES (?, ?, 'credit', ?, NOW())");
                $stmt->execute([1, $fee, "رسوم اشتراك المستخدم ID: $user_id"]);
                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Subscription fee added to platform wallet: $fee\n");

                if ($referral_code) {
                    $stmt = $pdo->prepare("SELECT id, referral_code, referred_by FROM users WHERE referral_code = ?");
                    $stmt->execute([$referral_code]);
                    $referrer = $stmt->fetch();
                    if ($referrer) {
                        $stmt = $pdo->prepare("UPDATE users SET referred_by = ? WHERE id = ?");
                        $stmt->execute([$referral_code, $user_id]);

                        $stmt = $pdo->prepare("INSERT INTO referrals (referrer_id, referred_id, generation) VALUES (?, ?, 1)");
                        $stmt->execute([$referrer['id'], $user_id]);
                        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Referral added: Referrer ID: {$referrer['id']}, Referred ID: $user_id, Generation: 1\n");

                        $reward = $settings['g1_reward'];
                        $stmt = $pdo->prepare("UPDATE platform_wallet SET balance = balance - ? WHERE id = 1");
                        $stmt->execute([$reward]);
                        $stmt = $pdo->prepare("INSERT INTO pays (wallet_id, amount, type, description, created_at) VALUES (?, ?, 'debit', ?, NOW())");
                        $stmt->execute([1, $reward, "مكافأة الجيل الأول للمستخدم ID: {$referrer['id']}"]);
                        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                        $stmt->execute([$reward, $referrer['id']]);
                        $stmt = $pdo->prepare("INSERT INTO rewards (user_id, amount, generation, referenced_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$referrer['id'], $reward, 1, $user_id]);
                        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Reward added: User ID: {$referrer['id']}, Amount: $reward, Generation: 1\n");

                        $generation = 1;
                        $current_referrer = $referrer;
                        while ($current_referrer['referred_by'] && $generation < 5) {
                            $stmt = $pdo->prepare("SELECT id, referral_code, referred_by FROM users WHERE referral_code = ?");
                            $stmt->execute([$current_referrer['referred_by']]);
                            $parent = $stmt->fetch();
                            if ($parent) {
                                $generation++;
                                $stmt = $pdo->prepare("INSERT INTO referrals (referrer_id, referred_id, generation) VALUES (?, ?, ?)");
                                $stmt->execute([$parent['id'], $user_id, $generation]);
                                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Referral added: Referrer ID: {$parent['id']}, Referred ID: $user_id, Generation: $generation\n");

                                $reward = $settings["g{$generation}_reward"];
                                $stmt = $pdo->prepare("UPDATE platform_wallet SET balance = balance - ? WHERE id = 1");
                                $stmt->execute([$reward]);
                                $stmt = $pdo->prepare("INSERT INTO pays (wallet_id, amount, type, description, created_at) VALUES (?, ?, 'debit', ?, NOW())");
                                $stmt->execute([1, $reward, "مكافأة الجيل $generation للمستخدم ID: {$parent['id']}"]);
                                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                                $stmt->execute([$reward, $parent['id']]);
                                $stmt = $pdo->prepare("INSERT INTO rewards (user_id, amount, generation, referenced_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                                $stmt->execute([$parent['id'], $reward, $generation, $user_id]);
                                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Reward added: User ID: {$parent['id']}, Amount: $reward, Generation: $generation\n");
                                $current_referrer = $parent;
                            } else {
                                break;
                            }
                        }
                    }
                }

                $stmt = $pdo->prepare("SELECT balance FROM platform_wallet WHERE id = 1");
                $stmt->execute();
                $platform_balance = $stmt->fetch()['balance'];
                if ($platform_balance < 0) {
                    throw new Exception("رصيد محفظة المنصة غير كافٍ لتوزيع المكافآت!");
                }

                $pdo->commit();
                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Transaction committed\n");
                header("Location: index.php?action=home");
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "خطأ: " . $e->getMessage();
                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Transaction rolled back: " . $e->getMessage() . "\n");
            }
        }
    }
}
?>