<?php
include 'auth-check.php';
include 'config.php';
include 'header.php';

if (!is_admin()) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM anime_series WHERE id = ?");
$stmt->execute([$id]);
$anime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anime) {
    echo "Anime tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];

    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail = $_FILES['thumbnail']['name'];
        $uniqueName = uniqid() . '-' . basename($thumbnail); // nama unik
        $thumbPath = "assets/images/" . $uniqueName;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbPath);

        // Simpan hanya nama file ke DB
        $stmt = $conn->prepare("UPDATE anime_series SET title=?, description=?, release_year=?, genre=?, thumbnail_url=? WHERE id=?");
        $stmt->execute([$title, $desc, $year, $genre, $uniqueName, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE anime_series SET title=?, description=?, release_year=?, genre=? WHERE id=?");
        $stmt->execute([$title, $desc, $year, $genre, $id]);
    }

    echo "<p style='color:green;'>✅ Berhasil diupdate. <a href='dashboard.php'>Kembali</a></p>";
    exit;
}
?>

<h2>Edit Anime</h2>
<form method="POST" enctype="multipart/form-data">
  Judul: <input name="title" value="<?= htmlspecialchars($anime['title']) ?>"><br>
  Deskripsi: <textarea name="description"><?= htmlspecialchars($anime['description']) ?></textarea><br>
  Tahun Rilis: <input type="number" name="year" value="<?= htmlspecialchars($anime['release_year']) ?>"><br>
  Genre (pisahkan koma): <input name="genre" value="<?= htmlspecialchars($anime['genre']) ?>"><br>
  Thumbnail Baru: <input type="file" name="thumbnail" accept="image/*"><br>
  <button type="submit">Update</button>
</form>
