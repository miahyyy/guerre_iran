<?php
// Create hashed password for admin
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "Hashed password for 'admin123': " . $hashedPassword . "\n";

// Test the hash
if (password_verify('admin123', $hashedPassword)) {
    echo "Hash verification: PASSED\n";
} else {
    echo "Hash verification: FAILED\n";
}
?>
