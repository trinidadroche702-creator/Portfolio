<?php
require_once __DIR__ . '/../includes/security.php';
require_admin();
$modules = [
    'skills' => ['Skills', ['name', 'category', 'icon_class', 'level', 'sort_order']],
    'projects' => ['Projects', ['title', 'description', 'image_path', 'technologies', 'project_url', 'sort_order']],
    'education' => ['Education', ['institution', 'qualification', 'dates', 'description', 'sort_order']],
    'experience' => ['Experience', ['title', 'organization', 'dates', 'description', 'sort_order']],
    'achievements' => ['Achievements', ['title', 'organization', 'achieved_on', 'description', 'image_path', 'sort_order']],
];
$type = $_GET['type'] ?? '';
if (!is_string($type) || !isset($modules[$type])) { http_response_code(404); exit('Section not found.'); }
[$title, $fields] = $modules[$type];
$error = '';
$idInput = $_GET['id'] ?? '';
$idValue = is_string($idInput) ? filter_var($idInput, FILTER_VALIDATE_INT) : false;
$id = $idInput === '' ? 0 : ($idValue === false ? 0 : (int) $idValue);
if ($idInput !== '' && !$id) { http_response_code(400); exit('Invalid record ID.'); }
$record = $id ? database()->prepare("SELECT * FROM $type WHERE id = ?") : null;
if ($record) { $record->execute([$id]); $record = $record->fetch(); }
if ($id && !$record) { http_response_code(404); exit('Record not found.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'] ?? '';
    $postIdInput = $_POST['id'] ?? '';
    $postIdValue = is_string($postIdInput) ? filter_var($postIdInput, FILTER_VALIDATE_INT) : false;
    $postId = $postIdInput === '' ? 0 : ($postIdValue === false ? 0 : (int) $postIdValue);
    if (!is_string($postIdInput) || ($postIdInput !== '' && $postIdValue === false)) { http_response_code(400); exit('Invalid record ID.'); }
    if ($action === 'delete' && $postId) { database()->prepare("DELETE FROM $type WHERE id = ?")->execute([$postId]); header("Location: manage.php?type=$type"); exit; }
    if ($action !== 'save') { http_response_code(400); exit('Invalid action.'); }
    $values = [];
    foreach ($fields as $field) { if ($field !== 'image_path') { $input = $_POST[$field] ?? ''; $values[$field] = is_string($input) ? trim($input) : ''; } }
    if (($values[$fields[0]] ?? '') === '') { $error = 'Complete the required field.'; }
    $limits = ['name' => 100, 'category' => 100, 'icon_class' => 100, 'title' => 190, 'institution' => 190, 'qualification' => 190, 'dates' => 100, 'organization' => 190, 'achieved_on' => 100, 'description' => 5000, 'technologies' => 500, 'project_url' => 500];
    foreach ($values as $field => $value) {
        if (isset($limits[$field]) && strlen($value) > $limits[$field]) { $error = 'A field is too long.'; }
    }
    foreach (['level', 'sort_order'] as $field) {
        if (isset($values[$field]) && $values[$field] === '') { $values[$field] = '0'; }
        if (isset($values[$field]) && filter_var($values[$field], FILTER_VALIDATE_INT) === false) { $error = 'Enter a whole number for ' . str_replace('_', ' ', $field) . '.'; }
    }
    if (isset($values['level']) && (int) $values['level'] > 100) { $error = 'Skill level cannot be above 100.'; }
    if (isset($values['project_url']) && $values['project_url'] !== '' && (!filter_var($values['project_url'], FILTER_VALIDATE_URL) || !str_starts_with($values['project_url'], 'https://'))) { $error = 'Project links must use HTTPS.'; }
    if ($error === '') {
        $existing = $postId && in_array('image_path', $fields, true) ? database()->prepare("SELECT image_path FROM $type WHERE id = ?") : null;
        if ($existing) { $existing->execute([$postId]); $oldImage = $existing->fetchColumn(); } else { $oldImage = ''; }
        if (in_array('image_path', $fields, true)) {
            try { $values['image_path'] = upload_image('image') ?? ($oldImage ?: ''); }
            catch (RuntimeException $exception) { $error = $exception->getMessage(); }
        }
        if ($error === '') {
            $columns = $fields;
            $params = array_map(fn($field) => $values[$field] ?? '', $columns);
            if ($postId) {
                $set = array_map(fn($field) => "$field = ?", $columns);
                $params[] = $postId;
                database()->prepare("UPDATE $type SET " . implode(', ', $set) . ' WHERE id = ?')->execute($params);
            } else {
                database()->prepare("INSERT INTO $type (" . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($columns), '?')) . ')')->execute($params);
            }
            header("Location: manage.php?type=$type");
            exit;
        }
    }
}
$rows = database()->query("SELECT * FROM $type ORDER BY sort_order, id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= escape_html($title) ?></title><link rel="stylesheet" href="../style.css"></head>
<body><main class="admin-page"><h1><?= escape_html($title) ?></h1><a href="index.php">Dashboard</a>
<?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data" action="manage.php?type=<?= escape_html($type) ?>"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>">
<?php foreach ($fields as $field): ?><label><?= escape_html(ucwords(str_replace('_', ' ', $field))) ?><?php if ($field === 'image_path'): ?><input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"><?php elseif ($field === 'description'): ?><textarea name="<?= escape_html($field) ?>"><?= escape_html($record[$field] ?? '') ?></textarea><?php else: ?><input type="<?= in_array($field, ['level', 'sort_order'], true) ? 'number' : 'text' ?>" name="<?= escape_html($field) ?>" value="<?= escape_html($record[$field] ?? '') ?>" <?= in_array($field, ['name', 'title', 'institution'], true) ? 'required' : '' ?>><?php endif; ?></label><?php endforeach; ?>
<button name="action" value="save">Save</button></form>
<?php foreach ($rows as $row): ?><article><a href="manage.php?type=<?= escape_html($type) ?>&amp;id=<?= (int) $row['id'] ?>"><?= escape_html($row[$fields[0]]) ?></a><form method="post" action="manage.php?type=<?= escape_html($type) ?>"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button name="action" value="delete">Delete</button></form></article><?php endforeach; ?>
</main></body></html>
