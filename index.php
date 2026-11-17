<?php
$activePage = 'Home'; $pageTitle = 'Home | Roche Trinidad'; require __DIR__ . '/includes/header.php';
?>
<main>
<section class="hero">
<div class="hero-content">
<p class="small-title">HI, I'M</p>
<h1><?= escape_html($profile['name']) ?></h1>
<h2><?= escape_html($profile['tagline']) ?></h2>
<p class="hero-description"><?= escape_html($profile['introduction']) ?></p>
<a href="projects.html" class="primary-btn">View My Projects <i class="fa-solid fa-arrow-right"></i></a>
</div><div class="hero-image-container">
<div class="pink-circle"></div>
<div class="profile-frame"><img src="<?= escape_html($profile['profile_image']) ?>" alt="<?= escape_html($profile['name']) ?>" class="profile-image"></div>
<div class="doodle doodle-one">♡</div>
<div class="doodle doodle-two">〰</div>
<div class="doodle doodle-three">♡</div>
</div>
</section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
