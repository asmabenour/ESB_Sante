USE esb_sante;

-- Add doctors with their specialities
INSERT INTO users (nom, prenom, email, password, role, specialite) VALUES
('Dubois', 'Marie', 'infirmier@esb-sante.edu', 'password123', 'doctor', 'infirmier'),
('Martin', 'Jean', 'generaliste@esb-sante.edu', 'password123', 'doctor', 'generaliste'),
('Bernard', 'Sophie', 'psychologue@esb-sante.edu', 'password123', 'doctor', 'psychologue');

-- Update the existing appointments table to ensure classe column exists
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS classe VARCHAR(50) NOT NULL DEFAULT '';
