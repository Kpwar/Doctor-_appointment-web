<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doc_id = $_SESSION['doctor_id'];

// Fetch doctor name
$doctor_result = mysqli_query($conn, "SELECT name FROM doctors WHERE id = $doc_id");
$doctor_name = mysqli_fetch_assoc($doctor_result)['name'] ?? 'Doctor';

echo "<h2>Welcome, Dr. {$doctor_name}</h2>";
echo "<a href='../logout.php'>Logout</a><br><br>";

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

?>

<!DOCTYPE html>
<html>

<head>
    <title>Doctor Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #444;
            text-align: center;
        }

        select,
        button {
            padding: 5px;
        }
    </style>
</head>

<body>
    <h3>Appointments:</h3>
    <table>
        <tr>
            <th>Patient</th>
            <th>Email</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Update</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['patient_email']) ?></td>
                <td><?= $row['appointment_date'] ?></td>
                <td><?= $row['appointment_time'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <select name="new_status">
                            <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>