<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أجيال كاش - منصة التسويق بالعمولة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php?action=home" class="brand">
                <i class="fas fa-coins"></i> أجيال كاش
            </a>
            <ul class="nav-links">
                <li><a href="index.php?action=home"><i class="fas fa-home"></i> الرئيسية</a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a href="index.php?action=profile"><i class="fas fa-user"></i> الملف الشخصي</a></li>
                    <li><a href="index.php?action=deposit"><i class="fas fa-money-bill-wave"></i> إيداع</a></li>
                    <li><a href="index.php?action=bonds"><i class="fas fa-file-invoice-dollar"></i> شراء السندات</a></li>
                    <?php
                    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    if ($stmt->fetch()['is_admin']) { ?>
                        <li><a href="index.php?action=admin"><i class="fas fa-cog"></i> لوحة الإدارة</a></li>
                    <?php } ?>
                    <li><a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                <?php } else { ?>
                    <li><a href="index.php?action=register"><i class="fas fa-user-plus"></i> تسجيل</a></li>
                    <li><a href="index.php?action=login"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a></li>
                <?php } ?>
            </ul>
            <button class="mobile-menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
    <main class="main-content">
        <?php if (isset($error) && $error) { ?>
            <div class="alert error fade-in">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>
        <?php if (isset($success) && $success) { ?>
            <div class="alert success fade-in">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php } ?>