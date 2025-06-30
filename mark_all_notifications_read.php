<?php
// File: mark_all_notifications_read.php
// AJAX handler for marking all notifications as read

session_start();
include('config/db.php');
include('includes/notifications.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Mark all notifications as read for the current user
$sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND user_type = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $user_type);

if ($stmt->execute()) {
    $affected_rows = $stmt->affected_rows;
    echo json_encode([
        'success' => true, 
        'message' => "Marked $affected_rows notifications as read"
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark notifications as read']);
}
?> 