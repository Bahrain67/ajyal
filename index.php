<?php
require_once __DIR__ . '/config/config.php';

// تحديد الإجراء
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
$error = $success = '';

fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Starting action: $action\n");

// معالجة الإجراءات
if ($_SERVER['REQUEST_METHOD'] == 'POST' || in_array($action, ['deposit_success', 'deposit_cancel', 'logout'])) {
    switch ($action) {
        case 'register':
            include __DIR__ . '/actions/register.php';
            break;
        case 'login':
            includeeeper.php';
            break;
        case 'forgot_password':
            include __DIR__ . '/actions/forgot_password.php';
            break;
        case 'reset_password':
            include __DIR__ . '/actions/reset_password.php';
            break;
        case 'update_profile':
            include __DIR__ . '/actions/update_profile.php';
            break;
        case 'subscribe':
            include __DIR__ . '/actions/subscribe.php';
            break;
        case 'deposit':
            include __DIR__ . '/actions/deposit.php';
            break;
        case 'deposit_success':
            include __DIR__ . '/actions/deposit_success.php';
            break;
        case 'deposit_cancel':
            include __DIR__ . '/actions/deposit_cancel.php';
            break;
        case 'buy_bond':
            include __DIR__ . '/actions/buy_bond.php';
            break;
        case 'calculate_daily_profits':
            include __DIR__ . '/actions/calculate_profits.php';
            break;
        case 'admin':
            include __DIR__ . '/actions/admin.php';
            break;
        case 'admin_update_bonds':
            include __DIR__ . '/actions/admin_update_bonds.php';
            break;
    }
}

if ($action == 'logout' && isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: index.php?action=home");
    exit;
}

// تضمين رأس الصفحة
include __DIR__ . '/templates/header.php';

// تضمين القالب بناءً على الإجراء
switch ($action) {
    case 'home':
        include __DIR__ . '/templates/home.php';
        break;
    case 'register':
        if (!isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/register.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'login':
        if (!isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/login.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'forgot_password':
        if (!isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/forgot_password.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'reset_password':
        if (!isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/reset_password.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'subscribe':
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/subscribe.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'deposit':
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/deposit.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'bonds':
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/bonds.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'profile':
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/profile.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    case 'admin':
        if (isset($_SESSION['user_id'])) {
            include __DIR__ . '/templates/admin.php';
        } else {
            include __DIR__ . '/templates/error.php';
        }
        break;
    default:
        include __DIR__ . '/templates/error.php';
        break;
}

// تضمين تذييل الصفحة
include __DIR__ . '/templates/footer.php';
?>