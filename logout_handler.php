<?php
session_start();

// Clear session data
$_SESSION = array();
session_destroy();

// Return success response
header('Content-Type: application/json');
echo json_encode(['success' => true]);
session_start();
header('Content-Type: application/json');

echo json_encode(['success' => true]);
?>
