<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'c:/xampp/htdocs/projet_fin-main/error.log');
require_once 'config.php';

// Check database connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// Validate appointment time
function isValidTime($time) {
    $hours = (int)substr($time, 0, 2);
    return $hours >= 8 && $hours < 17;
}

// Create a new appointment
function createAppointment($student_id, $doctor_id, $date, $time, $classe) {
    global $conn;
    
    // Validate date
    $appointmentDate = new DateTime($date);
    $today = new DateTime();
    if ($appointmentDate < $today) {
        return ['success' => false, 'message' => 'La date du rendez-vous doit être dans le futur'];
    }

    // Validate time
    if (!isValidTime($time)) {
        return ['success' => false, 'message' => 'Les rendez-vous sont possibles uniquement entre 8h et 17h'];
    }

    // Check if doctor exists
    $doctor_check = "SELECT id FROM users WHERE id = ? AND role = 'doctor'";
    $stmt = mysqli_prepare($conn, $doctor_check);
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
    mysqli_stmt_execute($stmt);
    if (!mysqli_stmt_fetch($stmt)) {
        return ['success' => false, 'message' => 'Médecin non trouvé'];
    }

    // Check if the time slot is available
    $check_query = "SELECT id FROM appointments 
                   WHERE doctor_id = ? AND appointment_date = ? 
                   AND appointment_time = ? AND status != 'cancelled'";
    
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $date, $time);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'Ce créneau horaire est déjà réservé'];
    }
    
    // If available, create the appointment
    $insert_query = "INSERT INTO appointments (student_id, doctor_id, appointment_date, appointment_time, status, classe) 
                    VALUES (?, ?, ?, ?, 'pending', ?)";
    
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "iisss", $student_id, $doctor_id, $date, $time, $classe);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Rendez-vous créé avec succès'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de la création du rendez-vous'];
    }
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    error_log("Received raw input: " . $input);
    
    $data = json_decode($input, true);
    error_log("Decoded data: " . print_r($data, true));
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
    
    if ($data['action'] === 'create') {
        if (!isset($data['student_id']) || !isset($data['doctor_id']) || 
            !isset($data['date']) || !isset($data['time']) || !isset($data['classe'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        $result = createAppointment(
            $data['student_id'],
            $data['doctor_id'],
            $data['date'],
            $data['time'],
            $data['classe']
        );
        
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

mysqli_close($conn);
?>