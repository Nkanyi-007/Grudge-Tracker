<?php
$pageTitle = "All Grudges — Grudge Tracker";
include 'includes/header.php';

// Dummy data for now — will be replaced with a DB query later
$grudges = [
  ["title" => "Ate my leftover pasta without asking", "category" => "Roommate", "severity" => "High", "status" => "Active", "date" => "Aug 4, 2026"],
  ["title" => "Took credit for my idea in the meeting", "category" => "Work", "severity" => "Critical", "status" => "In Progress", "date" => "Aug 2, 2026"],
  ["title" => "Never returned my charger", "category" => "Friend", "severity" => "Low", "status" => "Resolved", "date" => "Jul 30, 2026"],
  ["title" => "Cancelled plans last minute again", "category" => "Friend", "severity" => "Medium", "status" => "Active", "date" => "Jul 28, 2026"],
  ["title" => "Parked across two spaces", "category" => "Stranger", "severity" => "Low", "status" => "Archived", "date" => "Jul 25, 2026"],
  ["title" => "Didn't invite me to the group trip", "category" => "Friend", "severity" => "High", "status" => "In Progress", "date" => "Jul 20, 2026"],
  ["title" => "Loud music at 2am, third time", "category" => "Roommate", "severity" => "Critical", "status" => "Active", "date" => "Jul 18, 2026"],
  ["title" => "Ghosted after I helped move apartments", "category" => "Friend", "severity" => "Critical", "status" => "Resolved", "date" => "Jul 10, 2026"],
];
?>

<div class="dashboard-top">
 <h1 class="graffiti-heading heading-yellow-orange">ALL GRUDGES</h1>
  <a href="log-grudge.php" class="btn-sticker btn-pink btn-small">+ ADD GRUDGE</a>
</div>

<div class="filters-bar">
  <input type="text" class="search-input" id="searchInput" placeholder="Search grudges..." onkeyup="filterGrudges()">

  <select class="filter-select" id="severityFilter" onchange="filterGrudges()">
    <option value="">All Severities</option>
    <option value="Critical">Critical</option>
    <option value="High">High</option>
    <option value="Medium">Medium</option>
    <option value="Low">Low</option>
  </select>

  <select class="filter-select" id="statusFilter" onchange="filterGrudges()">
    <option value="">All Statuses</option>
    <option value="Active">Active</option>
    <option value="In Progress">In Progress</option>
    <option value="Resolved">Resolved</option>
    <option value="Archived">Archived</option>
  </select>

  <select class="filter-select" id="sortSelect" onchange="filterGrudges()">
    <option value="newest">Newest First</option>
    <option value="oldest">Oldest First</option>
    <option value="severity">Severity (High → Low)</option>
  </select>
</div>

<div class="grudge-grid" id="grudgeGrid">
  <?php foreach ($grudges as $grudge): ?>
  <div class="evidence-card grudge-card"
       data-title="<?php echo strtolower(htmlspecialchars($grudge['title'])); ?>"
       data-severity="<?php echo $grudge['severity']; ?>"
       data-status="<?php echo $grudge['status']; ?>">
    <div class="tape tape-left"></div>
    <span class="severity-tag severity-<?php echo strtolower($grudge['severity']); ?>"><?php echo strtoupper($grudge['severity']); ?></span>
    <h3 class="grudge-card-title"><?php echo htmlspecialchars($grudge['title']); ?></h3>
    <p class="grudge-card-meta"><?php echo $grudge['category']; ?> · <?php echo $grudge['date']; ?></p>
    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $grudge['status'])); ?>"><?php echo $grudge['status']; ?></span>
  </div>
  <?php endforeach; ?>
</div>

<p class="no-results" id="noResults" style="display: none;">No grudges match that search. Maybe you've let it go. Growth.</p>

<?php include 'includes/footer.php'; ?>