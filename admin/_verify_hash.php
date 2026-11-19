<?php
// Generate a fresh hash for the local test password and verify it round-trips.
$hash = password_hash('TestAdminPass123', PASSWORD_DEFAULT);
var_dump(password_verify('TestAdminPass123', $hash));
echo $hash, PHP_EOL;
