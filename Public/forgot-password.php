<?php
http_response_code(404);
$pageTitle = "404 — Grudge Tracker";
include 'includes/header.php';
?>

<main class="auth-page">
  <div class="evidence-card auth-card" style="text-align: center;">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>

    <h1 class="graffiti-heading">404</h1>
    <p class="auth-subtext">This page doesn't exist. Some things really are unforgivable — this feature just isn't built yet.</p>

    <a href="login.php" class="btn-sticker btn-pink" style="display: inline-block; margin-top: 1.5rem; text-decoration: none;">Back to Login →</a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>