<?php
echo "=== Database Connection Test ===\n";
echo "Testing MAMP MySQL connection...\n\n";

// Test 1: Check if MAMP is running
echo "1. Checking MAMP processes:\n";
exec("ps aux | grep -i mamp", $mamp_processes);
foreach ($mamp_processes as $process) {
    if (strpos($process, 'grep') === false) {
        echo "   Found: " . trim($process) . "\n";
    }
}

// Test 2: Check socket files
echo "\n2. Checking socket files:\n";
$socket_paths = [
    '/Applications/MAMP/tmp/mysql/mysql.sock',
    '/tmp/mysql.sock',
    '/var/mysql/mysql.sock'
];

foreach ($socket_paths as $socket) {
    if (file_exists($socket)) {
        echo "   ✓ Found: $socket\n";
    } else {
        echo "   ✗ Missing: $socket\n";
    }
}

// Test 3: Check port 3306
echo "\n3. Checking port 3306:\n";
exec("lsof -i :3306", $port_check);
if (empty($port_check)) {
    echo "   ✗ No service running on port 3306\n";
} else {
    foreach ($port_check as $line) {
        echo "   ✓ " . trim($line) . "\n";
    }
}

// Test 4: Try to connect
echo "\n4. Testing database connection:\n";
$host = 'localhost';
$username = 'root';
$password = 'root';
$port = 8889;

// Try to connect without specifying database first
try {
    $conn = new mysqli($host, $username, $password, '', $port);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Connected to MySQL successfully\n";
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE 'doctor_appointment'");
    
    if ($result->num_rows == 0) {
        echo "Database 'doctor_appointment' does not exist. Creating...\n";
        
        if ($conn->query("CREATE DATABASE doctor_appointment")) {
            echo "✅ Database 'doctor_appointment' created successfully\n";
        } else {
            echo "❌ Error creating database: " . $conn->error . "\n";
        }
    } else {
        echo "✅ Database 'doctor_appointment' already exists\n";
    }
    
    // Select the database
    $conn->select_db('doctor_appointment');
    echo "✅ Selected database 'doctor_appointment'\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Connection error: " . $e->getMessage() . "\n";
}

echo "\n=== End Test ===\n";
?> 