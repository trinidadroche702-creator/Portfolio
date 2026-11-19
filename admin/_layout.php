<?php

/** Shared admin shell: sidebar + main opening. Set $adminActive before including. */
$adminNav = [
    'dashboard' => ['index.php', 'Dashboard', 'fa-gauge-high'],
    'profile' => ['profile.php', 'Profile', 'fa-id-card'],
    'skills' => ['manage.php?type=skills', 'Skills', 'fa-lightbulb'],
    'projects' => ['manage.php?type=projects', 'Projects', 'fa-diagram-project'],
    'education' => ['manage.php?type=education', 'Education', 'fa-graduation-cap'],
    'experience' => ['manage.php?type=experience', 'Experience', 'fa-briefcase'],
    'achievements' => ['manage.php?type=achievements', 'Achievements', 'fa-award'],
    'messages' => ['messages.php', 'Messages', 'fa-envelope'],
    'settings' => ['settings.php', 'Settings', 'fa-sliders'],
    'account' => ['account.php', 'Account', 'fa-user-shield'],
];
$adminActive = $adminActive ?? '';
?>
<aside class="admin-sidebar">
    <a class="brand" href="index.php">Roejie Admin</a>
    <nav aria-label="Admin navigation">
        <?php foreach ($adminNav as $key => [$url, $label, $icon]): ?>
            <a href="<?= htmlspecialchars($url) ?>" class="<?= $adminActive === $key ? 'active' : '' ?>">
                <i class="fa-solid <?= $icon ?>"></i><?= htmlspecialchars($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <span><i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></span>
        <a href="../index.php" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> View website</a>
        <form method="post" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button class="btn btn-danger btn-small" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Log out</button>
        </form>
    </div>
</aside>
<main class="admin-main">