<?php
// Load data
$data = json_decode(file_get_contents(__DIR__ . "/data/epapers.json"), true);

// Get slug
$slug = $_GET['slug'] ?? null;
$paper = null;

// Detect language from paper
$lang = 'en';

// Find matching paper
foreach ($data as $item) {
    if ($item['slug'] === $slug) {
        $paper = $item;
        $lang = $item['language'] ?? 'en';
        break;
    }
}

// If not found
if (!$paper) {
    http_response_code(404);
    echo "<h2>E-paper not found</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($paper['title']) ?></title>
<link rel="stylesheet" href="/VidyarthiMitra_Website/epapers/assets/epapers-ui.css">

</head>

<body>

<!-- TOP BAR -->
<div class="viewer-header">
  <a class="back-link" href="index.php?lang=<?= $lang ?>">
    ← <?= $lang === 'mr' ? 'ई-पेपरकडे परत' : 'Back to E-Papers' ?>
  </a>

  <div class="title"><?= htmlspecialchars($paper['title']) ?></div>

  <button class="listen-btn" id="listenBtn">
    🔊 <?= $lang === 'mr' ? 'ऐका' : 'Listen' ?>
  </button>
</div>

<!-- AUDIO -->
<audio id="audioPlayer" src="<?= htmlspecialchars($paper['audio']) ?>"></audio>

<!-- PDF VIEWER -->
<div class="pdf-container">
  <iframe
    class="pdf-frame"
    src="<?= htmlspecialchars($paper['pdf']) ?>#toolbar=0&navpanes=0&scrollbar=0">
  </iframe>
</div>

<script>
(() => {
  const audio = document.getElementById("audioPlayer");
  const btn = document.getElementById("listenBtn");
  let isPlaying = false;

  btn.addEventListener("click", () => {
    if (!isPlaying) {
      audio.play();
      btn.innerText = "⏸ <?= $lang === 'mr' ? 'थांबवा' : 'Pause' ?>";
      isPlaying = true;
    } else {
      audio.pause();
      btn.innerText = "🔊 <?= $lang === 'mr' ? 'ऐका' : 'Listen' ?>";
      isPlaying = false;
    }
  });

  audio.addEventListener("ended", () => {
    btn.innerText = "🔊 <?= $lang === 'mr' ? 'ऐका' : 'Listen' ?>";
    isPlaying = false;
  });
})();
</script>

</body>
</html>
