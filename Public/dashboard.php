<?php
// session_start();
$pageTitle = "Dashboard — Grudge Tracker";
include 'includes/header.php';

$username = $_SESSION['username'] ?? 'Guest';
$level = 42;
$xpCurrent = 640;
$xpTotal = 1000;
$xpPercent = round(($xpCurrent / $xpTotal) * 100);

$active = 6;
$inProgress = 5;
$resolved = 12;
$archived = 1;
$totalGrudges = $active + $inProgress + $resolved + $archived;

$recentGrudges = [
  ["title" => "Ate my leftover pasta without asking", "category" => "Roommate", "severity" => "High", "date" => "Aug 4, 2026"],
  ["title" => "Took credit for my idea in the meeting", "category" => "Work", "severity" => "Critical", "date" => "Aug 2, 2026"],
  ["title" => "Never returned my charger", "category" => "Friend", "severity" => "Low", "date" => "Jul 30, 2026"],
];

// Donut chart math (SVG stroke-dasharray based)
$circumference = 2 * M_PI * 54; // radius 54
$activeLen = ($active / $totalGrudges) * $circumference;
$inProgressLen = ($inProgress / $totalGrudges) * $circumference;
$resolvedLen = ($resolved / $totalGrudges) * $circumference;
$archivedLen = ($archived / $totalGrudges) * $circumference;
?>

<div class="dashboard-top">
  <div>
    <h1 class="graffiti-heading">EVERY OFFENSE. NEVER FORGOTTEN.</h1>
    <h3 class="welcome-heading">Welcome back, <?php echo htmlspecialchars($username); ?></h3>
  </div>
  <a href="log-grudge.php" class="btn-sticker btn-pink btn-small">+ ADD GRUDGE</a>
</div>

<div class="dashboard-columns">

  <!-- Main column -->
  <div class="dashboard-main-col">
    <div class="evidence-card recent-card">
      <div class="tape tape-left"></div>
      <h2 class="card-heading">RECENT GRUDGES</h2>
      <ul class="recent-list">
        <?php foreach ($recentGrudges as $grudge): ?>
        <li class="recent-item">
          <span class="severity-tag severity-<?php echo strtolower($grudge['severity']); ?>"><?php echo strtoupper($grudge['severity']); ?></span>
          <div class="recent-item-text">
            <p class="recent-title"><?php echo htmlspecialchars($grudge['title']); ?></p>
            <p class="recent-meta"><?php echo $grudge['category']; ?> · <?php echo $grudge['date']; ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <a href="all-grudges.php" class="view-all-link">View all grudges →</a>
    </div>
  </div>

  <!-- Right column -->
  <div class="dashboard-side-col">

    <div class="evidence-card summary-card">
      <h2 class="card-heading">GRUDGE SUMMARY</h2>
      <div class="donut-wrap">
        <svg viewBox="0 0 120 120" class="donut-chart">
          <circle cx="60" cy="60" r="54" fill="none" stroke="#222" stroke-width="12"/>
          <circle cx="60" cy="60" r="54" fill="none" stroke="var(--pink)" stroke-width="12"
            stroke-dasharray="<?php echo $activeLen; ?> <?php echo $circumference; ?>"
            stroke-dashoffset="0" transform="rotate(-90 60 60)"/>
          <circle cx="60" cy="60" r="54" fill="none" stroke="var(--orange)" stroke-width="12"
            stroke-dasharray="<?php echo $inProgressLen; ?> <?php echo $circumference; ?>"
            stroke-dashoffset="<?php echo -$activeLen; ?>" transform="rotate(-90 60 60)"/>
          <circle cx="60" cy="60" r="54" fill="none" stroke="var(--yellow)" stroke-width="12"
            stroke-dasharray="<?php echo $resolvedLen; ?> <?php echo $circumference; ?>"
            stroke-dashoffset="<?php echo -($activeLen + $inProgressLen); ?>" transform="rotate(-90 60 60)"/>
          <circle cx="60" cy="60" r="54" fill="none" stroke="#666" stroke-width="12"
            stroke-dasharray="<?php echo $archivedLen; ?> <?php echo $circumference; ?>"
            stroke-dashoffset="<?php echo -($activeLen + $inProgressLen + $resolvedLen); ?>" transform="rotate(-90 60 60)"/>
          <text x="60" y="56" text-anchor="middle" class="donut-number"><?php echo $totalGrudges; ?></text>
          <text x="60" y="72" text-anchor="middle" class="donut-label">TOTAL</text>
        </svg>
        <ul class="donut-legend">
          <li><span class="dot dot-pink"></span>Active <b><?php echo $active; ?></b></li>
          <li><span class="dot dot-orange"></span>In Progress <b><?php echo $inProgress; ?></b></li>
          <li><span class="dot dot-yellow"></span>Resolved <b><?php echo $resolved; ?></b></li>
          <li><span class="dot dot-grey"></span>Archived <b><?php echo $archived; ?></b></li>
        </ul>
      </div>
    </div>

    <div class="evidence-card progress-card">
      <h2 class="card-heading">YOUR PROGRESS</h2>
      <p class="progress-level">LEVEL <?php echo $level; ?></p>
      <div class="xp-bar">
        <div class="xp-bar-fill" style="width: <?php echo $xpPercent; ?>%;"></div>
      </div>
      <p class="xp-label"><?php echo $xpCurrent; ?> / <?php echo $xpTotal; ?> XP</p>
    </div>

  </div>
</div>

<?php include 'includes/footer.php'; ?>