<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Test de connexion à la base de données
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'error' => 'Erreur de connexion à la base de données: ' . $conn->connect_error
    ]));
}

// Vérifier les médecins dans la base de données
$query = "SELECT id, nom, prenom, specialite, role FROM users WHERE role = 'doctor'";
$result = $conn->query($query);

if (!$result) {
    die(json_encode([
        'success' => false,
        'error' => 'Erreur de requête: ' . $conn->error
    ]));
}

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

// Afficher les résultats
echo json_encode([
    'success' => true,
    'message' => 'Test de connexion réussi',
    'doctorsCount' => count($doctors),
    'doctors' => $doctors,
    'phpVersion' => PHP_VERSION,
    'mysqlVersion' => $conn->server_info
]);

$conn->close();
?>
