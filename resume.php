<?php
$activePage = 'Resume'; $pageTitle = 'Resume | Roche Trinidad'; require __DIR__ . '/includes/header.php';
$education = portfolio_education(); $experience = portfolio_experience(); $skills = portfolio_skills();
?>
<main class="page-container resume-page">
<section class="page-heading"><p class="section-label">MY RESUME</p><h1>Education &amp; Experience</h1></section>
<section class="resume-grid">
<div class="resume-column">
<div class="resume-section"><h2>Education</h2>
<?php foreach ($education as $item): ?><article class="resume-item"><span class="resume-date"><?= escape_html($item['dates']) ?></span><h3><?= escape_html($item['qualification']) ?></h3><p><?= escape_html($item['institution']) ?></p><p><?= nl2br(escape_html($item['description'])) ?></p></article><?php endforeach; ?>
</div><div class="resume-section"><h2>Experience</h2>
<?php foreach ($experience as $item): ?><article class="resume-item"><span class="resume-date"><?= escape_html($item['dates']) ?></span><h3><?= escape_html($item['title']) ?></h3><p><?= escape_html($item['organization']) ?></p><p><?= nl2br(escape_html($item['description'])) ?></p></article><?php endforeach; ?>
</div></div>
<div class="resume-column"><h2>Skills</h2>
<?php foreach ($skills as $skill): ?><p><?= escape_html($skill['name']) ?></p><?php endforeach; ?>
</div>
</section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
