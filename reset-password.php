<?php
// Tampilkan error (debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';
include 'header.php';

$message = ""; // untuk feedback

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    $message = "<p style='color:red;'>❌ Password tidak cocok.</p>";
  } else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      $hashed = password_hash($new_password, PASSWORD_DEFAULT);

      // Simpan password hash dan raw password
      $update = $conn->prepare("UPDATE users SET password_hash = ?, raw_password = ? WHERE id = ?");
      $update->execute([$hashed, $new_password, $user['id']]);

      $message = "<p style='color:green;'>✅ Password berhasil direset. <a href='login.php'>Login di sini</a></p>";
    } else {
      $message = "<p style='color:red;'>❌ Username tidak ditemukan.</p>";
    }
  }
}
?>

<h2>Reset Password</h2>
<?= $message ?>
<form method="POST">
  Username:<br>
  <input type="text" name="username" required><br><br>

  Password Baru:<br>
  <input type="password" name="new_password" required><br><br>

  Konfirmasi Password:<br>
  <input type="password" name="confirm_password" required><br><br>

  <button type="submit">Reset</button>
</form>
