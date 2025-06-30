<?php
$host = 'localhost';
$username = 'root';
$password = 'root'; // MAMP default
$database = 'doctor_appointment';
$port = 8889; // MAMP uses port 8889 for MySQL

// MAMP socket path
$socket_path = '/Applications/MAMP/tmp/mysql/mysql.sock';

$conn = null;
$connected = false;

// First try with socket
if (file_exists($socket_path)) {
    try {
        $conn = new mysqli($host, $username, $password, $database, $port, $socket_path);
        if (!$conn->connect_error) {
            $connected = true;
        }
    } catch (Exception $e) {
        // Socket connection failed, try TCP
    }
}

// If socket connection failed, try TCP connection
if (!$connected) {
    try {
        $conn = new mysqli($host, $username, $password, $database, $port);
        if (!$conn->connect_error) {
            $connected = true;
        }
    } catch (Exception $e) {
        // Connection failed
    }
}

// Check connection
if (!$connected || $conn->connect_error) {
    die("Connection failed: " . ($conn ? $conn->connect_error : "Unable to establish database connection. Please ensure MAMP is running and MySQL server is started."));
}

// Set charset to prevent encoding issues
$conn->set_charset("utf8mb4");
?>
