<?php
$lang = $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'mr'])) $lang = 'en';

$data = json_decode(file_get_contents(__DIR__ . "/data/epapers.json"), true);
$highlightsData = json_decode(file_get_contents(__DIR__ . "/data/highlights.json"), true);
$highlights = $highlightsData[$lang] ?? [];

$ui = [
  'en' => [
    'title' => 'E-PAPERS',
    'latest' => 'Latest Edition',
    'previous' => 'Previous Editions',
    'read' => 'Read / Listen →',
    'archive' => 'View Archive →',
    'weekly' => 'Weekly education and entrance exam updates',
    'open' => 'Open',
    'updates' => "Today's Important Updates"
  ],
  'mr' => [
    'title' => 'ई-पेपर',
    'latest' => 'नवीन अंक',
    'previous' => 'मागील अंक',
    'read' => 'वाचा / ऐका →',
    'archive' => 'संपूर्ण संग्रह →',
    'weekly' => 'साप्ताहिक शिक्षण आणि प्रवेश परीक्षेचे अपडेट्स',
    'open' => 'खुले',
    'updates' => 'आजचे महत्त्वाचे अपडेट्स'
  ]
];

$data = array_values(array_filter($data, fn($i) => ($i['language'] ?? 'en') === $lang));
usort($data, fn($a,$b)=>strtotime($b['date'])-strtotime($a['date']));
?>

<!DOCTYPE html>
<html>
<head>
<title><?= $ui[$lang]['title'] ?></title>
<link rel="stylesheet" href="/VidyarthiMitra_Website/epapers/assets/epapers-ui.css">
</head>

<body>

<div class="top-nav">
<strong>VidyarthiMitra</strong>
<a href="#">Universities</a>
<a href="#">Colleges</a>
<a href="#">Courses</a>
<a href="#">Entrance Exams</a>
<a href="#">Admissions</a>
<a href="#">News</a>
<a href="#">Epaper</a>
</div>

<div class="page-header"><?= $ui[$lang]['title'] ?></div>

<div class="wrapper">

<!-- LANGUAGE SWITCH -->
<div class="lang-switch">
  <div class="lang-slider <?= $lang==='en'?'lang-en':'lang-mr' ?>"></div>
  <a class="<?= $lang==='en'?'lang-active':'' ?>" href="?lang=en">English</a>
  <a class="<?= $lang==='mr'?'lang-active':'' ?>" href="?lang=mr">Marathi</a>
</div>

<div class="container">

<div class="main">
<div class="section-title"><?= $ui[$lang]['latest'] ?></div>

<div class="latest-grid">
<div>
<h4><?= htmlspecialchars($data[0]['title']) ?></h4>

<?php if(!empty($data[0]['highlights'])): ?>
<div class="highlight-box">
<h5><?= $lang==='mr'?'महत्त्वाचे मुद्दे':'Key Highlights' ?></h5>
<ul>
<?php foreach($data[0]['highlights'] as $h): ?>
<li><?= htmlspecialchars($h) ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php else: ?>
<p><?= $ui[$lang]['weekly'] ?></p>
<?php endif; ?>

<a class="read-btn" href="view.php?slug=<?= urlencode($data[0]['slug']) ?>">
<?= $ui[$lang]['read'] ?>
</a>
</div>

<div class="thumb-box">
<img src="VM_Thumbnail.png">
<div class="thumb-fade"></div>
</div>
</div>

<div class="highlights-box">
<div class="section-title"><?= $ui[$lang]['updates'] ?></div>
<?php foreach($highlights as $h): ?>
<div class="highlight-item">
<div><?= $h['icon'] ?></div>
<div><?= htmlspecialchars($h['text']) ?></div>
</div>
<?php endforeach; ?>
</div>

</div>

<div class="sidebar">
<div class="section-title"><?= $ui[$lang]['previous'] ?></div>
<?php foreach(array_slice($data,1,3) as $p): ?>
<div class="card">
<strong><?= htmlspecialchars($p['title']) ?></strong><br>
<a href="view.php?slug=<?= urlencode($p['slug']) ?>">
<?= $ui[$lang]['open'] ?>
</a>
</div>
<?php endforeach; ?>
<a class="archive-link" href="archive.php?lang=<?= $lang ?>">
<?= $ui[$lang]['archive'] ?>
</a>
</div>

</div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/VidyarthiMitra_Website/epapers/includes/chatbotwidget.php'; ?>

</body>
</html>
