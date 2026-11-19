<?php
require_once __DIR__ . '/../includes/security.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $idInput = $_POST['id'] ?? '';
    $id = is_string($idInput) ? filter_var($idInput, FILTER_VALIDATE_INT) : false;
    $action = $_POST['action'] ?? '';
    if (!$id) { http_response_code(400); exit('Invalid message.'); }
    if ($action === 'delete') { database()->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]); }
    elseif (in_array($action, ['read', 'unread'], true)) { database()->prepare('UPDATE contact_messages SET is_read = ? WHERE id = ?')->execute([$action === 'read' ? 1 : 0, $id]); }
    header('Location: messages.php');
    exit;
}
$messages = database()->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 100')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Messages</title><link rel="stylesheet" href="../style.css"></head>
<body><main class="admin-page"><h1>Contact messages</h1><a href="index.php">Dashboard</a>
<?php foreach ($messages as $message): ?>
<article><h2><?= escape_html($message['subject']) ?></h2><p><?= escape_html($message['name']) ?> · <?= escape_html($message['email']) ?></p><p><?= nl2br(escape_html($message['message'])) ?></p><p><?= escape_html($message['created_at']) ?> · <?= $message['is_read'] ? 'Read' : 'Unread' ?></p>
<form method="post"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $message['id'] ?>"><button name="action" value="<?= $message['is_read'] ? 'unread' : 'read' ?>"><?= $message['is_read'] ? 'Mark unread' : 'Mark read' ?></button><button name="action" value="delete">Delete</button></form></article>
<?php endforeach; ?>
</main></body></html>
