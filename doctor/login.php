<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = md5($_POST['password']);

    $sql = "SELECT * FROM doctors WHERE email='$email' AND password='$pass'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['doctor_id'] = $row['id'];
        header("Location: dashboard.php");
    } else {
        $error = "Invalid login credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Login</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #000428, #004e92);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            background: rgba(0, 0, 0, 0.85);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 30px #00ffcc, 0 0 60px #00ccff;
            width: 360px;
            text-align: center;
        }

        .login-container h2 {
            color: #00ffcc;
            margin-bottom: 30px;
            text-shadow: 0 0 10px #00ffcc, 0 0 20px #00ccff;
        }

        .login-container input {
            width: 100%;
            padding: 12px 15px;
            margin: 12px 0;
            background: #111;
            border: 2px solid #00ffcc;
            border-radius: 8px;
            color: #00ffcc;
            font-size: 16px;
            box-shadow: inset 0 0 8px #00ffcc;
        }

        .login-container input:focus {
            outline: none;
            box-shadow: 0 0 12px #00ffcc, 0 0 12px #00ccff;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background: #00ffcc;
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 20px #00ffcc, 0 0 40px #00ccff;
            transition: 0.3s ease-in-out;
        }

        .login-container button:hover {
            background: #00ccff;
            color: #fff;
            box-shadow: 0 0 30px #00ccff, 0 0 60px #00ffcc;
        }

        .error {
            color: #ff4444;
            margin-top: 15px;
            text-shadow: 0 0 5px red;
        }
    </style>
</head>
<body>
    <form method="post" class="login-container">
        <h2>Doctor Login</h2>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
    </form>
</body>
</html>
