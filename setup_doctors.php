<?php
require_once 'config.php';

// Add doctors if they don't exist
$doctors = [
    [
        'nom' => 'Dubois',
        'prenom' => 'Marie',
        'email' => 'infirmier@esb-sante.edu',
        'password' => '123321',  // Using the same password pattern as other users
        'role' => 'doctor',
        'specialite' => 'infirmier'
    ],
    [
        'nom' => 'Martin',
        'prenom' => 'Jean',
        'email' => 'generaliste@esb-sante.edu',
        'password' => '123321',
        'role' => 'doctor',
        'specialite' => 'generaliste'
    ],
    [
        'nom' => 'Bernard',
        'prenom' => 'Sophie',
        'email' => 'psychologue@esb-sante.edu',
        'password' => '123321',
        'role' => 'doctor',
        'specialite' => 'psychologue'
    ]
];

foreach ($doctors as $doctor) {
    // Check if doctor already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $doctor['email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        // Doctor doesn't exist, add them
        $insert_query = "INSERT INTO users (nom, prenom, email, password, role, specialite) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ssssss", 
            $doctor['nom'], 
            $doctor['prenom'], 
            $doctor['email'], 
            $doctor['password'], 
            $doctor['role'], 
            $doctor['specialite']
        );
        
        if (mysqli_stmt_execute($stmt)) {
            echo "Added doctor: " . $doctor['prenom'] . " " . $doctor['nom'] . " (" . $doctor['specialite'] . ")<br>";
        } else {
            echo "Error adding doctor: " . $doctor['email'] . "<br>";
        }
    } else {
        echo "Doctor already exists: " . $doctor['email'] . "<br>";
    }
}

echo "Doctor setup complete!";
mysqli_close($conn);
?>
