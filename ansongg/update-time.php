<?php
include 'config.php';
session_start();

if (isset($_POST['episode_id'], $_POST['time']) && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE history SET last_time = :time WHERE user_id = :user_id AND episode_id = :episode_id");
    $stmt->execute([
        ':time' => $_POST['time'],
        ':user_id' => $_SESSION['user_id'],
        ':episode_id' => $_POST['episode_id']
    ]);
}
?>
