<div class="card fade-in" style="max-width: 500px; margin: 2rem auto;">
    <h2><i class="fas fa-user-plus"></i> إنشاء حساب جديد</h2>
    <form method="POST" action="index.php?action=register">
        <div class="form-group">
            <label><i class="fas fa-user"></i> الاسم الكامل</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> كلمة المرور</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-user-plus"></i> تسجيل
        </button>
        <p style="text-align: center; margin-top: 1rem;">
            لديك حساب؟ <a href="index.php?action=login">تسجيل الدخول</a>
        </p>
    </form>
</div>