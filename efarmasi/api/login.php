<?php
// api/login.php
session_start();
include_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $query = "SELECT id, email, password, full_name, role FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Login berhasil',
                'role' => $user['role']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Email atau password salah'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        ]);
    }
}
?>