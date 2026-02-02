<?php
header('Content-Type: application/json');
session_start();
include_once '../includes/config.php';
include_once '../includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak'
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';

switch($action) {
    case 'stats':
        getDashboardStats($db);
        break;
    case 'users':
        getUsers($db);
        break;
    case 'rules':
        getRules($db);
        break;
    case 'medicines':
        getMedicines($db);
        break;
    case 'consultations':
        getConsultations($db);
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Action tidak valid'
        ]);
}

function getDashboardStats($db) {
    try {
        // Total users
        $stmt = $db->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
        $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
        
        // Total consultations
        $stmt = $db->query("SELECT COUNT(*) as total_consultations FROM consultations");
        $total_consultations = $stmt->fetch(PDO::FETCH_ASSOC)['total_consultations'];
        
        // Active rules
        $stmt = $db->query("SELECT COUNT(*) as active_rules FROM forward_chaining_rules WHERE is_active = 1");
        $active_rules = $stmt->fetch(PDO::FETCH_ASSOC)['active_rules'];
        
        // Total medicines
        $stmt = $db->query("SELECT COUNT(*) as total_medicines FROM medicines");
        $total_medicines = $stmt->fetch(PDO::FETCH_ASSOC)['total_medicines'];
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_users' => $total_users,
                'total_consultations' => $total_consultations,
                'active_rules' => $active_rules,
                'total_medicines' => $total_medicines
            ]
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function getUsers($db) {
    try {
        $stmt = $db->query("
            SELECT id, email, full_name, phone, birth_date, gender, role, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $users
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function getRules($db) {
    try {
        $stmt = $db->query("SELECT * FROM forward_chaining_rules ORDER BY created_at DESC");
        $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $rules
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function getMedicines($db) {
    try {
        $stmt = $db->query("SELECT * FROM medicines ORDER BY name");
        $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $medicines
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function getConsultations($db) {
    try {
        $stmt = $db->query("
            SELECT c.*, u.full_name 
            FROM consultations c 
            JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC
        ");
        $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $consultations
        ]);
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
?>