<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $current_password = $_POST['current_password'];
    $new_password = !empty($_POST['new_password']) ? $_POST['new_password'] : null;
    $confirm_new_password = !empty($_POST['confirm_new_password']) ? $_POST['confirm_new_password'] : null;
    $country = !empty($_POST['country']) ? filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING) : null;
    $age = !empty($_POST['age']) ? filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT) : null;
    $gender = !empty($_POST['gender']) ? filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING) : null;

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!password_verify($current_password, $user['password'])) {
        $error = "كلمة المرور الحالية غير صحيحة!";
    } elseif (mb_strlen($username) < 4 || mb_strlen($username) > 50) {
        $error = "اسم المستخدم يجب أن يكون بين 4 و50 حرفًا!";
    } elseif ($new_password && ($new_password !== $confirm_new_password)) {
        $error = "كلمتا المرور الجديدتان غير متطابقتين!";
    } elseif ($new_password && strlen($new_password) < 6) {
        $error = "كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->rowCount() > 0) {
            $error = "البريد الإلكتروني مسجل مسبقًا!";
        } else {
            $pdo->beginTransaction();
            try {
                $update_query = "UPDATE users SET username = ?, email = ?";
                $update_params = [$username, $email];

                if ($new_password) {
                    $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $update_query .= ", password = ?";
                    $update_params[] = $hashed_new_password;
                }

                $update_query .= ", country = ?, age = ?, gender = ?";
                $update_params[] = $country;
                $update_params[] = $age;
                $update_params[] = $gender;
                $update_query .= " WHERE id = ?";
                $update_params[] = $user_id;

                $stmt = $pdo->prepare($update_query);
                $stmt->execute($update_params);

                $pdo->commit();
                $success = "تم تحديث الملف الشخصي بنجاح!";
                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Profile updated for user ID: $user_id\n");
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "خطأ أثناء تحديث الملف الشخصي: " . $e->getMessage();
                fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Profile update failed: " . $e->getMessage() . "\n");
            }
        }
    }
}
?>