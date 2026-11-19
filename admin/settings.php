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
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Website settings</title><link rel="stylesheet" href="../style.css"></head>
<body><main class="admin-page"><h1>Website settings</h1><a href="index.php">Dashboard</a>
<?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><label>Site title<input name="site_title" maxlength="190" value="<?= escape_html($settings['site_title']) ?>" required></label><label>Logo text<input name="logo_text" maxlength="80" value="<?= escape_html($settings['logo_text']) ?>" required></label><button type="submit">Save settings</button></form>
</main></body></html>
