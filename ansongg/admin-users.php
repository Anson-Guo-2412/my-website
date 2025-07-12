<?php
include 'auth-check.php';
include 'config.php';
include 'header.php';

if (!is_admin()) {
    header("Location: index.php");
    exit;
}

echo "<h2>Daftar Pengguna</h2>";

$stmt = $conn->query("SELECT id, username, role, raw_password FROM users ORDER BY id");

echo "<table border='1' cellpadding='8'>
  <tr>
    <th>ID</th>
    <th>Username</th>
    <th>Role</th>
    <th>Password (Plaintext)</th>
  </tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo "<tr>
    <td>{$row['id']}</td>
    <td>" . htmlspecialchars($row['username']) . "</td>
    <td>{$row['role']}</td>
    <td>" . htmlspecialchars($row['raw_password'] ?? '-') . "</td>
  </tr>";
}

echo "</table>";
?>
