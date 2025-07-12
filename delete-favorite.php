<?php
include 'config.php';
include 'auth-check.php';

if (!is_logged_in()) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $anime_id = (int)$_POST['id'];
  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("DELETE FROM anime_favorites WHERE user_id = ? AND anime_id = ?");
  $stmt->execute([$user_id, $anime_id]);
}

header("Location: favorites.php");
exit;
