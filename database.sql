-- Create database if not exists
CREATE DATABASE IF NOT EXISTS esb_sante;
USE esb_sante;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'student') NOT NULL,
    classe VARCHAR(50),
    specialite VARCHAR(100)
);

-- Insert doctors if they don't exist
INSERT INTO users (nom, prenom, email, password, role, specialite)
SELECT * FROM (
    SELECT 'Dubois' as nom, 'Marie' as prenom, 'infirmier@esb-sante.edu' as email, '123321' as password, 'doctor' as role, 'infirmier' as specialite
    UNION ALL
    SELECT 'Martin', 'Jean', 'generaliste@esb-sante.edu', '123321', 'doctor', 'generaliste'
    UNION ALL
    SELECT 'Bernard', 'Sophie', 'psychologue@esb-sante.edu', '123321', 'doctor', 'psychologue'
) AS tmp
WHERE NOT EXISTS (
    SELECT email FROM users WHERE email = tmp.email
) LIMIT 1;

-- Create appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    doctor_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    classe VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Insert sample users
INSERT INTO users (nom, prenom, email, password, role, classe, specialite) VALUES
('zydabe', '9ahba', 'selmi@zidane.com', '123321', 'admin', '2bis6', ''),
('Admin', 'ESB', 'admin@esb.tn', '$2y$10$YBRYr4LD6LLXL.xKb2LQSOiCwhGj.k1zsMejKTVS/GA...', 'admin', NULL, NULL),
('zydabe', '9ahba', 'zydabe@gmail.com', '123321', 'student', '2bis6', NULL),
('Bakr', 'Sassi', 'bakrtn9@gmail.com', '12345678', 'student', '2', NULL);