<?php
include('../config/db.php');
include('../includes/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $specialization = sanitize_input($_POST['specialization']);
    $bio = sanitize_input($_POST['bio']);
    $experience_years = (int)$_POST['experience_years'];
    $password = $_POST['password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($specialization) || empty($password)) {
        $error = "Name, email, specialization, and password are required.";
    } elseif (!validate_email($email)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($experience_years < 0 || $experience_years > 50) {
        $error = "Experience years must be between 0 and 50.";
    } else {
        $pass = md5($password);
        $photo_path = null;
        
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['photo']['type'], $allowed_types)) {
                $error = "Please upload a valid image file (JPEG, JPG, PNG, or GIF).";
            } elseif ($_FILES['photo']['size'] > $max_size) {
                $error = "Image size must be less than 5MB.";
            } else {
                $upload_dir = '../uploads/doctors/';
                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $photo_path = 'uploads/doctors/' . $file_name;
                } else {
                    $error = "Failed to upload image. Please try again.";
                }
            }
        }
        
        if (!isset($error)) {
            // Check if email already exists
            $check_query = "SELECT * FROM doctors WHERE email = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Email already registered! Please use a different email or login.";
            } else {
                $sql = "INSERT INTO doctors (name, email, specialization, password, photo, bio, experience_years) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssi", $name, $email, $specialization, $pass, $photo_path, $bio, $experience_years);
                
                if ($stmt->execute()) {
                    $success = "Physician registration successful! You can now login to access your medical dashboard.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physician Registration - MediCare Pro</title>
    <link rel="stylesheet" href="../includes/neon-theme.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .form-container {
            width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .medical-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 0 10px rgba(0, 255, 255, 0.5));
        }
        
        .input-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .photo-upload {
            border: 2px dashed #00ffff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: rgba(0, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .photo-upload:hover {
            border-color: #ff00ff;
            background: rgba(255, 0, 255, 0.05);
        }
        
        .photo-upload input[type="file"] {
            display: none;
        }
        
        .photo-upload label {
            color: #00ffff;
            cursor: pointer;
            font-weight: 500;
            text-shadow: 0 0 5px #00ffff;
        }
        
        .photo-upload label:hover {
            color: #ff00ff;
            text-shadow: 0 0 5px #ff00ff;
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
        <h2 class="neon-title">Physician Registration</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label for="specialization">Specialization</label>
                <select id="specialization" name="specialization" required>
                    <option value="">Select specialization</option>
                    <option value="Cardiology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                    <option value="Dermatology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                    <option value="Endocrinology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Endocrinology') ? 'selected' : ''; ?>>Endocrinology</option>
                    <option value="Gastroenterology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Gastroenterology') ? 'selected' : ''; ?>>Gastroenterology</option>
                    <option value="Neurology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                    <option value="Oncology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Oncology') ? 'selected' : ''; ?>>Oncology</option>
                    <option value="Orthopedics" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                    <option value="Pediatrics" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Pediatrics') ? 'selected' : ''; ?>>Pediatrics</option>
                    <option value="Psychiatry" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Psychiatry') ? 'selected' : ''; ?>>Psychiatry</option>
                    <option value="Radiology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Radiology') ? 'selected' : ''; ?>>Radiology</option>
                    <option value="Surgery" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
                    <option value="Urology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Urology') ? 'selected' : ''; ?>>Urology</option>
                </select>
            </div>
            
            <div class="input-group">
                <label for="experience_years">Years of Experience</label>
                <input type="number" id="experience_years" name="experience_years" min="0" max="50" required placeholder="Enter years of experience" value="<?php echo isset($_POST['experience_years']) ? htmlspecialchars($_POST['experience_years']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <label for="bio">Professional Bio</label>
                <textarea id="bio" name="bio" placeholder="Tell us about your professional background and expertise" rows="4"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password (min 6 characters)">
            </div>
            
            <div class="input-group">
                <div class="photo-upload">
                    <input type="file" id="photo" name="photo" accept="image/*">
                    <label for="photo">📷 Upload Professional Photo (Optional)</label>
                </div>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <div class="links">
            <a href="login.php">Already have an account? Login Here</a>
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
