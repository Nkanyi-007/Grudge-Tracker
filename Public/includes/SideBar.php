<?php
require_once 'includes/db.php';

$sidebarLevel = 1;
$sidebarXpPercent = 0;

if (isset($_SESSION['user_id'])) {
    $sidebarStmt = $pdo->prepare("SELECT xp, level FROM users WHERE id = ?");
    $sidebarStmt->execute([$_SESSION['user_id']]);
    $sidebarUser = $sidebarStmt->fetch();

    if ($sidebarUser) {
        $sidebarLevel = $sidebarUser['level'];
        $sidebarXpPercent = round(($sidebarUser['xp'] / 1000) * 100);
    }
}
?>
<nav class="sidebar">
  <div class="sidebar-logo">
    <span class="logo-text">GRUDGE<br>TRACKER</span>
  </div>

  <ul class="sidebar-links">
    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
    <li><a href="all-grudges.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'all-grudges.php' ? 'active' : ''; ?>">All Grudges</a></li>
    <li><a href="log-grudge.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'log-grudge.php' ? 'active' : ''; ?>">Log Grudge</a></li>
    <li><a href="timeline.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'timeline.php' ? 'active' : ''; ?>">Timeline</a></li>
    <li><a href="courtroom.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'courtroom.php' ? 'active' : ''; ?>">Dispute Courtroom</a></li>
    <li><a href="success.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'success.php' ? 'active' : ''; ?>">Success</a></li>
    <li><a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
  </ul>

  <div class="sidebar-user">
    <div class="mascot-badge">👑</div>
    <p class="sidebar-username"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></p>
    <p class="sidebar-level">LEVEL <?php echo $sidebarLevel; ?></p>
    <div class="xp-bar-mini">
      <div class="xp-bar-mini-fill" style="width: <?php echo $sidebarXpPercent; ?>%;"></div>
    </div>
    <a href="logout.php" class="logout-link">LOG OUT</a>
  </div>
</nav>