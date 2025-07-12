<?php
include 'config.php';
include 'auth-check.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$anime_id = (int)$_GET['id'];

// Cek apakah sudah difavoritkan
$stmt = $conn->prepare("SELECT id FROM anime_favorites WHERE user_id = :uid AND anime_id = :aid");
$stmt->execute([':uid' => $user_id, ':aid' => $anime_id]);
$exists = $stmt->fetch();

if ($exists) {
    // Jika sudah ada, hapus (unfavorite)
    $del = $conn->prepare("DELETE FROM anime_favorites WHERE user_id = :uid AND anime_id = :aid");
    $del->execute([':uid' => $user_id, ':aid' => $anime_id]);
} else {
    // Jika belum, tambahkan
    $ins = $conn->prepare("INSERT INTO anime_favorites (user_id, anime_id) VALUES (:uid, :aid)");
    $ins->execute([':uid' => $user_id, ':aid' => $anime_id]);
}

header("Location: anime.php?id=$anime_id");
exit;
?>
