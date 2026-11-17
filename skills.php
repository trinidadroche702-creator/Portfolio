<?php
$activePage = 'Skills'; $pageTitle = 'Skills | Roche Trinidad'; require __DIR__ . '/includes/header.php';
$skills = portfolio_skills();
?>
<main class="page-container skills-page">
<section class="page-heading">
<p class="section-label">MY SKILLS</p>
<h1>Skills &amp; Technologies</h1>
<p class="page-description">My skills and technologies, managed from the dashboard.</p>
</section>
<section class="technology-grid">
<?php foreach ($skills as $skill): ?>
<div class="technology-card">
<div class="technology-icon"><?php if ($skill['icon_class'] !== ''): ?><i class="<?= escape_html($skill['icon_class']) ?>"></i><?php else: ?><?= escape_html(substr($skill['name'], 0, 3)) ?><?php endif; ?></div>
<h3><?= escape_html($skill['name']) ?></h3>
<p><?= escape_html($skill['category']) ?></p>
</div>
<?php endforeach; ?>
</section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
