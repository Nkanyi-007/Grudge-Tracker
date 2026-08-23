<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$notificationId = $_GET['id'] ?? null;

if ($notificationId) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notificationId, $_SESSION['user_id']]);
}

header("Location: dashboard.php");
exit;