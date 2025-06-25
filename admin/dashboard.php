<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($conn, "
SELECT a.*, u.name AS patient, d.name AS doctor
FROM appointments a 
JOIN users u ON a.patient_id = u.id
JOIN doctors d ON a.doctor_id = d.id
ORDER BY a.appointment_date, a.appointment_time
");

echo "<h2>Admin Dashboard</h2>";
echo "<a href='../logout.php'>Logout</a><br><br>";

echo "<table border='1'>
<tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$row['patient']}</td>
        <td>{$row['doctor']}</td>
        <td>{$row['appointment_date']}</td>
        <td>{$row['appointment_time']}</td>
        <td>{$row['status']}</td>
    </tr>";
}
echo "</table>";
