<?php
include 'auth-check.php';
include 'config.php';
include 'header.php';

if (!is_admin()) {
  header("Location: index.php");
  exit;
}

function validate_anime_id($conn, $anime_id) {
  $stmt = $conn->prepare("SELECT 1 FROM anime_series WHERE id = :id");
  $stmt->execute([':id' => $anime_id]);
  return $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $anime_id = $_POST['anime_id'];
  $episode = $_POST['episode'] ?? null;
  $video = $_FILES['video']['name'];
  $tmp = $_FILES['video']['tmp_name'];

  // Validasi ID anime
  if (!validate_anime_id($conn, $anime_id)) {
    echo "<p style='color:red;'>❌ Anime ID tidak ditemukan.</p>";
    exit;
  }

  // Validasi ekstensi
  $allowed_ext = ['mp4'];
  $ext = strtolower(pathinfo($video, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed_ext)) {
    echo "<p style='color:red;'>❌ Format tidak didukung. Hanya MP4.</p>";
    exit;
  }

  // Auto episode jika tidak diisi
  if (empty($episode)) {
    $getEp = $conn->prepare("SELECT MAX(episode_number) FROM episodes WHERE anime_id = :id");
    $getEp->execute([':id' => $anime_id]);
    $last = $getEp->fetchColumn();
    $episode = $last ? $last + 1 : 1;
  }

  // Cek duplikat episode
  $cek = $conn->prepare("SELECT COUNT(*) FROM episodes WHERE anime_id = :a AND episode_number = :e");
  $cek->execute([':a' => $anime_id, ':e' => $episode]);
  if ($cek->fetchColumn() > 0) {
    echo "<p style='color:red;'>❌ Episode ke-$episode sudah ada.</p>";
    exit;
  }

  // Nama video unik
  $uniqueName = uniqid('ep_', true) . '.' . $ext;
  $target = "videos/" . $uniqueName;

  if (!move_uploaded_file($tmp, $target)) {
    echo "<p style='color:red;'>❌ Gagal upload file.</p>";
    exit;
  }

  try {
    $conn->beginTransaction();
    $stmt = $conn->prepare("INSERT INTO episodes (anime_id, episode_number, video_url) VALUES (:anime_id, :episode, :video)");
    $stmt->execute([
      ':anime_id' => $anime_id,
      ':episode' => $episode,
      ':video' => $uniqueName
    ]);
    $conn->commit();
    echo "<p style='color:green;'>✅ Berhasil upload Episode $episode. <a href='dashboard.php'>Kembali</a></p>";
  } catch (PDOException $e) {
    $conn->rollBack();
    echo "<p style='color:red;'>❌ Gagal menyimpan ke database: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
}
?>

<h2>Upload Video Episode</h2>
<form method="POST" enctype="multipart/form-data">
  ID Anime: <input name="anime_id" required><br>
  Episode (biarkan kosong jika otomatis): <input name="episode" type="number"><br>
  File Video: <input type="file" name="video" accept="video/mp4" required><br>
  <button type="submit">Upload</button>
</form>
