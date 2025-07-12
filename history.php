<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

if (!is_logged_in()) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

echo "<h2>Riwayat Tontonan</h2>";

$stmt = $conn->prepare("
  SELECT h.id AS history_id, a.title, e.episode_number, e.id AS episode_id, h.watch_time
  FROM history h
  JOIN episodes e ON h.episode_id = e.id
  JOIN anime_series a ON e.anime_id = a.id
  WHERE h.user_id = ?
  ORDER BY h.id DESC
");
$stmt->execute([$user_id]);
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($res) === 0) {
  echo "<p>Belum ada riwayat tontonan.</p>";
} else {
  foreach ($res as $row) {
    $minute = floor($row['watch_time'] / 60);
    $second = $row['watch_time'] % 60;
    $resumeInfo = $row['watch_time'] > 0 ? " - lanjut di menit {$minute}:" . str_pad($second, 2, '0', STR_PAD_LEFT) : "";

    echo "<div style='margin-bottom:10px; padding:10px; background:#eef; border-radius:5px;'>
      <a href='watch.php?id={$row['episode_id']}'><b>" . htmlspecialchars($row['title']) . " - Episode {$row['episode_number']}</b></a>
      <small>{$resumeInfo}</small>
      <form method='POST' action='delete-history.php' style='display:inline'>
        <input type='hidden' name='id' value='{$row['history_id']}'>
        <button type='submit' style='border:none; background:none; color:red; cursor:pointer;'>🗑️ Hapus</button>
      </form>
    </div>";
  }
}
?>
