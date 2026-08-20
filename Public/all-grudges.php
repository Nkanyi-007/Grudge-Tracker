<?php
require 'includes/db.php';

$pageTitle = "All Grudges — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM grudges WHERE user_id = ? ORDER BY date_occurred DESC");
$stmt->execute([$userId]);
$grudges = $stmt->fetchAll();
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

<?php if (count($grudges) === 0): ?>
  <p class="no-results">No grudges logged yet. Whatever you're holding back — let it out.</p>
<?php else: ?>

<div class="grudge-grid" id="grudgeGrid">
  <?php foreach ($grudges as $grudge): ?>
  <div class="evidence-card grudge-card"
       data-title="<?php echo strtolower(htmlspecialchars($grudge['title'])); ?>"
       data-severity="<?php echo $grudge['severity']; ?>"
       data-status="<?php echo $grudge['status']; ?>">
    <div class="tape tape-left"></div>
    <span class="severity-tag severity-<?php echo strtolower($grudge['severity']); ?>"><?php echo strtoupper($grudge['severity']); ?></span>
    <h3 class="grudge-card-title"><?php echo htmlspecialchars($grudge['title']); ?></h3>
    <p class="grudge-card-meta"><?php echo htmlspecialchars($grudge['category']); ?> · <?php echo date('M j, Y', strtotime($grudge['date_occurred'])); ?></p>
    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $grudge['status'])); ?>"><?php echo $grudge['status']; ?></span>
  </div>
  <?php endforeach; ?>
</div>

<p class="no-results" id="noResults" style="display: none;">No grudges match that search. Maybe you've let it go. Growth.</p>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>