<?php
session_start();
require_once 'config.php';

// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Accès non autorisé'
    ]);
    exit;
}

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes OPTIONS (pour CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Récupération du contenu JSON
$input = file_get_contents('php://input');
if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Aucune donnée reçue'
    ]);
    exit;
}

// Désactivation des messages d'erreur PHP dans la sortie
ini_set('display_errors', 0);
error_reporting(0);

// Fonction de validation du mot de passe
function validatePassword($password) {
    if (strlen($password) < 8) {
        throw new Exception('Le mot de passe doit contenir au moins 8 caractères');
    }
    if (!preg_match('/[A-Z]/', $password)) {
        throw new Exception('Le mot de passe doit contenir au moins une majuscule');
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new Exception('Le mot de passe doit contenir au moins une minuscule');
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new Exception('Le mot de passe doit contenir au moins un chiffre');
    }
}

try {
    // Récupérer et valider les données
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Données JSON invalides');
    }

    // Valider les champs requis
    $required_fields = ['nom', 'prenom', 'email', 'password', 'role'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Le champ '$field' est requis");
        }
    }

    // Nettoyer et valider les données
    $data['nom'] = trim(filter_var($data['nom'], FILTER_SANITIZE_STRING));
    $data['prenom'] = trim(filter_var($data['prenom'], FILTER_SANITIZE_STRING));
    $data['email'] = trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL));

    if (strlen($data['nom']) < 2 || strlen($data['nom']) > 50) {
        throw new Exception('Le nom doit contenir entre 2 et 50 caractères');
    }
    if (strlen($data['prenom']) < 2 || strlen($data['prenom']) > 50) {
        throw new Exception('Le prénom doit contenir entre 2 et 50 caractères');
    }

    // Valider le rôle
    if (!in_array($data['role'], ['student', 'doctor'])) {
        throw new Exception('Rôle invalide');
    }

    // Valider l'email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format d\'email invalide');
    }

    // Valider le domaine de l'email selon le rôle
    $emailDomain = substr(strrchr($data['email'], "@"), 1);
    if ($data['role'] === 'student' && $emailDomain !== 'etu.esb.edu') {
        throw new Exception('L\'email étudiant doit se terminer par @etu.esb.edu');
    } elseif ($data['role'] === 'doctor' && $emailDomain !== 'esb-sante.edu') {
        throw new Exception('L\'email professionnel doit se terminer par @esb-sante.edu');
    }

    // Valider le mot de passe
    validatePassword($data['password']);

    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $data['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Cet email est déjà utilisé');
    }

    // Hasher le mot de passe
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    // Préparer la requête d'insertion
    $query = "INSERT INTO users (nom, prenom, email, password, role";
    $values = "VALUES (?, ?, ?, ?, ?";
    $types = "sssss";
    $params = [$data['nom'], $data['prenom'], $data['email'], $data['password'], $data['role']];

    // Ajouter les champs spécifiques selon le rôle
    if ($data['role'] === 'student') {
        if (empty($data['classe'])) {
            throw new Exception('La classe est requise pour un étudiant');
        }
        $data['classe'] = trim(filter_var($data['classe'], FILTER_SANITIZE_STRING));
        if (strlen($data['classe']) < 2 || strlen($data['classe']) > 20) {
            throw new Exception('La classe doit contenir entre 2 et 20 caractères');
        }
        $query .= ", classe";
        $values .= ", ?";
        $types .= "s";
        $params[] = $data['classe'];
    } elseif ($data['role'] === 'doctor') {
        if (empty($data['specialite'])) {
            throw new Exception('La spécialité est requise pour un médecin');
        }
        if (!in_array($data['specialite'], ['generaliste', 'infirmier', 'psychologue'])) {
            throw new Exception('Spécialité invalide');
        }
        $query .= ", specialite";
        $values .= ", ?";
        $types .= "s";
        $params[] = $data['specialite'];
    }

    // Finaliser la requête
    $query .= ") " . $values . ")";
    
    // Préparer et exécuter la requête
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception('Erreur lors de l\'ajout de l\'utilisateur: ' . $stmt->error);
    }

    // Renvoyer la réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Utilisateur ajouté avec succès'
    ]);

} catch (Exception $e) {
    // Renvoyer l'erreur
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Fermer la connexion
if (isset($conn)) {
    $conn->close();
}
