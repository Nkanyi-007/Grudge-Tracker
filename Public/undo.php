<?php
require 'includes/db.php';
$pageTitle = "One Undo, Ever — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$undoCheckStmt = $pdo->prepare("SELECT undo_used FROM users WHERE id = ?");
$undoCheckStmt->execute([$userId]);
$undoUsed = $undoCheckStmt->fetch()['undo_used'];

$availableUndoStmt = $pdo->prepare("SELECT * FROM undo_log WHERE user_id = ? AND restored = 0 ORDER BY created_at DESC LIMIT 1");
$availableUndoStmt->execute([$userId]);
$availableUndo = $availableUndoStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore']) && $availableUndo && !$undoUsed) {
    $state = json_decode($availableUndo['previous_state'], true);

    $restoreStmt = $pdo->prepare("INSERT INTO grudges (user_id, title, person_involved, category, severity, status, emoji, notes, date_occurred) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $restoreStmt->execute([
        $state['user_id'], $state['title'], $state['person_involved'], $state['category'],
        $state['severity'], $state['status'], $state['emoji'], $state['notes'], $state['date_occurred']
    ]);

    $markRestoredStmt = $pdo->prepare("UPDATE undo_log SET restored = 1 WHERE id = ?");
    $markRestoredStmt->execute([$availableUndo['id']]);

    $markUsedStmt = $pdo->prepare("UPDATE users SET undo_used = 1, undo_used_at = NOW() WHERE id = ?");
    $markUsedStmt->execute([$userId]);

    header("Location: all-grudges.php");
    exit;
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-orange-pink">ONE UNDO, EVER</h1>
</div>

<div class="evidence-card delete-confirm-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <?php if ($undoUsed): ?>
    <p class="delete-warning-icon">🚫</p>
    <p class="delete-warning-text">You've already used your one undo.</p>
    <p class="delete-warning-sub">That's the deal — think twice next time.</p>
    <a href="dashboard.php" class="btn-sticker btn-pink btn-full" style="margin-top:1rem;">Back to Dashboard</a>
  <?php elseif (!$availableUndo): ?>
    <p class="delete-warning-icon">↩️</p>
    <p class="delete-warning-text">Nothing to undo right now.</p>
    <p class="delete-warning-sub">Your one undo is still saved for when you really need it.</p>
    <a href="dashboard.php" class="btn-sticker btn-pink btn-full" style="margin-top:1rem;">Back to Dashboard</a>
  <?php else: ?>
    <?php $state = json_decode($availableUndo['previous_state'], true); ?>
    <p class="delete-warning-icon">↩️</p>
    <p class="delete-warning-text">You can restore:</p>
    <p class="delete-grudge-title">"<?php echo htmlspecialchars($state['title']); ?>"</p>
    <p class="delete-warning-sub">This is your only undo. Ever. Use it wisely — there's no second chance after this.</p>

    <form method="POST" style="text-align:center;">
      <input type="hidden" name="restore" value="1">
      <button type="submit" class="btn-sticker btn-pink">RESTORE IT</button>
    </form>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>