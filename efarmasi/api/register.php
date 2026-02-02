<?php
header('Content-Type: application/json');
include_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $full_name = $data['full_name'] ?? '';
    $phone = $data['phone'] ?? '';
	$norm = $data['norm'] ?? '';
    $birth_date = $data['birth_date'] ?? '';
    $gender = $data['gender'] ?? '';
    
    // Validation
    if (empty($email) || empty($password) || empty($full_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Semua field wajib diisi'
        ]);
        exit;
    }
    
    // Check if email already exists
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email sudah terdaftar'
        ]);
        exit;
    }
    
    try {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $query = "INSERT INTO users (email, password, full_name, phone, norm, birth_date, gender) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $hashed_password, $full_name, $phone, $norm, $birth_date, $gender]);
        
        $user_id = $db->lastInsertId();
        
        // Create patient profile
        $query = "INSERT INTO patient_profiles (user_id) VALUES (?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan login.',
            'user_id' => $user_id
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>