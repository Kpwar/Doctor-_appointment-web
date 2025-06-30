<?php
// File: patient/advanced_booking.php
include('../config/db.php');
session_start();
include('../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Handle search and filtering
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$specialization = isset($_GET['specialization']) ? sanitize_input($_GET['specialization']) : '';
$date_filter = isset($_GET['date']) ? sanitize_input($_GET['date']) : '';

// Build the query with filters
$sql_doctors = "SELECT d.*, 
                (SELECT COUNT(*) FROM appointments a 
                 WHERE a.doctor_id = d.id AND a.status != 'cancelled') as appointment_count
                FROM doctors d WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql_doctors .= " AND (d.name LIKE ? OR d.specialization LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($specialization)) {
    $sql_doctors .= " AND d.specialization = ?";
    $params[] = $specialization;
    $types .= "s";
}

$sql_doctors .= " ORDER BY d.name";

$stmt = $conn->prepare($sql_doctors);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$doctors = $stmt->get_result();

// Get unique specializations for filter
$sql_specializations = "SELECT DISTINCT specialization FROM doctors ORDER BY specialization";
$specializations = $conn->query($sql_specializations);

// Handle appointment booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = (int)$_POST['doctor_id'];
    $appointment_date = sanitize_input($_POST['appointment_date']);
    $appointment_time = sanitize_input($_POST['appointment_time']);
    $notes = isset($_POST['notes']) ? sanitize_input($_POST['notes']) : '';
    
    // Check if the selected time slot is available
    $sql_check = "SELECT COUNT(*) as count FROM appointments 
                  WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? 
                  AND status != 'cancelled'";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $existing_count = $result_check->fetch_assoc()['count'];
    
    if ($existing_count > 0) {
        $error = "This time slot is already booked. Please select a different time.";
    } else {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $user_id, $doctor_id, $appointment_date, $appointment_time, $notes);
        
        if ($stmt->execute()) {
            $success = "Appointment booked successfully! We'll notify you once it's confirmed.";
        } else {
            $error = "Failed to book appointment. Please try again.";
        }
    }
}

// Get doctor availability for selected doctor
function getDoctorAvailability($conn, $doctor_id, $date) {
    $sql = "SELECT appointment_time FROM appointments 
            WHERE doctor_id = ? AND appointment_date = ? AND status != 'cancelled'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctor_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booked_times = [];
    while ($row = $result->fetch_assoc()) {
        $booked_times[] = $row['appointment_time'];
    }
    
    return $booked_times;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Booking - CyberCare Pro</title>
    <link rel="stylesheet" href="../includes/neon-theme.css">
    <style>
        .advanced-booking-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .search-filters {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
        }

        .filters-title {
            font-size: 1.8rem;
            color: #00ffff;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 10px #00ffff;
            font-weight: 600;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            color: #00ffff;
            margin-bottom: 8px;
            font-weight: 500;
            text-shadow: 0 0 5px #00ffff;
        }

        .filter-group input,
        .filter-group select {
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.7);
            border: 2px solid #00ffff;
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #ff00ff;
            box-shadow: 0 0 20px rgba(255, 0, 255, 0.5);
        }

        .search-btn {
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            color: #000;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.4);
        }

        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .doctor-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #ff00ff;
            border-radius: 20px;
            padding: 25px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 25px rgba(255, 0, 255, 0.3);
        }

        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(255, 0, 255, 0.5);
        }

        .doctor-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-right: 20px;
            color: #000;
            font-weight: bold;
        }

        .doctor-info h3 {
            color: #ff00ff;
            font-size: 1.4rem;
            margin-bottom: 5px;
            text-shadow: 0 0 8px #ff00ff;
        }

        .doctor-specialization {
            color: #00ffff;
            font-size: 1rem;
            margin-bottom: 5px;
            text-shadow: 0 0 5px #00ffff;
        }

        .doctor-stats {
            color: #fff;
            font-size: 0.9rem;
        }

        .doctor-bio {
            color: #ccc;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .booking-form {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid #00ffff;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #00ffff;
            margin-bottom: 5px;
            font-weight: 500;
            text-shadow: 0 0 5px #00ffff;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 12px;
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid #00ffff;
            border-radius: 8px;
            color: #fff;
            font-size: 0.9rem;
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .book-btn {
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            color: #000;
            border: none;
            padding: 12px 25px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
        }

        .book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 255, 255, 0.4);
        }

        .no-results {
            text-align: center;
            padding: 60px;
            color: #fff;
        }

        .no-results .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #00ffff;
            text-shadow: 0 0 15px #00ffff;
        }

        .message {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            font-weight: 500;
            text-align: center;
            font-size: 1.1rem;
        }

        .success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 2px solid #28a745;
            text-shadow: 0 0 5px #28a745;
        }

        .error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 2px solid #dc3545;
            text-shadow: 0 0 5px #dc3545;
        }

        @media (max-width: 768px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }

            .doctors-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .doctor-header {
                flex-direction: column;
                text-align: center;
            }

            .doctor-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="neon-header">
        <div class="header-content">
            <h1 class="neon-title">🔍 Advanced Booking</h1>
            <div class="user-info">
                <div class="user-avatar">👤</div>
                <div>
                    <div>Welcome, <?php echo htmlspecialchars($user_name); ?></div>
                    <div style="font-size: 0.9rem; opacity: 0.8;">Patient Portal</div>
                </div>
                <a href="dashboard.php" class="neon-btn">← Dashboard</a>
            </div>
        </div>
    </div>

    <div class="advanced-booking-container">
        <?php if (isset($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="search-filters">
            <div class="filters-title">🔍 Find Your Perfect Doctor</div>
            <form method="GET" action="">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="search">Search Doctors</label>
                        <input type="text" id="search" name="search" 
                               placeholder="Search by name or specialization..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="specialization">Specialization</label>
                        <select id="specialization" name="specialization">
                            <option value="">All Specializations</option>
                            <?php while ($spec = $specializations->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($spec['specialization']); ?>"
                                        <?php echo $specialization === $spec['specialization'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($spec['specialization']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date">Preferred Date</label>
                        <input type="date" id="date" name="date" 
                               min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo htmlspecialchars($date_filter); ?>">
                    </div>
                </div>
                <div style="text-align: center;">
                    <button type="submit" class="search-btn">Search Doctors</button>
                </div>
            </form>
        </div>

        <div class="doctors-grid">
            <?php if ($doctors->num_rows > 0): ?>
                <?php while ($doctor = $doctors->fetch_assoc()): ?>
                    <div class="doctor-card">
                        <div class="doctor-header">
                            <div class="doctor-avatar">
                                <?php echo strtoupper(substr($doctor['name'], 0, 1)); ?>
                            </div>
                            <div class="doctor-info">
                                <h3>Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                                <div class="doctor-specialization"><?php echo htmlspecialchars($doctor['specialization']); ?></div>
                                <div class="doctor-stats"><?php echo $doctor['appointment_count']; ?> appointments completed</div>
                            </div>
                        </div>
                        
                        <?php if (!empty($doctor['bio'])): ?>
                            <div class="doctor-bio"><?php echo htmlspecialchars($doctor['bio']); ?></div>
                        <?php endif; ?>

                        <form method="POST" class="booking-form">
                            <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="appointment_date_<?php echo $doctor['id']; ?>">Date</label>
                                    <input type="date" id="appointment_date_<?php echo $doctor['id']; ?>" 
                                           name="appointment_date" 
                                           min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="appointment_time_<?php echo $doctor['id']; ?>">Time</label>
                                    <select id="appointment_time_<?php echo $doctor['id']; ?>" 
                                            name="appointment_time" required>
                                        <option value="">Select time...</option>
                                        <option value="09:00:00">9:00 AM</option>
                                        <option value="10:00:00">10:00 AM</option>
                                        <option value="11:00:00">11:00 AM</option>
                                        <option value="12:00:00">12:00 PM</option>
                                        <option value="14:00:00">2:00 PM</option>
                                        <option value="15:00:00">3:00 PM</option>
                                        <option value="16:00:00">4:00 PM</option>
                                        <option value="17:00:00">5:00 PM</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes_<?php echo $doctor['id']; ?>">Reason for Visit</label>
                                <textarea id="notes_<?php echo $doctor['id']; ?>" 
                                          name="notes" 
                                          placeholder="Please describe your symptoms or reason for the appointment..." 
                                          required></textarea>
                            </div>
                            
                            <button type="submit" class="book-btn">Book Appointment</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="icon">🔍</div>
                    <div>No doctors found matching your criteria.</div>
                    <div style="margin-top: 10px; font-size: 0.9rem; color: #ccc;">
                        Try adjusting your search filters.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="neon-particles"></div>

    <script>
        // Real-time availability checking
        document.querySelectorAll('input[name="appointment_date"]').forEach(function(input) {
            input.addEventListener('change', function() {
                const doctorId = this.closest('form').querySelector('input[name="doctor_id"]').value;
                const date = this.value;
                const timeSelect = this.closest('form').querySelector('select[name="appointment_time"]');
                
                if (date) {
                    // Here you could make an AJAX call to check availability
                    // For now, we'll just enable the time selection
                    timeSelect.disabled = false;
                }
            });
        });
    </script>
</body>
</html> 