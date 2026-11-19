<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/site.php';
require_admin();
$profile = portfolio_profile();
$fields = ['name', 'tagline', 'introduction', 'biography', 'email', 'location', 'school', 'github_url', 'linkedin_url', 'facebook_url', 'instagram_url'];
$values = $profile;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    foreach ($fields as $field) { $input = $_POST[$field] ?? ''; $values[$field] = is_string($input) ? trim($input) : ''; }
    if ($values['name'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) { $error = 'Enter a name and a valid email address.'; }
    $limits = ['name' => 120, 'tagline' => 190, 'email' => 190, 'location' => 190, 'school' => 190, 'github_url' => 255, 'linkedin_url' => 255, 'facebook_url' => 255, 'instagram_url' => 255];
    foreach ($limits as $field => $limit) {
        if (strlen($values[$field]) > $limit) { $error = 'A profile field is too long.'; }
    }
    foreach (['github_url', 'linkedin_url', 'facebook_url', 'instagram_url'] as $field) {
        if ($values[$field] !== '' && (!filter_var($values[$field], FILTER_VALIDATE_URL) || !str_starts_with($values[$field], 'https://'))) { $error = 'Social links must use HTTPS.'; }
    }
    if ($error === '') {
        $columns = $fields;
        $params = array_map(fn($field) => $values[$field], $fields);
        try {
            foreach (['profile_image', 'about_image'] as $image) {
                $path = upload_image($image);
                if ($path !== null) { $columns[] = $image; $params[] = $path; }
            }
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
        if ($error === '') {
            $set = array_map(fn($field) => "$field = ?", $columns);
            database()->prepare('UPDATE site_profile SET ' . implode(', ', $set) . ' WHERE id = 1')->execute($params);
            header('Location: profile.php?saved=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit profile</title><link rel="stylesheet" href="../style.css"></head>
<body><main class="admin-page"><h1>Edit profile</h1><a href="index.php">Dashboard</a>
<?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
<?php foreach ($fields as $field): ?><label><?= escape_html(ucwords(str_replace('_', ' ', $field))) ?><?php if (in_array($field, ['introduction', 'biography'], true)): ?><textarea name="<?= escape_html($field) ?>" required><?= escape_html($values[$field]) ?></textarea><?php else: ?><input type="<?= $field === 'email' ? 'email' : (str_ends_with($field, '_url') ? 'url' : 'text') ?>" name="<?= escape_html($field) ?>" value="<?= escape_html($values[$field]) ?>" <?= in_array($field, ['name', 'email'], true) ? 'required' : '' ?>><?php endif; ?></label><?php endforeach; ?>
<label>Profile image <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp"></label>
<label>About image <input type="file" name="about_image" accept="image/jpeg,image/png,image/gif,image/webp"></label>
<button type="submit">Save profile</button></form></main></body></html>
