<?php
include 'config/db.php';

echo "=== Creating Missing Database Tables ===\n\n";

// Create admins table if it doesn't exist
$create_admins_table = "
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_admins_table) === TRUE) {
    echo "✅ Admins table created successfully\n";
} else {
    echo "❌ Error creating admins table: " . $conn->error . "\n";
}

// Insert default admin if not exists
$check_admin = "SELECT * FROM admins WHERE username = 'admin'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    $admin_password = md5('admin123');
    $insert_admin = "INSERT INTO admins (username, password, email) VALUES ('admin', '$admin_password', 'admin@medicare.com')";
    
    if ($conn->query($insert_admin) === TRUE) {
        echo "✅ Default admin created\n";
        echo "   Username: admin\n";
        echo "   Password: admin123\n";
    } else {
        echo "❌ Error creating default admin: " . $conn->error . "\n";
    }
} else {
    echo "✅ Default admin already exists\n";
}

// Check and create other tables if needed
$tables = [
    'users' => "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
    'doctors' => "
        CREATE TABLE IF NOT EXISTS doctors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            specialization VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            photo VARCHAR(255) DEFAULT NULL,
            bio TEXT,
            experience_years INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
    'appointments' => "
        CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            doctor_id INT NOT NULL,
            appointment_date DATE NOT NULL,
            appointment_time TIME NOT NULL,
            status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
        )"
];

foreach ($tables as $table_name => $create_sql) {
    $check_table = "SHOW TABLES LIKE '$table_name'";
    $result = $conn->query($check_table);
    
    if ($result->num_rows == 0) {
        if ($conn->query($create_sql) === TRUE) {
            echo "✅ $table_name table created successfully\n";
        } else {
            echo "❌ Error creating $table_name table: " . $conn->error . "\n";
        }
    } else {
        echo "✅ $table_name table already exists\n";
        
        // Check if photo column exists in doctors table
        if ($table_name === 'doctors') {
            $check_photo_column = "SHOW COLUMNS FROM doctors LIKE 'photo'";
            $photo_result = $conn->query($check_photo_column);
            
            if ($photo_result->num_rows == 0) {
                $add_photo_column = "ALTER TABLE doctors ADD COLUMN photo VARCHAR(255) DEFAULT NULL AFTER password";
                if ($conn->query($add_photo_column) === TRUE) {
                    echo "✅ Added photo column to doctors table\n";
                } else {
                    echo "❌ Error adding photo column: " . $conn->error . "\n";
                }
            } else {
                echo "✅ Photo column already exists in doctors table\n";
            }
            
            // Check if bio column exists
            $check_bio_column = "SHOW COLUMNS FROM doctors LIKE 'bio'";
            $bio_result = $conn->query($check_bio_column);
            
            if ($bio_result->num_rows == 0) {
                $add_bio_column = "ALTER TABLE doctors ADD COLUMN bio TEXT AFTER photo";
                if ($conn->query($add_bio_column) === TRUE) {
                    echo "✅ Added bio column to doctors table\n";
                } else {
                    echo "❌ Error adding bio column: " . $conn->error . "\n";
                }
            } else {
                echo "✅ Bio column already exists in doctors table\n";
            }
            
            // Check if experience_years column exists
            $check_exp_column = "SHOW COLUMNS FROM doctors LIKE 'experience_years'";
            $exp_result = $conn->query($check_exp_column);
            
            if ($exp_result->num_rows == 0) {
                $add_exp_column = "ALTER TABLE doctors ADD COLUMN experience_years INT DEFAULT 0 AFTER bio";
                if ($conn->query($add_exp_column) === TRUE) {
                    echo "✅ Added experience_years column to doctors table\n";
                } else {
                    echo "❌ Error adding experience_years column: " . $conn->error . "\n";
                }
            } else {
                echo "✅ Experience_years column already exists in doctors table\n";
            }
        }
    }
}

echo "\n=== Database Setup Complete ===\n";
$conn->close();
?> 