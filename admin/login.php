<?php

require_once __DIR__ . '/../includes/security.php';

if ((int) database()->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) {
    header('Location: setup.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $emailInput = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';
    $statement = database()->prepare('SELECT * FROM admins WHERE email = ?');
    $statement->execute([$email]);
    $admin = $statement->fetch();
    $locked = $admin && $admin['locked_until'] && strtotime($admin['locked_until']) > time();

    if ($admin && !$locked && password_verify($password, $admin['password_hash'])) {
        database()->prepare('UPDATE admins SET failed_login_attempts = 0, locked_until = NULL, last_login_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$admin['id']]);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['last_activity'] = time();
        header('Location: index.php');
        exit;
    }

    if ($admin && !$locked) {
        $attempts = (int) $admin['failed_login_attempts'] + 1;
        if ($attempts >= 5) {
            database()->prepare('UPDATE admins SET failed_login_attempts = 0, locked_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE) WHERE id = ?')->execute([$admin['id']]);
        } else {
            database()->prepare('UPDATE admins SET failed_login_attempts = ? WHERE id = ?')->execute([$attempts, $admin['id']]);
        }
    }
    $error = 'Email or password is incorrect. Try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrator login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<main class="admin-login">
    <h1>Administrator login</h1>
    <?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <label>Email <input type="email" name="email" maxlength="190" autocomplete="username" required></label>
        <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
        <button class="primary-btn" type="submit">Log in</button>
    </form>
</main>
</body>
</html>
