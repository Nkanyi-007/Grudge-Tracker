<?php
$pageTitle = "Profile — Grudge Tracker";
include 'includes/header.php';

// Dummy data for now — will be replaced with real DB values later
$username = "PETTY PRINCE";
$level = 42;
$trustScore = 62;
$favouriteEmoji = "😤";
$currentStreak = 3;
$totalGrudges = 24;
$resolved = 12;
$disputesWon = 6;

$achievements = [
  ["icon" => "🏆", "title" => "First Blood", "desc" => "Logged your first grudge", "unlocked" => true],
  ["icon" => "⚖️", "title" => "Day in Court", "desc" => "Filed your first dispute", "unlocked" => true],
  ["icon" => "🔥", "title" => "On a Streak", "desc" => "3-day logging streak", "unlocked" => true],
  ["icon" => "🕊️", "title" => "Let It Go", "desc" => "Resolved 10 grudges", "unlocked" => true],
  ["icon" => "👑", "title" => "Petty Royalty", "desc" => "Reached Level 40", "unlocked" => true],
  ["icon" => "🛡️", "title" => "Trusted", "desc" => "Reach 80 trust score", "unlocked" => false],
];

$recentActivity = [
  ["text" => "Filed a dispute against \"Took credit for my idea\"", "time" => "2 hours ago"],
  ["text" => "Resolved \"Never returned my charger\"", "time" => "3 days ago"],
  ["text" => "Logged a new grudge: \"Loud music at 2am\"", "time" => "5 days ago"],
  ["text" => "Reached Level 42", "time" => "1 week ago"],
];
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-yellow-green">YOUR RECORD</h1>
</div>

<div class="profile-columns">

  <!-- Left: identity card -->
  <div class="evidence-card profile-identity-card">
    <div class="tape tape-left"></div>
    <div class="profile-avatar">👑</div>
    <h2 class="profile-username"><?php echo $username; ?></h2>
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
        <div class="achievement-badge <?php echo $a['unlocked'] ? 'unlocked' : 'locked'; ?>">
          <span class="achievement-icon"><?php echo $a['unlocked'] ? $a['icon'] : '🔒'; ?></span>
          <p class="achievement-title"><?php echo $a['title']; ?></p>
          <p class="achievement-desc"><?php echo $a['desc']; ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <!-- Right: recent activity -->
  <div class="evidence-card profile-activity-card">
    <h2 class="card-heading">RECENT ACTIVITY</h2>
    <ul class="activity-list">
      <?php foreach ($recentActivity as $activity): ?>
      <li class="activity-item">
        <p class="activity-text"><?php echo htmlspecialchars($activity['text']); ?></p>
        <p class="activity-time"><?php echo $activity['time']; ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>

</div>

<?php include 'includes/footer.php'; ?>