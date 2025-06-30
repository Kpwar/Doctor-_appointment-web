<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch patient's appointments
$result = mysqli_query($conn, "
    SELECT a.*, d.name AS doctor_name, d.specialization 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    WHERE a.patient_id = $user_id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

$appointments_data = [];
$total_appointments = 0;
$pending_count = 0;
$completed_count = 0;
$cancelled_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $appointments_data[] = $row;
    $total_appointments++;
    switch ($row['status']) {
        case 'Pending':
            $pending_count++;
            break;
        case 'Completed':
            $completed_count++;
            break;
        case 'Cancelled':
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
    <title>My Appointments - MediCare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a, #1a1a2e, #16213e, #0f3460);
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
            background: radial-gradient(circle at center, rgba(0, 255, 204, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        .container {
            max-width: 1200px;
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
            color: #00ffcc;
            font-size: 3rem;
            text-shadow: 0 0 20px #00ffcc, 0 0 40px #00ccff;
            animation: glow 2s ease-in-out infinite alternate;
            margin-bottom: 10px;
        }

        @keyframes glow {
            from { text-shadow: 0 0 20px #00ffcc, 0 0 40px #00ccff; }
            to { text-shadow: 0 0 30px #00ffcc, 0 0 50px #00ccff; }
        }

        .header p {
            color: #ffffff;
            font-size: 1.2rem;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid #00ffcc;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 255, 204, 0.2);
        }

        .stat-number {
            color: #00ffcc;
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 0 0 10px #00ffcc;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #ffffff;
            font-size: 1rem;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        .appointments-section {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffcc;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .appointments-section::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00ffcc, #00ccff, #ff00ff, #00ffcc);
            border-radius: 20px;
            z-index: -1;
            /* animation: borderGlow 3s linear infinite; */
        }

        .section-title {
            color: #00ffcc;
            font-size: 2rem;
            margin-bottom: 25px;
            text-shadow: 0 0 15px #00ffcc;
            text-align: center;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            overflow: hidden;
        }

        .appointments-table th {
            background: rgba(0, 255, 204, 0.2);
            color: #00ffcc;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            text-shadow: 0 0 5px #00ffcc;
            border-bottom: 2px solid #00ffcc;
        }

        .appointments-table td {
            padding: 15px;
            text-align: center;
            color: #ffffff;
            border-bottom: 1px solid rgba(0, 255, 204, 0.3);
        }

        .appointments-table tr:hover {
            background: rgba(0, 255, 204, 0.1);
        }

        .status-pending {
            color: #ffaa00;
            text-shadow: 0 0 5px #ffaa00;
            font-weight: bold;
        }

        .status-completed {
            color: #00ffcc;
            text-shadow: 0 0 5px #00ffcc;
            font-weight: bold;
        }

        .status-cancelled {
            color: #ff4444;
            text-shadow: 0 0 5px #ff4444;
            font-weight: bold;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            color: #00ffcc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-shadow: 0 0 5px #00ffcc;
            z-index: 10;
        }

        .back-btn:hover {
            color: #00ccff;
            text-shadow: 0 0 10px #00ccff;
        }

        .no-appointments {
            text-align: center;
            color: #ffffff;
            font-size: 1.2rem;
            padding: 40px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: #00ffcc;
            border-radius: 50%;
            animation: float 8s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .appointments-table {
                font-size: 14px;
            }
            
            .appointments-table th,
            .appointments-table td {
                padding: 10px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-particles" id="particles"></div>
    
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    
    <div class="container">
        <div class="header">
            <h1>📋 My Appointments</h1>
            <p>View and track all your scheduled appointments</p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?= $total_appointments ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $pending_count ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $completed_count ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $cancelled_count ?></div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>

        <div class="appointments-section">
            <h2 class="section-title">📅 Appointment History</h2>
            
            <?php if (empty($appointments_data)): ?>
                <div class="no-appointments">
                    <p>No appointments found. <a href="book_appointment.php" style="color: #00ffcc; text-decoration: none;">Book your first appointment</a></p>
                </div>
            <?php else: ?>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments_data as $row): ?>
                            <tr>
                                <td>Dr. <?= htmlspecialchars($row['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($row['specialization']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['appointment_date'])) ?></td>
                                <td><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
                                <td class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 40;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize particles
        createParticles();
    </script>
</body>
</html> 