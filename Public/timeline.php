<?php
$pageTitle = "Timeline — Grudge Tracker";
include 'includes/header.php';

// Dummy data for now — will be replaced with a DB query, ordered by date, later
$timelineEvents = [
  ["title" => "Loud music at 2am, third time", "category" => "Roommate", "severity" => "Critical", "date" => "Jul 18, 2026", "note" => "This is becoming a pattern. Documenting for the record."],
  ["title" => "Didn't invite me to the group trip", "category" => "Friend", "severity" => "High", "date" => "Jul 20, 2026", "note" => "Found out through a story post. Not great."],
  ["title" => "Parked across two spaces", "category" => "Stranger", "severity" => "Low", "date" => "Jul 25, 2026", "note" => "Petty but satisfying to log."],
  ["title" => "Cancelled plans last minute again", "category" => "Friend", "severity" => "Medium", "date" => "Jul 28, 2026", "note" => "Third time this month. Pattern confirmed."],
  ["title" => "Never returned my charger", "category" => "Friend", "severity" => "Low", "date" => "Jul 30, 2026", "note" => "Resolved — they finally gave it back."],
  ["title" => "Took credit for my idea in the meeting", "category" => "Work", "severity" => "Critical", "date" => "Aug 2, 2026", "note" => "Filed a dispute over this one."],
  ["title" => "Ate my leftover pasta without asking", "category" => "Roommate", "severity" => "High", "date" => "Aug 4, 2026", "note" => "The betrayal runs deep."],
];
?>

<div class="dashboard-top">
  <div>
   <h1 class="graffiti-heading heading-green-cyan">THE TIMELINE</h1>
    <p class="auth-subtext">Every grudge, in order. Nothing forgotten.</p>
  </div>
</div>

<div class="timeline-wrap">
  <div class="timeline-line"></div>

  <?php foreach ($timelineEvents as $index => $event): ?>
  <div class="timeline-item timeline-<?php echo strtolower($event['severity']); ?> <?php echo $index % 2 === 0 ? 'timeline-left' : 'timeline-right'; ?>">
    <div class="timeline-dot"></div>
    <div class="evidence-card timeline-card">
      <div class="tape tape-left"></div>
      <div class="timeline-card-header">
        <span class="severity-tag severity-<?php echo strtolower($event['severity']); ?>"><?php echo strtoupper($event['severity']); ?></span>
        <span class="timeline-date"><?php echo $event['date']; ?></span>
      </div>
      <h3 class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></h3>
      <p class="timeline-category"><?php echo $event['category']; ?></p>
      <div class="pinned-note">
        <p class="pinned-note-text"><?php echo htmlspecialchars($event['note']); ?></p>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

</div>

<?php include 'includes/footer.php'; ?>