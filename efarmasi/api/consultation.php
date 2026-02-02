<?php
header('Content-Type: application/json');
session_start();
include_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda harus login terlebih dahulu'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    
    try {
        // Initialize Forward Chaining Engine
        $fc = new ForwardChaining($db);
        
        // Process consultation
        $result = $fc->processConsultation(
            $data['symptoms'] ?? [],
            $data['details'] ?? []
        );
        
        // Save consultation
        $query = "INSERT INTO consultations (user_id, main_symptom, additional_symptoms, answers, recommendation) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        $main_symptom = $data['symptoms'][0] ?? 'Unknown';
        $additional_symptoms = implode(', ', array_slice($data['symptoms'], 1));
        $answers = json_encode($data['details']);
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
        
        echo json_encode([
            'success' => true,
            'consultation_id' => $consultation_id,
            'recommendations' => $result['recommendations'],
            'applied_rules' => $result['applied_rules']
        ]);
        
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>