<?php
$pageTitle = "Dispute Courtroom — Grudge Tracker";
include 'includes/header.php';

// Dummy case data for now — will be replaced with a real DB record later
$case = [
  "id" => "GRD-015",
  "title" => "They broke my trust.",
  "filedDate" => "May 15, 2024",
  "status" => "In Session",
  "severity" => "Critical",
  "relationship" => "Friend",
  "addedOn" => "May 15, 2024",
  "lastUpdated" => "Today, 11:42 AM",
  "evidenceCount" => 4,
  "witnessCount" => 2,
];

$claims = [
  ["text" => "They shared my secret with others.", "date" => "May 15, 2024"],
  ["text" => "Ignored me when I needed them.", "date" => "Apr 22, 2024"],
  ["text" => "Talked behind my back.", "date" => "Mar 03, 2024"],
];

$defenses = [
  ["text" => "I didn't share anything.", "date" => "May 16, 2024"],
  ["text" => "I was dealing with my own stuff.", "date" => "Apr 23, 2024"],
  ["text" => "That's not what happened.", "date" => "Mar 04, 2024"],
];

$jurors = [
  ["name" => "Juror #1", "vote" => "valid"],
  ["name" => "Juror #2", "vote" => "valid"],
  ["name" => "Juror #3", "vote" => "not-valid"],
  ["name" => "Juror #4", "vote" => "pending"],
  ["name" => "Juror #5", "vote" => "valid"],
  ["name" => "Juror #6", "vote" => "pending"],
];
$validCount = count(array_filter($jurors, fn($j) => $j['vote'] === 'valid'));
$notValidCount = count(array_filter($jurors, fn($j) => $j['vote'] === 'not-valid'));
$pendingCount = count(array_filter($jurors, fn($j) => $j['vote'] === 'pending'));
?>

<div class="dashboard-top">
  <div>
    <h1 class="graffiti-heading heading-orange-pink">DISPUTE COURTROOM</h1>
    <p class="auth-subtext">Present your case. Defend your record. Let the truth be judged.</p>
  </div>
  <a href="#" class="btn-sticker btn-pink btn-small">+ FILE A DISPUTE</a>
</div>

<div class="courtroom-columns">

  <!-- Main courtroom area -->
  <div class="courtroom-main">

    <!-- Case header strip -->
    <div class="evidence-card case-header-card">
      <div class="tape tape-left"></div>
      <div class="tape tape-right"></div>
      <div class="case-header-row">
        <span class="case-id-tag">CASE #<?php echo $case['id']; ?></span>
        <div class="case-title-block">
          <p class="case-title"><?php echo strtoupper($case['title']); ?></p>
          <p class="case-filed">Filed on <?php echo $case['filedDate']; ?></p>
        </div>
        <div class="case-status-block">
          <p class="case-status-label">STATUS</p>
          <p class="case-status-value">IN SESSION →</p>
        </div>
      </div>
    </div>

    <!-- Prosecution / Judge / Defense -->
    <div class="courtroom-grid">

      <div class="evidence-card court-side prosecution-side">
        <h2 class="court-side-heading heading-pink">PROSECUTION (YOU)</h2>
        <p class="court-side-subheading">Your Claims</p>
        <ul class="claim-list">
          <?php foreach ($claims as $claim): ?>
          <li class="claim-item">
            <p class="claim-text"><?php echo htmlspecialchars($claim['text']); ?></p>
            <p class="claim-date"><?php echo $claim['date']; ?></p>
          </li>
          <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-outline btn-outline-pink">+ ADD EVIDENCE</button>
      </div>

      <div class="evidence-card court-side judge-side">
        <h2 class="court-side-heading">THE JUDGE</h2>
        <div class="judge-icon">⚖️</div>
        <p class="judge-verdict-label">JUDGE'S VERDICT</p>
        <p class="judge-verdict-text">The evidence is under review. Both sides will be heard.</p>

        <div class="verdict-slider">
          <span class="verdict-label-left">NOT VALID</span>
          <div class="verdict-track">
            <div class="verdict-dot"></div>
          </div>
          <span class="verdict-label-right">FULLY VALID</span>
        </div>
        <p class="verdict-pending">PENDING</p>
      </div>

      <div class="evidence-card court-side defense-side">
        <h2 class="court-side-heading heading-orange">DEFENDANT (THEM)</h2>
        <p class="court-side-subheading">Their Defense</p>
        <ul class="claim-list">
          <?php foreach ($defenses as $defense): ?>
          <li class="claim-item">
            <p class="claim-text"><?php echo htmlspecialchars($defense['text']); ?></p>
            <p class="claim-date"><?php echo $defense['date']; ?></p>
          </li>
          <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-outline btn-outline-orange">+ ADD RESPONSE</button>
      </div>

    </div>

    <!-- Jury Panel -->
    <div class="evidence-card jury-panel-card">
      <h2 class="card-heading">THE JURY HAS SPOKEN (SORT OF)</h2>
      <p class="auth-subtext">6 jurors. Their word isn't final, but it counts.</p>

      <div class="jury-grid">
        <?php foreach ($jurors as $juror): ?>
        <div class="juror-chip juror-<?php echo $juror['vote']; ?>">
          <span class="juror-icon">
            <?php
              echo $juror['vote'] === 'valid' ? '✓' : ($juror['vote'] === 'not-valid' ? '✗' : '…');
            ?>
          </span>
          <span class="juror-name"><?php echo $juror['name']; ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="jury-tally">
        <div class="tally-item">
          <span class="tally-count tally-green"><?php echo $validCount; ?></span>
          <span class="tally-label">VALID</span>
        </div>
        <div class="tally-item">
          <span class="tally-count tally-pink"><?php echo $notValidCount; ?></span>
          <span class="tally-label">NOT VALID</span>
        </div>
        <div class="tally-item">
          <span class="tally-count tally-grey"><?php echo $pendingCount; ?></span>
          <span class="tally-label">PENDING</span>
        </div>
      </div>

      <button type="button" class="btn-outline btn-outline-pink btn-full-outline" onclick="openVerdictModal()">CAST YOUR VOTE</button>

    <!-- Court action buttons -->
    <div class="evidence-card court-actions-card">
      <h2 class="card-heading">WHAT SHOULD THE COURT DO?</h2>
      <div class="court-actions-grid">
        <button type="button" class="court-action-btn action-pink">RULE IN YOUR FAVOR</button>
        <button type="button" class="court-action-btn action-orange">NEUTRAL RULING</button>
        <button type="button" class="court-action-btn action-yellow">REQUEST MORE EVIDENCE</button>
        <button type="button" class="court-action-btn action-grey">DISMISS CASE</button>
      </div>
    </div>

  </div>

  <!-- Sidebar -->
  <div class="courtroom-side">

    <div class="evidence-card case-summary-card">
      <h2 class="card-heading">CASE SUMMARY</h2>
      <ul class="summary-list">
        <li><span class="summary-label">SEVERITY</span><span class="summary-value severity-tag severity-critical"><?php echo strtoupper($case['severity']); ?></span></li>
        <li><span class="summary-label">RELATIONSHIP</span><span class="summary-value"><?php echo $case['relationship']; ?></span></li>
        <li><span class="summary-label">ADDED ON</span><span class="summary-value"><?php echo $case['addedOn']; ?></span></li>
        <li><span class="summary-label">LAST UPDATED</span><span class="summary-value"><?php echo $case['lastUpdated']; ?></span></li>
        <li><span class="summary-label">EVIDENCE</span><span class="summary-value"><?php echo $case['evidenceCount']; ?> items</span></li>
        <li><span class="summary-label">WITNESSES</span><span class="summary-value"><?php echo $case['witnessCount']; ?></span></li>
      </ul>
      <a href="#" class="btn-sticker btn-pink btn-full">VIEW FULL DETAILS →</a>
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

    <div class="evidence-card past-data-card">
      <p class="past-data-text">PAST ISN'T PAST.<br>IT'S DATA.</p>
    </div>

  </div>

</div>

<!-- Jury Verdict Modal -->
<div class="verdict-modal-overlay" id="verdictModalOverlay">
  <div class="evidence-card verdict-modal">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>
    <button type="button" class="modal-close" onclick="closeVerdictModal()">✕</button>

    <h2 class="verdict-modal-heading">CAST YOUR VERDICT</h2>
    <p class="auth-subtext">Case #GRD-015 — They broke my trust.</p>

    <p class="verdict-modal-question">Based on the evidence, how do you find the defendant?</p>

    <div class="verdict-choice-grid">
      <button type="button" class="verdict-choice-btn choice-guilty" onclick="selectVerdict('guilty')">
        <span class="verdict-choice-label">GUILTY</span>
        <span class="verdict-choice-sub">The claims hold up.</span>
      </button>

      <button type="button" class="verdict-choice-btn choice-innocent" onclick="selectVerdict('innocent')">
        <span class="verdict-choice-label">INNOCENT</span>
        <span class="verdict-choice-sub">Not enough evidence.</span>
      </button>
    </div>

    <div class="verdict-reasoning-group">
      <label for="verdictReasoning">WHY? (OPTIONAL)</label>
      <textarea id="verdictReasoning" rows="2" placeholder="Explain your reasoning to the court..."></textarea>
    </div>

    <button type="button" class="btn-sticker btn-submit-verdict" onclick="submitVerdict()">SUBMIT VERDICT</button>
    <p class="verdict-confirm-msg" id="verdictConfirmMsg"></p>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/footer.php'; ?>