<?php
// File: includes/health_records.php
// Health Records Management System

function createHealthRecord($conn, $patient_id, $doctor_id, $record_type, $title, $description, $file_path = null, $appointment_id = null) {
    $sql = "INSERT INTO health_records (patient_id, doctor_id, record_type, title, description, file_path, appointment_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssi", $patient_id, $doctor_id, $record_type, $title, $description, $file_path, $appointment_id);
    
    return $stmt->execute();
}

function getPatientHealthRecords($conn, $patient_id, $record_type = null) {
    $sql = "SELECT hr.*, d.name as doctor_name, d.specialization 
            FROM health_records hr 
            JOIN doctors d ON hr.doctor_id = d.id 
            WHERE hr.patient_id = ?";
    
    if ($record_type) {
        $sql .= " AND hr.record_type = ?";
    }
    
    $sql .= " ORDER BY hr.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($record_type) {
        $stmt->bind_param("is", $patient_id, $record_type);
    } else {
        $stmt->bind_param("i", $patient_id);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

function getDoctorHealthRecords($conn, $doctor_id, $patient_id = null) {
    $sql = "SELECT hr.*, u.name as patient_name 
            FROM health_records hr 
            JOIN users u ON hr.patient_id = u.id 
            WHERE hr.doctor_id = ?";
    
    if ($patient_id) {
        $sql .= " AND hr.patient_id = ?";
    }
    
    $sql .= " ORDER BY hr.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($patient_id) {
        $stmt->bind_param("ii", $doctor_id, $patient_id);
    } else {
        $stmt->bind_param("i", $doctor_id);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

function updateHealthRecord($conn, $record_id, $title, $description) {
    $sql = "UPDATE health_records SET title = ?, description = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $description, $record_id);
    
    return $stmt->execute();
}

function deleteHealthRecord($conn, $record_id) {
    $sql = "DELETE FROM health_records WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $record_id);
    
    return $stmt->execute();
}

function uploadHealthRecordFile($file, $patient_id) {
    $upload_dir = "uploads/health_records/";
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    
    $file_name = "patient_" . $patient_id . "_" . time() . "." . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_path;
    }
    
    return false;
}

function getHealthRecordTypes() {
    return [
        'lab_results' => 'Lab Results',
        'imaging' => 'Imaging (X-Ray, MRI, CT)',
        'prescription' => 'Prescription',
        'vaccination' => 'Vaccination Record',
        'surgery' => 'Surgery Report',
        'consultation' => 'Consultation Notes',
        'allergy' => 'Allergy Information',
        'medication' => 'Medication History',
        'family_history' => 'Family History',
        'lifestyle' => 'Lifestyle Information',
        'other' => 'Other'
    ];
}

function createHealthRecordsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS health_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        record_type ENUM('lab_results', 'imaging', 'prescription', 'vaccination', 'surgery', 'consultation', 'allergy', 'medication', 'family_history', 'lifestyle', 'other') NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(500),
        appointment_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES users(id),
        FOREIGN KEY (doctor_id) REFERENCES doctors(id),
        FOREIGN KEY (appointment_id) REFERENCES appointments(id)
    )";
    
    return $conn->query($sql);
}

function getHealthRecordStats($conn, $patient_id) {
    $sql = "SELECT 
                record_type,
                COUNT(*) as count
            FROM health_records 
            WHERE patient_id = ? 
            GROUP BY record_type";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function searchHealthRecords($conn, $patient_id, $search_term) {
    $sql = "SELECT hr.*, d.name as doctor_name, d.specialization 
            FROM health_records hr 
            JOIN doctors d ON hr.doctor_id = d.id 
            WHERE hr.patient_id = ? 
            AND (hr.title LIKE ? OR hr.description LIKE ?)
            ORDER BY hr.created_at DESC";
    
    $search_param = "%$search_term%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $patient_id, $search_param, $search_param);
    $stmt->execute();
    
    return $stmt->get_result();
}

function generateHealthReport($conn, $patient_id, $start_date = null, $end_date = null) {
    $sql = "SELECT hr.*, d.name as doctor_name, d.specialization, u.name as patient_name
            FROM health_records hr 
            JOIN doctors d ON hr.doctor_id = d.id 
            JOIN users u ON hr.patient_id = u.id 
            WHERE hr.patient_id = ?";
    
    $params = [$patient_id];
    $types = "i";
    
    if ($start_date) {
        $sql .= " AND hr.created_at >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if ($end_date) {
        $sql .= " AND hr.created_at <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    $sql .= " ORDER BY hr.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    return $stmt->get_result();
}

function getRecentHealthRecords($conn, $patient_id, $limit = 5) {
    $sql = "SELECT hr.*, d.name as doctor_name, d.specialization 
            FROM health_records hr 
            JOIN doctors d ON hr.doctor_id = d.id 
            WHERE hr.patient_id = ? 
            ORDER BY hr.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $patient_id, $limit);
    $stmt->execute();
    
    return $stmt->get_result();
}

function createAllergyRecord($conn, $patient_id, $doctor_id, $allergen, $severity, $reaction, $notes = '') {
    $title = "Allergy to " . $allergen;
    $description = "Severity: $severity\nReaction: $reaction\nNotes: $notes";
    
    return createHealthRecord($conn, $patient_id, $doctor_id, 'allergy', $title, $description);
}

function createLabResultRecord($conn, $patient_id, $doctor_id, $test_name, $results, $normal_range, $interpretation = '') {
    $title = "Lab Results - " . $test_name;
    $description = "Results: $results\nNormal Range: $normal_range\nInterpretation: $interpretation";
    
    return createHealthRecord($conn, $patient_id, $doctor_id, 'lab_results', $title, $description);
}

function createConsultationNote($conn, $patient_id, $doctor_id, $appointment_id, $symptoms, $diagnosis, $treatment_plan, $follow_up = '') {
    $title = "Consultation Notes";
    $description = "Symptoms: $symptoms\nDiagnosis: $diagnosis\nTreatment Plan: $treatment_plan\nFollow-up: $follow_up";
    
    return createHealthRecord($conn, $patient_id, $doctor_id, 'consultation', $title, $description, null, $appointment_id);
}
?> 