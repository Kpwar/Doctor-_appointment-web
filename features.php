<?php
// File: features.php
// Features Showcase Page

include('config/db.php');
include('includes/analytics.php');
include('includes/reviews.php');

// Get system statistics
$system_stats = getSystemStats($conn);
$top_doctors = getTopRatedDoctors($conn, 5);
$recent_reviews = getRecentReviews($conn, 3);
$review_stats = getReviewStats($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - CyberCare Pro</title>
    <link rel="stylesheet" href="includes/neon-theme.css">
    <style>
        .features-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .hero-section {
            text-align: center;
            padding: 60px 20px;
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 30px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 50px rgba(0, 255, 255, 0.3);
        }

        .hero-title {
            font-size: 3.5rem;
            color: #00ffff;
            margin-bottom: 20px;
            text-shadow: 0 0 20px #00ffff;
            font-weight: 700;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .feature-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #ff00ff;
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(255, 0, 255, 0.3);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 0, 255, 0.5);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            text-shadow: 0 0 15px #ff00ff;
        }

        .feature-title {
            font-size: 1.5rem;
            color: #ff00ff;
            margin-bottom: 15px;
            text-shadow: 0 0 10px #ff00ff;
            font-weight: 600;
        }

        .feature-description {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            color: #fff;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .feature-list li:before {
            content: '✨';
            position: absolute;
            left: 0;
            color: #00ffff;
        }

        .stats-section {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
        }

        .stats-title {
            font-size: 2rem;
            color: #00ffff;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 0 10px #00ffff;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(0, 255, 255, 0.3);
        }

        .stat-number {
            font-size: 2.5rem;
            color: #00ffff;
            font-weight: bold;
            text-shadow: 0 0 10px #00ffff;
        }

        .stat-label {
            color: #fff;
            font-size: 1rem;
            margin-top: 5px;
        }

        .cta-section {
            text-align: center;
            padding: 40px;
            background: linear-gradient(45deg, rgba(0, 255, 255, 0.1), rgba(255, 0, 255, 0.1));
            border: 2px solid #00ffff;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .cta-title {
            font-size: 2rem;
            color: #00ffff;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #00ffff;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            color: #000;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.4);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="neon-header">
        <div class="header-content">
            <h1 class="neon-title">🚀 CyberCare Pro Features</h1>
            <div class="user-info">
                <a href="index.php" class="neon-btn">← Back to Home</a>
            </div>
        </div>
    </div>

    <div class="features-container">
        <div class="hero-section">
            <h1 class="hero-title">Advanced Healthcare Management</h1>
            <p class="hero-subtitle">Experience the future of healthcare with our cutting-edge features</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">📊</span>
                <h3 class="feature-title">Advanced Analytics</h3>
                <p class="feature-description">Comprehensive dashboard with interactive charts and real-time statistics.</p>
                <ul class="feature-list">
                    <li>Monthly appointment trends</li>
                    <li>Status distribution charts</li>
                    <li>Specialization analytics</li>
                    <li>Weekly performance metrics</li>
                </ul>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🔍</span>
                <h3 class="feature-title">Smart Booking System</h3>
                <p class="feature-description">Advanced search and filtering for finding the perfect doctor.</p>
                <ul class="feature-list">
                    <li>Real-time doctor search</li>
                    <li>Specialization filtering</li>
                    <li>Availability checking</li>
                    <li>One-click booking</li>
                </ul>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🔔</span>
                <h3 class="feature-title">Notification System</h3>
                <p class="feature-description">Stay informed with email and SMS notifications.</p>
                <ul class="feature-list">
                    <li>Email confirmations</li>
                    <li>SMS reminders</li>
                    <li>In-app notifications</li>
                    <li>Real-time updates</li>
                </ul>
            </div>

            <div class="feature-card">
                <span class="feature-icon">💊</span>
                <h3 class="feature-title">Prescription Management</h3>
                <p class="feature-description">Digital prescriptions with refill tracking and management.</p>
                <ul class="feature-list">
                    <li>Digital prescriptions</li>
                    <li>Refill requests</li>
                    <li>Medication history</li>
                    <li>PDF generation</li>
                </ul>
            </div>

            <div class="feature-card">
                <span class="feature-icon">📋</span>
                <h3 class="feature-title">Health Records</h3>
                <p class="feature-description">Comprehensive health record management system.</p>
                <ul class="feature-list">
                    <li>Lab results storage</li>
                    <li>Imaging records</li>
                    <li>Allergy information</li>
                    <li>Medical history</li>
                </ul>
            </div>

            <div class="feature-card">
                <span class="feature-icon">⭐</span>
                <h3 class="feature-title">Reviews & Ratings</h3>
                <p class="feature-description">Patient feedback and doctor rating system.</p>
                <ul class="feature-list">
                    <li>5-star rating system</li>
                    <li>Patient reviews</li>
                    <li>Doctor rankings</li>
                    <li>Anonymous reviews</li>
                </ul>
            </div>
        </div>

        <div class="stats-section">
            <h2 class="stats-title">System Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $system_stats['total_patients']; ?></div>
                    <div class="stat-label">Registered Patients</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $system_stats['total_doctors']; ?></div>
                    <div class="stat-label">Healthcare Providers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $system_stats['total_appointments']; ?></div>
                    <div class="stat-label">Total Appointments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($review_stats['average_rating'] ?? 0, 1); ?></div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <h2 class="cta-title">Ready to Experience the Future?</h2>
            <div class="cta-buttons">
                <a href="patient/login.php" class="cta-btn">Patient Login</a>
                <a href="doctor/login.php" class="cta-btn">Doctor Login</a>
                <a href="admin/login.php" class="cta-btn">Admin Login</a>
            </div>
        </div>
    </div>

    <div class="neon-particles"></div>
</body>
</html> 