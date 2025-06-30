<?php
session_start();
include('../config/db.php');
include('../includes/functions.php');
include('../includes/analytics.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];

// Get system statistics
$system_stats = getSystemStats($conn);
$monthly_data = getMonthlyAppointments($conn);
$status_data = getAppointmentsByStatus($conn);
$top_specializations = getTopSpecializations($conn);
$recent_activities = getRecentActivity($conn, 5);
$weekly_trends = getAppointmentTrends($conn);

// Convert data to JSON for JavaScript charts
$monthly_json = json_encode($monthly_data);
$status_json = json_encode($status_data);
$specializations_json = json_encode($top_specializations);
$weekly_json = json_encode($weekly_trends);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CyberCare Pro</title>
    <link rel="stylesheet" href="../includes/neon-theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-container {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #ff00ff;
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(255, 0, 255, 0.3);
        }

        .chart-title {
            font-size: 1.5rem;
            color: #ff00ff;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 0 0 10px #ff00ff;
            font-weight: 600;
        }

        .chart-canvas {
            width: 100% !important;
            height: 300px !important;
        }

        .recent-activity {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
        }

        .activity-title {
            font-size: 1.8rem;
            color: #00ffff;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 10px #00ffff;
            font-weight: 600;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(0, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .activity-icon {
            font-size: 2rem;
            margin-right: 15px;
            color: #ff00ff;
            text-shadow: 0 0 10px #ff00ff;
        }

        .activity-details {
            flex: 1;
        }

        .activity-patient {
            color: #00ffff;
            font-weight: 600;
            font-size: 1.1rem;
            text-shadow: 0 0 5px #00ffff;
        }

        .activity-doctor {
            color: #fff;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .activity-date {
            color: #ccc;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .activity-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 15px;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .status-confirmed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .status-completed {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid #17a2b8;
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .action-btn {
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            color: #000;
            border: none;
            padding: 15px 25px;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.4);
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-overview {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .activity-item {
                flex-direction: column;
                text-align: center;
            }

            .activity-status {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="neon-header">
        <div class="header-content">
            <h1 class="neon-title">⚙️ Admin Dashboard</h1>
            <div class="user-info">
                <div class="user-avatar">👤</div>
                <div>
                    <div>Welcome, <?php echo htmlspecialchars($admin_username); ?></div>
                    <div style="font-size: 0.9rem; opacity: 0.8;">Administrator</div>
                </div>
                <a href="../logout.php" class="neon-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div class="stats-overview">
            <div class="stat-card">
                <span class="stat-icon">👥</span>
                <div class="stat-number"><?php echo $system_stats['total_patients']; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">👨‍⚕️</span>
                <div class="stat-number"><?php echo $system_stats['total_doctors']; ?></div>
                <div class="stat-label">Total Doctors</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📅</span>
                <div class="stat-number"><?php echo $system_stats['total_appointments']; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📊</span>
                <div class="stat-number"><?php echo $system_stats['today_appointments']; ?></div>
                <div class="stat-label">Today's Appointments</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">⏳</span>
                <div class="stat-number"><?php echo $system_stats['pending_appointments']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <span class="stat-icon">✅</span>
                <div class="stat-number"><?php echo $system_stats['confirmed_appointments']; ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <div class="chart-title">📈 Monthly Appointments Trend</div>
                <canvas id="monthlyChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">📊 Appointment Status Distribution</div>
                <canvas id="statusChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">🏥 Top Specializations</div>
                <canvas id="specializationsChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">📅 Weekly Trends</div>
                <canvas id="weeklyChart" class="chart-canvas"></canvas>
            </div>
        </div>

        <div class="recent-activity">
            <div class="activity-title">🕒 Recent Activity</div>
            <?php if (!empty($recent_activities)): ?>
                <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">📋</div>
                        <div class="activity-details">
                            <div class="activity-patient"><?php echo htmlspecialchars($activity['patient_name']); ?></div>
                            <div class="activity-doctor">Dr. <?php echo htmlspecialchars($activity['doctor_name']); ?> - <?php echo htmlspecialchars($activity['specialization']); ?></div>
                            <div class="activity-date"><?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?></div>
                        </div>
                        <span class="activity-status status-<?php echo strtolower($activity['status']); ?>">
                            <?php echo ucfirst($activity['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; color: #fff; padding: 40px;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                    <div>No recent activity found.</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="quick-actions">
            <a href="manage_doctors.php" class="action-btn">Manage Doctors</a>
            <a href="manage_patients.php" class="action-btn">Manage Patients</a>
            <a href="appointments.php" class="action-btn">View All Appointments</a>
            <a href="reports.php" class="action-btn">Generate Reports</a>
        </div>
    </div>

    <div class="neon-particles"></div>

    <script>
        // Chart.js configuration
        Chart.defaults.color = '#fff';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

        // Monthly Appointments Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = <?php echo $monthly_json; ?>;
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Appointments',
                    data: monthlyData.map(item => item.count),
                    borderColor: '#00ffff',
                    backgroundColor: 'rgba(0, 255, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = <?php echo $status_json; ?>;
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.status),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#17a2b8',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#000'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff',
                            padding: 20
                        }
                    }
                }
            }
        });

        // Specializations Chart
        const specCtx = document.getElementById('specializationsChart').getContext('2d');
        const specData = <?php echo $specializations_json; ?>;
        
        new Chart(specCtx, {
            type: 'bar',
            data: {
                labels: specData.map(item => item.specialization),
                datasets: [{
                    label: 'Appointments',
                    data: specData.map(item => item.count),
                    backgroundColor: 'rgba(255, 0, 255, 0.6)',
                    borderColor: '#ff00ff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff',
                            maxRotation: 45
                        }
                    }
                }
            }
        });

        // Weekly Trends Chart
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyData = <?php echo $weekly_json; ?>;
        
        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: weeklyData.map(item => item.date),
                datasets: [{
                    label: 'Appointments',
                    data: weeklyData.map(item => item.count),
                    backgroundColor: 'rgba(0, 255, 255, 0.6)',
                    borderColor: '#00ffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#fff'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
