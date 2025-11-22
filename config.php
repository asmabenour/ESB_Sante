<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "esb_sante";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}
?>
