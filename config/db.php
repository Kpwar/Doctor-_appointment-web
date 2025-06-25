<?php
$host = 'localhost';
$username = 'root';
$password = 'root'; // MAMP default
$database = 'doctor_appointment';
$port = 3306;

// 👇 MOST IMPORTANT: Set correct socket path for MAMP
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port, $socket);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
