<?php
require_once __DIR__ . '/../includes/security.php';
if ((int) database()->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0) { http_response_code(404); exit('Setup is already complete.'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $emailInput = $_POST['email'] ?? ''; $passwordInput = $_POST['password'] ?? ''; $confirmInput = $_POST['confirm_password'] ?? '';
    $email = is_string($emailInput) ? trim($emailInput) : ''; $password = is_string($passwordInput) ? $passwordInput : ''; $confirm = is_string($confirmInput) ? $confirmInput : '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) { $error = 'Enter a valid email address.'; }
    elseif (strlen($password) < 12 || $password !== $confirm) { $error = 'Use a matching password with at least 12 characters.'; }
    else {
        $statement = database()->prepare('INSERT INTO admins (email, password_hash) VALUES (?, ?)');
        $statement->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) database()->lastInsertId(); $_SESSION['admin_email'] = $email;
        header('Location: index.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin setup</title><link rel="stylesheet" href="../style.css"></head>
<body><main class="admin-login"><h1>Create the administrator account</h1><p>This page closes after the first account is created.</p>
<?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
<label>Email <input type="email" name="email" maxlength="190" required></label>
<label>Password <input type="password" name="password" minlength="12" required></label>
<label>Confirm password <input type="password" name="confirm_password" minlength="12" required></label>
<button type="submit">Create account</button></form></main></body></html>
