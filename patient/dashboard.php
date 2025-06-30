<?php
session_start();
include('../config/db.php');
include('../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get patient's appointments
$sql = "SELECT a.*, d.name as doctor_name, d.specialization 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        WHERE a.patient_id = ? 
        ORDER BY a.appointment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result();

// Get upcoming appointments count
$sql_upcoming = "SELECT COUNT(*) as count FROM appointments 
                 WHERE patient_id = ? AND appointment_date >= CURDATE() 
                 AND status != 'cancelled'";
$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param("i", $user_id);
$stmt_upcoming->execute();
$upcoming_count = $stmt_upcoming->get_result()->fetch_assoc()['count'];

// Get total appointments count
$sql_total = "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_count = $stmt_total->get_result()->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - CyberCare Pro</title>
    <link rel="stylesheet" href="../includes/neon-theme.css">
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 255, 255, 0.4);
        }

        .stat-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            display: block;
            text-shadow: 0 0 15px #00ffff;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #00ffff;
            margin-bottom: 10px;
            text-shadow: 0 0 10px #00ffff;
        }

        .stat-label {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 500;
        }

        .section-title {
            font-size: 2.2rem;
            color: #ff00ff;
            margin-bottom: 25px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 0 15px #ff00ff;
        }

        .appointments-table {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #ff00ff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(255, 0, 255, 0.3);
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
        }

        .table-header {
            background: linear-gradient(45deg, #ff00ff, #00ffff);
            color: #000;
            padding: 25px;
            font-weight: 700;
            font-size: 1.3rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .appointment-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 25px;
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            align-items: center;
            transition: all 0.3s ease;
        }

        .appointment-row:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.02);
        }

        .appointment-row:last-child {
            border-bottom: none;
        }

        .appointment-date {
            font-size: 1.4rem;
            color: #00ffff;
            font-weight: 600;
            text-shadow: 0 0 8px #00ffff;
        }

        .appointment-time {
            color: #fff;
            font-size: 1rem;
            margin-top: 8px;
        }

        .doctor-name {
            font-size: 1.3rem;
            color: #ff00ff;
            font-weight: 600;
            text-shadow: 0 0 8px #ff00ff;
        }

        .specialization {
            color: #fff;
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .notes-section {
            color: #fff;
        }

        .notes-section strong {
            color: #00ffff;
            text-shadow: 0 0 5px #00ffff;
            display: block;
            margin-bottom: 5px;
        }

        .status {
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status.pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 2px solid #ffc107;
            text-shadow: 0 0 5px #ffc107;
        }

        .status.confirmed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 2px solid #28a745;
            text-shadow: 0 0 5px #28a745;
        }

        .status.cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 2px solid #dc3545;
            text-shadow: 0 0 5px #dc3545;
        }

        .status.completed {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 2px solid #17a2b8;
            text-shadow: 0 0 5px #17a2b8;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #ff4757);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #95a5a6);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
        }

        .no-appointments {
            text-align: center;
            padding: 60px;
            color: #fff;
            font-size: 1.2rem;
        }

        .no-appointments .icon {
            font-size: 5rem;
            margin-bottom: 25px;
            color: #00ffff;
            text-shadow: 0 0 15px #00ffff;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .action-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.2);
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 255, 255, 0.4);
            border-color: #ff00ff;
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            text-shadow: 0 0 15px #00ffff;
        }

        .action-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #00ffff;
            margin-bottom: 15px;
            text-shadow: 0 0 8px #00ffff;
        }

        .action-desc {
            color: #fff;
            font-size: 1rem;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .appointment-row {
                grid-template-columns: 1fr;
                gap: 15px;
                text-align: center;
            }

            .action-buttons {
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="neon-header">
        <div class="header-content">
            <h1 class="neon-title">🏥 Patient Dashboard</h1>
            <div class="user-info">
                <div class="user-avatar">👤</div>
                <div>
                    <div>Welcome, <?php echo htmlspecialchars($user_name); ?></div>
                    <div style="font-size: 0.9rem; opacity: 0.8;">Patient Portal</div>
                </div>
                <?php include('../includes/notification_bell.php'); ?>
                <a href="../logout.php" class="neon-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-icon">📅</span>
                <div class="stat-number"><?php echo $upcoming_count; ?></div>
                <div class="stat-label">Upcoming Appointments</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📋</span>
                <div class="stat-number"><?php echo $total_count; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">👨‍⚕️</span>
                <div class="stat-number"><?php echo $appointments->num_rows; ?></div>
                <div class="stat-label">Doctors Consulted</div>
            </div>
        </div>

        <div class="section-title">
            📋 Appointment History
        </div>

        <div class="appointments-table">
            <div class="table-header">
                Your Medical Appointments
            </div>
            
            <?php if ($appointments->num_rows > 0): ?>
                <?php while ($appointment = $appointments->fetch_assoc()): ?>
                    <div class="appointment-row">
                        <div>
                            <div class="appointment-date">
                                <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?>
                            </div>
                            <div class="appointment-time">
                                <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                            </div>
                        </div>
                        <div>
                            <div class="doctor-name">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                            <div class="specialization"><?php echo htmlspecialchars($appointment['specialization']); ?></div>
                        </div>
                        <div class="notes-section">
                            <strong>Reason:</strong>
                            <div><?php echo htmlspecialchars($appointment['notes']); ?></div>
                        </div>
                        <div>
                            <span class="status <?php echo strtolower($appointment['status']); ?>">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                        </div>
                        <div class="action-buttons">
                            <?php if ($appointment['status'] == 'pending'): ?>
                                <a href="book_appointment.php?cancel=<?php echo $appointment['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    Cancel
                                </a>
                            <?php endif; ?>
                            <?php if ($appointment['status'] == 'confirmed'): ?>
                                <span class="btn btn-secondary">Confirmed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-appointments">
                    <div class="icon">📋</div>
                    <div>No appointments found.</div>
                    <div style="margin-top: 15px; font-size: 1rem; color: #ccc;">
                        Book your first appointment to get started!
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="quick-actions">
            <a href="book_appointment.php" class="action-card">
                <span class="action-icon">📅</span>
                <div class="action-title">Book Appointment</div>
                <div class="action-desc">Schedule a new consultation with our specialists</div>
            </a>
            <a href="advanced_booking.php" class="action-card">
                <span class="action-icon">🔍</span>
                <div class="action-title">Advanced Booking</div>
                <div class="action-desc">Search and filter doctors with smart booking</div>
            </a>
            <a href="appointments.php" class="action-card">
                <span class="action-icon">📋</span>
                <div class="action-title">View Appointments</div>
                <div class="action-desc">Manage and track your medical appointments</div>
            </a>
            <a href="../index.php" class="action-card">
                <span class="action-icon">🏠</span>
                <div class="action-title">Back to Home</div>
                <div class="action-desc">Return to the main healthcare portal</div>
            </a>
        </div>
    </div>

    <div class="neon-particles"></div>
</body>
</html>
