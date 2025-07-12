<?php include 'auth-check.php'; ?>
<!DOCTYPE html>
<html>
  <link rel="stylesheet" href="style1-final.css">
<head>
  <meta charset="UTF-8">
  <title>Streaming Online</title>
</head>
<body>
  <h1><a href="index.php">Streaming Online</a></h1>
  <nav style="margin-bottom: 10px;">
    <a href="index.php">Beranda</a>

    <?php if (is_logged_in()): ?>
      <a href="favorites.php">Favorit</a>
      <a href="history.php">Riwayat</a>

      <?php if (is_admin()): ?>
        <a href="dashboard.php">Dashboard Admin</a>
        <a href="upload-anime.php">Upload Series</a>
        <a href="upload-video.php">Upload Episode</a>
        <a href="admin-users.php">Kelola Pengguna</a>
      <?php endif; ?>

      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </nav>
  <hr>
