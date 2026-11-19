<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/site.php';
require_admin();
$settings = portfolio_settings();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $titleInput = $_POST['site_title'] ?? '';
    $logoInput = $_POST['logo_text'] ?? '';
    $title = is_string($titleInput) ? trim($titleInput) : '';
    $logo = is_string($logoInput) ? trim($logoInput) : '';
    if ($title === '' || strlen($title) > 190 || $logo === '' || strlen($logo) > 80) { $error = 'Enter a title and logo text within the limits.'; }
    else { database()->prepare('UPDATE site_settings SET site_title = ?, logo_text = ? WHERE id = 1')->execute([$title, $logo]); header('Location: settings.php'); exit; }
}
$adminActive = 'settings';
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Website settings | Portfolio Admin</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="admin.css"></head>
<body class="admin-body">
<?php require __DIR__ . '/_layout.php'; ?>
<div class="admin-topbar"><h1>Website <span>Settings</span></h1></div>
<?php if ($error !== ''): ?><p class="alert alert-error"><?= escape_html($error) ?></p><?php endif; ?>
<div class="admin-card">
<h2><i class="fa-solid fa-sliders"></i> Branding</h2>
<form class="admin-form" method="post"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
<label>Site title<input name="site_title" maxlength="190" value="<?= escape_html($settings['site_title']) ?>" required></label>
<label>Logo text<input name="logo_text" maxlength="80" value="<?= escape_html($settings['logo_text']) ?>" required></label>
<div><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save settings</button></div>
</form></div>
</main></body></html>
