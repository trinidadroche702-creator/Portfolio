<?php
$activePage = 'About'; $pageTitle = 'About | Roche Trinidad'; require __DIR__ . '/includes/header.php';
$education = portfolio_education();
$achievements = portfolio_achievements();
?>
<main class="page-container">
<section class="page-heading"><p class="section-label">ABOUT ME</p><h1>Get to Know Me <span class="heading-heart">♡</span></h1></section>
<section class="about-intro">
<div class="about-photo"><img src="<?= escape_html($profile['about_image']) ?>" alt="<?= escape_html($profile['name']) ?>"></div>
<div class="about-text"><h2>My Background</h2><p><?= nl2br(escape_html($profile['biography'])) ?></p></div>
</section>
<section class="about-cards">
<div class="about-card"><div class="card-icon"><i class="fa-solid fa-graduation-cap"></i></div><h3>Education</h3>
<?php foreach ($education as $item): ?><p><?= escape_html($item['qualification']) ?><br><?= escape_html($item['institution']) ?><br><?= escape_html($item['dates']) ?></p><?php endforeach; ?>
</div><div class="about-card"><div class="card-icon"><i class="fa-regular fa-heart"></i></div><h3>Achievements</h3>
<?php foreach ($achievements as $item): ?><p><?= escape_html($item['title']) ?></p><?php endforeach; ?>
</div>
</section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
