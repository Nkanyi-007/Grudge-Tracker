<?php
require 'includes/db.php';

$pageTitle = "Timeline — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Pull all grudges for this user, oldest to newest, so the timeline reads chronologically
$stmt = $pdo->prepare("SELECT * FROM grudges WHERE user_id = ? ORDER BY date_occurred ASC");
$stmt->execute([$userId]);
$timelineEvents = $stmt->fetchAll();
?>

<div class="dashboard-top">
  <div>
    <h1 class="graffiti-heading heading-green-cyan">THE TIMELINE</h1>
    <p class="auth-subtext">Every grudge, in order. Nothing forgotten.</p>
  </div>
</div>

<?php if (count($timelineEvents) === 0): ?>
  <p class="no-results">Nothing logged yet. Your timeline starts the moment you do.</p>
<?php else: ?>

<div class="timeline-wrap">
  <div class="timeline-line"></div>

  <?php foreach ($timelineEvents as $index => $event): ?>
  <div class="timeline-item timeline-<?php echo strtolower($event['severity']); ?> <?php echo $index % 2 === 0 ? 'timeline-left' : 'timeline-right'; ?>">
    <div class="timeline-dot"></div>
    <div class="evidence-card timeline-card">
      <div class="tape tape-left"></div>
      <div class="timeline-card-header">
        <span class="severity-tag severity-<?php echo strtolower($event['severity']); ?>"><?php echo strtoupper($event['severity']); ?></span>
        <span class="timeline-date"><?php echo date('M j, Y', strtotime($event['date_occurred'])); ?></span>
      </div>
      <h3 class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></h3>
      <p class="timeline-category"><?php echo htmlspecialchars($event['category']); ?></p>
      <?php if (!empty($event['notes'])): ?>
      <div class="pinned-note">
        <p class="pinned-note-text"><?php echo htmlspecialchars($event['notes']); ?></p>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

</div>

<?php endif; ?>