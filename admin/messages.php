<?php
require_once __DIR__ . '/../includes/security.php';
require_admin();
$adminActive = 'messages';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $idInput = $_POST['id'] ?? '';
    $id = is_string($idInput) ? filter_var($idInput, FILTER_VALIDATE_INT) : false;
    $action = $_POST['action'] ?? '';
    if (!$id) {
        http_response_code(400);
        exit('Invalid message.');
    }
    if ($action === 'delete') {
        database()->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
    } elseif (in_array($action, ['read', 'unread'], true)) {
        database()->prepare('UPDATE contact_messages SET is_read = ? WHERE id = ?')->execute([$action === 'read' ? 1 : 0, $id]);
    }
    header('Location: messages.php');
    exit;
}
$messages = database()->query('SELECT * FROM contact_messages ORDER BY is_read, created_at DESC, id DESC LIMIT 100')->fetchAll();
$unread = count(array_filter($messages, fn($m) => !(int) $m['is_read']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Messages | Portfolio Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body class="admin-body">
    <?php require __DIR__ . '/_layout.php'; ?>
    <div class="admin-topbar">
        <h1>Contact <span>Messages</span></h1>
        <span class="who"><span class="badge badge-unread"><?= $unread ?> unread</span> <span class="badge badge-read"><?= count($messages) - $unread ?> read</span></span>
    </div>
    <?php if ($messages === []): ?>
        <div class="admin-card">
            <h2><i class="fa-solid fa-inbox"></i> Inbox empty</h2>
            <p>No messages have been received yet.</p>
        </div>
    <?php endif; ?>
    <?php foreach ($messages as $message): ?>
        <article class="admin-card">
            <h2><i class="fa-<?= (int) $message['is_read'] ? 'solid fa-envelope-open' : 'regular fa-envelope' ?>"></i>
                <?= htmlspecialchars($message['subject']) ?>
                <span class="badge <?= (int) $message['is_read'] ? 'badge-read' : 'badge-unread' ?>"><?= (int) $message['is_read'] ? 'Read' : 'Unread' ?></span>
            </h2>
            <p style="color:var(--adm-muted);margin-bottom:10px">
                <i class="fa-solid fa-user"></i> <?= htmlspecialchars($message['name']) ?>
                &nbsp;·&nbsp; <i class="fa-solid fa-at"></i> <?= htmlspecialchars($message['email']) ?>
                &nbsp;·&nbsp; <i class="fa-solid fa-clock"></i> <?= htmlspecialchars($message['created_at']) ?>
            </p>
            <p style="margin-bottom:16px"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
            <div class="actions" style="display:flex;gap:10px;flex-wrap:wrap">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                    <button class="btn btn-ghost btn-small" name="action" value="<?= (int) $message['is_read'] ? 'unread' : 'read' ?>">
                        <i class="fa-<?= (int) $message['is_read'] ? 'regular fa-envelope' : 'solid fa-envelope-open' ?>"></i>
                        <?= (int) $message['is_read'] ? 'Mark unread' : 'Mark read' ?>
                    </button>
                    <button class="btn btn-danger btn-small" name="action" value="delete" onclick="return confirm('Delete this message?');"><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
    </main>
</body>

</html>