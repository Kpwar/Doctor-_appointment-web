<?php
// File: includes/sms_notifications.php
// SMS Notification System using Twilio API

// Twilio Configuration (you'll need to get these from Twilio)
define('TWILIO_ACCOUNT_SID', 'your_account_sid_here');
define('TWILIO_AUTH_TOKEN', 'your_auth_token_here');
define('TWILIO_PHONE_NUMBER', '+1234567890'); // Your Twilio phone number

function sendSMS($to_number, $message) {
    // For demo purposes, we'll simulate SMS sending
    // In production, you would use Twilio API
    
    $url = "https://api.twilio.com/2010-04-01/Accounts/" . TWILIO_ACCOUNT_SID . "/Messages.json";
    
    $data = array(
        'From' => TWILIO_PHONE_NUMBER,
        'To' => $to_number,
        'Body' => $message
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, TWILIO_ACCOUNT_SID . ":" . TWILIO_AUTH_TOKEN);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    // For demo purposes, we'll just log the SMS
    error_log("SMS sent to $to_number: $message");
    
    return json_decode($response, true);
}

function sendAppointmentReminderSMS($phone_number, $patient_name, $doctor_name, $appointment_date, $appointment_time) {
    $message = "Hi $patient_name! Reminder: Your appointment with Dr. $doctor_name is tomorrow at " . 
               date('h:i A', strtotime($appointment_time)) . ". Please arrive 10 minutes early. " .
               "Call us if you need to reschedule. - CyberCare Pro";
    
    return sendSMS($phone_number, $message);
}

function sendAppointmentConfirmationSMS($phone_number, $patient_name, $doctor_name, $appointment_date, $appointment_time) {
    $message = "Hi $patient_name! Your appointment with Dr. $doctor_name has been confirmed for " . 
               date('M d, Y', strtotime($appointment_date)) . " at " . date('h:i A', strtotime($appointment_time)) . 
               ". Please arrive 10 minutes early. - CyberCare Pro";
    
    return sendSMS($phone_number, $message);
}

function sendAppointmentCancellationSMS($phone_number, $patient_name, $doctor_name, $appointment_date, $appointment_time) {
    $message = "Hi $patient_name! Your appointment with Dr. $doctor_name on " . 
               date('M d, Y', strtotime($appointment_date)) . " at " . date('h:i A', strtotime($appointment_time)) . 
               " has been cancelled. Please call us to reschedule. - CyberCare Pro";
    
    return sendSMS($phone_number, $message);
}

function sendDoctorNotificationSMS($phone_number, $doctor_name, $patient_name, $appointment_date, $appointment_time) {
    $message = "Dr. $doctor_name, you have a new appointment request from $patient_name for " . 
               date('M d, Y', strtotime($appointment_date)) . " at " . date('h:i A', strtotime($appointment_time)) . 
               ". Please log in to confirm. - CyberCare Pro";
    
    return sendSMS($phone_number, $message);
}

// Demo function for testing (without actual SMS sending)
function sendDemoSMS($phone_number, $message) {
    // This function simulates SMS sending for demo purposes
    $log_entry = date('Y-m-d H:i:s') . " - SMS to $phone_number: $message\n";
    file_put_contents('sms_log.txt', $log_entry, FILE_APPEND);
    
    return array('success' => true, 'message' => 'SMS sent successfully (demo mode)');
}
?> 