<?php
require_once __DIR__ . '/site.php';
$profile = portfolio_profile();
$settings = portfolio_settings();
$pageTitle = $pageTitle ?? $settings['site_title'];
$activePage = $activePage ?? '';
$navigation = ['Home' => 'index.html', 'About' => 'about.html', 'Skills' => 'skills.html', 'Projects' => 'projects.html', 'Resume' => 'resume.html', 'Contact' => 'contact.html'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= escape_html($pageTitle) ?></title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Pacifico&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="style.css"></head>
<body>
<header class="header">
<a class="logo" href="index.html"><?= escape_html($settings['logo_text']) ?></a>
<nav class="navbar">
<?php foreach ($navigation as $label => $url): ?>
<a href="<?= escape_html($url) ?>" class="<?= $activePage === $label ? 'active' : '' ?>"><?= escape_html($label) ?></a>
<?php endforeach; ?>
</nav>
<button class="theme-btn" id="themeBtn" aria-label="Change theme"><i class="fa-solid fa-sun"></i></button>
</header>
