<?php
require 'includes/db.php';

$pageTitle = "Register — Grudge Tracker";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords don't match.";
    } else {
        // Check if username or email already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);

        if ($checkStmt->fetch()) {
            $error = "That username or email is already taken.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $insertStmt->execute([$username, $email, $hashedPassword]);

            // Log them in immediately after registering
            session_start();
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;

            header("Location: dashboard.php");
            exit;
        }
    }
}

include 'includes/header.php';
?>

<main class="auth-page">
  <div class="evidence-card auth-card">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>

    <h1 class="graffiti-heading">FILE YOUR FIRST GRUDGE</h1>
    <p class="auth-subtext">Every account starts with zero trust. Earn it.</p>

    <?php if ($error): ?>
      <p style="color: var(--pink); font-size: 0.85rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST" class="auth-form">
      <div class="form-group">
        <label for="username">USERNAME</label>
        <input type="text" id="username" name="username" placeholder="the_unforgiving" required>
      </div>

      <div class="form-group">
        <label for="email">EMAIL</label>
        <input type="email" id="email" name="email" placeholder="you@grudges.com" required>
      </div>

      <div class="form-group">
        <label for="password">PASSWORD</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">CONFIRM PASSWORD</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn-sticker btn-pink">CREATE MY CASE FILE</button>
    </form>

    <p class="auth-switch">
      Already have a record? <a href="login.php">Sign in →</a>
    </p>
  </div>
</main>

<?php include 'includes/footer.php'; ?>