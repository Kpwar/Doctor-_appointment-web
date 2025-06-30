<?php
// File: patient/book_appointment.php
include('../config/db.php');
session_start();
include('../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Handle appointment cancellation
if (isset($_GET['cancel'])) {
    $appointment_id = (int)$_GET['cancel'];
    $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $appointment_id, $user_id);
    if ($stmt->execute()) {
        $success = "Appointment cancelled successfully.";
    } else {
        $error = "Failed to cancel appointment.";
    }
}

// Handle new appointment booking
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

// Get available doctors
$sql_doctors = "SELECT * FROM doctors ORDER BY name";
$doctors = $conn->query($sql_doctors);

// Get user's existing appointments
$sql_appointments = "SELECT a.*, d.name as doctor_name, d.specialization 
                     FROM appointments a 
                     JOIN doctors d ON a.doctor_id = d.id 
                     WHERE a.patient_id = ? 
                     ORDER BY a.appointment_date DESC";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $user_id);
$stmt_appointments->execute();
$user_appointments = $stmt_appointments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - CyberCare Pro</title>
    <link rel="stylesheet" href="../includes/neon-theme.css">
    <style>
        .booking-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .booking-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }

        .booking-form {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .form-title {
            font-size: 2rem;
            color: #00ffff;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 0 10px #00ffff;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #00ffff;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 1.1rem;
            text-shadow: 0 0 5px #00ffff;
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            background: rgba(0, 0, 0, 0.7);
            border: 2px solid #00ffff;
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff00ff;
            box-shadow: 0 0 20px rgba(255, 0, 255, 0.5);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group select option {
            background: #000;
            color: #fff;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.4);
        }

        .appointments-list {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #ff00ff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 0 30px rgba(255, 0, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .list-title {
            font-size: 1.8rem;
            color: #ff00ff;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 10px #ff00ff;
            font-weight: 600;
        }

        .appointment-item {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid #00ffff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .appointment-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.3);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .appointment-date {
            font-size: 1.3rem;
            color: #00ffff;
            font-weight: 600;
            text-shadow: 0 0 5px #00ffff;
        }

        .appointment-time {
            color: #fff;
            font-size: 1rem;
            margin-top: 5px;
        }

        .status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status.pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .status.confirmed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .status.cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .status.completed {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid #17a2b8;
        }

        .doctor-info {
            margin-bottom: 15px;
        }

        .doctor-name {
            font-size: 1.2rem;
            color: #ff00ff;
            font-weight: 600;
            text-shadow: 0 0 5px #ff00ff;
        }

        .specialization {
            color: #fff;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .notes {
            margin-bottom: 15px;
        }

        .notes strong {
            color: #00ffff;
            text-shadow: 0 0 5px #00ffff;
        }

        .notes div {
            color: #fff;
            margin-top: 5px;
            line-height: 1.5;
        }

        .cancel-btn {
            background: linear-gradient(45deg, #dc3545, #ff4757);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .no-appointments {
            text-align: center;
            padding: 40px;
            color: #fff;
            font-size: 1.1rem;
        }

        .no-appointments .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #00ffff;
            text-shadow: 0 0 10px #00ffff;
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
            .booking-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .booking-form,
            .appointments-list {
                padding: 25px 20px;
            }

            .form-title,
            .list-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="neon-header">
        <div class="header-content">
            <h1 class="neon-title">📅 Book Appointment</h1>
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

    <div class="booking-container">
        <?php if (isset($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="booking-grid">
            <div class="booking-form">
                <div class="form-title">
                    📋 Schedule New Appointment
                </div>

                <form method="post">
                    <div class="form-group">
                        <label for="doctor_id">Select Healthcare Provider</label>
                        <select id="doctor_id" name="doctor_id" required>
                            <option value="">Choose a doctor...</option>
                            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                <option value="<?php echo $doctor['id']; ?>">
                                    Dr. <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialization']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment_date">Preferred Date</label>
                        <input type="date" id="appointment_date" name="appointment_date" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="appointment_time">Preferred Time</label>
                        <select id="appointment_time" name="appointment_time" required>
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

                    <div class="form-group">
                        <label for="notes">Reason for Visit</label>
                        <textarea id="notes" name="notes" placeholder="Please describe your symptoms or reason for the appointment..." required><?php echo isset($notes) ? htmlspecialchars($notes) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Schedule Appointment</button>
                </form>
            </div>

            <div class="appointments-list">
                <div class="list-title">
                    📋 Your Appointments
                </div>

                <?php if ($user_appointments->num_rows > 0): ?>
                    <?php while ($appointment = $user_appointments->fetch_assoc()): ?>
                        <div class="appointment-item">
                            <div class="appointment-header">
                                <div>
                                    <div class="appointment-date">
                                        <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?>
                                    </div>
                                    <div class="appointment-time">
                                        <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                                    </div>
                                </div>
                                <span class="status <?php echo strtolower($appointment['status']); ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </div>

                            <div class="doctor-info">
                                <div class="doctor-name">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                                <div class="specialization"><?php echo htmlspecialchars($appointment['specialization']); ?></div>
                            </div>

                            <div class="notes">
                                <strong>Reason:</strong>
                                <div><?php echo htmlspecialchars($appointment['notes']); ?></div>
                            </div>

                            <?php if ($appointment['status'] == 'pending'): ?>
                                <a href="?cancel=<?php echo $appointment['id']; ?>" 
                                   class="cancel-btn" 
                                   onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    Cancel Appointment
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-appointments">
                        <div class="icon">📋</div>
                        <div>No appointments found.</div>
                        <div style="margin-top: 10px; font-size: 0.9rem; color: #ccc;">
                            Book your first appointment to get started!
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="neon-particles"></div>
</body>
</html>
