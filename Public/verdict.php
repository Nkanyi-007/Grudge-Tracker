<?php
require 'includes/db.php';
$pageTitle = "Cast Your Verdict — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$disputeId = $_GET['dispute_id'] ?? null;
$error = "";

if (!$disputeId) {
    header("Location: courtroom.php");
    exit;
}

// Load the case
$disputeStmt = $pdo->prepare("
    SELECT d.*, g.title AS grudge_title
    FROM disputes d
    JOIN grudges g ON g.id = d.grudge_id
    WHERE d.id = ?
");
$disputeStmt->execute([$disputeId]);
$dispute = $disputeStmt->fetch();

if (!$dispute) {
    header("Location: courtroom.php");
    exit;
}

// Confirm this user is actually an invited juror
$isJurorStmt = $pdo->prepare("SELECT id FROM dispute_jurors WHERE dispute_id = ? AND user_id = ?");
$isJurorStmt->execute([$disputeId, $userId]);
$isJuror = (bool) $isJurorStmt->fetch();

$hasVotedStmt = $pdo->prepare("SELECT id FROM jury_votes WHERE dispute_id = ? AND juror_id = ?");
$hasVotedStmt->execute([$disputeId, $userId]);
$hasVoted = (bool) $hasVotedStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vote = $_POST['vote'] ?? '';
    $reasoning = trim($_POST['reasoning'] ?? '');

    if (!$isJuror) {
        $error = "You weren't invited to serve on this jury.";
    } elseif ($hasVoted) {
        $error = "You've already voted on this case.";
    } elseif (empty($vote)) {
        $error = "Please select Guilty or Innocent before submitting.";
    } else {
        $voteStmt = $pdo->prepare("INSERT IGNORE INTO jury_votes (dispute_id, juror_id, vote, reasoning) VALUES (?, ?, ?, ?)");
        $voteStmt->execute([$disputeId, $userId, $vote, $reasoning]);
        header("Location: courtroom.php?dispute_id=" . $disputeId);
        exit;
    }
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-orange-pink">CAST YOUR VERDICT</h1>
</div>

<div class="evidence-card verdict-page-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <p class="auth-subtext">Case #<?php echo $dispute['id']; ?> — <?php echo htmlspecialchars($dispute['grudge_title']); ?></p>

  <?php if ($error): ?>
    <p style="color: var(--pink); font-size: 0.85rem; margin: 1rem 0;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <?php if (!$isJuror): ?>
    <p class="verdict-modal-question">You weren't invited to serve on this jury.</p>
    <a href="courtroom.php?dispute_id=<?php echo $disputeId; ?>" class="btn-sticker btn-pink btn-full" style="margin-top: 1rem;">Back to Case</a>
  <?php elseif ($hasVoted): ?>
    <p class="verdict-modal-question">You've already cast your vote on this case.</p>
    <a href="courtroom.php?dispute_id=<?php echo $disputeId; ?>" class="btn-sticker btn-pink btn-full" style="margin-top: 1rem;">Back to Case</a>
  <?php else: ?>

  <form action="verdict.php?dispute_id=<?php echo $disputeId; ?>" method="POST">
    <p class="verdict-modal-question">Based on the evidence, how do you find the defendant?</p>

    <div class="verdict-choice-grid">
      <label class="verdict-choice-btn choice-guilty" id="choiceGuilty">
        <input type="radio" name="vote" value="Guilty" style="display:none;" onclick="selectVerdict('guilty')">
        <span class="verdict-choice-label">GUILTY</span>
        <span class="verdict-choice-sub">The claims hold up.</span>
      </label>

      <label class="verdict-choice-btn choice-innocent" id="choiceInnocent">
        <input type="radio" name="vote" value="Innocent" style="display:none;" onclick="selectVerdict('innocent')">
        <span class="verdict-choice-label">INNOCENT</span>
        <span class="verdict-choice-sub">Not enough evidence.</span>
      </label>
    </div>

    <div class="verdict-reasoning-group">
      <label for="reasoning">WHY? (OPTIONAL)</label>
      <textarea id="reasoning" name="reasoning" rows="3" placeholder="Explain your reasoning to the court..."></textarea>
    </div>

    <button type="submit" class="btn-sticker btn-submit-verdict">SUBMIT VERDICT</button>
  </form>

  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>