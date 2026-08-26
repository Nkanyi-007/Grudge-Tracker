<?php
require 'includes/db.php';
$pageTitle = "Grudge Details — Grudge Tracker";
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

// Handle adding a comment ("Witness Statement")
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'])) {
    $content = trim($_POST['comment_content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO comments (grudge_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$grudgeId, $userId, $content]);
    }
    header("Location: view-grudge.php?id=" . $grudgeId);
    exit;
}

// Handle toggling a like ("I Relate")
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_like'])) {
    $checkStmt = $pdo->prepare("SELECT id FROM likes WHERE grudge_id = ? AND user_id = ?");
    $checkStmt->execute([$grudgeId, $userId]);
    $existingLike = $checkStmt->fetch();

    if ($existingLike) {
        $deleteStmt = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $deleteStmt->execute([$existingLike['id']]);
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO likes (grudge_id, user_id) VALUES (?, ?)");
        $insertStmt->execute([$grudgeId, $userId]);
    }
    header("Location: view-grudge.php?id=" . $grudgeId);
    exit;
}

// Load the grudge
$grudgeStmt = $pdo->prepare("SELECT * FROM grudges WHERE id = ?");
$grudgeStmt->execute([$grudgeId]);
$grudge = $grudgeStmt->fetch();

if (!$grudge) {
    header("Location: all-grudges.php");
    exit;
}

$isOwner = ($grudge['user_id'] == $userId);

// Load comments
$commentsStmt = $pdo->prepare("
    SELECT c.*, u.username
    FROM comments c
    JOIN users u ON u.id = c.user_id
    WHERE c.grudge_id = ?
    ORDER BY c.created_at ASC
");
$commentsStmt->execute([$grudgeId]);
$comments = $commentsStmt->fetchAll();

// Like count + whether current user has liked it
$likeCountStmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE grudge_id = ?");
$likeCountStmt->execute([$grudgeId]);
$likeCount = $likeCountStmt->fetch()['count'];

$userLikedStmt = $pdo->prepare("SELECT id FROM likes WHERE grudge_id = ? AND user_id = ?");
$userLikedStmt->execute([$grudgeId, $userId]);
$userLiked = (bool) $userLikedStmt->fetch();
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-cyan-pink">GRUDGE DETAILS</h1>
</div>

<div class="grudge-detail-layout">

  <div class="evidence-card grudge-detail-card">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>

    <div class="grudge-detail-header">
      <span class="severity-tag severity-<?php echo strtolower($grudge['severity']); ?>"><?php echo strtoupper($grudge['severity']); ?></span>
      <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $grudge['status'])); ?>"><?php echo $grudge['status']; ?></span>
    </div>

    <h2 class="grudge-detail-title"><?php echo htmlspecialchars($grudge['title']); ?></h2>
    <p class="grudge-detail-meta"><?php echo htmlspecialchars($grudge['person_involved']); ?> · <?php echo htmlspecialchars($grudge['category']); ?> · <?php echo date('M j, Y', strtotime($grudge['date_occurred'])); ?></p>

    <?php if (!empty($grudge['notes'])): ?>
      <p class="grudge-detail-notes"><?php echo htmlspecialchars($grudge['notes']); ?></p>
    <?php endif; ?>

    <?php if (!empty($grudge['emoji'])): ?>
      <p class="grudge-detail-emoji"><?php echo $grudge['emoji']; ?></p>
    <?php endif; ?>

    <div class="grudge-detail-actions">
      <form action="view-grudge.php?id=<?php echo $grudgeId; ?>" method="POST" style="display:inline;">
        <input type="hidden" name="toggle_like" value="1">
        <button type="submit" class="btn-outline <?php echo $userLiked ? 'btn-outline-pink' : 'btn-outline-orange'; ?>">
          <?php echo $userLiked ? '💔 UN-RELATE' : '❤️ I RELATE'; ?> (<?php echo $likeCount; ?>)
        </button>
      </form>

      <?php if ($isOwner): ?>
        <a href="edit-grudge.php?id=<?php echo $grudgeId; ?>" class="btn-outline btn-outline-pink" style="text-decoration:none; display:inline-block;">EDIT</a>
        <a href="confirm-delete.php?id=<?php echo $grudgeId; ?>" class="btn-outline btn-outline-orange" style="text-decoration:none; display:inline-block;">DELETE</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="evidence-card comments-card">
    <h2 class="card-heading">WITNESS STATEMENTS (<?php echo count($comments); ?>)</h2>

    <ul class="comments-list">
      <?php if (count($comments) === 0): ?>
        <li class="comment-item"><p class="comment-text">No witnesses yet. Be the first to weigh in.</p></li>
      <?php endif; ?>
      <?php foreach ($comments as $comment): ?>
      <li class="comment-item">
        <p class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></p>
        <p class="comment-text"><?php echo htmlspecialchars($comment['content']); ?></p>
        <p class="comment-time"><?php echo date('M j, Y g:ia', strtotime($comment['created_at'])); ?></p>
      </li>
      <?php endforeach; ?>
    </ul>

    <form action="view-grudge.php?id=<?php echo $grudgeId; ?>" method="POST" class="comment-form">
      <textarea name="comment_content" rows="2" placeholder="Add a witness statement..." required></textarea>
      <button type="submit" class="btn-sticker btn-pink btn-small" style="margin-top: 0.6rem;">Submit</button>
    </form>
  </div>

</div>

<?php include 'includes/footer.php'; ?>