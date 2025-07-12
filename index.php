<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Form pencarian
echo "<form method='GET' style='margin-bottom:20px;'>
  <input type='text' name='q' placeholder='Cari anime...' style='padding:6px; width:200px;' value='" . htmlspecialchars($q) . "'>
  <select name='genre' style='padding:6px;'>
    <option value=''>- Semua Genre -</option>
    <option value='Action' " . ($genre == 'Action' ? 'selected' : '') . ">Action</option>
    <option value='Romance' " . ($genre == 'Romance' ? 'selected' : '') . ">Romance</option>
    <option value='Fantasy' " . ($genre == 'Fantasy' ? 'selected' : '') . ">Fantasy</option>
    <option value='Comedy' " . ($genre == 'Comedy' ? 'selected' : '') . ">Comedy</option>
    <option value='Adventure' " . ($genre == 'Adventure' ? 'selected' : '') . ">Adventure</option>
  </select>
  <button type='submit' style='padding:6px;'>Cari</button>
</form>";

// SQL Query dasar
$sql = "SELECT * FROM anime_series WHERE 1=1";
$params = [];

// Tambah filter pencarian
if (!empty($q)) {
  $sql .= " AND title LIKE ?";
  $params[] = '%' . $q . '%';
}

// Tambah filter genre
if (!empty($genre)) {
  $sql .= " AND genre LIKE ?";
  $params[] = '%' . $genre . '%';
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Anime Terbaru</h2>";
foreach ($result as $row) {
  echo "<div style='display:flex; gap:20px; margin: 8px 0; padding: 8px; background:#f9f9f9; border-radius:6px;'>
    <img src='assets/images/" . htmlspecialchars($row["thumbnail_url"]) . "' width='120' height='160' style='object-fit:cover;'>
    <div>
      <a href='anime.php?id={$row['id']}' style='font-size:18px; font-weight:bold; color:purple;'>" . htmlspecialchars($row['title']) . "</a><br>
      <small>Genre: " . htmlspecialchars($row['genre']) . "</small>
    </div>
  </div>";
}
?>
