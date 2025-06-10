<div class="card fade-in" style="max-width: 500px; margin: 2rem auto;">
    <h2><i class="fas fa-key"></i> إعادة تعيين كلمة المرور</h2>
    <form method="POST" action="index.php?action=reset_password">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <div class="form-group">
            <label><i class="fas fa-lock"></i> كلمة المرور الجديدة</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> تأكيد كلمة المرور</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-save"></i> تغيير كلمة المرور
        </button>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="index.php?action=login">العودة إلى تسجيل الدخول</a>
        </p>
    </form>
</div>