<?php
// Simple test to check what's in the login view
$loginFile = '/var/www/html/resources/views/login.blade.php';

echo "=== LOGIN VIEW TEST ===\n";
echo "File exists: " . (file_exists($loginFile) ? "YES" : "NO") . "\n";
echo "File size: " . filesize($loginFile) . " bytes\n";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($loginFile)) . "\n\n";

$content = file_get_contents($loginFile);

echo "Contains 'showSignupModal': " . (strpos($content, 'showSignupModal') !== false ? "YES" : "NO") . "\n";
echo "Contains 'signupModal': " . (strpos($content, 'signupModal') !== false ? "YES" : "NO") . "\n";
echo "Contains 'FORCE RECOMPILE': " . (strpos($content, 'FORCE RECOMPILE') !== false ? "YES" : "NO") . "\n";
echo "Contains 'Version 3.0': " . (strpos($content, 'Version 3.0') !== false ? "YES" : "NO") . "\n\n";

// Show the Sign up link line
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'Sign up') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}

echo "\n=== FIRST 10 LINES ===\n";
for ($i = 0; $i < 10 && $i < count($lines); $i++) {
    echo ($i + 1) . ": " . $lines[$i] . "\n";
}
