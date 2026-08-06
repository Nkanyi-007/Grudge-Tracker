<?php
session_start();
if (!isset($pageTitle)) {
  $pageTitle = "Grudge Tracker";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&family=Manrope:wght@400;600;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/Grudge-Tracker/Public/css/main.css">
</head>
<body>

<?php $showSidebar = isset($_SESSION['user_id']) || true; // temp: always show while auth isn't wired up ?>

<div class="app-shell">
  <?php if ($showSidebar): include 'includes/sidebar.php'; endif; ?>

  <div class="app-main">