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
    <title>Administrator login | Portfolio Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body class="admin-body">
    <div class="admin-login-wrap">
        <div class="admin-login-card">
            <div class="logo-icon"><i class="fa-solid fa-lock"></i></div>
            <h1>Administrator Login</h1>
            <p class="sub">Sign in to manage your portfolio content.</p>
            <?php if ($error !== ''): ?><p class="alert alert-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <label>Email
                    <input type="email" name="email" maxlength="190" autocomplete="username" required>
                </label>
                <label>Password
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <button class="btn" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Log in</button>
            </form>
        </div>
    </div>
</body>

</html>