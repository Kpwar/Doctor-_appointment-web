<?php
include('../config/db.php');
include('../includes/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST['email']);
    $password = md5($_POST['password']);
    
    $sql = "SELECT * FROM doctors WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $doctor = $result->fetch_assoc();
        session_start();
        $_SESSION['doctor_id'] = $doctor['id'];
        $_SESSION['doctor_name'] = $doctor['name'];
        $_SESSION['user_type'] = 'doctor';
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physician Login - MediCare Pro</title>
    <link rel="stylesheet" href="../includes/neon-theme.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .medical-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 0 10px rgba(0, 255, 255, 0.5));
        }
        
        .links {
            margin-top: 25px;
        }
        
        .links a {
            color: #00ffff;
            text-decoration: none;
            margin: 0 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            text-shadow: 0 0 5px #00ffff;
        }
        
        .links a:hover {
            color: #ff00ff;
            text-shadow: 0 0 10px #ff00ff;
            text-decoration: underline;
        }
        
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #00ffff;
            text-decoration: none;
            font-weight: 500;
            text-shadow: 0 0 5px #00ffff;
            transition: all 0.3s ease;
        }
        
        .back-home:hover {
            color: #ff00ff;
            text-shadow: 0 0 10px #ff00ff;
        }
    </style>
</head>
<body>
    <div class="floating-particles" id="particles"></div>
    
    <a href="../index.php" class="back-home">← Back to Home</a>
    
    <div class="form-container">
        <span class="medical-icon">👨‍⚕️</span>
        <h2 class="neon-title">Physician Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="links">
            <a href="register.php">New Physician? Register Here</a>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 60;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize particles
        createParticles();
    </script>
</body>
</html>
