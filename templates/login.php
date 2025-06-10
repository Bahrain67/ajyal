<div class="card fade-in" style="max-width: 500px; margin: 2rem auto;">
    <h2><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</h2>
    <form method="POST" action="index.php?action=login">
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> كلمة المرور</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
        </button>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="index.php?action=forgot_password">نسيت كلمة المرور؟</a>
        </p>
        <p style="text-align: center; margin-top: 0.5rem;">
            ليس لديك حساب؟ <a href="index.php?action=register">إنشاء حساب</a>
        </p>
    </form>
</div>