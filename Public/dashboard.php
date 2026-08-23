<?php
require 'includes/db.php';
$pageTitle = "Dashboard — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Guest';

// Get real level/XP from the database
$userStmt = $pdo->prepare("SELECT xp, level FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$userRow = $userStmt->fetch();

$level = $userRow['level'];
$xpCurrent = $userRow['xp'];
$xpTotal = 1000;
$xpPercent = round(($xpCurrent / $xpTotal) * 100);

// Count grudges by status for the donut chart
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

// Pull the 3 most recent grudges
$recentStmt = $pdo->prepare("SELECT * FROM grudges WHERE user_id = ? ORDER BY date_occurred DESC LIMIT 3");
$recentStmt->execute([$userId]);
$recentGrudges = $recentStmt->fetchAll();

// Unread notifications for the banner
$notifStmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
$notifStmt->execute([$userId]);
$notifications = $notifStmt->fetchAll();

// Donut chart math (SVG stroke-dasharray based)
$circumference = 2 * M_PI * 54; // radius 54
$safeTotal = $totalGrudges > 0 ? $totalGrudges : 1; // avoid divide-by-zero for new accounts
$activeLen = ($active / $safeTotal) * $circumference;
$inProgressLen = ($inProgress / $safeTotal) * $circumference;
$resolvedLen = ($resolved / $safeTotal) * $circumference;
$archivedLen = ($archived / $safeTotal) * $circumference;
?>

<?php foreach ($notifications as $notif): ?>
<div class="notification-banner">
  <span class="notification-icon"><?php echo $notif['type'] === 'juror_invite' ? '⚖️' : '📢'; ?></span>
  <span class="notification-text"><?php echo htmlspecialchars($notif['message']); ?></span>
  <a href="courtroom.php?dispute_id=<?php echo $notif['dispute_id']; ?>" class="notification-link">View Case</a>
  <a href="dismiss-notification.php?id=<?php echo $notif['id']; ?>" class="notification-close">✕</a>
</div>
<?php endforeach; ?>

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
        <?php if (count($recentGrudges) === 0): ?>
        <li class="recent-item"><p class="recent-meta">No grudges yet — go log one.</p></li>
        <?php else: ?>
        <?php foreach ($recentGrudges as $grudge): ?>
        <li class="recent-item">
          <span class="severity-tag severity-<?php echo strtolower($grudge['severity']); ?>"><?php echo strtoupper($grudge['severity']); ?></span>
          <div class="recent-item-text">
            <p class="recent-title"><?php echo htmlspecialchars($grudge['title']); ?></p>
            <p class="recent-meta"><?php echo htmlspecialchars($grudge['category']); ?> · <?php echo date('M j, Y', strtotime($grudge['date_occurred'])); ?></p>
          </div>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
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