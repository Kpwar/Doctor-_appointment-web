<?php
// File: setup_new_features.php
// Setup script for new advanced features

include('config/db.php');
include('includes/notifications.php');
include('includes/prescriptions.php');
include('includes/health_records.php');
include('includes/reviews.php');

echo "<h1>🚀 Setting up Advanced Features</h1>";

// Create all tables
createNotificationsTable($conn);
createPrescriptionsTable($conn);
createHealthRecordsTable($conn);
createReviewsTable($conn);

// Create upload directories
$directories = ['uploads/health_records', 'uploads/prescriptions'];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

echo "<h2>✅ Setup Complete!</h2>";
echo "<p>All advanced features are now ready:</p>";
echo "<ul>";
echo "<li>📧 Notification System</li>";
echo "<li>💊 Prescription Management</li>";
echo "<li>📋 Health Records System</li>";
echo "<li>⭐ Reviews & Ratings</li>";
echo "<li>📱 SMS Notifications</li>";
echo "</ul>";
?> 