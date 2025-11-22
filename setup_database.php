<?php
$host = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute SQL file
$sql = file_get_contents('database.sql');

if ($conn->multi_query($sql)) {
    echo "Database setup completed successfully";
} else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
?>
