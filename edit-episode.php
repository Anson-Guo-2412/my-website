<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

if (!is_admin()) {
  header("Location: index.php");
  exit;
}

$id = (int)$_GET['id'];

// Ambil data episode
$stmt = $conn->prepare("SELECT * FROM episodes WHERE id = ?");
$stmt->execute([$id]);
$episode = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$episode) {
  echo "Episode tidak ditemukan.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ep_num = $_POST['episode_number'];

  if (!empty($_FILES['video']['name'])) {
    // Hapus file lama (jika ada)
    $old = 'videos/' . $episode['video_url'];
    if (file_exists($old)) unlink($old);

    // Gunakan nama unik agar tidak tertimpa
    $original = $_FILES['video']['name'];
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $uniqueName = uniqid('ep_', true) . '.' . $ext;
    $target = 'videos/' . $uniqueName;

    if (move_uploaded_file($_FILES['video']['tmp_name'], $target)) {
      $stmt = $conn->prepare("UPDATE episodes SET episode_number=?, video_url=? WHERE id=?");
      $stmt->execute([$ep_num, $uniqueName, $id]);
    } else {
      echo "<p style='color:red;'>Gagal mengupload file.</p>";
    }
  } else {
    $stmt = $conn->prepare("UPDATE episodes SET episode_number=? WHERE id=?");
    $stmt->execute([$ep_num, $id]);
  }

  echo "Berhasil diupdate. <a href='dashboard.php'>Kembali</a>";
  exit;
}
?>

<h2>Edit Episode</h2>
<form method="POST" enctype="multipart/form-data">
  Episode Ke: <input name="episode_number" type="number" value="<?= htmlspecialchars($episode['episode_number']) ?>"><br>
  Ganti Video (opsional): <input type="file" name="video" accept="video/mp4"><br>
  <button type="submit">Update</button>
</form>
