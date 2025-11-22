<?php
require_once 'config.php';

// Vérifier la connexion
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Vérifier si l'administrateur existe déjà
$check_admin = "SELECT * FROM users WHERE role = 'admin'";
$result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($result) == 0) {
    // Insérer l'administrateur par défaut
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO users (nom, prenom, email, password, role) 
                     VALUES ('Admin', 'ESB', 'admin@esb-sante.edu', '$admin_password', 'admin')";
    
    if (mysqli_query($conn, $insert_admin)) {
        echo "Administrateur créé avec succès\n";
    } else {
        echo "Erreur lors de la création de l'administrateur: " . mysqli_error($conn) . "\n";
    }
} else {
    // Mettre à jour le mot de passe de l'administrateur existant
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $update_admin = "UPDATE users SET password = '$admin_password' WHERE role = 'admin' AND email = 'admin@esb-sante.edu'";
    
    if (mysqli_query($conn, $update_admin)) {
        echo "Mot de passe administrateur mis à jour avec succès\n";
    } else {
        echo "Erreur lors de la mise à jour du mot de passe: " . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);
echo "Terminé\n";
?>
