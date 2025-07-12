<?php
include 'auth-check.php';
include 'config.php';

if (!is_admin()) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Hapus episode terkait
$stmt1 = $conn->prepare("DELETE FROM episodes WHERE anime_id = ?");
$stmt1->execute([$id]);

// Hapus anime
$stmt2 = $conn->prepare("DELETE FROM anime_series WHERE id = ?");
$stmt2->execute([$id]);

header("Location: dashboard.php");
exit;
?>
