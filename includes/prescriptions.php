<?php
// File: includes/prescriptions.php
// Prescription Management System

function createPrescription($conn, $appointment_id, $patient_id, $doctor_id, $medications, $instructions, $dosage, $duration, $refills = 0) {
    $sql = "INSERT INTO prescriptions (appointment_id, patient_id, doctor_id, medications, instructions, dosage, duration, refills, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiissssi", $appointment_id, $patient_id, $doctor_id, $medications, $instructions, $dosage, $duration, $refills);
    
    return $stmt->execute();
}

function getPatientPrescriptions($conn, $patient_id) {
    $sql = "SELECT p.*, d.name as doctor_name, d.specialization, a.appointment_date 
            FROM prescriptions p 
            JOIN doctors d ON p.doctor_id = d.id 
            JOIN appointments a ON p.appointment_id = a.id 
            WHERE p.patient_id = ? 
            ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function getDoctorPrescriptions($conn, $doctor_id) {
    $sql = "SELECT p.*, u.name as patient_name, a.appointment_date 
            FROM prescriptions p 
            JOIN users u ON p.patient_id = u.id 
            JOIN appointments a ON p.appointment_id = a.id 
            WHERE p.doctor_id = ? 
            ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function updatePrescriptionStatus($conn, $prescription_id, $status) {
    $sql = "UPDATE prescriptions SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $prescription_id);
    
    return $stmt->execute();
}

function requestRefill($conn, $prescription_id, $patient_id) {
    $sql = "INSERT INTO prescription_refills (prescription_id, patient_id, status, requested_at) 
            VALUES (?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $prescription_id, $patient_id);
    
    return $stmt->execute();
}

function getPendingRefills($conn, $doctor_id) {
    $sql = "SELECT pr.*, p.medications, p.dosage, u.name as patient_name 
            FROM prescription_refills pr 
            JOIN prescriptions p ON pr.prescription_id = p.id 
            JOIN users u ON pr.patient_id = u.id 
            WHERE p.doctor_id = ? AND pr.status = 'pending' 
            ORDER BY pr.requested_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function approveRefill($conn, $refill_id) {
    $sql = "UPDATE prescription_refills SET status = 'approved', approved_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $refill_id);
    
    return $stmt->execute();
}

function denyRefill($conn, $refill_id, $reason = '') {
    $sql = "UPDATE prescription_refills SET status = 'denied', denied_reason = ?, denied_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $reason, $refill_id);
    
    return $stmt->execute();
}

function getPrescriptionHistory($conn, $patient_id) {
    $sql = "SELECT p.*, d.name as doctor_name, d.specialization, a.appointment_date,
                   (SELECT COUNT(*) FROM prescription_refills pr WHERE pr.prescription_id = p.id) as refill_count
            FROM prescriptions p 
            JOIN doctors d ON p.doctor_id = d.id 
            JOIN appointments a ON p.appointment_id = a.id 
            WHERE p.patient_id = ? 
            ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function createPrescriptionsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS prescriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        appointment_id INT NOT NULL,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        medications TEXT NOT NULL,
        instructions TEXT NOT NULL,
        dosage VARCHAR(255) NOT NULL,
        duration VARCHAR(255) NOT NULL,
        refills INT DEFAULT 0,
        status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (appointment_id) REFERENCES appointments(id),
        FOREIGN KEY (patient_id) REFERENCES users(id),
        FOREIGN KEY (doctor_id) REFERENCES doctors(id)
    )";
    
    $conn->query($sql);
    
    // Create prescription refills table
    $sql_refills = "CREATE TABLE IF NOT EXISTS prescription_refills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prescription_id INT NOT NULL,
        patient_id INT NOT NULL,
        status ENUM('pending', 'approved', 'denied') DEFAULT 'pending',
        requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        approved_at TIMESTAMP NULL,
        denied_at TIMESTAMP NULL,
        denied_reason TEXT,
        FOREIGN KEY (prescription_id) REFERENCES prescriptions(id),
        FOREIGN KEY (patient_id) REFERENCES users(id)
    )";
    
    return $conn->query($sql_refills);
}

function generatePrescriptionPDF($prescription_data) {
    // This function would generate a PDF prescription
    // For demo purposes, we'll return HTML that can be printed
    
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px solid #000;'>
        <div style='text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;'>
            <h1 style='color: #00ffff; text-shadow: 2px 2px 4px rgba(0,255,255,0.3);'>🏥 CyberCare Pro</h1>
            <h2>Digital Prescription</h2>
        </div>
        
        <div style='margin-bottom: 20px;'>
            <strong>Patient:</strong> " . htmlspecialchars($prescription_data['patient_name']) . "<br>
            <strong>Doctor:</strong> Dr. " . htmlspecialchars($prescription_data['doctor_name']) . "<br>
            <strong>Date:</strong> " . date('M d, Y', strtotime($prescription_data['created_at'])) . "<br>
            <strong>Prescription ID:</strong> #" . $prescription_data['id'] . "
        </div>
        
        <div style='background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>
            <h3>Medication Details:</h3>
            <strong>Medications:</strong> " . htmlspecialchars($prescription_data['medications']) . "<br>
            <strong>Dosage:</strong> " . htmlspecialchars($prescription_data['dosage']) . "<br>
            <strong>Duration:</strong> " . htmlspecialchars($prescription_data['duration']) . "<br>
            <strong>Refills:</strong> " . $prescription_data['refills'] . " refills remaining
        </div>
        
        <div style='background: #e9ecef; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>
            <h3>Instructions:</h3>
            " . nl2br(htmlspecialchars($prescription_data['instructions'])) . "
        </div>
        
        <div style='text-align: center; margin-top: 40px;'>
            <div style='border-top: 1px solid #000; padding-top: 20px;'>
                <strong>Dr. " . htmlspecialchars($prescription_data['doctor_name']) . "</strong><br>
                " . htmlspecialchars($prescription_data['specialization']) . "<br>
                CyberCare Pro
            </div>
        </div>
        
        <div style='margin-top: 30px; font-size: 0.9rem; color: #666;'>
            <p><strong>Important:</strong> This prescription is valid for 30 days from the date of issue. 
            Please follow all instructions carefully and contact your doctor if you experience any side effects.</p>
        </div>
    </div>";
    
    return $html;
}
?> 