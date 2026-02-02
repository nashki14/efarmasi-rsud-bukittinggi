DELETE FROM forward_chaining_rules;

INSERT INTO forward_chaining_rules (rule_name, conditions, conclusions, confidence_level) VALUES
('Demam Ringan Paracetamol', '{"symptoms": ["Demam"], "temperature": "<38.5", "duration": "<3"}', '{"medicine": "Paracetamol 500mg", "dosage": "1 tablet", "frequency": "setiap 4-6 jam", "max_daily": "4 tablet", "duration": "3 hari", "warning": "Jika demam tidak turun dalam 3 hari, konsultasi dokter"}', 0.90),
('Demam Tinggi Paracetamol', '{"symptoms": ["Demam"], "temperature": ">=38.5", "duration": "<3"}', '{"medicine": "Paracetamol 500mg", "dosage": "1 tablet", "frequency": "setiap 4-6 jam", "max_daily": "4 tablet", "duration": "3 hari", "warning": "Jika demam >39°C atau berlanjut >3 hari, segera ke dokter"}', 0.85),
('Batuk Kering Dextromethorphan', '{"symptoms": ["Batuk"], "type": "kering", "duration": "<7"}', '{"medicine": "Dextromethorphan 15mg", "dosage": "1 tablet", "frequency": "setiap 6-8 jam", "max_daily": "4 tablet", "duration": "7 hari", "warning": "Jika batuk disertai demam tinggi atau sesak napas, konsultasi dokter"}', 0.80),
('Batuk Berdahak Ekspektoran', '{"symptoms": ["Batuk"], "type": "berdahak", "duration": "<7"}', '{"medicine": "Guaifenesin 100mg", "dosage": "1 tablet", "frequency": "setiap 6 jam", "max_daily": "4 tablet", "duration": "7 hari", "warning": "Perbanyak minum air putih untuk membantu mengencerkan dahak"}', 0.75),
('Diare Ringan Loperamide', '{"symptoms": ["Diare"], "frequency": "<5", "duration": "<2"}', '{"medicine": "Loperamide 2mg", "dosage": "2 tablet awal, lalu 1 tablet setelah tiap BAB", "frequency": "setelah BAB", "max_daily": "8 tablet", "duration": "2 hari", "warning": "Minum oralit atau cairan elektrolit, jika diare berlanjut konsultasi dokter"}', 0.85),
('Sakit Kepala Paracetamol', '{"symptoms": ["Sakit Kepala"], "severity": "ringan", "duration": "<2"}', '{"medicine": "Paracetamol 500mg", "dosage": "1 tablet", "frequency": "setiap 6 jam", "max_daily": "4 tablet", "duration": "2 hari", "warning": "Jika sakit kepala hebat atau disertai muntah, segera ke dokter"}', 0.88),
('Alergi Ringan CTM', '{"symptoms": ["Gatal-gatal"], "severity": "ringan", "has_fever": false}', '{"medicine": "CTM 4mg", "dosage": "1 tablet", "frequency": "setiap 6 jam", "max_daily": "4 tablet", "duration": "3 hari", "warning": "Hindari penyebab alergi, obat dapat menyebabkan mengantuk, jangan mengemudi"}', 0.70),
('Flu dan Pilek', '{"symptoms": ["Pilek", "Demam"], "temperature": "<38", "duration": "<5"}', '{"medicine": "Paracetamol 500mg", "dosage": "1 tablet", "frequency": "setiap 6 jam", "max_daily": "4 tablet", "duration": "5 hari", "warning": "Istirahat yang cukup, perbanyak minum air putih dan vitamin C"}', 0.82),
('Nyeri Otot Ibuprofen', '{"symptoms": ["Nyeri Otot"], "severity": "ringan-sedang", "duration": "<3"}', '{"medicine": "Ibuprofen 400mg", "dosage": "1 tablet", "frequency": "setiap 8 jam", "max_daily": "3 tablet", "duration": "3 hari", "warning": "Konsumsi setelah makan, hindari jika ada riwayat maag"}', 0.78),
('Mual dan Muntah', '{"symptoms": ["Mual", "Muntah"], "frequency": "<3", "duration": "<1"}', '{"medicine": "Antasida DOEN", "dosage": "1-2 tablet", "frequency": "setelah makan dan sebelum tidur", "max_daily": "8 tablet", "duration": "2 hari", "warning": "Jika muntah berlanjut atau disertai demam tinggi, konsultasi dokter"}', 0.75);

UPDATE medicines SET 
indications = 'Untuk meredakan demam, sakit kepala, nyeri ringan hingga sedang, dan nyeri haid',
contraindications = 'Hipersensitif terhadap paracetamol, gangguan hati berat',
side_effects = 'Pada dosis tinggi dapat menyebabkan kerusakan hati, reaksi alergi kulit'
WHERE name = 'Paracetamol 500mg';

UPDATE medicines SET 
indications = 'Untuk meredakan nyeri, demam, inflamasi, dan nyeri haid',
contraindications = 'Ulkus lambung, gangguan ginjal, asma, alergi terhadap aspirin',
side_effects = 'Gangguan pencernaan, pusing, tinitus, reaksi alergi'
WHERE name = 'Ibuprofen 400mg';

UPDATE medicines SET 
indications = 'Untuk mengatasi alergi, gatal-gatal, biduran, dan rinitis alergi',
contraindications = 'Glaukoma, hipertrofi prostat, obstruksi leher kandung kemih',
side_effects = 'Mengantuk, mulut kering, pandangan kabur, retensi urin'
WHERE name = 'CTM 4mg';

UPDATE medicines SET 
indications = 'Untuk meredakan batuk kering tanpa dahak',
contraindications = 'Batuk berdahak, asma, penggunaan bersama MAOI',
side_effects = 'Mual, pusing, mengantuk, gangguan pencernaan'
WHERE name = 'Dextromethorphan 15mg';

UPDATE medicines SET 
indications = 'Untuk mengatasi diare akut non-spesifik',
contraindications = 'Diare dengan demam tinggi, kolitis ulseratif, disentri',
side_effects = 'Sembelit, sakit perut, kembung, mual'
WHERE name = 'Loperamide 2mg';