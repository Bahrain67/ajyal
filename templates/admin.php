<?php
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
if (!$stmt->fetch()['is_admin']) {
    header("Location: index.php?action=home&error=" . urlencode("غير مصرح لك بالوصول إلى لوحة الأدمن!"));
    exit;
}
?>
<div class="card fade-in">
    <h2><i class="fas fa-cog"></i> لوحة الإدارة</h2>
    <div class="tabs">
        <div class="tab-buttons">
            <button class="tab-button active" onclick="showTab('settings')">إعدادات العمولات</button>
            <button class="tab-button" onclick="showTab('bonds')">إعدادات السندات</button>
            <button class="tab-button" onclick="showTab('profits')">حساب الأرباح</button>
        </div>
        <div id="settings" class="tab-content active">
            <form method="POST" action="index.php?action=admin">
                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> رسوم الاشتراك</label>
                    <input type="number" name="subscription_fee" step="0.01" value="<?php echo htmlspecialchars($settings['subscription_fee'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-trophy"></i> مكافأة الجيل الأول</label>
                    <input type="number" name="g1_reward" step="0.01" value="<?php echo htmlspecialchars($settings['g1_reward'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-trophy"></i> مكافأة الجيل الثاني</label>
                    <input type="number" name="g2_reward" step="0.01" value="<?php echo htmlspecialchars($settings['g2_reward'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-trophy"></i> مكافأة الجيل الثالث</label>
                    <input type="number" name="g3_reward" step="0.01" value="<?php echo htmlspecialchars($settings['g3_reward'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-trophy"></i> مكافأة الجيل الرابع</label>
                    <input type="number" name="g4_reward" step="0.01" value="<?php echo htmlspecialchars($settings['g4_reward'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-trophy"></i> مكافأة الجيل الخامس</label>
                    <input type="number" name="g5_reward" step="0.01" value="<?php echo htmlspecialchars($settings['g5_reward'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-users"></i> الحد الأقصى للأجيال للحصول على المكافأة</label>
                    <input type="number" name="max_referrals_bonus" value="<?php echo htmlspecialchars($settings['max_referrals_bonus'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> الحد الأقصى لمبلغ المكافأة</label>
                    <input type="number" name="max_bonus_amount" step="0.01" value="<?php echo htmlspecialchars($settings['max_bonus_amount'] ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </form>
        </div>
        <div id="bonds" class="tab-content">
            <h3><i class="fas fa-file-invoice-dollar"></i> إدارة السندات</h3>
            <form method="POST" action="index.php?action=admin_update_bonds">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>قيمة السند (دولار)</th>
                                <th>نسبة الربح (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM bonds ORDER BY bond_value ASC");
                            $stmt->execute();
                            $bonds = $stmt->fetchAll();
                            foreach ($bonds as $bond) { ?>
                                <tr>
                                    <td>
                                        <input type="number" name="bond_value[<?php echo $bond['id']; ?>]" step="0.01" value="<?php echo htmlspecialchars($bond['bond_value']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="profit_percentage[<?php echo $bond['id']; ?>]" step="0.01" value="<?php echo htmlspecialchars($bond['profit_percentage']); ?>" required>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-save"></i> تحديث إعدادات السندات
                </button>
            </form>
        </div>
        <div id="profits" class="tab-content">
            <h3><i class="fas fa-calculator"></i> حساب الأرباح اليومية</h3>
            <p>اضغط على الزر أدناه لحساب الأرباح اليومية لجميع السندات النشطة وإضافتها إلى محافظ المستخدمين.</p>
            <a href="index.php?action=calculate_daily_profits" class="btn btn-success" style="width: 100%; margin-top: 1rem;">
                <i class="fas fa-play"></i> تشغيل حساب الأرباح اليومية
            </a>
        </div>
    </div>
</div>