<?php if (isset($_SESSION['user_id'])) { ?>
    <!-- Dashboard -->
    <?php
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT username, is_subscribed, referral_code, wallet_balance, bonds_wallet FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    $generations = [];
    for ($i = 1; $i <= 5; $i++) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM referrals WHERE referrer_id = ? AND generation = ?");
        $stmt->execute([$user_id, $i]);
        $generations[$i] = $stmt->fetch()['count'];
    }
    ?>
    <div class="hero fade-in">
        <p>أهلاً وسهلاً بك في منصة أجيال كاش</p>
        <h1><?php echo htmlspecialchars($user['username']); ?></h1>
        <?php if (!$user['is_subscribed']) { ?>
            <a href="index.php?action=subscribe" class="btn btn-success btn-large pulse">
                <i class="fas fa-star"></i> تفعيل الاشتراك <?php echo htmlspecialchars($settings['subscription_fee']); ?>$
            </a>
        <?php } ?>
    </div>

    <?php if ($user['is_subscribed']) { ?>
        <div class="card fade-in">
            <h2><i class="fas fa-link"></i> رمز الأجيال الخاص بك</h2>
            <div style="background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 2rem; border-radius: 15px; text-align: center; font-size: 1.5rem; font-weight: 700; letter-spacing: 2px;">
                <?php echo htmlspecialchars($user['referral_code']); ?>
            </div>
            <p style="text-align: center; margin-top: 1rem; color: #666;">شارك هذا الرمز مع أصدقائك</p>
        </div>
    <?php } ?>

    <div class="stats-grid">
        <div class="stat-card fade-in">
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <div class="number"><?php echo htmlspecialchars($user['wallet_balance']); ?></div>
            <div class="label">رصيد المحفظة (دولار)</div>
        </div>
        <div class="stat-card fade-in">
            <div class="icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="number"><?php echo htmlspecialchars($user['bonds_wallet']); ?></div>
            <div class="label">محفظة السندات (دولار)</div>
        </div>
    </div>
    <div class="card fade-in">
        <h2><i class="fas fa-sitemap"></i> شبكة الأجيال</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>الجيل</th>
                        <th>عدد الأعضاء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                        <tr>
                            <td>الجيل <?php echo $i; ?></td>
                            <td><?php echo $generations[$i]; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <!-- Welcome Page -->
    <div class="hero fade-in">
        <h1>مرحباً بكم في أجيال كاش</h1>
        <p>منصة تسويق الأجيال الرائدة في العالم</p>
        <div style="margin-top: 2rem;">
            <a href="index.php?action=register" class="btn btn-primary btn-large" style="margin: 0.5rem;">
                <i class="fas fa-user-plus"></i> انضم إلينا الآن
            </a>
            <a href="index.php?action=login" class="btn btn-success btn-large" style="margin: 0.5rem;">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </a>
        </div>
    </div>

    <div class="features-grid">
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <h3>أرباح متزايدة</h3>
            <p>نظام عمولات متطور يضمن لك أرباحاً مستمرة ومتنامية من خلال 5 أجيال</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-users"></i></div>
            <h3>شبكة واسعة</h3>
            <p>انضم لشبكة من الآلاف من المسوقين الناجحين واستفد من خبراتهم</p>
        </div>
        <div class="feature-card fade-in">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <h3>أمان مضمون</h3>
            <p>حماية قصوى لبياناتك ومعاملاتك المالية مع أحدث تقنيات الأمان</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card fade-in">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div class="number">10,000+</div>
            <div class="label">عضو نشط</div>
        </div>
        <div class="stat-card fade-in">
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="number">$250K+</div>
            <div class="label">إجمالي الأرباح</div>
        </div>
        <div class="stat-card fade-in">
            <div class="icon"><i class="fas fa-star"></i></div>
            <div class="number">95%</div>
            <div class="label">معدل الرضا</div>
        </div>
        <div class="stat-card fade-in">
            <div class="icon"><i class="fas fa-headset"></i></div>
            <div class="number">24/7</div>
            <div class="label">دعم فني</div>
        </div>
    </div>
<?php } ?>