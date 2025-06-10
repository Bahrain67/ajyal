<?php
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM pays WHERE wallet_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM rewards WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$rewards = $stmt->fetchAll();
?>
<div class="card fade-in">
    <h2><i class="fas fa-user"></i> الملف الشخصي</h2>
    <form method="POST" action="index.php?action=update_profile">
        <div class="form-group">
            <label><i class="fas fa-user"></i> الاسم الكامل</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-globe"></i> الدولة</label>
            <input type="text" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><i class="fas fa-birthday-cake"></i> العمر</label>
            <input type="number" name="age" value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><i class="fas fa-venus-mars"></i> الجنس</label>
            <select name="gender">
                <option value="">اختر</option>
                <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>ذكر</option>
                <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>أنثى</option>
            </select>
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> كلمة المرور الحالية</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-key"></i> كلمة المرور الجديدة (اختياري)</label>
            <input type="password" name="new_password">
        </div>
        <div class="form-group">
            <label><i class="fas fa-key"></i> تأكيد كلمة المرور الجديدة</label>
            <input type="password" name="confirm_new_password">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-save"></i> تحديث الملف الشخصي
        </button>
    </form>
</div>