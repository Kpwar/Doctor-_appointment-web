<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security and validation functions

// Sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($conn) {
        $data = mysqli_real_escape_string($conn, $data);
    }
    return $data;
}

// Validate email format
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Check if user is doctor
function is_doctor() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'doctor';
}

// Check if user is patient
function is_patient() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'patient';
}

// Redirect with message
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit();
}

// Display message
function display_message() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'success';
        $message = $_SESSION['message'];
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        $class = ($type === 'error') ? 'error' : 'success';
        return "<div class='$class'>$message</div>";
    }
    return '';
}

// Validate appointment date
function validate_appointment_date($date) {
    $appointment_date = strtotime($date);
    $today = strtotime(date('Y-m-d'));
    
    if ($appointment_date < $today) {
        return false; // Past date
    }
    
    return true;
}

// Check if appointment slot is available
function is_slot_available($doctor_id, $date, $time) {
    global $conn;
    
    $sql = "SELECT * FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $doctor_id, $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows === 0;
}

// Get available time slots
function get_available_slots($doctor_id, $date) {
    $slots = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'
    ];
    
    $available = [];
    foreach ($slots as $slot) {
        if (is_slot_available($doctor_id, $date, $slot)) {
            $available[] = $slot;
        }
    }
    
    return $available;
}

// Error handler
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error_message = "Error [$errno]: $errstr in $errfile on line $errline";
    
    // Log error
    error_log($error_message);
    
    // Display user-friendly message in production
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        echo "An error occurred. Please try again later.";
    } else {
        echo $error_message;
    }
    
    return true;
}

// Set custom error handler
set_error_handler("custom_error_handler");
?> 