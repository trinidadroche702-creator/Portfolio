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
$adminActive = $type;
$labels = [
    'skills' => ['Skills', 'fa-lightbulb'],
    'projects' => ['Projects', 'fa-diagram-project'],
    'education' => ['Education', 'fa-graduation-cap'],
    'experience' => ['Experience', 'fa-briefcase'],
    'achievements' => ['Achievements', 'fa-award'],
];
$icon = $labels[$type][1];
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= escape_html($title) ?> | Portfolio Admin</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="admin.css"></head>
<body class="admin-body">
<?php require __DIR__ . '/_layout.php'; ?>
<div class="admin-topbar">
    <h1>Manage <span><?= escape_html($title) ?></span></h1>
    <span class="who"><i class="fa-solid <?= $icon ?>"></i> <?= count($rows) ?> record<?= count($rows) === 1 ? '' : 's' ?></span>
</div>
<?php if ($error !== ''): ?><p class="alert alert-error"><?= escape_html($error) ?></p><?php endif; ?>
<div class="admin-card">
    <h2><i class="fa-solid <?= $id ? 'fa-pen' : 'fa-plus' ?>"></i> <?= $id ? 'Edit record' : 'Add new record' ?></h2>
    <form class="admin-form" method="post" enctype="multipart/form-data" action="manage.php?type=<?= escape_html($type) ?><?= $id ? '&amp;id=' . (int) $id : '' ?>"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>">
    <?php foreach ($fields as $field): ?><?php if ($field === 'image_path'): ?>
        <fieldset>
            <legend>Image</legend>
            <?php if (!empty($record['image_path'])): ?><img class="thumb" src="../<?= escape_html($record['image_path']) ?>" alt="Current image" style="width:120px;border-radius:10px;display:block;margin-bottom:10px"><?php endif; ?>
            <label>Upload image (JPG, PNG, GIF or WEBP)<input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"></label>
        </fieldset>
    <?php elseif ($field === 'description'): ?>
        <label>Description<textarea name="<?= escape_html($field) ?>" rows="5"><?= escape_html($record[$field] ?? '') ?></textarea></label>
    <?php else: ?>
        <label><?= escape_html(ucwords(str_replace('_', ' ', $field))) ?><input type="<?= in_array($field, ['level', 'sort_order'], true) ? 'number' : 'text' ?>" name="<?= escape_html($field) ?>" value="<?= escape_html($record[$field] ?? '') ?>" <?= in_array($field, ['name', 'title', 'institution'], true) ? 'required' : '' ?>></label>
    <?php endif; ?><?php endforeach; ?>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn" name="action" value="save"><i class="fa-solid fa-floppy-disk"></i> <?= $id ? 'Save changes' : 'Add record' ?></button>
        <?php if ($id): ?><a class="btn btn-ghost" href="manage.php?type=<?= escape_html($type) ?>"><i class="fa-solid fa-plus"></i> New instead</a><?php endif; ?>
    </div>
    </form>
</div>
<div class="admin-table-wrap">
<table class="admin-table">
    <caption>Existing <?= strtolower($title) ?></caption>
    <thead><tr><th>#</th><th><?= escape_html(ucwords(str_replace('_', ' ', $fields[0]))) ?></th><th>Actions</th></tr></thead>
    <tbody>
    <?php if ($rows === []): ?><tr><td colspan="3" style="color:var(--adm-muted)">Nothing here yet — add your first record above.</td></tr><?php endif; ?>
    <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= (int) $row['id'] ?></td>
            <td>
                <?php if (!empty($row['image_path'])): ?><img class="thumb" src="../<?= escape_html($row['image_path']) ?>" alt="" style="margin-right:10px"><?php endif; ?>
                <?= escape_html((string) $row[$fields[0]]) ?>
            </td>
            <td class="actions">
                <a class="btn btn-ghost btn-small" href="manage.php?type=<?= escape_html($type) ?>&amp;id=<?= (int) $row['id'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                <form method="post" action="manage.php?type=<?= escape_html($type) ?>" onsubmit="return confirm('Delete this record?');"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="btn btn-danger btn-small" name="action" value="delete"><i class="fa-solid fa-trash"></i> Delete</button></form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</main></body></html>
