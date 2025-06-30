<?php
// File: includes/reviews.php
// Doctor Reviews and Ratings System

function createReview($conn, $patient_id, $doctor_id, $appointment_id, $rating, $review_text, $anonymous = false) {
    // Check if patient has already reviewed this appointment
    $sql_check = "SELECT id FROM doctor_reviews WHERE appointment_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $appointment_id);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        return false; // Already reviewed
    }
    
    $sql = "INSERT INTO doctor_reviews (patient_id, doctor_id, appointment_id, rating, review_text, anonymous, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiissi", $patient_id, $doctor_id, $appointment_id, $rating, $review_text, $anonymous);
    
    return $stmt->execute();
}

function getDoctorReviews($conn, $doctor_id, $limit = 10) {
    $sql = "SELECT r.*, u.name as patient_name, a.appointment_date 
            FROM doctor_reviews r 
            JOIN users u ON r.patient_id = u.id 
            JOIN appointments a ON r.appointment_id = a.id 
            WHERE r.doctor_id = ? 
            ORDER BY r.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $doctor_id, $limit);
    $stmt->execute();
    
    return $stmt->get_result();
}

function getDoctorAverageRating($conn, $doctor_id) {
    $sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews 
            FROM doctor_reviews 
            WHERE doctor_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

function getDoctorRatingBreakdown($conn, $doctor_id) {
    $sql = "SELECT rating, COUNT(*) as count 
            FROM doctor_reviews 
            WHERE doctor_id = ? 
            GROUP BY rating 
            ORDER BY rating DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function getPatientReviews($conn, $patient_id) {
    $sql = "SELECT r.*, d.name as doctor_name, d.specialization, a.appointment_date 
            FROM doctor_reviews r 
            JOIN doctors d ON r.doctor_id = d.id 
            JOIN appointments a ON r.appointment_id = a.id 
            WHERE r.patient_id = ? 
            ORDER BY r.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    
    return $stmt->get_result();
}

function updateReview($conn, $review_id, $rating, $review_text, $anonymous = false) {
    $sql = "UPDATE doctor_reviews SET rating = ?, review_text = ?, anonymous = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $rating, $review_text, $anonymous, $review_id);
    
    return $stmt->execute();
}

function deleteReview($conn, $review_id, $patient_id) {
    // Only allow patient to delete their own review
    $sql = "DELETE FROM doctor_reviews WHERE id = ? AND patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $review_id, $patient_id);
    
    return $stmt->execute();
}

function getTopRatedDoctors($conn, $limit = 10) {
    $sql = "SELECT d.*, AVG(r.rating) as average_rating, COUNT(r.id) as review_count 
            FROM doctors d 
            LEFT JOIN doctor_reviews r ON d.id = r.doctor_id 
            GROUP BY d.id 
            HAVING review_count > 0 
            ORDER BY average_rating DESC, review_count DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    
    return $stmt->get_result();
}

function getRecentReviews($conn, $limit = 10) {
    $sql = "SELECT r.*, u.name as patient_name, d.name as doctor_name, d.specialization 
            FROM doctor_reviews r 
            JOIN users u ON r.patient_id = u.id 
            JOIN doctors d ON r.doctor_id = d.id 
            ORDER BY r.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    
    return $stmt->get_result();
}

function searchReviews($conn, $search_term, $limit = 20) {
    $sql = "SELECT r.*, u.name as patient_name, d.name as doctor_name, d.specialization 
            FROM doctor_reviews r 
            JOIN users u ON r.patient_id = u.id 
            JOIN doctors d ON r.doctor_id = d.id 
            WHERE r.review_text LIKE ? OR d.name LIKE ? OR d.specialization LIKE ? 
            ORDER BY r.created_at DESC 
            LIMIT ?";
    
    $search_param = "%$search_term%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $search_param, $search_param, $search_param, $limit);
    $stmt->execute();
    
    return $stmt->get_result();
}

function createReviewsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS doctor_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        appointment_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT,
        anonymous BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES users(id),
        FOREIGN KEY (doctor_id) REFERENCES doctors(id),
        FOREIGN KEY (appointment_id) REFERENCES appointments(id),
        UNIQUE KEY unique_appointment_review (appointment_id)
    )";
    
    return $conn->query($sql);
}

function getReviewStats($conn) {
    $sql = "SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                COUNT(DISTINCT doctor_id) as doctors_reviewed,
                COUNT(DISTINCT patient_id) as patients_reviewed
            FROM doctor_reviews";
    
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function getDoctorReviewStats($conn, $doctor_id) {
    $sql = "SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                MIN(rating) as lowest_rating,
                MAX(rating) as highest_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            FROM doctor_reviews 
            WHERE doctor_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

function generateStarRating($rating) {
    $stars = '';
    $full_stars = floor($rating);
    $half_star = $rating - $full_stars >= 0.5;
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $full_stars) {
            $stars .= '⭐'; // Full star
        } elseif ($i == $full_stars + 1 && $half_star) {
            $stars .= '⭐'; // Half star (using full star for simplicity)
        } else {
            $stars .= '☆'; // Empty star
        }
    }
    
    return $stars;
}

function formatReviewDate($date) {
    $now = new DateTime();
    $review_date = new DateTime($date);
    $diff = $now->diff($review_date);
    
    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    } elseif ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    } elseif ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}
?> 