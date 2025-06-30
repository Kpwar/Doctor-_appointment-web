<?php
// Script to check all doctors in the database
include 'config/db.php';

echo "=== Current Doctors in Database ===\n\n";

$sql = "SELECT id, name, email, specialization FROM doctors ORDER BY name";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "ID | Name | Email | Specialization\n";
    echo "---|------|-------|---------------\n";
    
    while($row = mysqli_fetch_assoc($result)) {
        echo $row["id"] . " | " . $row["name"] . " | " . $row["email"] . " | " . $row["specialization"] . "\n";
    }
} else {
    echo "No doctors found in the database.\n";
}

mysqli_close($conn);
?>