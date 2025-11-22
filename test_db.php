<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Test de la connexion
echo "Test de connexion à la base de données:<br>";
if ($conn) {
    echo "✅ Connexion à la base de données réussie<br>";
} else {
    echo "❌ Échec de la connexion à la base de données: " . mysqli_connect_error() . "<br>";
    exit;
}

// Test de la table users
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "✅ Table users accessible. Nombre d'utilisateurs : " . $row['count'] . "<br>";
} else {
    echo "❌ Erreur lors de l'accès à la table users: " . mysqli_error($conn) . "<br>";
}

// Test des utilisateurs par rôle
$roles = ['admin', 'doctor', 'student'];
foreach ($roles as $role) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "✅ Nombre d'utilisateurs avec le rôle '$role': " . $row['count'] . "<br>";
}

echo "<br>Pour tester la connexion :<br>";
echo "Admin : admin@esb-sante.edu / admin123<br>";
echo "Médecin : generaliste@esb-sante.edu / 123321<br>";
echo "Étudiant : bakrtn9@gmail.com / 12345678<br>";
?>
