# 🏥 CyberCare Pro - Doctor Appointment Management System

A modern, feature-rich healthcare management system built with PHP, MySQL, and cutting-edge web technologies. This system provides comprehensive solutions for patients, doctors, and administrators to manage appointments, health records, and healthcare services efficiently.

![CyberCare Pro](https://img.shields.io/badge/Version-2.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.4+-green)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Table of Contents

- [✨ Features](#-features)
- [🎨 Screenshots](#-screenshots)
- [🚀 Quick Start](#-quick-start)
- [📦 Installation](#-installation)
- [⚙️ Configuration](#️-configuration)
- [👥 User Roles](#-user-roles)
- [🔧 System Requirements](#-system-requirements)
- [📁 Project Structure](#-project-structure)
- [🛠️ API Documentation](#️-api-documentation)
- [🐛 Troubleshooting](#-troubleshooting)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

## ✨ Features

### 🎯 Core Features
- **Multi-User System**: Separate interfaces for patients, doctors, and administrators
- **Smart Appointment Booking**: Real-time availability checking and instant booking
- **Advanced Analytics**: Comprehensive dashboards with interactive charts
- **Notification System**: Email and SMS notifications for appointments
- **Health Records Management**: Digital storage and management of medical records
- **Prescription Management**: Digital prescriptions with refill tracking
- **Reviews & Ratings**: Patient feedback and doctor rating system

### 🔐 Security Features
- **Secure Authentication**: Password hashing and session management
- **Input Validation**: Comprehensive data sanitization and validation
- **SQL Injection Protection**: Prepared statements and parameterized queries
- **XSS Protection**: HTML entity encoding and output filtering

### 📱 User Experience
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile
- **Modern UI**: Neon-themed interface with smooth animations
- **Real-time Updates**: Live notifications and status updates
- **Intuitive Navigation**: User-friendly interface with clear navigation

## 🎨 Screenshots

### 🏠 Homepage
```
┌─────────────────────────────────────────────────────────────┐
│                    🚀 CyberCare Pro                        │
├─────────────────────────────────────────────────────────────┤
│  Advanced Healthcare Management System                     │
│  Experience the future of healthcare with our              │
│  cutting-edge features                                     │
├─────────────────────────────────────────────────────────────┤
│  [Patient Login] [Doctor Login] [Admin Login]              │
└─────────────────────────────────────────────────────────────┘
```

### 📊 Dashboard
```
┌─────────────────────────────────────────────────────────────┐
│  📊 Dashboard                    👤 Welcome, Dr. Smith     │
├─────────────────────────────────────────────────────────────┤
│  📈 Appointments: 15    📅 Today: 3    ⭐ Rating: 4.8      │
│  💊 Prescriptions: 8    📋 Records: 12  🔔 Notifications: 2 │
├─────────────────────────────────────────────────────────────┤
│  📋 Recent Appointments                                    │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ John Doe - Cardiology - 2:30 PM - Confirmed            │ │
│  │ Jane Smith - General - 4:00 PM - Pending               │ │
│  └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 📝 Appointment Booking
```
┌─────────────────────────────────────────────────────────────┐
│  📅 Book Appointment                                        │
├─────────────────────────────────────────────────────────────┤
│  Doctor: Dr. Johnson (Cardiology)                          │
│  Date: [2024-01-15 ▼]                                      │
│  Time: [09:00 ▼] [09:30] [10:00] [10:30]                  │
│  Notes: [Enter appointment notes...]                       │
│                                                             │
│  [Confirm Booking] [Cancel]                                │
└─────────────────────────────────────────────────────────────┘
```

## 🚀 Quick Start

### Prerequisites
- PHP 8.4 or higher
- MySQL 5.7 or higher
- MAMP/XAMPP (for local development)
- Modern web browser

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/doctor-appointment.git
cd doctor-appointment
```

### 2. Setup Database
```bash
# Run the database setup script
php setup_database.php
```

### 3. Start Development Server
```bash
# Start PHP development server
php -S localhost:8000
```

### 4. Access the Application
Open your browser and navigate to:
```
http://localhost:8000
```

## 📦 Installation

### Step 1: Environment Setup
1. **Install MAMP/XAMPP** (if not already installed)
2. **Start MAMP/XAMPP** services (Apache & MySQL)
3. **Place project** in the web server directory

### Step 2: Database Configuration
1. **Edit** `config/db.php` with your database credentials:
```php
$host = 'localhost';
$username = 'root';
$password = 'root'; // MAMP default
$database = 'doctor_appointment';
$port = 8889; // MAMP MySQL port
```

### Step 3: Run Setup Scripts
```bash
# Create database and tables
php setup_database.php

# Verify installation
php error_check.php
```

### Step 4: Access the System
- **Homepage**: `http://localhost:8000`
- **Admin Login**: `http://localhost:8000/admin/login.php`
  - Username: `admin`
  - Password: `admin123`

## ⚙️ Configuration

### Database Settings
```php
// config/db.php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'doctor_appointment';
$port = 3306; // or 8889 for MAMP
```

### Email Configuration
```php
// includes/sms_notifications.php
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_username = 'your_email@gmail.com';
$smtp_password = 'your_app_password';
```

### File Upload Settings
```php
// Maximum file upload size
$max_file_size = 5 * 1024 * 1024; // 5MB

// Allowed file types
$allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
```

## 👥 User Roles

### 👤 Patient
- **Register/Login**: Create account and access patient portal
- **Book Appointments**: Search doctors and schedule appointments
- **View History**: Access appointment and health record history
- **Manage Profile**: Update personal information and preferences
- **Leave Reviews**: Rate and review doctors after appointments

### 👨‍⚕️ Doctor
- **Manage Schedule**: Set availability and appointment slots
- **View Appointments**: See upcoming and past appointments
- **Patient Records**: Access and update patient health records
- **Prescriptions**: Create and manage digital prescriptions
- **Analytics**: View performance metrics and patient feedback

### 👨‍💼 Administrator
- **User Management**: Manage patients, doctors, and staff accounts
- **System Analytics**: View comprehensive system statistics
- **Content Management**: Update system content and settings
- **Reports**: Generate detailed reports and insights
- **System Maintenance**: Monitor system health and performance

## 🔧 System Requirements

### Server Requirements
- **PHP**: 8.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache/Nginx (or PHP built-in server)
- **Extensions**: mysqli, session, json, fileinfo

### Client Requirements
- **Browser**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **JavaScript**: Enabled
- **Cookies**: Enabled
- **Internet**: Required for external services (email, SMS)

### Recommended Specifications
- **RAM**: 4GB or higher
- **Storage**: 10GB free space
- **Processor**: Multi-core processor
- **Network**: Stable internet connection

## 📁 Project Structure

```
doctor-appointment/
├── 📁 admin/                    # Administrator interface
│   ├── dashboard.php           # Admin dashboard
│   └── login.php               # Admin login
├── 📁 config/                   # Configuration files
│   └── db.php                  # Database configuration
├── 📁 doctor/                   # Doctor interface
│   ├── dashboard.php           # Doctor dashboard
│   ├── login.php               # Doctor login
│   └── register.php            # Doctor registration
├── 📁 includes/                 # Shared components
│   ├── analytics.php           # Analytics functions
│   ├── functions.php           # Utility functions
│   ├── health_records.php      # Health records management
│   ├── neon-theme.css          # Styling
│   ├── notifications.php       # Notification system
│   ├── prescriptions.php       # Prescription management
│   ├── reviews.php             # Review system
│   └── sms_notifications.php   # SMS functionality
├── 📁 patient/                  # Patient interface
│   ├── appointments.php        # Appointment management
│   ├── book_appointment.php    # Appointment booking
│   ├── dashboard.php           # Patient dashboard
│   ├── login.php               # Patient login
│   └── register.php            # Patient registration
├── 📁 uploads/                  # File uploads
│   ├── doctors/                # Doctor profile pictures
│   ├── health_records/         # Health record files
│   └── prescriptions/          # Prescription files
├── index.php                   # Homepage
├── features.php                # Features showcase
├── setup_database.php          # Database setup
├── error_check.php             # System diagnostics
└── README.md                   # This file
```

## 🛠️ API Documentation

### Authentication Endpoints
```php
// Patient Login
POST /patient/login.php
{
    "email": "patient@example.com",
    "password": "password123"
}

// Doctor Login
POST /doctor/login.php
{
    "email": "doctor@example.com",
    "password": "password123"
}

// Admin Login
POST /admin/login.php
{
    "username": "admin",
    "password": "admin123"
}
```

### Appointment Endpoints
```php
// Book Appointment
POST /patient/book_appointment.php
{
    "doctor_id": 1,
    "appointment_date": "2024-01-15",
    "appointment_time": "09:00",
    "notes": "Regular checkup"
}

// Get Appointments
GET /patient/appointments.php
GET /doctor/appointments.php
```

### Health Records Endpoints
```php
// Upload Health Record
POST /includes/health_records.php
{
    "patient_id": 1,
    "record_type": "lab_result",
    "file": "file_upload",
    "description": "Blood test results"
}

// Get Health Records
GET /includes/health_records.php?patient_id=1
```

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
```bash
# Error: Connection failed
# Solution: Check MAMP/XAMPP is running
php error_check.php
```

#### Session Errors
```bash
# Error: Session cannot be started
# Solution: Ensure no output before session_start()
# Check for whitespace or BOM in PHP files
```

#### File Upload Issues
```bash
# Error: File upload failed
# Solution: Check upload directory permissions
chmod 755 uploads/
chmod 755 uploads/doctors/
chmod 755 uploads/health_records/
chmod 755 uploads/prescriptions/
```

#### 404 Errors
```bash
# Error: Page not found
# Solution: Check file paths and web server configuration
# Ensure .htaccess is properly configured
```

### Performance Optimization
```php
// Enable caching
session_cache_limiter('private');
session_cache_expire(30);

// Optimize database queries
// Use indexes on frequently queried columns
// Implement query result caching
```

### Security Best Practices
```php
// Always validate and sanitize input
$clean_input = sanitize_input($_POST['user_input']);

// Use prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

// Implement CSRF protection
// Use HTTPS in production
// Regular security updates
```

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Add comments for complex logic
- Write unit tests for new features
- Update documentation for API changes
- Test thoroughly before submitting

### Code Style
```php
// Use meaningful variable names
$patientAppointments = getPatientAppointments($patientId);

// Add proper comments
/**
 * Calculate appointment statistics
 * @param int $doctorId Doctor ID
 * @return array Statistics data
 */
function getAppointmentStats($doctorId) {
    // Implementation
}
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### License Summary
- ✅ Commercial use
- ✅ Modification
- ✅ Distribution
- ✅ Private use
- ❌ Liability
- ❌ Warranty

## 📞 Support

### Getting Help
- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs via GitHub Issues
- **Discussions**: Use GitHub Discussions for questions
- **Email**: support@cybercarepro.com

### Community
- **Discord**: Join our community server
- **Twitter**: Follow us for updates
- **Blog**: Read our latest articles
- **YouTube**: Watch tutorial videos

---

<div align="center">

**Made with ❤️ by the CyberCare Pro Team**

[![GitHub stars](https://img.shields.io/github/stars/yourusername/doctor-appointment?style=social)](https://github.com/yourusername/doctor-appointment/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/yourusername/doctor-appointment?style=social)](https://github.com/yourusername/doctor-appointment/network)
[![GitHub issues](https://img.shields.io/github/issues/yourusername/doctor-appointment)](https://github.com/yourusername/doctor-appointment/issues)

</div> 