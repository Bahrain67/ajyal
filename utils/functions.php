<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

// وظيفة لإرسال بريد إلكتروني
function sendResetEmail($email, $token, $debug_log) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.hylpress.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@hylpress.net';
        $mail->Password = 'BesBes22#';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('info@hylpress.net', 'أجيال كاش');
        $mail->addAddress($email);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = 'إعادة تعيين كلمة المرور';
        $reset_link = "https://ajyalcash.com/index.php?action=reset_password&token=$token";
        $mail->Body = "
            <h2>إعادة تعيين كلمة المرور</h2>
            <p>لقد طلبت إعادة تعيين كلمة المرور الخاصة بك. انقر على الرابط أدناه لتغيير كلمة المرور:</p>
            <p><a href='$reset_link'>إعادة تعيين كلمة المرور</a></p>
            <p>الرابط صالح لمدة 24 ساعة. إذا لم تطلب هذا، تجاهل الرسالة.</p>
        ";
        $mail->AltBody = "لإعادة تعيين كلمة المرور، انسخ الرابط: $reset_link\nالرابط صالح لمدة 24 ساعة.";
        $mail->send();
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Password reset email sent to: $email\n");
        return true;
    } catch (Exception $e) {
        fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] PHPMailer error: {$mail->ErrorInfo}\n");
        return false;
    }
}
?>