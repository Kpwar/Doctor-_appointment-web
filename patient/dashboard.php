<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}
echo "<h2>Welcome Patient!</h2>";
echo "<a href='book_appointment.php'>Book Appointment</a> | <a href='appointments.php'>My Appointments</a> | <a href='../logout.php'>Logout</a>";
