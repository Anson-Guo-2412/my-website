<?php
include 'auth-check.php';
include 'config.php';
include 'header.php';

if (!is_admin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];

    // Upload file thumbnail
    $thumbnail = $_FILES['thumbnail']['name'];
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];

    // Buat nama file unik
    $thumbnail_name = uniqid() . '-' . basename($thumbnail);
    $upload_path = "assets/images/" . $thumbnail_name;

    move_uploaded_file($thumbnail_tmp, $upload_path);

    // Simpan ke database dengan PDO
    $stmt = $conn->prepare("INSERT INTO anime_series (title, description, release_year, genre, thumbnail_url) 
                            VALUES (:title, :desc, :year, :genre, :thumb)");
    $stmt->execute([
        ':title' => $title,
        ':desc' => $desc,
        ':year' => $year,
        ':genre' => $genre,
        ':thumb' => $thumbnail_name
    ]);

    echo "Anime berhasil ditambahkan. <a href='index.php'>Lihat Daftar</a>";
    exit;
}
?>

<h2>Upload Anime Baru</h2>
<form method="POST" enctype="multipart/form-data">
  Judul: <input name="title"><br>
  Deskripsi: <textarea name="description"></textarea><br>
  Tahun: <input name="year" type="number"><br>
  Genre (pisahkan dengan koma): <input name="genre"><br>
  Thumbnail: <input type="file" name="thumbnail" accept="image/*"><br>
  <button type="submit">Upload</button>
</form>
