<?php
require 'includes/db.php';
require_once 'includes/achievements.php';
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// require 'includes/db.php';


$pageTitle = "Log a Grudge — Grudge Tracker";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $person = trim($_POST['person']);
    $category = $_POST['category'];
    $dateOccurred = $_POST['date'];
    $severity = $_POST['severity'];
    $emoji = $_POST['emoji'] ?? null;
    $notes = trim($_POST['notes']);
    $userId = $_SESSION['user_id'];

    if (empty($title) || empty($person) || empty($category) || empty($dateOccurred) || empty($severity)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO grudges (user_id, title, person_involved, category, severity, emoji, notes, date_occurred) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $title, $person, $category, $severity, $emoji, $notes, $dateOccurred]);
        $grudgeId = $pdo->lastInsertId();

        if (!empty($_FILES['evidence']['name'][0])) {
            $uploadDir = 'uploads/evidence/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            foreach ($_FILES['evidence']['name'] as $index => $fileName) {
                if ($_FILES['evidence']['error'][$index] === 0) {
                    $tmpPath = $_FILES['evidence']['tmp_name'][$index];
                    $safeName = time() . '_' . basename($fileName);
                    $destination = $uploadDir . $safeName;
                    if (move_uploaded_file($tmpPath, $destination)) {
                        $evidenceStmt = $pdo->prepare("INSERT INTO grudge_evidence (grudge_id, file_path) VALUES (?, ?)");
                        $evidenceStmt->execute([$grudgeId, $destination]);
                    }
                }
            }
        }

        $xpGain = 50;
        $userStmt = $pdo->prepare("SELECT xp, level FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $userRow = $userStmt->fetch();

        $newXp = $userRow['xp'] + $xpGain;
        $newLevel = $userRow['level'];
        while ($newXp >= 1000) {
            $newXp -= 1000;
            $newLevel++;
        }

        $updateStmt = $pdo->prepare("UPDATE users SET xp = ?, level = ? WHERE id = ?");
        $updateStmt->execute([$newXp, $newLevel, $userId]);

        checkAchievements($pdo, $userId);

        header("Location: all-grudges.php");
        exit;
    }
}
?>

<div class="dashboard-top">
  <h1 class="graffiti-heading heading-cyan-pink">GET IT OFF YOUR CHEST</h1>
</div>

<div class="evidence-card log-grudge-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <?php if ($error): ?>
    <p style="color: var(--pink); font-size: 0.85rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form action="log-grudge.php" method="POST" enctype="multipart/form-data" class="grudge-form">

    <div class="form-group">
      <label for="title">WHAT HAPPENED</label>
      <textarea id="title" name="title" rows="3" placeholder="Tell it exactly how it went down..." required></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="person">PERSON INVOLVED</label>
        <input type="text" id="person" name="person" placeholder="Who wronged you?" required>
      </div>

      <div class="form-group">
        <label for="category">CATEGORY</label>
        <select id="category" name="category" required>
          <option value="">Select category</option>
          <option value="Roommate">Roommate</option>
          <option value="Work">Work</option>
          <option value="Friend">Friend</option>
          <option value="Family">Family</option>
          <option value="Stranger">Stranger</option>
          <option value="Partner">Partner</option>
          <option value="Other">Other</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="date">DATE IT HAPPENED</label>
        <input type="date" id="date" name="date" required>
      </div>

      <div class="form-group">
        <label for="severity">SEVERITY</label>
        <select id="severity" name="severity" required>
          <option value="">Select severity</option>
          <option value="Low">Low — mildly annoying</option>
          <option value="Medium">Medium — actually irritating</option>
          <option value="High">High — cannot let this go</option>
          <option value="Critical">Critical — blood feud</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>HOW DOES IT FEEL</label>
      <div class="emoji-picker" id="emojiPicker">
        <button type="button" class="emoji-option" data-emoji="😤">😤</button>
        <button type="button" class="emoji-option" data-emoji="😡">😡</button>
        <button type="button" class="emoji-option" data-emoji="💀">💀</button>
        <button type="button" class="emoji-option" data-emoji="🙄">🙄</button>
        <button type="button" class="emoji-option" data-emoji="😭">😭</button>
        <button type="button" class="emoji-option" data-emoji="🔥">🔥</button>
      </div>
      <input type="hidden" id="selectedEmoji" name="emoji">
    </div>

    <div class="form-group">
      <label for="evidence">EVIDENCE (SCREENSHOTS, RECEIPTS, ETC.)</label>
      <div class="evidence-upload">
        <input type="file" id="evidence" name="evidence[]" multiple accept="image/*,.pdf">
        <p class="upload-hint">Drop files or click to upload — the jury will want proof.</p>
      </div>
    </div>

    <div class="form-group">
      <label for="notes">ADDITIONAL NOTES</label>
      <textarea id="notes" name="notes" rows="3" placeholder="Anything else the court should know..."></textarea>
    </div>

    <button type="submit" class="btn-sticker btn-submit-grudge">FILE THE GRUDGE</button>

  </form>
</div>

<?php include 'includes/footer.php'; ?>