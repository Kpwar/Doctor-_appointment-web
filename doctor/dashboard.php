<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doc_id = $_SESSION['doctor_id'];

// Fetch doctor information including photo
$doctor_result = mysqli_query($conn, "SELECT name, email, specialization, photo, bio, experience_years FROM doctors WHERE id = $doc_id");
$doctor_data = mysqli_fetch_assoc($doctor_result);
$doctor_name = $doctor_data['name'] ?? 'Doctor';
$doctor_specialization = $doctor_data['specialization'] ?? '';
$doctor_photo = $doctor_data['photo'] ?? '';
$doctor_bio = $doctor_data['bio'] ?? '';
$doctor_experience = $doctor_data['experience_years'] ?? 0;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['new_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $appointment_id);
    $stmt->execute();
}

// Fetch appointments
$result = mysqli_query($conn, "SELECT a.*, u.name AS patient_name, u.email AS patient_email 
    FROM appointments a 
    JOIN users u ON a.patient_id = u.id 
    WHERE a.doctor_id = $doc_id 
    ORDER BY appointment_date, appointment_time");

// Count appointments by status
$pending_count = 0;
$completed_count = 0;
$cancelled_count = 0;
$total_count = 0;

$appointments_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments_data[] = $row;
    $total_count++;
    switch ($row['status']) {
        case 'pending':
            $pending_count++;
            break;
        case 'completed':
            $completed_count++;
            break;
        case 'cancelled':
            $cancelled_count++;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physician Dashboard - MediCare Pro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #4a90e2, #7bb3f0);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            padding: 40px 0;
            margin-bottom: 50px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 3rem;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            margin-bottom: 10px;
        }

        .header p {
            color: #ffffff;
            font-size: 1.2rem;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .profile-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #4a90e2;
            flex-shrink: 0;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-photo .default-photo {
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #4a90e2, #7bb3f0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .profile-info h2 {
            color: #2a5298;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .profile-info .specialization {
            color: #4a90e2;
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .profile-info .experience {
            color: #666;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .profile-info .bio {
            color: #555;
            line-height: 1.6;
            max-width: 600px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #4a90e2;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }

        .stat-number {
            color: #2a5298;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
        }

        .appointments-section {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #4a90e2;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            color: #2a5298;
            font-size: 2rem;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .appointments-table th {
            background: #4a90e2;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .appointments-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #333;
        }

        .appointments-table tr:hover {
            background: rgba(74, 144, 226, 0.1);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            margin: 2px;
            transition: all 0.3s ease;
        }

        .btn-confirm {
            background: #28a745;
            color: white;
        }

        .btn-complete {
            background: #17a2b8;
            color: white;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }

        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .logout-btn:hover {
            background: rgba(220, 53, 69, 1);
            transform: translateY(-2px);
        }

        .no-appointments {
            text-align: center;
            color: #666;
            padding: 40px;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .profile-section {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-photo {
                width: 120px;
                height: 120px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <a href="../logout.php" class="logout-btn">Logout</a>
    
    <div class="container">
        <div class="header">
            <h1>👨‍⚕️ Physician Dashboard</h1>
            <p>Welcome to your medical practice management portal</p>
        </div>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-photo">
                <?php if ($doctor_photo): ?>
                    <img src="../<?php echo htmlspecialchars($doctor_photo); ?>" alt="Dr. <?php echo htmlspecialchars($doctor_name); ?>">
                <?php else: ?>
                    <div class="default-photo">👨‍⚕️</div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h2>Dr. <?php echo htmlspecialchars($doctor_name); ?></h2>
                <div class="specialization"><?php echo htmlspecialchars($doctor_specialization); ?></div>
                <?php if ($doctor_experience > 0): ?>
                    <div class="experience"><?php echo $doctor_experience; ?> years of experience</div>
                <?php endif; ?>
                <?php if ($doctor_bio): ?>
                    <div class="bio"><?php echo htmlspecialchars($doctor_bio); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_count; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $completed_count; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $cancelled_count; ?></div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="appointments-section">
            <h2 class="section-title">Patient Appointments</h2>
            
            <?php if (empty($appointments_data)): ?>
                <div class="no-appointments">
                    <p>No appointments scheduled yet.</p>
                </div>
            <?php else: ?>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments_data as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                        <?php if ($appointment['status'] === 'pending'): ?>
                                            <button type="submit" name="new_status" value="confirmed" class="action-btn btn-confirm">Confirm</button>
                                            <button type="submit" name="new_status" value="cancelled" class="action-btn btn-cancel">Cancel</button>
                                        <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                            <button type="submit" name="new_status" value="completed" class="action-btn btn-complete">Complete</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>