<?php
session_start();
date_default_timezone_set('Asia/Bahrain');

// إعدادات الاتصال بقاعدة البيانات
$host = 'localhost';
$db = 'ajyalcash_gen25';
$user = 'ajyalcash_gen25';
$pass = 'BesBes22#';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// جلب الإعدادات
$stmt = $pdo->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch();

// فتح ملف لتسجيل التصحيح
$debug_log = fopen('debug.log', 'a');
fwrite($debug_log, "[" . date('Y-m-d H:i:s') . "] Script started\n");
?>