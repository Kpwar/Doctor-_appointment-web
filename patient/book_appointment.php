<?php
// File: patient/book_appointment.php
include('../config/db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_POST['doctor_id']) || empty($_POST['doctor_id'])) {
    die("Error: Please select a doctor.");
  }

  $doctor = $_POST["doctor_id"];
  $date = $_POST["date"];
  $time = $_POST["time"];
  $user_email = $_SESSION['user'];

  $user_result = $conn->query("SELECT id FROM users WHERE email='$user_email'");
  if ($user_result->num_rows === 0) {
    die("Error: User not found.");
  }
  $user_id = $user_result->fetch_assoc()['id'];

  $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iiss", $user_id, $doctor, $date, $time);
  $stmt->execute();

  echo "<p style='color: green;'>Appointment booked successfully!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book Appointment</title>
</head>
<body>
  <h2>Book a Doctor Appointment</h2>
  <form method="POST">
    <label>Select Doctor:</label>
    <select name="doctor_id" required>
      <option value="">-- Select Doctor --</option>
      <option value="1">Dr. Maulik Davda - Cardiologist</option>
      <option value="2">Dr. Kavya Patel - Dermatologist</option>
      <option value="3">Dr. Vishnu Prajapati - Neurologist</option>
      <option value="4">Dr. Harsh Lakhari - Orthopedic</option>
      <option value="5">Dr. Megha Topani - Pediatrician</option>
      <?php
      $doctors = $conn->query("SELECT * FROM doctors");
      while ($d = $doctors->fetch_assoc()) {
        echo "<option value='{$d['id']}'>{$d['name']} - {$d['specialization']}</option>";
      }
      ?>
    </select>

    <br><br>
    <label>Appointment Date:</label>
    <input type="date" name="date" required>

    <br><br>
    <label>Time:</label>
    <input type="time" name="time" required>

    <br><br>
    <button type="submit">Book Appointment</button>
  </form>
</body>
</html>
