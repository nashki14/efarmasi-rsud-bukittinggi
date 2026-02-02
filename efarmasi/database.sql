CREATE DATABASE pharmedice;
USE pharmedice;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patient_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    blood_type ENUM('A', 'B', 'AB', 'O'),
    allergies TEXT,
    chronic_diseases TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE symptoms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT
);

CREATE TABLE consultations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    main_symptom VARCHAR(255),
    additional_symptoms TEXT,
    answers JSON,
    recommendation TEXT,
    status ENUM('pending', 'completed') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE medicines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    dosage VARCHAR(100),
    indications TEXT,
    contraindications TEXT,
    side_effects TEXT,
    is_prescription BOOLEAN DEFAULT FALSE
);

CREATE TABLE forward_chaining_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rule_name VARCHAR(255),
    conditions JSON,
    conclusions JSON,
    confidence_level DECIMAL(3,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE medicine_recommendations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    consultation_id INT NOT NULL,
    medicine_id INT NOT NULL,
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    duration VARCHAR(100),
    notes TEXT,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
);

CREATE TABLE consultation_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    symptoms_data JSON,
    applied_rules JSON,
    final_recommendation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO symptoms (name, category, description) VALUES
('Demam', 'Umum', 'Suhu tubuh di atas 37.5°C'),
('Batuk', 'Pernapasan', 'Batuk kering atau berdahak'),
('Pilek', 'Pernapasan', 'Hidung tersumbat atau berair'),
('Sakit Kepala', 'Neurologi', 'Nyeri di kepala'),
('Diare', 'Pencernaan', 'Buang air besar encer lebih dari 3 kali sehari'),
('Mual', 'Pencernaan', 'Perasaan ingin muntah'),
('Muntah', 'Pencernaan', 'Mengeluarkan isi lambung'),
('Nyeri Otot', 'Musculoskeletal', 'Nyeri pada otot'),
('Gatal-gatal', 'Kulit', 'Rasa gatal pada kulit'),
('Sesak Napas', 'Pernapasan', 'Kesulitan bernapas');

INSERT INTO medicines (name, category, dosage, indications, contraindications, side_effects, is_prescription) VALUES
('Paracetamol 500mg', 'Analgesik', '500mg', 'Demam, sakit kepala, nyeri ringan', 'Hipersensitif terhadap paracetamol', 'Reaksi alergi, gangguan hati pada dosis tinggi', FALSE),
('Ibuprofen 400mg', 'Anti-inflamasi', '400mg', 'Nyeri, demam, inflamasi', 'Ulkus lambung, gangguan ginjal', 'Gangguan pencernaan, pusing', FALSE),
('CTM 4mg', 'Antihistamin', '4mg', 'Alergi, gatal-gatal', 'Glaukoma, hipertrofi prostat', 'Mengantuk, mulut kering', FALSE),
('Dextromethorphan 15mg', 'Antitusif', '15mg', 'Batuk kering', 'Batuk berdahak, asma', 'Mual, pusing', FALSE),
('Loperamide 2mg', 'Antidiare', '2mg', 'Diare akut', 'Diare dengan demam tinggi, kolitis ulseratif', 'Sembelit, sakit perut', FALSE);

INSERT INTO forward_chaining_rules (rule_name, conditions, conclusions, confidence_level) VALUES
('Demam Ringan', '{"symptoms": ["Demam"], "temperature": "<38.5", "duration": "<3"}', '{"medicine": "Paracetamol 500mg", "dosage": "1 tablet setiap 4-6 jam", "max_daily": "4 tablet", "warning": "Jika demam >3 hari, konsultasi dokter"}', 0.85),
('Batuk Kering', '{"symptoms": ["Batuk"], "type": "kering", "duration": "<7", "has_allergy": false}', '{"medicine": "Dextromethorphan 15mg", "dosage": "1 tablet setiap 6-8 jam", "max_daily": "4 tablet", "warning": "Jika batuk disertai demam tinggi, konsultasi dokter"}', 0.80),
('Diare Ringan', '{"symptoms": ["Diare"], "frequency": "<5", "duration": "<2"}', '{"medicine": "Loperamide 2mg", "dosage": "2 tablet awal, lalu 1 tablet setelah tiap BAB", "max_daily": "8 tablet", "warning": "Minum banyak air, jika diare berlanjut konsultasi dokter"}', 0.75),
('Alergi Ringan', '{"symptoms": ["Gatal-gatal"], "severity": "ringan", "has_fever": false}', '{"medicine": "CTM 4mg", "dosage": "1 tablet setiap 6 jam", "max_daily": "4 tablet", "warning": "Hindari penyebab alergi, jika sesak napas segera ke UGD"}', 0.70);

INSERT INTO users (email, password, full_name, phone, birth_date, gender, role) VALUES
('admin@pharmedice.com', 'pharmedice01', 'Admin Pharmedice', '085717379709', '2006-10-22', 'female', 'admin');