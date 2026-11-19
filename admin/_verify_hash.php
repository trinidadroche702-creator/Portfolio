<?php
// One-off local dev helper: reset the local test admin password via PDO.
$hash = password_hash('TestAdminPass123', PASSWORD_DEFAULT);
$connection = mysqli_connect('127.0.0.1', 'root', '', 'trinidad_portfolio');
$statement = $connection->prepare('UPDATE admins SET password_hash = ?, failed_login_attempts = 0, locked_until = NULL');
$statement->bind_param('s', $hash);
$statement->execute();
$result = $connection->query('SELECT password_hash FROM admins LIMIT 1')->fetch_row();
var_dump(password_verify('TestAdminPass123', $result[0]));
