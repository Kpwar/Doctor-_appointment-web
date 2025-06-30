<?php
// File: includes/notifications.php
// Notification System for Doctor Appointment System

function sendAppointmentConfirmation($patient_email, $patient_name, $doctor_name, $appointment_date, $appointment_time) {
    $subject = "Appointment Confirmed - CyberCare Pro";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #0a0a0a; color: #fff; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(45deg, #00ffff, #ff00ff); color: #000; padding: 20px; text-align: center; border-radius: 10px; }
            .content { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 20px 0; }
            .highlight { color: #00ffff; font-weight: bold; }
            .footer { text-align: center; color: #ccc; font-size: 0.9rem; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🏥 CyberCare Pro</h1>
                <p>Your appointment has been confirmed!</p>
            </div>
            <div class='content'>
                <p>Dear <span class='highlight'>$patient_name</span>,</p>
                <p>Your appointment with <span class='highlight'>Dr. $doctor_name</span> has been confirmed.</p>
                <p><strong>Date:</strong> " . date('l, F d, Y', strtotime($appointment_date)) . "</p>
                <p><strong>Time:</strong> " . date('h:i A', strtotime($appointment_time)) . "</p>
                <p>Please arrive 10 minutes before your scheduled time.</p>
                <p>If you need to reschedule or cancel, please contact us as soon as possible.</p>
            </div>
            <div class='footer'>
                <p>Thank you for choosing CyberCare Pro</p>
                <p>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: CyberCare Pro <noreply@cybercare.com>" . "\r\n";
    
    return mail($patient_email, $subject, $message, $headers);
}

function sendAppointmentReminder($patient_email, $patient_name, $doctor_name, $appointment_date, $appointment_time) {
    $subject = "Appointment Reminder - CyberCare Pro";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #0a0a0a; color: #fff; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(45deg, #ff00ff, #00ffff); color: #000; padding: 20px; text-align: center; border-radius: 10px; }
            .content { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 20px 0; }
            .highlight { color: #ff00ff; font-weight: bold; }
            .footer { text-align: center; color: #ccc; font-size: 0.9rem; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>⏰ Appointment Reminder</h1>
                <p>Your appointment is tomorrow!</p>
            </div>
            <div class='content'>
                <p>Dear <span class='highlight'>$patient_name</span>,</p>
                <p>This is a friendly reminder about your upcoming appointment with <span class='highlight'>Dr. $doctor_name</span>.</p>
                <p><strong>Date:</strong> " . date('l, F d, Y', strtotime($appointment_date)) . "</p>
                <p><strong>Time:</strong> " . date('h:i A', strtotime($appointment_time)) . "</p>
                <p>Please remember to:</p>
                <ul>
                    <li>Arrive 10 minutes early</li>
                    <li>Bring any relevant medical documents</li>
                    <li>Have your insurance information ready</li>
                </ul>
                <p>If you need to reschedule, please contact us immediately.</p>
            </div>
            <div class='footer'>
                <p>Thank you for choosing CyberCare Pro</p>
                <p>This is an automated reminder, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: CyberCare Pro <noreply@cybercare.com>" . "\r\n";
    
    return mail($patient_email, $subject, $message, $headers);
}

function sendAppointmentCancellation($patient_email, $patient_name, $doctor_name, $appointment_date, $appointment_time) {
    $subject = "Appointment Cancelled - CyberCare Pro";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #0a0a0a; color: #fff; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(45deg, #dc3545, #ff4757); color: #fff; padding: 20px; text-align: center; border-radius: 10px; }
            .content { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 20px 0; }
            .highlight { color: #ff4757; font-weight: bold; }
            .footer { text-align: center; color: #ccc; font-size: 0.9rem; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>❌ Appointment Cancelled</h1>
                <p>Your appointment has been cancelled</p>
            </div>
            <div class='content'>
                <p>Dear <span class='highlight'>$patient_name</span>,</p>
                <p>Your appointment with <span class='highlight'>Dr. $doctor_name</span> has been cancelled.</p>
                <p><strong>Original Date:</strong> " . date('l, F d, Y', strtotime($appointment_date)) . "</p>
                <p><strong>Original Time:</strong> " . date('h:i A', strtotime($appointment_time)) . "</p>
                <p>To reschedule your appointment, please visit our portal or contact us directly.</p>
                <p>We apologize for any inconvenience this may have caused.</p>
            </div>
            <div class='footer'>
                <p>Thank you for choosing CyberCare Pro</p>
                <p>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: CyberCare Pro <noreply@cybercare.com>" . "\r\n";
    
    return mail($patient_email, $subject, $message, $headers);
}

function sendDoctorNotification($doctor_email, $doctor_name, $patient_name, $appointment_date, $appointment_time) {
    $subject = "New Appointment Request - CyberCare Pro";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #0a0a0a; color: #fff; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(45deg, #28a745, #20c997); color: #000; padding: 20px; text-align: center; border-radius: 10px; }
            .content { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 20px 0; }
            .highlight { color: #20c997; font-weight: bold; }
            .footer { text-align: center; color: #ccc; font-size: 0.9rem; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>📋 New Appointment Request</h1>
                <p>You have a new appointment request</p>
            </div>
            <div class='content'>
                <p>Dear <span class='highlight'>Dr. $doctor_name</span>,</p>
                <p>You have received a new appointment request from <span class='highlight'>$patient_name</span>.</p>
                <p><strong>Date:</strong> " . date('l, F d, Y', strtotime($appointment_date)) . "</p>
                <p><strong>Time:</strong> " . date('h:i A', strtotime($appointment_time)) . "</p>
                <p>Please log into your dashboard to review and confirm this appointment.</p>
            </div>
            <div class='footer'>
                <p>CyberCare Pro - Doctor Portal</p>
                <p>This is an automated notification, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: CyberCare Pro <noreply@cybercare.com>" . "\r\n";
    
    return mail($doctor_email, $subject, $message, $headers);
}

function createNotification($conn, $user_id, $user_type, $title, $message, $type = 'info') {
    $sql = "INSERT INTO notifications (user_id, user_type, title, message, type, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $user_type, $title, $message, $type);
    return $stmt->execute();
}

function getNotifications($conn, $user_id, $user_type, $limit = 10) {
    $sql = "SELECT * FROM notifications 
            WHERE user_id = ? AND user_type = ? 
            ORDER BY created_at DESC 
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $user_id, $user_type, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

function markNotificationAsRead($conn, $notification_id) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notification_id);
    return $stmt->execute();
}

function getUnreadNotificationCount($conn, $user_id, $user_type) {
    $sql = "SELECT COUNT(*) as count FROM notifications 
            WHERE user_id = ? AND user_type = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'];
}

// Create notifications table if it doesn't exist
function createNotificationsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('patient', 'doctor', 'admin') NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    return $conn->query($sql);
}
?> 