<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

if (!is_logged_in()) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
echo "<h2>Favorit Saya</h2>";

// Ambil anime favorit pengguna
$stmt = $conn->prepare("
  SELECT af.id, a.title, a.id AS anime_id 
  FROM anime_favorites af
  JOIN anime_series a ON af.anime_id = a.id
  WHERE af.user_id = ?
");
$stmt->execute([$user_id]);
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($res) === 0) {
  echo "<p>Belum ada anime favorit.</p>";
} else {
  foreach ($res as $row) {
    echo "<div style='margin-bottom:10px; padding:10px; background:#eef; border-radius:5px;'>
      <a href='anime.php?id=" . $row['anime_id'] . "'><b>" . htmlspecialchars($row['title']) . "</b></a>
      <form method='POST' action='delete-favorite.php' style='display:inline'>
        <input type='hidden' name='id' value='" . $row['anime_id'] . "'>
        <button type='submit' style='border:none; background:none; color:red; cursor:pointer;'>💔 Hapus</button>
      </form>
    </div>";
  }
}
?>
