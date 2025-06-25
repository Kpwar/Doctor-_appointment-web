<?php
include('../config/db.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $spec = $_POST['specialization'];
    $pass = md5($_POST['password']);

    $sql = "INSERT INTO doctors (name, email, specialization, password) VALUES ('$name', '$email', '$spec', '$pass')";
    if (mysqli_query($conn, $sql)) {
        echo "Doctor registered! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<form method="post">
    <h2>Doctor Registration</h2>
    Name: <input type="text" name="name" required><br>
    Specialization: <input type="text" name="specialization" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
