# Roche Trinidad Portfolio

The existing portfolio is being upgraded to a PHP and MySQL application while retaining its current visual design and page paths.

## Requirements

- PHP 8.1 or later with PDO MySQL enabled
- MySQL 8.0 or MariaDB 10.4 or later
- Apache with PHP enabled (XAMPP is suitable)

## Local setup

1. Copy `config.example.php` to `config.local.php` and set your database credentials. `config.local.php` is intentionally excluded from Git.
2. Import `database/schema.sql` in phpMyAdmin or the MySQL command line. It creates the `trinidad_portfolio` database and inserts the current portfolio content as starter data.
3. Keep the existing project folder and start Apache and MySQL from the XAMPP control panel.
4. Open the portfolio at its existing local URL. Administrator setup instructions will be added with the authentication feature.

Do not commit database credentials, administrator passwords, or real visitor messages.
