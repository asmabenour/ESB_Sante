<?php
session_start();
require_once 'config.php';

// Activer le rapport d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Get POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }

    // Valider les données reçues
    if (empty($data['email']) || empty($data['password']) || empty($data['role'])) {
        throw new Exception('Tous les champs sont requis');
    }
    
    $email = $data['email'];
    $password = $data['password'];
    $role = $data['role'];
    
    // Vérifier la connexion à la base de données
    if (!$conn) {
        throw new Exception('Erreur de connexion à la base de données: ' . mysqli_connect_error());
    }

    // Rechercher l'utilisateur dans la base de données
    $query = "SELECT * FROM users WHERE email = ? AND role = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Erreur de préparation de la requête: ' . $conn->error);
    }
    
    $stmt->bind_param('ss', $email, $role);
    
    if (!$stmt->execute()) {
        throw new Exception('Erreur d\'exécution de la requête: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Utilisateur non trouvé');
    }
    
    $user = $result->fetch_assoc();
    
    // Temporairement, faire une comparaison directe du mot de passe
    // En production, utilisez password_verify()
    if ($password !== $user['password']) {
        throw new Exception('Mot de passe incorrect');
    }
    
    // Créer la session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'nom' => $user['nom'],
        'prenom' => $user['prenom'],
        'role' => $user['role']
    ];
    
    // Renvoyer la réponse de succès
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'role' => $user['role'],
            'specialite' => $user['specialite'] ?? null,
            'classe' => $user['classe'] ?? null
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Fermer la connexion à la base de données
if (isset($conn)) {
    $conn->close();
}