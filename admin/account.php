<?php
require_once __DIR__ . '/../includes/security.php';
require_admin();
$statement = database()->prepare('SELECT * FROM admins WHERE id = ?');
$statement->execute([$_SESSION['admin_id']]);
$admin = $statement->fetch();
if (!$admin) { session_unset(); header('Location: login.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $emailInput = $_POST['email'] ?? ''; $currentInput = $_POST['current_password'] ?? '';
    $newInput = $_POST['new_password'] ?? ''; $confirmInput = $_POST['confirm_password'] ?? '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $current = is_string($currentInput) ? $currentInput : '';
    $new = is_string($newInput) ? $newInput : ''; $confirm = is_string($confirmInput) ? $confirmInput : '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) { $error = 'Enter a valid email address.'; }
    elseif (!password_verify($current, $admin['password_hash'])) { $error = 'Current password is incorrect.'; }
    elseif (strlen($new) < 12 || $new !== $confirm) { $error = 'New passwords must match and have at least 12 characters.'; }
    else {
        $save = database()->prepare('UPDATE admins SET email = ?, password_hash = ? WHERE id = ?');
        $save->execute([$email, password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
        $_SESSION['admin_email'] = $email; session_regenerate_id(true);
        header('Location: account.php?saved=1'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin account</title><link rel="stylesheet" href="../style.css"></head>
<body><main class="admin-page"><h1>Account security</h1><a href="index.php">Dashboard</a>
<?php if (isset($_GET['saved'])): ?><p>Account updated.</p><?php endif; ?><?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
<label>Email <input type="email" name="email" maxlength="190" value="<?= escape_html($admin['email']) ?>" required></label>
<label>Current password <input type="password" name="current_password" required></label>
<label>New password <input type="password" name="new_password" minlength="12" required></label>
<label>Confirm new password <input type="password" name="confirm_password" minlength="12" required></label>
<button type="submit">Update account</button></form></main></body></html>
