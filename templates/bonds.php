<?php
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT wallet_balance, bonds_wallet FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM bonds ORDER BY bond_value ASC");
$stmt->execute();
$bonds = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT ub.*, b.profit_percentage 
                       FROM user_bonds ub 
                       JOIN bonds b ON ub.bond_id = b.id 
                       WHERE ub.user_id = ? 
                       ORDER BY ub.purchase_date DESC");
$stmt->execute([$user_id]);
$user_bonds = $stmt->fetchAll();
?>
<div class="card fade-in">
    <h2><i class="fas fa-file-invoice-dollar"></i> شراء سندات استثمارية</h2>
    <div class="stats-grid" style="grid-template-columns: 1fr 1fr; margin: 2rem 0;">
        <div class="stat-card">
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <div class="number"><?php echo htmlspecialchars($user['wallet_balance']); ?></div>
            <div class="label">رصيد المحفظة (دولار)</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="number"><?php echo htmlspecialchars($user['bonds_wallet']); ?></div>
            <div class="label">محفظة السندات (دولار)</div>
        </div>
    </div>
    <h3><i class="fas fa-list"></i> السندات المتوفرة</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>قيمة السند (دولار)</th>
                    <th>نسبة الربح</th>
                    <th>الربح اليومي</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bonds as $bond) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bond['bond_value']); ?></td>
                        <td><?php echo htmlspecialchars($bond['profit_percentage']); ?>%</td>
                        <td><?php echo number_format(($bond['bond_value'] * ($bond['profit_percentage'] / 100)) / 365, 2); ?></td>
                        <td>
                            <form method="POST" action="index.php?action=buy_bond">
                                <input type="hidden" name="bond_id" value="<?php echo $bond['id']; ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-shopping-cart"></i> شراء
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <h3><i class="fas fa-history"></i> سنداتك النشطة</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>قيمة السند</th>
                    <th>نسبة الربح</th>
                    <th>الربح اليومي</th>
                    <th>إجمالي الأرباح</th>
                    <th>تاريخ الشراء</th>
                    <th>تاريخ الانتهاء</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($user_bonds)) { ?>
                    <tr>
                        <td colspan="7">لا توجد سندات نشطة حاليًا</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($user_bonds as $bond) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bond['bond_value']); ?></td>
                            <td><?php echo htmlspecialchars($bond['profit_percentage']); ?>%</td>
                            <td><?php echo htmlspecialchars($bond['daily_profit']); ?></td>
                            <td><?php echo htmlspecialchars($bond['total_profit_earned']); ?></td>
                            <td><?php echo htmlspecialchars($bond['purchase_date']); ?></td>
                            <td><?php echo htmlspecialchars($bond['expiry_date']); ?></td>
                            <td><?php echo $bond['status'] == 'active' ? 'نشط' : 'منتهي'; ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>