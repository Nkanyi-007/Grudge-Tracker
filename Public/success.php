<?php
require 'includes/db.php';
$pageTitle = "Success — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Real level/XP from the users table
$userStmt = $pdo->prepare("SELECT xp, level FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$userRow = $userStmt->fetch();

$level = $userRow['level'];
$xpCurrent = $userRow['xp'];
$xpTotal = 1000;
$xpPercent = round(($xpCurrent / $xpTotal) * 100);

// Real progress stats
$targetsAddedStmt = $pdo->prepare("SELECT COUNT(DISTINCT person_involved) as count FROM grudges WHERE user_id = ?");
$targetsAddedStmt->execute([$userId]);
$targetsAdded = $targetsAddedStmt->fetch()['count'];

$disputesFiledStmt = $pdo->prepare("SELECT COUNT(*) as count FROM disputes WHERE filed_by = ?");
$disputesFiledStmt->execute([$userId]);
$disputesFiled = $disputesFiledStmt->fetch()['count'];

$grudgesResolvedStmt = $pdo->prepare("SELECT COUNT(*) as count FROM grudges WHERE user_id = ? AND status = 'Resolved'");
$grudgesResolvedStmt->execute([$userId]);
$grudgesResolved = $grudgesResolvedStmt->fetch()['count'];

// Real grudge summary counts for the donut chart
$statusStmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM grudges WHERE user_id = ? GROUP BY status");
$statusStmt->execute([$userId]);
$statusCounts = ['Active' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Archived' => 0];
foreach ($statusStmt->fetchAll() as $row) {
    $statusCounts[$row['status']] = (int) $row['count'];
}

$active = $statusCounts['Active'];
$inProgress = $statusCounts['In Progress'];
$resolved = $statusCounts['Resolved'];
$archived = $statusCounts['Archived'];
$totalGrudges = $active + $inProgress + $resolved + $archived;

$circumference = 2 * M_PI * 42;
$safeTotal = $totalGrudges > 0 ? $totalGrudges : 1;
$activeLen = ($active / $safeTotal) * $circumference;
$inProgressLen = ($inProgress / $safeTotal) * $circumference;
$resolvedLen = ($resolved / $safeTotal) * $circumference;
$archivedLen = ($archived / $safeTotal) * $circumference;
?>

<div class="success-page">

  <div class="success-columns">

    <div class="evidence-card success-main-card">
      <div class="success-heading-row">
        <div>
          <h1 class="success-heading">SUCCESS</h1>
          <p class="success-subheading">YOU HANDLED IT.</p>
          <p class="success-text">Grudge closed. Peace earned.<br>You chose growth over chaos.</p>
        </div>
        <div class="success-mascot">😌</div>
      </div>

      <div class="what-you-did">
        <h2 class="card-heading-sm">WHAT YOU'VE DONE SO FAR</h2>
        <div class="did-grid">
          <div class="did-item">
            <span class="did-icon did-icon-green">✓</span>
            <p class="did-title">RESOLVED</p>
            <p class="did-sub"><?php echo $grudgesResolved; ?> grudge<?php echo $grudgesResolved === 1 ? '' : 's'; ?> closed.</p>
          </div>
          <div class="did-item">
            <span class="did-icon did-icon-pink">♥</span>
            <p class="did-title">LOGGED</p>
            <p class="did-sub"><?php echo $totalGrudges; ?> total grudge<?php echo $totalGrudges === 1 ? '' : 's'; ?>.</p>
          </div>
          <div class="did-item">
            <span class="did-icon did-icon-yellow">▦</span>
            <p class="did-title">DISPUTED</p>
            <p class="did-sub"><?php echo $disputesFiled; ?> case<?php echo $disputesFiled === 1 ? '' : 's'; ?> filed.</p>
          </div>
          <div class="did-item">
            <span class="did-icon did-icon-orange">♛</span>
            <p class="did-title">LEVEL <?php echo $level; ?></p>
            <p class="did-sub">That's how leaders level up.</p>
          </div>
        </div>
      </div>

      <div class="success-quote">
        <p class="quote-text">"Not everything needs revenge. Growth hits different."</p>
      </div>
    </div>

    <div class="success-side-col">

      <div class="evidence-card summary-card-sm">
        <h2 class="card-heading-sm">GRUDGE SUMMARY</h2>
        <div class="donut-wrap">
          <svg viewBox="0 0 100 100" class="donut-chart-sm">
            <circle cx="50" cy="50" r="42" fill="none" stroke="#222" stroke-width="10"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="var(--pink)" stroke-width="10"
              stroke-dasharray="<?php echo $activeLen; ?> <?php echo $circumference; ?>" transform="rotate(-90 50 50)"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="var(--orange)" stroke-width="10"
              stroke-dasharray="<?php echo $inProgressLen; ?> <?php echo $circumference; ?>"
              stroke-dashoffset="<?php echo -$activeLen; ?>" transform="rotate(-90 50 50)"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="var(--yellow)" stroke-width="10"
              stroke-dasharray="<?php echo $resolvedLen; ?> <?php echo $circumference; ?>"
              stroke-dashoffset="<?php echo -($activeLen + $inProgressLen); ?>" transform="rotate(-90 50 50)"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="#666" stroke-width="10"
              stroke-dasharray="<?php echo $archivedLen; ?> <?php echo $circumference; ?>"
              stroke-dashoffset="<?php echo -($activeLen + $inProgressLen + $resolvedLen); ?>" transform="rotate(-90 50 50)"/>
            <text x="50" y="47" text-anchor="middle" class="donut-number-sm"><?php echo $totalGrudges; ?></text>
            <text x="50" y="60" text-anchor="middle" class="donut-label-sm">TOTAL</text>
          </svg>
          <ul class="donut-legend-sm">
            <li><span class="dot dot-pink"></span>Active <b><?php echo $active; ?></b></li>
            <li><span class="dot dot-orange"></span>In Progress <b><?php echo $inProgress; ?></b></li>
            <li><span class="dot dot-yellow"></span>Resolved <b><?php echo $resolved; ?></b></li>
            <li><span class="dot dot-grey"></span>Archived <b><?php echo $archived; ?></b></li>
          </ul>
        </div>
      </div>

      <div class="evidence-card progress-card-sm">
        <h2 class="card-heading-sm">YOUR PROGRESS</h2>
        <p class="progress-level-sm">LEVEL <?php echo $level; ?></p>
        <div class="xp-bar">
          <div class="xp-bar-fill" style="width: <?php echo $xpPercent; ?>%;"></div>
        </div>
        <p class="xp-label"><?php echo $xpCurrent; ?> / <?php echo $xpTotal; ?> XP</p>

        <div class="progress-stats-row">
          <div class="progress-stat">
            <span class="progress-stat-num stat-pink"><?php echo $targetsAdded; ?></span>
            <span class="progress-stat-label">TARGETS<br>ADDED</span>
          </div>
          <div class="progress-stat">
            <span class="progress-stat-num stat-orange"><?php echo $disputesFiled; ?></span>
            <span class="progress-stat-label">DISPUTES<br>FILED</span>
          </div>
          <div class="progress-stat">
            <span class="progress-stat-num stat-yellow"><?php echo $grudgesResolved; ?></span>
            <span class="progress-stat-label">GRUDGES<br>RESOLVED</span>
          </div>
        </div>
      </div>

      <div class="evidence-card keep-it-up-card">
        <h2 class="card-heading-sm">KEEP IT UP</h2>
        <p class="keep-it-up-text">You're building a legacy, not a list of regrets.</p>
        <a href="profile.php" class="btn-sticker btn-pink btn-small-full">VIEW YOUR JOURNEY →</a>
      </div>

    </div>
  </div>

  <div class="principles-strip">
    <div class="principle-card principle-pink">
      <span class="principle-num">01</span>
      <p class="principle-title">TRUST HAS TO BE <span class="hl-pink">EARNED</span></p>
      <p class="principle-sub">We don't forgive easily. Prove it or pay for it.</p>
    </div>
    <div class="principle-card principle-orange">
      <span class="principle-num">02</span>
      <p class="principle-title">ONE UNDO, <span class="hl-orange">EVER.</span></p>
      <p class="principle-sub">Think twice. You get one shot to take it back.</p>
    </div>
    <div class="principle-card principle-yellow">
      <span class="principle-num">03</span>
      <p class="principle-title">SUCCESS FITS ON <span class="hl-yellow">ONE SCREEN</span></p>
      <p class="principle-sub">Everything you need. Nothing you don't.</p>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>