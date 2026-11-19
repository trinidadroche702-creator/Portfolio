<?php
require_once __DIR__ . '/includes/security.php';
$activePage = 'Contact'; $pageTitle = 'Contact | Roche Trinidad';
$fields = ['name' => '', 'email' => '', 'subject' => '', 'message' => '']; $error = ''; $sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    foreach ($fields as $key => $value) { $input = $_POST[$key] ?? ''; $fields[$key] = is_string($input) ? trim($input) : ''; }
    if (empty($_POST['website'])) {
        if (strlen($fields['name']) > 120 || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL) || strlen($fields['email']) > 190 || strlen($fields['subject']) > 190 || strlen($fields['message']) > 5000 || in_array('', $fields, true)) { $error = 'Please fill in all fields with valid information.'; }
        else { database()->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)')->execute(array_values($fields)); $sent = true; }
    } else { $sent = true; }
}
require __DIR__ . '/includes/header.php';
?>
<main class="page-container contact-page">
<section class="page-heading"><p class="section-label">GET IN TOUCH</p><h1>Let's Connect</h1></section>
<section class="contact-grid"><div class="contact-info"><h2>Contact Information</h2><p><?= escape_html($profile['email']) ?></p><p><?= escape_html($profile['location']) ?></p><p><?= escape_html($profile['school']) ?></p></div>
<div class="contact-form-container"><?php if ($sent): ?><p>Thank you! Your message has been received.</p><?php endif; ?><?php if ($error !== ''): ?><p class="form-error"><?= escape_html($error) ?></p><?php endif; ?>
<form class="contact-form" method="post" action="contact.html"><input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
<label style="position:absolute;left:-10000px">Leave empty<input name="website" tabindex="-1" autocomplete="off"></label>
<label>Your name<input name="name" maxlength="120" value="<?= escape_html($fields['name']) ?>" required></label><label>Your email<input type="email" name="email" maxlength="190" value="<?= escape_html($fields['email']) ?>" required></label>
<label>Subject<input name="subject" maxlength="190" value="<?= escape_html($fields['subject']) ?>" required></label><label>Message<textarea name="message" maxlength="5000" required><?= escape_html($fields['message']) ?></textarea></label>
<button class="primary-btn" type="submit">Send message</button></form></div></section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
