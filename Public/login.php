<?php
require 'includes/db.php';

$pageTitle = "Login — Grudge Tracker";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Incorrect email or password.";
    }
}

include 'includes/header.php';
?>

<main class="auth-page">
  <div class="evidence-card auth-card">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>

    <h1 class="graffiti-heading">SIGN IN, SNITCH</h1>
    <p class="auth-subtext">Trust isn't given here. It's logged.</p>

    <?php if ($error): ?>
      <p style="color: var(--pink); font-size: 0.85rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST" class="auth-form">
      <div class="form-group">
        <label for="email">EMAIL</label>
        <input type="email" id="email" name="email" placeholder="you@grudges.com" required>
      </div>

      <div class="form-group">
        <label for="password">PASSWORD</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <a href="forgot-password.php" class="forgot-link">forgot it? happens.</a>

      <button type="submit" class="btn-sticker btn-pink">ENTER THE COURTROOM</button>
    </form>

    <p class="auth-switch">
      No account? <a href="register.php">File your first grudge →</a>
    </p>
  </div>
</main>

<?php include 'includes/footer.php'; ?>