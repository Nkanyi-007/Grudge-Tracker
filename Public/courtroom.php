<?php
require 'includes/db.php';
$pageTitle = "Dispute Courtroom — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_content'])) {
    $disputeId = $_POST['dispute_id'];
    $side = $_POST['side'];
    $content = trim($_POST['claim_content']);

    if (!empty($content)) {
        $claimStmt = $pdo->prepare("INSERT INTO dispute_claims (dispute_id, submitted_by, side, content) VALUES (?, ?, ?, ?)");
        $claimStmt->execute([$disputeId, $userId, $side, $content]);

        // If new prosecution evidence was added, notify the defendant
        if ($side === 'Prosecution') {
            $defStmt = $pdo->prepare("SELECT defendant_id FROM disputes WHERE id = ?");
            $defStmt->execute([$disputeId]);
            $defendantId = $defStmt->fetch()['defendant_id'];

            if ($defendantId) {
                $notifyStmt = $pdo->prepare("INSERT INTO notifications (user_id, dispute_id, type, message) VALUES (?, ?, 'new_evidence', ?)");
                $notifyStmt->execute([$defendantId, $disputeId, "New evidence was submitted against you on Case #$disputeId."]);
            }
        }
    }

    header("Location: courtroom.php?dispute_id=" . $disputeId);
    exit;
}

$disputeId = $_GET['dispute_id'] ?? null;

if ($disputeId) {
    $disputeStmt = $pdo->prepare("
        SELECT d.*, g.title AS grudge_title, g.severity, g.person_involved, g.category, g.date_occurred
        FROM disputes d
        JOIN grudges g ON g.id = d.grudge_id
        WHERE d.id = ?
    ");
    $disputeStmt->execute([$disputeId]);
} else {
    $disputeStmt = $pdo->prepare("
        SELECT d.*, g.title AS grudge_title, g.severity, g.person_involved, g.category, g.date_occurred
        FROM disputes d
        JOIN grudges g ON g.id = d.grudge_id
        LEFT JOIN dispute_jurors dj ON dj.dispute_id = d.id AND dj.user_id = ?
        WHERE d.filed_by = ? OR d.defendant_id = ? OR dj.user_id = ?
        ORDER BY d.created_at DESC
        LIMIT 1
    ");
    $disputeStmt->execute([$userId, $userId, $userId, $userId]);
}

$dispute = $disputeStmt->fetch();
?>

<div class="dashboard-top">
  <div>
    <h1 class="graffiti-heading heading-orange-pink">DISPUTE COURTROOM</h1>
    <p class="auth-subtext">Present your case. Defend your record. Let the truth be judged.</p>
  </div>
  <a href="file-dispute.php" class="btn-sticker btn-pink btn-small">+ FILE A DISPUTE</a>
</div>

<?php if (!$dispute): ?>

  <p class="no-results">No disputes yet. File one, or wait to be called for jury duty.</p>

<?php else: ?>

<?php
$disputeId = $dispute['id'];
$isFiler = ($dispute['filed_by'] == $userId);
$isDefendant = ($dispute['defendant_id'] == $userId);

$claimsStmt = $pdo->prepare("SELECT * FROM dispute_claims WHERE dispute_id = ? AND side = 'Prosecution' ORDER BY created_at ASC");
$claimsStmt->execute([$disputeId]);
$claims = $claimsStmt->fetchAll();

$defensesStmt = $pdo->prepare("SELECT * FROM dispute_claims WHERE dispute_id = ? AND side = 'Defense' ORDER BY created_at ASC");
$defensesStmt->execute([$disputeId]);
$defenses = $defensesStmt->fetchAll();

$jurorsStmt = $pdo->prepare("
    SELECT u.username, jv.vote
    FROM dispute_jurors dj
    JOIN users u ON u.id = dj.user_id
    LEFT JOIN jury_votes jv ON jv.dispute_id = dj.dispute_id AND jv.juror_id = dj.user_id
    WHERE dj.dispute_id = ?
");
$jurorsStmt->execute([$disputeId]);
$jurors = $jurorsStmt->fetchAll();

$guiltyCount = count(array_filter($jurors, fn($j) => $j['vote'] === 'Guilty'));
$innocentCount = count(array_filter($jurors, fn($j) => $j['vote'] === 'Innocent'));
$pendingCount = count(array_filter($jurors, fn($j) => $j['vote'] === null));

$isJurorStmt = $pdo->prepare("SELECT id FROM dispute_jurors WHERE dispute_id = ? AND user_id = ?");
$isJurorStmt->execute([$disputeId, $userId]);
$isJuror = (bool) $isJurorStmt->fetch();

$hasVotedStmt = $pdo->prepare("SELECT id FROM jury_votes WHERE dispute_id = ? AND juror_id = ?");
$hasVotedStmt->execute([$disputeId, $userId]);
$hasVoted = (bool) $hasVotedStmt->fetch();
?>

<div class="courtroom-columns">

  <div class="courtroom-main">

    <div class="evidence-card case-header-card">
      <div class="tape tape-left"></div>
      <div class="tape tape-right"></div>
      <div class="case-header-row">
        <span class="case-id-tag">CASE #<?php echo $dispute['id']; ?></span>
        <div class="case-title-block">
          <p class="case-title"><?php echo strtoupper(htmlspecialchars($dispute['grudge_title'])); ?></p>
          <p class="case-filed">Filed on <?php echo date('M j, Y', strtotime($dispute['created_at'])); ?></p>
        </div>
        <div class="case-status-block">
          <p class="case-status-label">STATUS</p>
          <p class="case-status-value"><?php echo strtoupper($dispute['status']); ?></p>
        </div>
      </div>
    </div>

    <div class="courtroom-grid">

      <div class="evidence-card court-side prosecution-side">
        <h2 class="court-side-heading heading-pink">PROSECUTION</h2>
        <p class="court-side-subheading">Claims</p>
        <ul class="claim-list">
          <?php if (count($claims) === 0): ?>
          <li class="claim-item"><p class="claim-text">No claims filed yet.</p></li>
          <?php endif; ?>
          <?php foreach ($claims as $claim): ?>
          <li class="claim-item">
            <p class="claim-text"><?php echo htmlspecialchars($claim['content']); ?></p>
            <p class="claim-date"><?php echo date('M j, Y', strtotime($claim['created_at'])); ?></p>
          </li>
          <?php endforeach; ?>
        </ul>

        <?php if ($isFiler): ?>
          <button type="button" class="btn-outline btn-outline-pink" onclick="toggleForm('addClaimForm')">+ ADD EVIDENCE</button>
          <form action="courtroom.php" method="POST" class="inline-claim-form" id="addClaimForm" style="display:none; margin-top: 0.8rem;">
            <input type="hidden" name="dispute_id" value="<?php echo $disputeId; ?>">
            <input type="hidden" name="side" value="Prosecution">
            <textarea name="claim_content" rows="2" placeholder="Add another claim..." required></textarea>
            <button type="submit" class="btn-sticker btn-pink btn-small" style="margin-top: 0.5rem;">Submit</button>
          </form>
        <?php endif; ?>
      </div>

      <div class="evidence-card court-side judge-side">
        <h2 class="court-side-heading">THE JURY</h2>
        <div class="judge-icon">⚖️</div>
        <p class="judge-verdict-label">VERDICT</p>
        <p class="judge-verdict-text">
          <?php echo $dispute['verdict'] === 'Pending' ? 'Awaiting jury votes.' : strtoupper($dispute['verdict']); ?>
        </p>
      </div>

      <div class="evidence-card court-side defense-side">
        <h2 class="court-side-heading heading-orange">DEFENSE</h2>
        <p class="court-side-subheading">Responses</p>
        <ul class="claim-list">
          <?php if (count($defenses) === 0): ?>
          <li class="claim-item"><p class="claim-text">No response yet.</p></li>
          <?php endif; ?>
          <?php foreach ($defenses as $defense): ?>
          <li class="claim-item">
            <p class="claim-text"><?php echo htmlspecialchars($defense['content']); ?></p>
            <p class="claim-date"><?php echo date('M j, Y', strtotime($defense['created_at'])); ?></p>
          </li>
          <?php endforeach; ?>
        </ul>

        <?php if ($isDefendant): ?>
          <button type="button" class="btn-outline btn-outline-orange" onclick="toggleForm('addDefenseForm')">+ ADD RESPONSE</button>
          <form action="courtroom.php" method="POST" class="inline-claim-form" id="addDefenseForm" style="display:none; margin-top: 0.8rem;">
            <input type="hidden" name="dispute_id" value="<?php echo $disputeId; ?>">
            <input type="hidden" name="side" value="Defense">
            <textarea name="claim_content" rows="2" placeholder="Respond to the claim..." required></textarea>
            <button type="submit" class="btn-sticker btn-pink btn-small" style="margin-top: 0.5rem;">Submit</button>
          </form>
        <?php endif; ?>
      </div>

    </div>

    <div class="evidence-card jury-panel-card">
      <h2 class="card-heading">THE JURY</h2>
      <p class="auth-subtext"><?php echo count($jurors); ?> jurors invited. Their word decides the case.</p>

      <div class="jury-grid">
        <?php foreach ($jurors as $juror): ?>
        <?php
          $voteClass = $juror['vote'] === 'Guilty' ? 'not-valid' : ($juror['vote'] === 'Innocent' ? 'valid' : 'pending');
          $voteIcon = $juror['vote'] === 'Guilty' ? '✗' : ($juror['vote'] === 'Innocent' ? '✓' : '…');
        ?>
        <div class="juror-chip juror-<?php echo $voteClass; ?>">
          <span class="juror-icon"><?php echo $voteIcon; ?></span>
          <span class="juror-name"><?php echo htmlspecialchars($juror['username']); ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="jury-tally">
        <div class="tally-item">
          <span class="tally-count tally-green"><?php echo $innocentCount; ?></span>
          <span class="tally-label">INNOCENT</span>
        </div>
        <div class="tally-item">
          <span class="tally-count tally-pink"><?php echo $guiltyCount; ?></span>
          <span class="tally-label">GUILTY</span>
        </div>
        <div class="tally-item">
          <span class="tally-count tally-grey"><?php echo $pendingCount; ?></span>
          <span class="tally-label">PENDING</span>
        </div>
      </div>

      <?php if (!$isJuror): ?>
        <p class="auth-subtext">You weren't invited to serve on this jury.</p>
      <?php elseif ($hasVoted): ?>
        <p class="auth-subtext">You've already cast your vote on this case.</p>
      <?php else: ?>
        <a href="verdict.php?dispute_id=<?php echo $disputeId; ?>" class="btn-outline btn-outline-pink btn-full-outline" style="display:block; text-align:center; text-decoration:none;">CAST YOUR VOTE</a>
      <?php endif; ?>
    </div>

  </div>

  <div class="courtroom-side">
    <div class="evidence-card case-summary-card">
      <h2 class="card-heading">CASE SUMMARY</h2>
      <ul class="summary-list">
        <li><span class="summary-label">SEVERITY</span><span class="summary-value severity-tag severity-<?php echo strtolower($dispute['severity']); ?>"><?php echo strtoupper($dispute['severity']); ?></span></li>
        <li><span class="summary-label">PERSON</span><span class="summary-value"><?php echo htmlspecialchars($dispute['person_involved']); ?></span></li>
        <li><span class="summary-label">CATEGORY</span><span class="summary-value"><?php echo htmlspecialchars($dispute['category']); ?></span></li>
        <li><span class="summary-label">GRUDGE DATE</span><span class="summary-value"><?php echo date('M j, Y', strtotime($dispute['date_occurred'])); ?></span></li>
      </ul>
    </div>

    <div class="evidence-card court-rules-card">
      <h2 class="card-heading">COURT RULES</h2>
      <ul class="rules-list">
        <li><p class="rule-title">Tell the truth.</p><p class="rule-sub">Lies will backfire.</p></li>
        <li><p class="rule-title">Respect the court.</p><p class="rule-sub">No abuse. No drama.</p></li>
        <li><p class="rule-title">One undo, ever.</p><p class="rule-sub">Use it wisely.</p></li>
        <li><p class="rule-title">Verdict is final.</p><p class="rule-sub">Move on or hold it.</p></li>
      </ul>
    </div>
  </div>

</div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>