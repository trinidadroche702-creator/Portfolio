<?php
require_once __DIR__ . '/../includes/security.php';
require_admin();
$adminActive = 'dashboard';

$tables = [
    'projects' => ['Projects', 'fa-diagram-project'],
    'skills' => ['Skills', 'fa-lightbulb'],
    'contact_messages' => ['Messages', 'fa-envelope'],
    'experience' => ['Experience', 'fa-briefcase'],
    'education' => ['Education', 'fa-graduation-cap'],
    'achievements' => ['Achievements', 'fa-award'],
];
$counts = [];
foreach ($tables as $table => $meta) {
    $counts[$table] = (int) database()->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}
$unread = (int) database()->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Portfolio Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body class="admin-body">
    <?php require __DIR__ . '/_layout.php'; ?>
    <div class="admin-topbar">
        <h1>Welcome back, <span><?= htmlspecialchars(explode('@', $_SESSION['admin_email'] ?? 'admin')[0]) ?></span></h1>
        <span class="who"><i class="fa-solid fa-shield-halved"></i> Signed in as <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></span>
    </div>

    <section class="stat-grid" aria-label="Website statistics">
        <?php foreach ($tables as $table => [$label, $icon]): ?>
            <article class="stat-card">
                <div class="icon"><i class="fa-solid <?= $icon ?>"></i></div>
                <div>
                    <h2><?= htmlspecialchars($label) ?></h2>
                    <p><?= $counts[$table] ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <div class="admin-card">
        <h2><i class="fa-solid fa-envelope-open-text"></i> Message status</h2>
        <p>
            <span class="badge badge-unread"><?= $unread ?> unread</span>
            <span class="badge badge-read"><?= $counts['contact_messages'] - $unread ?> read</span>
        </p>
        <p style="margin-top:14px"><a class="btn btn-ghost btn-small" href="messages.php"><i class="fa-solid fa-inbox"></i> Open inbox</a></p>
    </div>

    <div class="admin-card">
        <h2><i class="fa-solid fa-rocket"></i> Quick actions</h2>
        <div class="actions" style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn btn-small" href="manage.php?type=projects"><i class="fa-solid fa-plus"></i> Add project</a>
            <a class="btn btn-small" href="manage.php?type=skills"><i class="fa-solid fa-plus"></i> Add skill</a>
            <a class="btn btn-small" href="profile.php"><i class="fa-solid fa-user-pen"></i> Edit profile</a>
            <a class="btn btn-ghost btn-small" href="../index.php" target="_blank" rel="noopener"><i class="fa-solid fa-eye"></i> View website</a>
        </div>
    </div>
    </main>
</body>

</html>