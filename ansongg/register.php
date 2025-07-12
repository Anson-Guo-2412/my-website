<?php
session_start();
include 'config.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $raw = $_POST['password'];

  // Cek apakah username adalah 'admin'
  $role = ($username === 'admin') ? 'admin' : 'user';

  try {
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, raw_password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $password, $raw, $role]);
    echo "<p style='color:green;'>Registrasi berhasil. <a href='login.php'>Login</a></p>";
  } catch (PDOException $e) {
    if (strpos($e->getMessage(), 'UNIQUE') !== false || strpos($e->getMessage(), 'users.username') !== false) {
      echo "<p style='color:red;'>❌ Username sudah digunakan. <a href='register.php'>Coba lagi</a></p>";
    } else {
      echo "<p style='color:red;'>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
  }
}
?>

<form method="POST">
  <input name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Register</button>
</form>
