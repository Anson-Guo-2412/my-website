<?php
include 'config.php';
include 'auth-check.php';
include 'header.php';

$episode_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? null;

// Ambil episode + judul anime
$stmt = $conn->prepare("SELECT e.*, a.title AS anime_title FROM episodes e JOIN anime_series a ON e.anime_id = a.id WHERE e.id = :id");
$stmt->execute([':id' => $episode_id]);
$ep = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ep) {
  echo "Episode tidak ditemukan.";
  exit;
}

$anime_id = $ep['anime_id'];
$start_time = 0;

// Simpan ke history dan ambil waktu terakhir nonton (jika login)
if ($user_id) {
  $check = $conn->prepare("SELECT watch_time FROM history WHERE user_id = :uid AND episode_id = :eid");
  $check->execute([':uid' => $user_id, ':eid' => $episode_id]);
  $watch_data = $check->fetch(PDO::FETCH_ASSOC);

  if ($watch_data) {
    $start_time = $watch_data['watch_time'];
  } else {
    $insert = $conn->prepare("INSERT INTO history (user_id, episode_id, watch_time) VALUES (:uid, :eid, 0)");
    $insert->execute([':uid' => $user_id, ':eid' => $episode_id]);
  }
}

echo "<h2>{$ep['anime_title']} - Episode {$ep['episode_number']}</h2>";
?>

<div style="display: flex; gap: 20px;">
  <div>
    <video id="videoPlayer" width="600" controls>
      <source src="videos/<?= htmlspecialchars($ep['video_url']) ?>" type="video/mp4">
    </video>
  </div>

  <div style="max-width: 300px;">
    <h3>Daftar Episode</h3>
    <div class='episode-list'>
    <?php
    $eps = $conn->prepare("SELECT id, episode_number FROM episodes WHERE anime_id = :aid ORDER BY episode_number");
    $eps->execute([':aid' => $anime_id]);
    while ($e = $eps->fetch(PDO::FETCH_ASSOC)) {
      $active = $e['id'] == $episode_id ? 'active' : '';
      echo "<a href='watch.php?id={$e['id']}' class='episode-btn $active'>Ep {$e['episode_number']}</a>";
    }
    ?>
    </div>
  </div>
</div>

<script>
const player = document.getElementById('videoPlayer');
const start = <?= $start_time ?>;

player.addEventListener('loadedmetadata', () => {
  if (start > 0 && start < player.duration) {
    player.currentTime = start;
  }
});

<?php if ($user_id): ?>
setInterval(() => {
  const time = Math.floor(player.currentTime);
  fetch('update-watch-time.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `episode_id=<?= $episode_id ?>&time=${time}`
  });
}, 10000);
<?php endif; ?>
</script>
