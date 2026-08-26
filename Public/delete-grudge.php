<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$grudgeId = $_GET['id'] ?? null;

if ($grudgeId) {
    $grudgeStmt = $pdo->prepare("SELECT * FROM grudges WHERE id = ? AND user_id = ?");
    $grudgeStmt->execute([$grudgeId, $userId]);
    $grudge = $grudgeStmt->fetch();

    if ($grudge) {
        $undoCheckStmt = $pdo->prepare("SELECT undo_used FROM users WHERE id = ?");
        $undoCheckStmt->execute([$userId]);
        $undoUsed = $undoCheckStmt->fetch()['undo_used'];

        // Only bother logging a restorable snapshot if the user still has their one undo available
        if (!$undoUsed) {
            $logStmt = $pdo->prepare("INSERT INTO undo_log (user_id, action_type, reference_id, previous_state) VALUES (?, 'delete_grudge', ?, ?)");
            $logStmt->execute([$userId, $grudgeId, json_encode($grudge)]);
        }

        $deleteStmt = $pdo->prepare("DELETE FROM grudges WHERE id = ? AND user_id = ?");
        $deleteStmt->execute([$grudgeId, $userId]);
    }
}

header("Location: all-grudges.php");
exit;