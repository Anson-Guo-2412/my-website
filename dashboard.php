<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

if (!is_logged_in()) {
  header("Location: login.php");
  exit;
}

echo "<h2>Dashboard Pengguna</h2>";
echo "<p>Selamat datang, <strong>" . htmlspecialchars($_SESSION['username']) . "</strong></p>";

if (is_admin()) {
  echo "<hr><h3>Daftar Anime (admin)</h3>";
  $stmt = $conn->query("SELECT * FROM anime_series ORDER BY id DESC");
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $title = htmlspecialchars($row['title']);
    $year = $row['release_year'];
    echo "<div>";
    echo "<b>{$title}</b> ({$year}) - ";
    echo "<a href='edit-anime.php?id={$id}'>Edit</a> | ";
    echo "<a href='delete-anime.php?id={$id}' onclick=\"return confirm('Hapus anime ini?');\">Hapus</a>";
    echo "</div>";
  }

  // Daftar semua episode
  echo "<h3>Daftar Episode</h3>";

  $sql = "SELECT e.id AS eid, e.episode_number, e.video_url, a.title, a.id AS aid 
          FROM episodes e 
          JOIN anime_series a ON e.anime_id = a.id 
          ORDER BY a.id DESC, e.episode_number ASC";
  $res = $conn->query($sql);

  $hasData = false;
  while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $hasData = true;
    echo "<div style='margin-bottom:10px; padding:10px; border:1px solid #ccc; border-radius:5px;'>
      <b>" . htmlspecialchars($row['title']) . "</b> - Episode " . $row['episode_number'] . "<br>
      <video width='200' controls><source src='videos/" . htmlspecialchars($row['video_url']) . "' type='video/mp4'></video><br>
      <a href='edit-episode.php?id=" . $row['eid'] . "'>✏️ Edit</a> | 
      <a href='delete-episode.php?id=" . $row['eid'] . "' onclick=\"return confirm('Yakin ingin hapus episode ini?')\">🗑️ Hapus</a>
    </div>";
  }

  if (!$hasData) {
    echo "<p>Belum ada episode yang diupload.</p>";
  }
} else {
  // Bukan admin
  header("Location: index.php");
  exit;
}
?>
