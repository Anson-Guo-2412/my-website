<?php
include 'auth-check.php';
include 'config.php';
include 'header.php';

if (!is_admin()) {
  header("Location: index.php");
  exit;
}
?>

<h2>Admin Panel</h2>
<ul>
  <li><a href='upload-anime.php'>Upload Anime Baru</a></li>
  <li><a href='upload-video.php'>Upload Video Episode</a></li>
</ul>
