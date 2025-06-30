<?php
// File: includes/analytics.php
// Advanced Analytics and Dashboard Statistics

function getSystemStats($conn) {
    $stats = [];
    
    // Total users count
    $sql_users = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($sql_users);
    $stats['total_patients'] = $result->fetch_assoc()['count'];
    
    // Total doctors count
    $sql_doctors = "SELECT COUNT(*) as count FROM doctors";
    $result = $conn->query($sql_doctors);
    $stats['total_doctors'] = $result->fetch_assoc()['count'];
    
    // Total appointments count
    $sql_appointments = "SELECT COUNT(*) as count FROM appointments";
    $result = $conn->query($sql_appointments);
    $stats['total_appointments'] = $result->fetch_assoc()['count'];
    
    // Today's appointments
    $sql_today = "SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = CURDATE()";
    $result = $conn->query($sql_today);
    $stats['today_appointments'] = $result->fetch_assoc()['count'];
    
    // Pending appointments
    $sql_pending = "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'";
    $result = $conn->query($sql_pending);
    $stats['pending_appointments'] = $result->fetch_assoc()['count'];
    
    // Confirmed appointments
    $sql_confirmed = "SELECT COUNT(*) as count FROM appointments WHERE status = 'confirmed'";
    $result = $conn->query($sql_confirmed);
    $stats['confirmed_appointments'] = $result->fetch_assoc()['count'];
    
    // Completed appointments
    $sql_completed = "SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'";
    $result = $conn->query($sql_completed);
    $stats['completed_appointments'] = $result->fetch_assoc()['count'];
    
    // Cancelled appointments
    $sql_cancelled = "SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled'";
    $result = $conn->query($sql_cancelled);
    $stats['cancelled_appointments'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

function getMonthlyAppointments($conn) {
    $sql = "SELECT 
                DATE_FORMAT(appointment_date, '%Y-%m') as month,
                COUNT(*) as count
            FROM appointments 
            WHERE appointment_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
            ORDER BY month";
    
    $result = $conn->query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'month' => date('M Y', strtotime($row['month'] . '-01')),
            'count' => (int)$row['count']
        ];
    }
    
    return $data;
}

function getAppointmentsByStatus($conn) {
    $sql = "SELECT 
                status,
                COUNT(*) as count
            FROM appointments 
            GROUP BY status";
    
    $result = $conn->query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'status' => ucfirst($row['status']),
            'count' => (int)$row['count']
        ];
    }
    
    return $data;
}

function getTopSpecializations($conn) {
    $sql = "SELECT 
                d.specialization,
                COUNT(a.id) as appointment_count
            FROM doctors d
            LEFT JOIN appointments a ON d.id = a.doctor_id
            GROUP BY d.specialization
            ORDER BY appointment_count DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'specialization' => $row['specialization'],
            'count' => (int)$row['appointment_count']
        ];
    }
    
    return $data;
}

function getPatientStats($conn, $patient_id) {
    $stats = [];
    
    // Total appointments for this patient
    $sql_total = "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?";
    $stmt = $conn->prepare($sql_total);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stats['total_appointments'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Upcoming appointments
    $sql_upcoming = "SELECT COUNT(*) as count FROM appointments 
                     WHERE patient_id = ? AND appointment_date >= CURDATE() 
                     AND status != 'cancelled'";
    $stmt = $conn->prepare($sql_upcoming);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stats['upcoming_appointments'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Completed appointments
    $sql_completed = "SELECT COUNT(*) as count FROM appointments 
                      WHERE patient_id = ? AND status = 'completed'";
    $stmt = $conn->prepare($sql_completed);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stats['completed_appointments'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Doctors consulted
    $sql_doctors = "SELECT COUNT(DISTINCT doctor_id) as count FROM appointments WHERE patient_id = ?";
    $stmt = $conn->prepare($sql_doctors);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stats['doctors_consulted'] = $stmt->get_result()->fetch_assoc()['count'];
    
    return $stats;
}

function getDoctorStats($conn, $doctor_id) {
    $stats = [];
    
    // Total appointments for this doctor
    $sql_total = "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql_total);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stats['total_appointments'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Today's appointments
    $sql_today = "SELECT COUNT(*) as count FROM appointments 
                  WHERE doctor_id = ? AND DATE(appointment_date) = CURDATE()";
    $stmt = $conn->prepare($sql_today);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stats['today_appointments'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Pending appointments
    $sql_pending = "SELECT COUNT(*) as count FROM appointments 
                    WHERE doctor_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql_pending);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stats['pending_appointments'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Patients treated
    $sql_patients = "SELECT COUNT(DISTINCT patient_id) as count FROM appointments WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql_patients);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stats['patients_treated'] = $stmt->get_result()->fetch_assoc()['count'];
    
    return $stats;
}

function getRecentActivity($conn, $limit = 10) {
    $sql = "SELECT 
                a.*,
                u.name as patient_name,
                d.name as doctor_name,
                d.specialization
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            JOIN doctors d ON a.doctor_id = d.id
            ORDER BY a.created_at DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    return $activities;
}

function getAppointmentTrends($conn) {
    // Last 7 days appointments
    $sql_weekly = "SELECT 
                       DATE(appointment_date) as date,
                       COUNT(*) as count
                   FROM appointments 
                   WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                   GROUP BY DATE(appointment_date)
                   ORDER BY date";
    
    $result = $conn->query($sql_weekly);
    $weekly_data = [];
    
    while ($row = $result->fetch_assoc()) {
        $weekly_data[] = [
            'date' => date('M d', strtotime($row['date'])),
            'count' => (int)$row['count']
        ];
    }
    
    return $weekly_data;
}
?> 