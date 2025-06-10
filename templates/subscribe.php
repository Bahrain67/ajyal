<div class="card fade-in" style="max-width: 500px; margin: 2rem auto;">
    <h2><i class="fas fa-star"></i> تفعيل الاشتراك</h2>
    <p>رسوم الاشتراك: <strong><?php echo htmlspecialchars($settings['subscription_fee']); ?>$</strong></p>
    <?php
    $stmt = $pdo->prepare("SELECT wallet_balance, is_subscribed FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    ?>
    <p>رصيد محفظتك الحالي: <strong><?php echo htmlspecialchars($user['wallet_balance']); ?>$</strong></p>
    <?php if ($user['is_subscribed']) { ?>
        <p style="color: #16a34a; text-align: center; font-weight: 600;">
            <i class="fas fa-check-circle"></i> لقد تم تفعيل اشتراكك مسبقاً!
        </p>
    <?php } else { ?>
        <form method="POST" action="index.php?action=subscribe">
            <div class="form-group">
                <label><i class="fas fa-link"></i> رمز الأجيال (اختياري)</label>
                <input type="text" name="referral_code" placeholder="أدخل رمز الأجيال إذا كان لديك">
            </div>
            <button type="submit" class="btn btn-success" style="width: 100%;">
                <i class="fas fa-star"></i> تفعيل الآن
            </button>
        </form>
    <?php } ?>
</div>