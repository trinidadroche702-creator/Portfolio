<?php
require_once __DIR__ . '/../includes/security.php';
require_admin();
$tables = ['projects', 'skills', 'contact_messages', 'experience'];
$counts = [];
foreach ($tables as $table) { $counts[$table] = (int) database()->query("SELECT COUNT(*) FROM $table")->fetchColumn(); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Portfolio Dashboard</title><link rel="stylesheet" href="../style.css"></head>
<body>
<main class="admin-page">
<h1>Portfolio Dashboard</h1>
<p>Signed in as <?= escape_html($_SESSION['admin_email'] ?? '') ?></p>
<form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><button type="submit">Log out</button></form>
<h2>Website statistics</h2>
<?php foreach ($counts as $label => $count): ?>
<p><?= escape_html(ucwords(str_replace('_', ' ', $label))) ?>: <?= $count ?></p>
<?php endforeach; ?>
<nav><a href="profile.php">Edit profile</a> <a href="manage.php?type=skills">Manage content</a> <a href="messages.php">Messages</a></nav>
</main></body></html>
