<?php
include 'config.php';
include 'auth-check.php';

if (!is_logged_in()) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $history_id = (int)$_POST['id'];
  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("DELETE FROM history WHERE id = ? AND user_id = ?");
  $stmt->execute([$history_id, $user_id]);
}

header("Location: history.php");
?>