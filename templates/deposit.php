<div class="card fade-in" style="max-width: 500px; margin: 2rem auto;">
    <h2><i class="fas fa-money-bill-wave"></i> إيداع الأموال</h2>
    <form method="POST" action="index.php?action=deposit">
        <div class="form-group">
            <label><i class="fas fa-dollar-sign"></i> المبلغ (بالدولار)</label>
            <input type="number" name="amount" step="0.01" min="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-paper-plane"></i> إيداع عبر PayPal
        </button>
    </form>
</div>