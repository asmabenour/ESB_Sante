<?php
header('Content-Type: text/plain');
require_once 'config.php';

// Update doctor specialties to ensure consistent capitalization
$update_query = "UPDATE users SET 
    specialite = CASE 
        WHEN LOWER(specialite) = 'infirmier' THEN 'infirmier'
        WHEN LOWER(specialite) = 'generaliste' THEN 'generaliste'
        WHEN LOWER(specialite) = 'psychologue' THEN 'psychologue'
    END
    WHERE role = 'doctor'";

if (mysqli_query($conn, $update_query)) {
    echo "Successfully updated doctor specialties\n";
} else {
    echo "Error updating doctor specialties: " . mysqli_error($conn) . "\n";
}

// Add any missing doctors
$doctors = [
    [
        'nom' => 'Dubois',
        'prenom' => 'Marie',
        'email' => 'infirmier@esb-sante.edu',
        'password' => '123321',
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
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $doctor['email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
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
            echo "Added doctor: " . $doctor['prenom'] . " " . $doctor['nom'] . " (" . $doctor['specialite'] . ")\n";
        } else {
            echo "Error adding doctor: " . $doctor['email'] . "\n";
        }
    } else {
        echo "Doctor exists: " . $doctor['email'] . "\n";
    }
}

echo "\nUpdate complete. Please try making an appointment now.\n";
mysqli_close($conn);
?>
