<?php
include('../config/db.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = md5($_POST['password']);

    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$pass')";
    if (mysqli_query($conn, $query)) {
        echo "Registered successfully! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<form method="post">
    <h2>Patient Registration</h2>
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
