<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare Pro - Professional Healthcare Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a, #1a1a2e, #16213e, #0f3460);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Neon grid background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            z-index: 0;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .hero {
            text-align: center;
            padding: 80px 20px;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(0, 255, 255, 0.2) 0%, transparent 70%);
            animation: neonPulse 3s ease-in-out infinite;
        }

        @keyframes neonPulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        .hero h1 {
            font-size: 4rem;
            color: #00ffff;
            text-shadow: 
                0 0 10px #00ffff,
                0 0 20px #00ffff,
                0 0 30px #00ffff,
                0 0 40px #00ffff;
            margin-bottom: 20px;
            animation: neonGlow 2s ease-in-out infinite alternate;
            position: relative;
            z-index: 1;
            font-weight: 700;
            letter-spacing: 2px;
        }

        @keyframes neonGlow {
            from { 
                text-shadow: 
                    0 0 10px #00ffff,
                    0 0 20px #00ffff,
                    0 0 30px #00ffff,
                    0 0 40px #00ffff;
            }
            to { 
                text-shadow: 
                    0 0 15px #00ffff,
                    0 0 25px #00ffff,
                    0 0 35px #00ffff,
                    0 0 45px #00ffff,
                    0 0 55px #00ffff;
            }
        }

        .hero p {
            font-size: 1.3rem;
            color: #ffffff;
            margin-bottom: 50px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 1;
            font-weight: 300;
        }

        .medical-badge {
            display: inline-block;
            background: rgba(0, 255, 255, 0.1);
            padding: 15px 30px;
            border-radius: 30px;
            margin-bottom: 30px;
            border: 2px solid #00ffff;
            backdrop-filter: blur(10px);
            box-shadow: 
                0 0 20px rgba(0, 255, 255, 0.3),
                inset 0 0 20px rgba(0, 255, 255, 0.1);
            animation: badgeGlow 3s ease-in-out infinite;
        }

        @keyframes badgeGlow {
            0%, 100% { 
                box-shadow: 
                    0 0 20px rgba(0, 255, 255, 0.3),
                    inset 0 0 20px rgba(0, 255, 255, 0.1);
            }
            50% { 
                box-shadow: 
                    0 0 30px rgba(0, 255, 255, 0.5),
                    inset 0 0 30px rgba(0, 255, 255, 0.2);
            }
        }

        .medical-badge span {
            color: #00ffff;
            font-weight: bold;
            font-size: 1.2rem;
            text-shadow: 0 0 10px #00ffff;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 60px;
            position: relative;
            z-index: 1;
        }

        .card {
            background: rgba(26, 26, 46, 0.8);
            border: 2px solid #00ffff;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(15px);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.5),
                0 0 20px rgba(0, 255, 255, 0.2);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #00ffff, #ff00ff, #ffff00, #00ffff);
            animation: borderGlow 3s linear infinite;
        }

        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .card:hover::after {
            left: 100%;
        }

        .card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.6),
                0 0 40px rgba(0, 255, 255, 0.4);
            border-color: #ff00ff;
        }

        .card h3 {
            color: #00ffff;
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
            text-shadow: 0 0 15px #00ffff;
        }

        .card p {
            color: #ffffff;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 1rem;
            opacity: 0.9;
        }

        .medical-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 0 10px rgba(0, 255, 255, 0.5));
        }

        .btn {
            display: inline-block;
            padding: 15px 35px;
            background: linear-gradient(45deg, #00ffff, #0080ff);
            color: #000000;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 
                0 5px 15px rgba(0, 255, 255, 0.4),
                0 0 20px rgba(0, 255, 255, 0.2);
            margin: 5px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 
                0 10px 25px rgba(0, 255, 255, 0.6),
                0 0 30px rgba(0, 255, 255, 0.4);
            background: linear-gradient(45deg, #0080ff, #00ffff);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #ff00ff, #ff0080);
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: linear-gradient(45deg, #ff0080, #ff00ff);
            box-shadow: 
                0 10px 25px rgba(255, 0, 255, 0.6),
                0 0 30px rgba(255, 0, 255, 0.4);
        }

        .btn-admin {
            background: linear-gradient(45deg, #ffff00, #ff8000);
            color: #000000;
        }

        .btn-admin:hover {
            background: linear-gradient(45deg, #ff8000, #ffff00);
            box-shadow: 
                0 10px 25px rgba(255, 255, 0, 0.6),
                0 0 30px rgba(255, 255, 0, 0.4);
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: #00ffff;
            border-radius: 50%;
            animation: float 8s infinite linear;
            box-shadow: 0 0 10px #00ffff;
        }

        .particle:nth-child(even) {
            background: #ff00ff;
            box-shadow: 0 0 10px #ff00ff;
        }

        .particle:nth-child(3n) {
            background: #ffff00;
            box-shadow: 0 0 10px #ffff00;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        .features {
            margin-top: 80px;
            text-align: center;
        }

        .features h2 {
            color: #00ffff;
            font-size: 3rem;
            margin-bottom: 50px;
            text-shadow: 
                0 0 15px #00ffff,
                0 0 25px #00ffff;
            font-weight: 700;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-item {
            background: rgba(26, 26, 46, 0.6);
            padding: 30px 25px;
            border-radius: 20px;
            border: 1px solid #00ffff;
            backdrop-filter: blur(15px);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.3),
                0 0 20px rgba(0, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.4),
                0 0 30px rgba(0, 255, 255, 0.2);
            border-color: #ff00ff;
        }

        .feature-item h4 {
            color: #00ffff;
            margin-bottom: 15px;
            font-size: 1.3rem;
            text-shadow: 0 0 10px #00ffff;
        }

        .feature-item p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
            }

            .features h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-particles" id="particles"></div>
    
    <div class="container">
        <div class="hero">
            <div class="medical-badge">
                <span>🏥 Professional Healthcare Management System</span>
            </div>
            <h1>MediCare Pro</h1>
            <p>Advanced Healthcare Management Platform for Modern Medical Practices</p>
        </div>

        <div class="cards-container">
            <div class="card">
                <span class="medical-icon">👤</span>
                <h3>Patient Portal</h3>
                <p>Comprehensive patient management with secure appointment booking, medical history tracking, and personalized healthcare experience.</p>
                <a href="patient/register.php" class="btn">Register as Patient</a>
                <br>
                <a href="patient/login.php" class="btn btn-secondary">Patient Login</a>
            </div>

            <div class="card">
                <span class="medical-icon">👨‍⚕️</span>
                <h3>Physician Portal</h3>
                <p>Professional medical dashboard for healthcare providers to manage appointments, patient records, and clinical workflows efficiently.</p>
                <a href="doctor/register.php" class="btn">Register as Physician</a>
                <br>
                <a href="doctor/login.php" class="btn btn-secondary">Physician Login</a>
            </div>

            <div class="card">
                <span class="medical-icon">⚙️</span>
                <h3>Administrative Portal</h3>
                <p>Comprehensive system administration with user management, analytics, and healthcare facility oversight capabilities.</p>
                <a href="admin/login.php" class="btn btn-admin">Administrative Access</a>
            </div>
        </div>

        <div class="features">
            <h2>Healthcare Excellence Features</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <h4>🔒 Secure & HIPAA Compliant</h4>
                    <p>Advanced security protocols ensuring patient data protection and regulatory compliance.</p>
                </div>
                <div class="feature-item">
                    <h4>📱 Mobile Responsive</h4>
                    <p>Optimized for all devices ensuring seamless access from smartphones, tablets, and desktops.</p>
                </div>
                <div class="feature-item">
                    <h4>⚡ Real-time Updates</h4>
                    <p>Instant notifications and live updates for appointments, cancellations, and schedule changes.</p>
                </div>
                <div class="feature-item">
                    <h4>📊 Analytics Dashboard</h4>
                    <p>Comprehensive reporting and analytics for healthcare performance monitoring and optimization.</p>
                </div>
            </div>
            <div style="margin-top: 40px;">
                <a href="features.php" class="btn btn-secondary">🚀 View All Advanced Features</a>
            </div>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 80;

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
