<div class="card fade-in" style="max-width: 500px; margin: 2rem auto;">
    <h2><i class="fas fa-key"></i> إعادة تعيين كلمة المرور</h2>
    <form method="POST" action="index.php?action=forgot_password">
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
            <input type="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-paper-plane"></i> إرسال رابط إعادة التعيين
        </button>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="index.php?action=login">العودة إلى تسجيل الدخول</a>
        </p>
    </form>
</div>