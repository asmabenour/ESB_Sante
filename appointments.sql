-- Create appointments table if it doesn't exist
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    doctor_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Insert sample doctor data if not exists
INSERT IGNORE INTO users (nom, prenom, email, password, role, specialite) VALUES
('Ben Jaafer', 'Bilel', 'bilel.benjaafer@esb.tn', 'doctor123', 'doctor', 'infirmier'),
('Gharbi', 'Syrine', 'syrine.gharbi@esb.tn', 'doctor123', 'doctor', 'psychologue'),
('Ben Amor', 'Walid', 'walid.benamor@esb.tn', 'doctor123', 'doctor', 'generaliste');
