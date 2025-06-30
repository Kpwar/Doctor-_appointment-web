<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== Comprehensive Error Check for Doctor Appointment System ===\n\n";

// 1. Check PHP version
echo "1. PHP Version Check:\n";
echo "   Current PHP Version: " . phpversion() . "\n";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "   ✅ PHP version is compatible\n";
} else {
    echo "   ❌ PHP version should be 7.4 or higher\n";
}

// 2. Check required extensions
echo "\n2. Required Extensions Check:\n";
$required_extensions = ['mysqli', 'session', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext extension is loaded\n";
    } else {
        echo "   ❌ $ext extension is missing\n";
    }
}

// 3. Check file permissions
echo "\n3. File Permissions Check:\n";
$files_to_check = [
    'config/db.php',
    'includes/functions.php',
    'patient/register.php',
    'doctor/register.php',
    'admin/login.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "   ✅ $file is readable\n";
        } else {
            echo "   ❌ $file is not readable\n";
        }
    } else {
        echo "   ❌ $file does not exist\n";
    }
}

// 4. Database connection test
echo "\n4. Database Connection Test:\n";
try {
    include 'config/db.php';
    echo "   ✅ Database connection successful\n";
    echo "   Server info: " . $conn->server_info . "\n";
    
    // Check if required tables exist
    $tables = ['users', 'doctors', 'appointments', 'admins'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "   ✅ Table '$table' exists\n";
        } else {
            echo "   ❌ Table '$table' is missing\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

// 5. Session functionality test
echo "\n5. Session Functionality Test:\n";
$_SESSION['test'] = 'test_value';
if (isset($_SESSION['test']) && $_SESSION['test'] === 'test_value') {
    echo "   ✅ Sessions are working\n";
} else {
    echo "   ❌ Sessions are not working\n";
}
unset($_SESSION['test']);

// 6. Include files test
echo "\n6. Include Files Test:\n";
try {
    include 'includes/functions.php';
    echo "   ✅ Functions file loaded successfully\n";
    
    // Test a function
    if (function_exists('sanitize_input')) {
        echo "   ✅ sanitize_input function exists\n";
    } else {
        echo "   ❌ sanitize_input function missing\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Functions file error: " . $e->getMessage() . "\n";
}

// 7. Security check
echo "\n7. Security Check:\n";
if (function_exists('mysqli_real_escape_string')) {
    echo "   ✅ mysqli_real_escape_string available\n";
} else {
    echo "   ❌ mysqli_real_escape_string not available\n";
}

if (function_exists('filter_var')) {
    echo "   ✅ filter_var available for email validation\n";
} else {
    echo "   ❌ filter_var not available\n";
}

// 8. URL accessibility test
echo "\n8. URL Accessibility Test:\n";
$base_url = "http://localhost:8080";
$urls_to_test = [
    '/',
    '/patient/register.php',
    '/doctor/register.php',
    '/admin/login.php'
];

foreach ($urls_to_test as $url) {
    $full_url = $base_url . $url;
    $headers = @get_headers($full_url);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "   ✅ $url is accessible\n";
    } else {
        echo "   ❌ $url is not accessible\n";
    }
}

echo "\n=== Error Check Complete ===\n";
echo "\nIf you see any ❌ errors above, please fix them before using the system.\n";
echo "If all checks show ✅, your system is ready to use!\n";
?> 