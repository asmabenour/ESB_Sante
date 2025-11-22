<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get doctors by speciality
function getDoctors($speciality = null) {
    global $conn;
      $query = "SELECT id, nom, prenom, LOWER(specialite) as specialite FROM users WHERE role = 'doctor'";
    if ($speciality) {
        $query .= " AND LOWER(specialite) = LOWER(?)";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    if ($speciality) {
        mysqli_stmt_bind_param($stmt, "s", $speciality);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $doctors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $specialite = strtolower($row['specialite']); // Normaliser en minuscules
        if (!isset($doctors[$specialite])) {
            $doctors[$specialite] = [];
        }
        $doctors[$specialite][] = [
            'id' => $row['id'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom']
        ];
    }
    
    return $doctors;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $speciality = isset($_GET['specialite']) ? $_GET['specialite'] : null;
    $doctors = getDoctors($speciality);
    echo json_encode(['success' => true, 'doctors' => $doctors]);
}

mysqli_close($conn);
?>
