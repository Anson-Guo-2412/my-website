<?php
include 'config.php';
include 'header.php';

$query = isset($_GET['q']) ? '%' . $_GET['q'] . '%' : '%';
$genre = $_GET['genre'] ?? '';

$sql = "SELECT * FROM anime_series WHERE title LIKE ?";
$params = [$query];

if ($genre !== '') {
  $sql .= " AND genre LIKE ?";
  $params[] = '%' . $genre . '%';
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Hasil Pencarian</h2>";
foreach ($result as $row) {
  echo "<div style='margin: 8px 0; padding: 8px; background:#fff; border-radius:6px;'>
    <img src='assets/images/" . htmlspecialchars($row['thumbnail_url']) . "' width='100' style='vertical-align:middle;'> 
    <a href='anime.php?id={$row['id']}' style='font-size:18px; font-weight:bold;'> " . htmlspecialchars($row['title']) . "</a><br>
    <small>Genre: " . htmlspecialchars($row['genre']) . "</small>
  </div>";
}
?>
