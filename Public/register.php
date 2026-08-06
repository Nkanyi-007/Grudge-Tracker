<?php
// Static template for now — DB/auth logic gets wired in later
$pageTitle = "Register — Grudge Tracker";
include 'includes/header.php';
?>

<main class="auth-page">
  <div class="evidence-card auth-card">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>

    <h1 class="graffiti-heading">FILE YOUR FIRST GRUDGE</h1>
    <p class="auth-subtext">Every account starts with zero trust. Earn it.</p>

    <form action="#" method="POST" class="auth-form">
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