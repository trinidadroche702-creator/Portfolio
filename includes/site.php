<?php require_once __DIR__ . '/db.php'; function portfolio_profile(): array { $profile = database()->query('SELECT * FROM site_profile WHERE id = 1')->fetch(); if (!$profile) { throw new RuntimeException('The portfolio profile has not been configured.'); } return $profile; }
function portfolio_skills(): array { return database()->query('SELECT * FROM skills ORDER BY sort_order, id')->fetchAll(); }
function portfolio_projects(): array { return database()->query('SELECT * FROM projects ORDER BY sort_order, id')->fetchAll(); }
function portfolio_settings(): array { $settings = database()->query('SELECT * FROM site_settings WHERE id = 1')->fetch(); if (!$settings) { throw new RuntimeException('Website settings have not been configured.'); } return $settings; }
