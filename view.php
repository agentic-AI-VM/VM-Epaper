<?php
$data = json_decode(file_get_contents(__DIR__ . "/data/epapers.json"), true);

$slug = $_GET['slug'] ?? null;
$paper = null;
$lang = 'en';

foreach ($data as $item) {
    if ($item['slug'] === $slug) {
        $paper = $item;
        $lang = $item['language'] ?? 'en';
        break;
    }
}

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

<!-- TOP VIEWER BAR -->
<div class="viewer-header">
  <a class="back-link" href="index.php?lang=<?= $lang ?>">
    ← <?= $lang === 'mr' ? 'ई-पेपरकडे परत' : 'Back to E-Papers' ?>
  </a>

  <div class="title"><?= htmlspecialchars($paper['title']) ?></div>

  <div style="display:flex; gap:10px;">
    <button class="listen-btn" id="listenBtn">
      🔊 <?= $lang === 'mr' ? 'ऐका' : 'Listen' ?>
    </button>
    <button class="listen-btn" id="toggleThumbs">
      ☰ <?= $lang === 'mr' ? 'पाने' : 'Pages' ?>
    </button>
  </div>
</div>

<!-- AUDIO -->
<audio id="audioPlayer" src="<?= htmlspecialchars($paper['audio']) ?>"></audio>

<!-- PDF VIEWER -->
<div class="pdf-viewer">

  <!-- THUMBNAILS -->
  <div class="pdf-thumbs" id="thumbs"></div>

  <!-- MAIN VIEW -->
  <div class="pdf-main">
    <div class="pdf-toolbar">
      <button class="read-btn" id="prevPage">◀</button>
      <span id="pageInfo">Page 1 / 1</span>
      <button class="read-btn" id="nextPage">▶</button>

      <button class="read-btn" id="zoomOut">−</button>
      <button class="read-btn" id="zoomIn">+</button>
    </div>

    <canvas id="pdfCanvas"></canvas>
  </div>

</div>

<!-- PDF.JS LOGIC (MODULE) -->
<script type="module">
import * as pdfjsLib from "/VidyarthiMitra_Website/epapers/assets/pdfjs/pdf.mjs";

pdfjsLib.GlobalWorkerOptions.workerSrc =
  "/VidyarthiMitra_Website/epapers/assets/pdfjs/pdf.worker.mjs";

const url = "<?= htmlspecialchars($paper['pdf']) ?>";

let pdfDoc = null;
let pageNum = 1;
let scale = 1.25;

const canvas = document.getElementById("pdfCanvas");
const ctx = canvas.getContext("2d");

async function renderPage(num) {
  const page = await pdfDoc.getPage(num);
  const viewport = page.getViewport({ scale });

  canvas.width = viewport.width;
  canvas.height = viewport.height;

  await page.render({
    canvasContext: ctx,
    viewport
  }).promise;

  document.getElementById("pageInfo").innerText =
    `Page ${pageNum} / ${pdfDoc.numPages}`;
}

pdfjsLib.getDocument(url).promise.then(async pdf => {
  pdfDoc = pdf;
  renderPage(pageNum);

  const thumbs = document.getElementById("thumbs");
  thumbs.innerHTML = "";

  for (let i = 1; i <= pdf.numPages; i++) {
    const page = await pdf.getPage(i);
    const vp = page.getViewport({ scale: 0.2 });

    const thumb = document.createElement("canvas");
    const tctx = thumb.getContext("2d");

    thumb.width = vp.width;
    thumb.height = vp.height;

    await page.render({
      canvasContext: tctx,
      viewport: vp
    }).promise;

    thumb.onclick = () => {
      pageNum = i;
      renderPage(pageNum);
    };

    thumbs.appendChild(thumb);
  }
});

// Navigation
document.getElementById("prevPage").onclick = () => {
  if (pageNum > 1) {
    pageNum--;
    renderPage(pageNum);
  }
};

document.getElementById("nextPage").onclick = () => {
  if (pageNum < pdfDoc.numPages) {
    pageNum++;
    renderPage(pageNum);
  }
};

document.getElementById("zoomIn").onclick = () => {
  scale += 0.1;
  renderPage(pageNum);
};

document.getElementById("zoomOut").onclick = () => {
  scale = Math.max(scale - 0.1, 0.5);
  renderPage(pageNum);
};

// Toggle thumbnails
document.getElementById("toggleThumbs").onclick = () => {
  document.getElementById("thumbs").classList.toggle("hidden");
};
</script>

<!-- AUDIO LOGIC -->
<script>
(() => {
  const audio = document.getElementById("audioPlayer");
  const btn = document.getElementById("listenBtn");
  let playing = false;

  btn.onclick = () => {
    if (!playing) {
      audio.play();
      btn.innerText = "⏸ <?= $lang === 'mr' ? 'थांबवा' : 'Pause' ?>";
      playing = true;
    } else {
      audio.pause();
      btn.innerText = "🔊 <?= $lang === 'mr' ? 'ऐका' : 'Listen' ?>";
      playing = false;
    }
  };

  audio.onended = () => {
    btn.innerText = "🔊 <?= $lang === 'mr' ? 'ऐका' : 'Listen' ?>";
    playing = false;
  };
})();
</script>

<?php include $_SERVER['DOCUMENT_ROOT'].'/VidyarthiMitra_Website/epapers/includes/chatbotwidget.php'; ?>

</body>
</html>
