<?php
require 'includes/db.php';
require_once 'includes/game-logic.php';
$pageTitle = "Edit Grudge — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$grudgeId = $_GET['id'] ?? null;

if (!$grudgeId) {
    header("Location: all-grudges.php");
    exit;
}

$grudgeStmt = $pdo->prepare("SELECT * FROM grudges WHERE id = ? AND user_id = ?");
$grudgeStmt->execute([$grudgeId, $userId]);
$grudge = $grudgeStmt->fetch();

if (!$grudge) {
    header("Location: all-grudges.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $person = trim($_POST['person']);
    $category = $_POST['category'];
    $dateOccurred = $_POST['date'];
    $severity = $_POST['severity'];
    $status = $_POST['status'];
    $notes = trim($_POST['notes']);

    if (empty($title) || empty($person) || empty($category) || empty($dateOccurred) || empty($severity)) {
        $error = "Please fill in all required fields.";
    } else {
        $wasResolved = ($grudge['status'] === 'Resolved');
        $isNowResolved = ($status === 'Resolved');

        $updateStmt = $pdo->prepare("UPDATE grudges SET title = ?, person_involved = ?, category = ?, severity = ?, status = ?, notes = ?, date_occurred = ? WHERE id = ? AND user_id = ?");
        $updateStmt->execute([$title, $person, $category, $severity, $status, $notes, $dateOccurred, $grudgeId, $userId]);

        // Reward trust the moment a grudge is newly marked as Resolved
        if (!$wasResolved && $isNowResolved) {
            adjustTrust($pdo, $userId, 2);
        }

        header("Location: view-grudge.php?id=" . $grudgeId);
        exit;
    }
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-cyan-pink">EDIT GRUDGE</h1>
</div>

<div class="evidence-card log-grudge-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <?php if ($error): ?>
    <p style="color: var(--pink); font-size: 0.85rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form action="edit-grudge.php?id=<?php echo $grudgeId; ?>" method="POST" class="grudge-form">

    <div class="form-group">
      <label for="title">WHAT HAPPENED</label>
      <textarea id="title" name="title" rows="3" required><?php echo htmlspecialchars($grudge['title']); ?></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="person">PERSON INVOLVED</label>
        <input type="text" id="person" name="person" value="<?php echo htmlspecialchars($grudge['person_involved']); ?>" required>
      </div>

      <div class="form-group">
        <label for="category">CATEGORY</label>
        <select id="category" name="category" required>
          <?php foreach (['Roommate','Work','Friend','Family','Stranger','Partner','Other'] as $cat): ?>
            <option value="<?php echo $cat; ?>" <?php echo $grudge['category'] === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="date">DATE IT HAPPENED</label>
        <input type="date" id="date" name="date" value="<?php echo $grudge['date_occurred']; ?>" required>
      </div>

      <div class="form-group">
        <label for="severity">SEVERITY</label>
        <select id="severity" name="severity" required>
          <?php foreach (['Low','Medium','High','Critical'] as $sev): ?>
            <option value="<?php echo $sev; ?>" <?php echo $grudge['severity'] === $sev ? 'selected' : ''; ?>><?php echo $sev; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="status">STATUS</label>
      <select id="status" name="status" required>
        <?php foreach (['Active','In Progress','Resolved','Archived'] as $st): ?>
          <option value="<?php echo $st; ?>" <?php echo $grudge['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="notes">ADDITIONAL NOTES</label>
      <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($grudge['notes']); ?></textarea>
    </div>

    <button type="submit" class="btn-sticker btn-submit-grudge">SAVE CHANGES</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>