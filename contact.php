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
$socialLinks = [
    ['field' => 'facebook_url', 'label' => 'Facebook', 'icon' => 'fa-facebook-f'],
    ['field' => 'instagram_url', 'label' => 'Instagram', 'icon' => 'fa-instagram'],
    ['field' => 'github_url', 'label' => 'GitHub', 'icon' => 'fa-github'],
    ['field' => 'linkedin_url', 'label' => 'LinkedIn', 'icon' => 'fa-linkedin-in'],
];
require __DIR__ . '/includes/header.php';
?>
<main class="page-container contact-page">
    <section class="page-heading">
        <p class="section-label">GET IN TOUCH</p>
        <h1>Let's Connect <span class="heading-decoration">♡</span></h1>
        <p class="page-description">Have a question, project idea, or just want to say hello? Send me a message.</p>
    </section>
    <section class="contact-grid">
        <div class="contact-info">
            <h2>Contact Information</h2>
            <p class="contact-intro">I'm always open to connecting with new people, learning new things, and discussing interesting ideas and projects.</p>
            <div class="contact-item">
                <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                <div><h3>Email</h3><p><a href="mailto:<?= escape_html($profile['email']) ?>"><?= escape_html($profile['email']) ?></a></p></div>
            </div>
            <div class="contact-item">
                <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div><h3>Location</h3><p><?= escape_html($profile['location']) ?></p></div>
            </div>
            <div class="contact-item">
                <div class="contact-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div><h3>School</h3><p><?= escape_html($profile['school']) ?></p></div>
            </div>
            <div class="contact-social">
                <h3>Follow Me</h3>
                <div class="social-links">
                    <?php foreach ($socialLinks as $link): ?>
                        <?php if (!empty($profile[$link['field']])): ?>
                            <a href="<?= escape_html($profile[$link['field']]) ?>" aria-label="<?= escape_html($link['label']) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands <?= escape_html($link['icon']) ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="contact-form-container">
            <?php if ($sent): ?><p class="form-notice" role="status">Thank you! Your message has been received.</p><?php endif; ?>
            <?php if ($error !== ''): ?><p class="form-error" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
            <form class="contact-form" method="post" action="contact.html">
                <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <div class="form-honeypot" aria-hidden="true"><label>Leave this field empty<input name="website" tabindex="-1" autocomplete="off"></label></div>
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" maxlength="120" autocomplete="name" placeholder="Enter your name" value="<?= escape_html($fields['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" maxlength="190" autocomplete="email" placeholder="Enter your email" value="<?= escape_html($fields['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" maxlength="190" placeholder="What is this about?" value="<?= escape_html($fields['subject']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" maxlength="5000" placeholder="Write your message here..." required><?= escape_html($fields['message']) ?></textarea>
                </div>
                <button class="primary-btn" type="submit">Send Message <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </section>
    <section class="contact-message"><span>✦</span><p>Thank you for visiting my portfolio!</p><span>♡</span></section>
    <div class="contact-decoration">〰</div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
