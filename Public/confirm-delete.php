<?php
require 'includes/db.php';
$pageTitle = "Confirm Delete — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$grudgeId = $_GET['id'] ?? null;

if (!$grudgeId) {
    header("Location: all-grudges.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM grudges WHERE id = ? AND user_id = ?");
$stmt->execute([$grudgeId, $userId]);
$grudge = $stmt->fetch();

if (!$grudge) {
    header("Location: all-grudges.php");
    exit;
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-orange-pink">ARE YOU SURE?</h1>
</div>

<div class="evidence-card delete-confirm-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <p class="delete-warning-icon">⚠️</p>
  <p class="delete-warning-text">You're about to permanently delete:</p>
  <p class="delete-grudge-title">"<?php echo htmlspecialchars($grudge['title']); ?>"</p>
  <p class="delete-warning-sub">This can't be undone. Once it's gone, it's gone — no undo, no recovery.</p>

  <div class="delete-confirm-actions">
    <a href="view-grudge.php?id=<?php echo $grudgeId; ?>" class="btn-outline btn-outline-pink" style="text-decoration:none;">CANCEL</a>
    <a href="delete-grudge.php?id=<?php echo $grudgeId; ?>" class="btn-sticker btn-pink" style="text-decoration:none; display:inline-block;">YES, DELETE IT</a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>