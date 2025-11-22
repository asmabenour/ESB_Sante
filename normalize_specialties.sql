USE esb_sante;

-- Normalize specialties to lowercase
UPDATE users 
SET specialite = LOWER(specialite) 
WHERE role = 'doctor';

-- Verify the changes
SELECT id, nom, prenom, email, specialite 
FROM users 
WHERE role = 'doctor';
