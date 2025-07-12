<?php
session_start();
include 'config.php';
include 'header.php';

if (isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $user['role'];
    header("Location: index.php");
    exit;
  }

  echo "<p style='color:red'>Login gagal: Username atau password salah</p>";
}
?>

<form method="POST">
  <input name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>
<p><a href="reset-password.php">Lupa Password?</a></p>
