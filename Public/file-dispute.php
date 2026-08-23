<?php
require 'includes/db.php';
$pageTitle = "File a Dispute — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$error = "";

$grudgesStmt = $pdo->prepare("SELECT id, title FROM grudges WHERE user_id = ? ORDER BY date_occurred DESC");
$grudgesStmt->execute([$userId]);
$myGrudges = $grudgesStmt->fetchAll();

$usersStmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ? ORDER BY username ASC");
$usersStmt->execute([$userId]);
$otherUsers = $usersStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grudgeId = $_POST['grudge_id'];
    $defendantId = $_POST['defendant_id'];
    $claimText = trim($_POST['claim']);
    $selectedJurors = $_POST['jurors'] ?? [];

    if (empty($grudgeId) || empty($defendantId) || empty($claimText)) {
        $error = "Please fill in all fields.";
    } elseif (empty($selectedJurors)) {
        $error = "Please select at least one juror.";
    } else {
        $disputeStmt = $pdo->prepare("INSERT INTO disputes (grudge_id, filed_by, defendant_id) VALUES (?, ?, ?)");
        $disputeStmt->execute([$grudgeId, $userId, $defendantId]);
        $disputeId = $pdo->lastInsertId();

        $claimStmt = $pdo->prepare("INSERT INTO dispute_claims (dispute_id, submitted_by, side, content) VALUES (?, ?, 'Prosecution', ?)");
        $claimStmt->execute([$disputeId, $userId, $claimText]);

        // Invite only the specifically selected jurors
        $inviteStmt = $pdo->prepare("INSERT INTO dispute_jurors (dispute_id, user_id) VALUES (?, ?)");
        $notifyJurorStmt = $pdo->prepare("INSERT INTO notifications (user_id, dispute_id, type, message) VALUES (?, ?, 'juror_invite', ?)");

        foreach ($selectedJurors as $jurorId) {
            // Never allow the defendant to also be picked as a juror on their own case
            if ($jurorId == $defendantId) continue;

            $inviteStmt->execute([$disputeId, $jurorId]);
            $notifyJurorStmt->execute([$jurorId, $disputeId, "You've been called for jury duty on Case #$disputeId."]);
        }

        // Notify the defendant
        $notifyDefendantStmt = $pdo->prepare("INSERT INTO notifications (user_id, dispute_id, type, message) VALUES (?, ?, 'new_evidence', ?)");
        $notifyDefendantStmt->execute([$defendantId, $disputeId, "A dispute has been filed against you on Case #$disputeId."]);

        header("Location: courtroom.php?dispute_id=" . $disputeId);
        exit;
    }
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-orange-pink">FILE A DISPUTE</h1>
</div>

<div class="evidence-card log-grudge-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <?php if ($error): ?>
    <p style="color: var(--pink); font-size: 0.85rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <?php if (count($myGrudges) === 0): ?>
    <p class="auth-subtext">You need to log a grudge before you can dispute it. <a href="log-grudge.php" style="color: var(--cyan);">Log one now →</a></p>
  <?php elseif (count($otherUsers) === 0): ?>
    <p class="auth-subtext">No other users exist yet. Register a second test account first.</p>
  <?php else: ?>

  <form action="file-dispute.php" method="POST" class="grudge-form">
    <div class="form-group">
      <label for="grudge_id">WHICH GRUDGE</label>
      <select id="grudge_id" name="grudge_id" required>
        <option value="">Select a grudge to dispute</option>
        <?php foreach ($myGrudges as $g): ?>
          <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['title']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="defendant_id">WHO ARE YOU DISPUTING</label>
      <select id="defendant_id" name="defendant_id" required>
        <option value="">Select the defendant</option>
        <?php foreach ($otherUsers as $u): ?>
          <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['username']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label>SELECT YOUR JURY</label>
      <div class="juror-checkbox-list">
        <?php foreach ($otherUsers as $u): ?>
        <label class="juror-checkbox-item">
          <input type="checkbox" name="jurors[]" value="<?php echo $u['id']; ?>">
          <span><?php echo htmlspecialchars($u['username']); ?></span>
        </label>
        <?php endforeach; ?>
      </div>
      <p class="upload-hint">Pick who you want deciding this case. The person you're disputing can't also serve as a juror.</p>
    </div>

    <div class="form-group">
      <label for="claim">YOUR OPENING CLAIM</label>
      <textarea id="claim" name="claim" rows="4" placeholder="State your case to the court..." required></textarea>
    </div>

    <button type="submit" class="btn-sticker btn-submit-grudge">OPEN THE CASE</button>
  </form>

  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>