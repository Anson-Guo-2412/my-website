<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM anime_series WHERE id = ?");
$stmt->execute([$id]);
$anime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anime) {
    echo "Anime tidak ditemukan.";
    exit;
}

echo "<h2>" . htmlspecialchars($anime['title']) . "</h2>";

if (!empty($anime['thumbnail_url'])) {
    echo "<img src='assets/images/" . htmlspecialchars($anime['thumbnail_url']) . "' width='200' style='border-radius:8px;'><br><br>";
}

echo "<p><b>Tahun:</b> " . htmlspecialchars($anime['release_year']) . "</p>";
echo "<p><b>Deskripsi:</b> " . nl2br(htmlspecialchars($anime['description'])) . "</p>";
echo "<p><b>Genre:</b> " . htmlspecialchars($anime['genre']) . "</p>";

if (is_logged_in()) {
  $user_id = $_SESSION['user_id'];
  $check = $conn->prepare("SELECT 1 FROM anime_favorites WHERE user_id = ? AND anime_id = ?");
  $check->execute([$user_id, $id]);

  if ($check->fetch()) {
    // Sudah difavoritkan, tampilkan tombol Unfavorite
    echo "<p><a href='add-favorite.php?id={$id}' style='color:red;'>💔 Hapus dari Favorit</a></p>";
  } else {
    // Belum difavoritkan, tampilkan tombol Favorite
    echo "<p><a href='add-favorite.php?id={$id}'>❤️ Tambah ke Favorit</a></p>";
  }
}

// Ambil daftar episode
$ep = $conn->prepare("SELECT * FROM episodes WHERE anime_id = ? ORDER BY episode_number");
$ep->execute([$id]);

echo "<h3>Daftar Episode</h3>";
$episodes = $ep->fetchAll(PDO::FETCH_ASSOC);
if (empty($episodes)) {
    echo "<p>Belum ada episode.</p>";
} else {
    echo "<div class='episode-list'>";
    foreach ($episodes as $row) {
        echo "<a href='watch.php?id={$row['id']}' class='episode-btn'>Ep {$row['episode_number']}</a>";
    }
    echo "</div>";
}
?>
