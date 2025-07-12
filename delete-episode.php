<?php
include 'config.php';
include 'auth-check.php';

if (!is_admin()) {
  header("Location: index.php");
  exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil nama file video dulu
$stmt = $conn->prepare("SELECT video_url FROM episodes WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
  $file = 'videos/' . $data['video_url'];
  if (file_exists($file)) {
    unlink($file); // hapus file fisik
  }

  // Hapus episode dari database
  $del = $conn->prepare("DELETE FROM episodes WHERE id = ?");
  $del->execute([$id]);
}

header("Location: dashboard.php");
exit;
