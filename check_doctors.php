<?php
header('Content-Type: application/json');
require_once 'config.php';

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

$query = "SELECT * FROM users WHERE role = 'doctor'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode([
        'success' => false,
        'error' => mysqli_error($conn)
    ]);
    exit;
}

$doctors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $doctors[] = [
        'id' => $row['id'],
        'nom' => $row['nom'],
        'prenom' => $row['prenom'],
        'specialite' => $row['specialite']
    ];
}

echo json_encode([
    'success' => true,
    'count' => count($doctors),
    'doctors' => $doctors
]);

mysqli_close($conn);
?>
