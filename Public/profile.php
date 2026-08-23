<?php
require 'includes/db.php';

$pageTitle = "Profile — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Core user data
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

$username = $user['username'];
$level = $user['level'];
$trustScore = $user['trust_score'];
$currentStreak = $user['streak_count'];

// Stats
$totalStmt = $pdo->prepare("SELECT COUNT(*) as count FROM grudges WHERE user_id = ?");
$totalStmt->execute([$userId]);
$totalGrudges = $totalStmt->fetch()['count'];

$resolvedStmt = $pdo->prepare("SELECT COUNT(*) as count FROM grudges WHERE user_id = ? AND status = 'Resolved'");
$resolvedStmt->execute([$userId]);
$resolved = $resolvedStmt->fetch()['count'];

$disputesWonStmt = $pdo->prepare("SELECT COUNT(*) as count FROM disputes WHERE filed_by = ? AND verdict = 'Guilty'");
$disputesWonStmt->execute([$userId]);
$disputesWon = $disputesWonStmt->fetch()['count'];

// Favourite emoji — whichever emoji appears most often across this user's grudges
$emojiStmt = $pdo->prepare("SELECT emoji, COUNT(*) as count FROM grudges WHERE user_id = ? AND emoji IS NOT NULL GROUP BY emoji ORDER BY count DESC LIMIT 1");
$emojiStmt->execute([$userId]);
$emojiRow = $emojiStmt->fetch();
$favouriteEmoji = $emojiRow ? $emojiRow['emoji'] : '—';

// Achievements — all master achievements, marked unlocked if this user has them
$achievementsStmt = $pdo->prepare("
    SELECT a.*, ua.unlocked_at
    FROM achievements a
    LEFT JOIN user_achievements ua ON ua.achievement_id = a.id AND ua.user_id = ?
    ORDER BY a.id ASC
");
$achievementsStmt->execute([$userId]);
$achievements = $achievementsStmt->fetchAll();

// Recent activity — most recent grudges logged, as a simple activity feed
$activityStmt = $pdo->prepare("SELECT title, created_at FROM grudges WHERE user_id = ? ORDER BY created_at DESC LIMIT 4");
$activityStmt->execute([$userId]);
$recentActivity = $activityStmt->fetchAll();

// Helper to turn a timestamp into "X hours ago" style text
function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 3600) return floor($diff / 60) . " minutes ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    if ($diff < 604800) return floor($diff / 86400) . " days ago";
    return floor($diff / 604800) . " weeks ago";
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-yellow-green">YOUR RECORD</h1>
</div>

<div class="profile-columns">

  <!-- Left: identity card -->
  <div class="evidence-card profile-identity-card">
    <div class="tape tape-left"></div>
    <div class="profile-avatar">👑</div>
    <h2 class="profile-username"><?php echo htmlspecialchars($username); ?></h2>
    <p class="profile-level">LEVEL <?php echo $level; ?></p>

    <div class="trust-meter">
      <div class="trust-meter-fill" style="width: <?php echo $trustScore; ?>%;"></div>
    </div>
    <p class="trust-score-label"><?php echo $trustScore; ?>/100 trust — earned, not given.</p>

    <div class="profile-mini-stats">
      <div class="mini-stat">
        <span class="mini-stat-emoji"><?php echo $favouriteEmoji; ?></span>
        <span class="mini-stat-label">Favourite Emoji</span>
      </div>
      <div class="mini-stat">
        <span class="mini-stat-num stat-cyan"><?php echo $currentStreak; ?>🔥</span>
        <span class="mini-stat-label">Day Streak</span>
      </div>
    </div>
  </div>

  <!-- Middle: stats + achievements -->
  <div class="profile-main-col">

    <div class="evidence-card profile-stats-card">
      <h2 class="card-heading">STATISTICS</h2>
      <div class="stats-row">
        <div class="stat-block">
          <span class="stat-number stat-orange"><?php echo $totalGrudges; ?></span>
          <span class="stat-label">Total Grudges</span>
        </div>
        <div class="stat-block">
          <span class="stat-number stat-green"><?php echo $resolved; ?></span>
          <span class="stat-label">Resolved</span>
        </div>
        <div class="stat-block">
          <span class="stat-number stat-pink"><?php echo $disputesWon; ?></span>
          <span class="stat-label">Disputes Won</span>
        </div>
      </div>
    </div>

    <div class="evidence-card achievements-card">
      <h2 class="card-heading">ACHIEVEMENTS</h2>
      <div class="achievements-grid">
        <?php foreach ($achievements as $a): ?>
        <?php $unlocked = !empty($a['unlocked_at']); ?>
        <div class="achievement-badge <?php echo $unlocked ? 'unlocked' : 'locked'; ?>">
          <span class="achievement-icon"><?php echo $unlocked ? $a['icon'] : '🔒'; ?></span>
          <p class="achievement-title"><?php echo htmlspecialchars($a['title']); ?></p>
          <p class="achievement-desc"><?php echo htmlspecialchars($a['description']); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <!-- Right: recent activity -->
  <div class="evidence-card profile-activity-card">
    <h2 class="card-heading">RECENT ACTIVITY</h2>
    <?php if (count($recentActivity) === 0): ?>
      <p class="activity-text">No activity yet. Go log something.</p>
    <?php else: ?>
    <ul class="activity-list">
      <?php foreach ($recentActivity as $activity): ?>
      <li class="activity-item">
        <p class="activity-text">Logged a new grudge: "<?php echo htmlspecialchars($activity['title']); ?>"</p>
        <p class="activity-time"><?php echo timeAgo($activity['created_at']); ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>

</div>

<?php include 'includes/footer.php'; ?>