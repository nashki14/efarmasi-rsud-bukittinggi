<?php
session_start();
include_once 'includes/auth.php';
requireLogin();

// === TAMBAHKAN CODE INI DI AWAL FILE ===
include_once 'includes/config.php';

// Jika form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    
    try {
        // DEBUG: Log data yang diterima
        error_log("=== CONSULTATION FORM SUBMITTED ===");
        error_log("User ID: " . $user_id);
        error_log("Symptoms: " . print_r($data['symptoms'], true));
        error_log("Details: " . print_r($data['details'], true));
        error_log("Patient Info: " . print_r($data['patientInfo'], true));

        // Initialize Forward Chaining Engine
        $fc = new ForwardChaining($db);
        
        // Process consultation - PERBAIKI PARAMETER INI
        $result = $fc->processConsultation(
            $data['symptoms'] ?? [],
            [
                'details' => $data['details'] ?? [],
                'patientInfo' => $data['patientInfo'] ?? [],
                'symptom_duration' => $data['patientInfo']['symptom_duration'] ?? '',
                'temperature' => $data['patientInfo']['temperature'] ?? '',
                'allergies' => $data['patientInfo']['allergies'] ?? '',
                'current_meds' => $data['patientInfo']['current_meds'] ?? '',
                'medical_history' => $data['patientInfo']['medical_history'] ?? ''
            ]
        );
        
        // DEBUG: Log result
        error_log("Forward Chaining Result: " . print_r($result, true));

        // Save consultation
        $query = "INSERT INTO consultations (user_id, main_symptom, additional_symptoms, answers, recommendation) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        $main_symptom = $data['symptoms'][0] ?? 'Unknown';
        $additional_symptoms = implode(', ', array_slice($data['symptoms'], 1));
        $answers = json_encode([
            'details' => $data['details'] ?? [],
            'patientInfo' => $data['patientInfo'] ?? []
        ]);
        $recommendation = json_encode($result['recommendations']);
        
        $stmt->execute([
            $user_id, 
            $main_symptom, 
            $additional_symptoms, 
            $answers, 
            $recommendation
        ]);
        
        $consultation_id = $db->lastInsertId();
        
        // Save consultation log
        $query = "INSERT INTO consultation_logs (user_id, symptoms_data, applied_rules, final_recommendation) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $user_id,
            json_encode($data['symptoms']),
            json_encode($result['applied_rules']),
            $recommendation
        ]);
        
        // Kirim response JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'consultation_id' => $consultation_id,
            'recommendations' => $result['recommendations'],
            'applied_rules' => $result['applied_rules']
        ]);
        exit;
        
    } catch(Exception $e) {
        error_log("Error in consultation: " . $e->getMessage());
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
        exit;
    }
}
// === END OF PHP PROCESSING CODE ===
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Obat - eFarmasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/consultation.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="consultation-container">
        <div class="container">
            <div class="consultation-header">
                <h1><i class="fas fa-stethoscope"></i> Konsultasi Obat</h1>
                <p>Isi form berikut untuk mendapatkan rekomendasi obat yang tepat</p>
            </div>
            
            <div class="consultation-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Gejala Utama</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Detail Gejala</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Data Tambahan</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Konfirmasi</div>
                </div>
            </div>
            
            <form id="consultationForm" class="consultation-form">
                <!-- Step 1: Main Symptoms -->
                <div class="step-content active" data-step="1">
                    <h3>Pilih Gejala Utama Anda</h3>
                    <p class="step-description">Pilih satu atau lebih gejala yang sedang Anda alami</p>
                    <div class="symptom-grid" id="mainSymptoms">
                        <!-- Symptoms loaded via JavaScript -->
                    </div>
                    <div class="error-message" id="step1Error">
                        <i class="fas fa-exclamation-circle"></i> Pilih minimal satu gejala utama
                    </div>
                </div>
                
                <!-- Step 2: Symptom Details -->
                <div class="step-content" data-step="2">
                    <h3>Detail Gejala</h3>
                    <p class="step-description">Berikan informasi detail tentang gejala yang Anda pilih</p>
                    <div id="symptomDetails">
                        <!-- Dynamic content -->
                    </div>
                </div>
                
                <!-- Step 3: Additional Data -->
                <div class="step-content" data-step="3">
                    <h3>Data Tambahan</h3>
                    <p class="step-description">Informasi tambahan untuk rekomendasi yang lebih akurat</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Suhu Tubuh (°C)</label>
                            <input type="number" class="form-input" name="temperature" step="0.1" min="35" max="42" placeholder="37.5">
                            <small class="form-help">Kosongkan jika tidak diukur</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Durasi Gejala</label>
                            <select class="form-select" name="symptom_duration" required>
                                <option value="">Pilih durasi</option>
                                <option value="<1">Kurang dari 1 hari</option>
                                <option value="1-3">1-3 hari</option>
                                <option value="4-7">4-7 hari</option>
                                <option value=">7">Lebih dari 7 hari</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Alergi Obat (jika ada)</label>
                        <input type="text" class="form-input" name="allergies" placeholder="Misal: Paracetamol, Penicillin">
                        <small class="form-help">Kosongkan jika tidak ada alergi</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Obat yang Sedang Dikonsumsi</label>
                        <input type="text" class="form-input" name="current_meds" placeholder="Misal: Vitamin C, Obat darah tinggi">
                        <small class="form-help">Kosongkan jika tidak sedang minum obat</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Riwayat Penyakit</label>
                        <input type="text" class="form-input" name="medical_history" placeholder="Misal: Diabetes, Hipertensi">
                        <small class="form-help">Kosongkan jika tidak ada</small>
                    </div>
                </div>
                
                <!-- Step 4: Confirmation -->
                <div class="step-content" data-step="4">
                    <h3>Konfirmasi Data</h3>
                    <p class="step-description">Periksa kembali data yang telah Anda masukkan</p>
                    <div id="confirmationData">
                        <!-- Data summary will be displayed here -->
                    </div>
                </div>
                
                <div class="navigation-buttons">
                    <button type="button" class="btn-nav btn-prev" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn-nav btn-next" onclick="nextStep()">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/consultation.js"></script>
</body>
</html>
