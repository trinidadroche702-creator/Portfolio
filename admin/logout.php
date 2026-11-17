<?php

require_once __DIR__ . '/../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use the logout button to end your session.');
}

verify_csrf_token();
session_unset();
session_regenerate_id(true);
header('Location: login.php');
exit;
