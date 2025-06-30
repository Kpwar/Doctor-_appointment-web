<?php
echo "=== Setting up Doctor Appointment Database ===\n\n";

$host = 'localhost';
$username = 'root';
$password = 'root';
$port = 8889;
$socket_path = '/Applications/MAMP/tmp/mysql/mysql.sock';

// Connect without specifying database first
try {
    if (file_exists($socket_path)) {
        $conn = new mysqli($host, $username, $password, '', $port, $socket_path);
    } else {
        $conn = new mysqli($host, $username, $password, '', $port);
    }
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    
    echo "✅ Connected to MySQL successfully\n";
    
    // Create database if it doesn't exist
    $result = $conn->query("SHOW DATABASES LIKE 'doctor_appointment'");
    
    if ($result->num_rows == 0) {
        echo "Creating database 'doctor_appointment'...\n";
        
        if ($conn->query("CREATE DATABASE doctor_appointment")) {
            echo "✅ Database 'doctor_appointment' created successfully\n";
        } else {
            echo "❌ Error creating database: " . $conn->error . "\n";
            exit;
        }
    } else {
        echo "✅ Database 'doctor_appointment' already exists\n";
    }
    
    // Select the database
    $conn->select_db('doctor_appointment');
    echo "✅ Selected database 'doctor_appointment'\n";
    
    // Now run the table creation script
    echo "\n=== Creating Tables ===\n";
    include 'create_tables.php';
    
    echo "\n✅ Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 