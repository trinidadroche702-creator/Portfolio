<?php
$activePage = 'Projects'; $pageTitle = 'Projects | Roche Trinidad'; require __DIR__ . '/includes/header.php';
$projects = portfolio_projects();
?>
<main class="page-container projects-page">
<section class="page-heading"><p class="section-label">MY PROJECTS</p><h1>Featured Projects</h1></section>
<section class="projects-grid">
<?php foreach ($projects as $project): ?>
<article class="project-card">
<div class="project-image"><?php if ($project['image_path'] !== ''): ?><img src="<?= escape_html($project['image_path']) ?>" alt="<?= escape_html($project['title']) ?>"><?php endif; ?></div>
<div class="project-content">
<h2><?= escape_html($project['title']) ?></h2>
<p><?= nl2br(escape_html($project['description'])) ?></p>
<div class="project-tech"><?php foreach (array_filter(array_map('trim', explode(',', $project['technologies']))) as $technology): ?><span><?= escape_html($technology) ?></span><?php endforeach; ?></div>
<?php if (filter_var($project['project_url'], FILTER_VALIDATE_URL) && str_starts_with($project['project_url'], 'https://')): ?><a href="<?= escape_html($project['project_url']) ?>">View project</a><?php endif; ?>
</div>
</article>
<?php endforeach; ?>
</section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
