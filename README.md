# Roche Trinidad Portfolio

The existing portfolio is being upgraded to a PHP and MySQL application while retaining its current visual design and page paths.

## Requirements

- PHP 8.0 or later with PDO MySQL enabled
- MySQL 8.0 or MariaDB 10.4 or later
- Apache with PHP enabled (XAMPP is suitable)

## Local setup

1. Copy `config.example.php` to `config.local.php` and set your database credentials. `config.local.php` is intentionally excluded from Git.
2. Import `database/schema.sql` in phpMyAdmin or the MySQL command line. It creates the `trinidad_portfolio` database and inserts the current portfolio content as starter data.
3. Keep the existing project folder and start Apache and MySQL from the XAMPP control panel.
4. Open the portfolio at its existing local URL. The existing `.html` page addresses continue to work.
5. Visit `/admin/setup.php` once to create the first administrator. Use a strong password of at least 12 characters.
6. Sign in at `/admin/login.php`. The setup page closes automatically after the first administrator is created.
7. Use the dashboard to edit your profile and settings, manage skills, projects, education, experience, achievements, and contact messages.

## Database and security

- The SQL file creates the tables and inserts starter portfolio content.
- Use a MySQL account with access only to this database on a deployed site.
- Keep `config.local.php` private and change the default XAMPP database credentials before deployment.
- Contact messages are validated and saved in MySQL. Admin changes use CSRF tokens and prepared statements.
- Uploaded images are limited to 3 MB and checked as image files. PHP execution is blocked in `uploads/`.
- Deploy over HTTPS and confirm Apache allows the project `.htaccess` rules before publishing.

Do not commit database credentials, administrator passwords, or real visitor messages.
