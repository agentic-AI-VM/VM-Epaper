<?php
$data = json_decode(file_get_contents(__DIR__ . "/data/epapers.json"), true);

// Language toggle (default: English)
$lang = $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'mr'])) {
    $lang = 'en';
}

// Filter by language
$data = array_values(array_filter($data, function ($item) use ($lang) {
    return ($item['language'] ?? 'en') === $lang;
}));

// Sort latest first
usort($data, function ($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>

<!DOCTYPE html>
<html>
<head>
  <title>E-Paper Archive | Vidyarthi Mitra</title>
  <link rel="stylesheet" href="/VidyarthiMitra_Website/epapers/assets/epapers-ui.css">
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
  <div class="navbar-left">
    <div class="logo">VidyarthiMitra</div>
    <div class="nav-links">
      <a href="#">Universities</a>
      <a href="#">Colleges</a>
      <a href="#">Courses</a>
      <a href="#">Entrance Exams</a>
      <a href="#">Mock Exams</a>
      <a href="#">Cut-offs</a>
      <a href="#">Admissions</a>
      <a href="#">News</a>
      <a href="#">Epaper <span class="badge">NEW</span></a>
      <a href="#">Guide Me</a>
    </div>
  </div>
</div>

<!-- ORANGE HEADER -->
<div class="page-header">
  <h1>E-PAPER ARCHIVE</h1>
</div>

<div class="wrapper">

  <!-- LANGUAGE SWITCH (SAME AS INDEX) -->
  <div class="lang-switch">
    <div class="lang-slider <?= $lang === 'en' ? 'lang-en' : 'lang-mr' ?>"></div>
    <a href="?lang=en" class="<?= $lang === 'en' ? 'lang-active' : '' ?>">English</a>
    <a href="?lang=mr" class="<?= $lang === 'mr' ? 'lang-active' : '' ?>">Marathi</a>
  </div>

<?php if (empty($data)): ?>
  <p>No archived e-papers available.</p>
<?php else: ?>

  <div class="archive-list">
    <?php foreach ($data as $paper): ?>
      <div class="archive-item">
        <strong><?= htmlspecialchars($paper['title']) ?></strong>
        <small><?= date("d M Y", strtotime($paper['date'])) ?></small>
        <a href="view.php?slug=<?= urlencode($paper['slug']) ?>">
          Read / Listen →
        </a>
      </div>
    <?php endforeach; ?>
  </div>

<?php endif; ?>

  <a class="back-link" href="index.php?lang=<?= $lang ?>">
    ← Back to Latest
  </a>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/VidyarthiMitra_Website/epapers/includes/chatbotwidget.php'; ?>

</body>
</html>
