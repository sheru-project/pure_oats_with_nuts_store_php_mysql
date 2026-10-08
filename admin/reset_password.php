<?php
if(PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}

require_once __DIR__ . '/../config/db.php';

fwrite(STDOUT, 'Admin username to reset: ');
$input = fgets(STDIN);
if($input === false) {
    fwrite(STDERR, "No username was provided.\n");
    exit(1);
}

$username = trim($input);
if($username === '' || strlen($username) > 50 || preg_match('/[\x00-\x1F\x7F]/', $username)) {
    fwrite(STDERR, "Invalid username.\n");
    exit(1);
}

$lookup = $conn->prepare('SELECT id FROM admins WHERE username = ? LIMIT 1');
$lookup->bind_param('s', $username);
$lookup->execute();
$lookup->bind_result($admin_id);
$admin_exists = $lookup->fetch();
$lookup->close();

if(!$admin_exists) {
    fwrite(STDERR, "No admin account found for that username.\n");
    exit(1);
}

$temporary_password = rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=');
$password_hash = password_hash($temporary_password, PASSWORD_DEFAULT);

$update = $conn->prepare('UPDATE admins SET password = ? WHERE id = ?');
$update->bind_param('si', $password_hash, $admin_id);
$update->execute();
$updated = $update->affected_rows === 1;
$update->close();

if(!$updated) {
    fwrite(STDERR, "The admin password could not be updated.\n");
    exit(1);
}

fwrite(STDOUT, "Password reset successfully.\n");
fwrite(STDOUT, "Temporary password (shown only once): {$temporary_password}\n");
fwrite(STDOUT, "Store this password securely. Run this command again if it is lost.\n");
