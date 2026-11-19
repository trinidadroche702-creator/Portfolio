<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(400);
        exit('The form expired. Please go back and try again.');
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }

    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
        session_unset();
        session_regenerate_id(true);
        header('Location: login.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function upload_image(string $field): string|null
{
    if (!isset($_FILES[$field])) { return null; }
    $file = $_FILES[$field];
    if (!is_array($file) || !isset($file['error'], $file['size'], $file['tmp_name'])) { throw new RuntimeException('Invalid image upload.'); }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) { return null; }
    if (!is_int($file['error']) || !is_int($file['size']) || !is_string($file['tmp_name'])) { throw new RuntimeException('Invalid image upload.'); }
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 3 * 1024 * 1024) { throw new RuntimeException('Choose an image smaller than 3 MB.'); }
    $image = getimagesize($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!$image || !isset($extensions[$image['mime']])) { throw new RuntimeException('Upload a valid JPG, PNG, GIF, or WEBP image.'); }
    $directory = dirname(__DIR__) . '/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) { throw new RuntimeException('Could not create the upload directory.'); }
    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$image['mime']];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) { throw new RuntimeException('The image could not be saved.'); }
    return 'uploads/' . $filename;
}
