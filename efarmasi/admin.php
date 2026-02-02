<?php
session_start();
include_once 'includes/auth.php';
requireAdmin();

include_once 'includes/config.php';
$database = new Database();
$db = $database->getConnection();

// Get stats for dashboard
$stats = [];
try {
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total consultations
    $stmt = $db->query("SELECT COUNT(*) as total FROM consultations");
    $stats['total_consultations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Active rules
    $stmt = $db->query("SELECT COUNT(*) as total FROM forward_chaining_rules WHERE is_active = 1");
    $stats['active_rules'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total medicines
    $stmt = $db->query("SELECT COUNT(*) as total FROM medicines");
    $stats['total_medicines'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch(PDOException $e) {
    // Handle error
    $stats = [
        'total_users' => 0,
        'total_consultations' => 0,
        'active_rules' => 0,
        'total_medicines' => 0
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - eFarmasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="admin-dashboard">
        <div class="container">
            <!-- Admin Header -->
            <div class="admin-header">
                <h1><i class="fas fa-cog"></i> Admin Panel</h1>
                <p>Kelola data sistem eFarmasi</p>
            </div>

            <!-- Statistics -->
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-label">Total Pengguna</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_consultations']; ?></div>
                    <div class="stat-label">Total Konsultasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['active_rules']; ?></div>
                    <div class="stat-label">Aturan Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_medicines']; ?></div>
                    <div class="stat-label">Data Obat</div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="admin-tabs">
                <button class="tab-btn active" data-tab="dashboard">Dashboard</button>
                <button class="tab-btn" data-tab="users">Manage Users</button>
                <button class="tab-btn" data-tab="rules">Manage Rules</button>
                <button class="tab-btn" data-tab="medicines">Manage Medicines</button>
                <button class="tab-btn" data-tab="consultations">Consultations</button>
            </div>

            <!-- Tab Contents -->
            <div class="tab-content active" id="dashboard-tab">
                <h3>Dashboard Overview</h3>
                <p>Selamat datang di Admin Panel eFarmasi. Gunakan menu di atas untuk mengelola berbagai aspek sistem.</p>
                
                <div class="quick-stats">
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <div>
                            <h4>Pengguna Terdaftar</h4>
                            <p><?php echo $stats['total_users']; ?> pengguna</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-stethoscope"></i>
                        <div>
                            <h4>Konsultasi Hari Ini</h4>
                            <p><?php 
                                $stmt = $db->query("SELECT COUNT(*) as total FROM consultations WHERE DATE(created_at) = CURDATE()");
                                echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?> konsultasi</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-brain"></i>
                        <div>
                            <h4>Sistem Pakar</h4>
                            <p><?php echo $stats['active_rules']; ?> aturan aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="users-tab">
                <h3>Manage Users</h3>
                <button class="btn-add" onclick="openModal('addUserModal')">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="tab-content" id="rules-tab">
                <h3>Manage Forward Chaining Rules</h3>
                <button class="btn-add" onclick="openModal('addRuleModal')">
                    <i class="fas fa-plus"></i> Tambah Rule
                </button>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Rule</th>
                            <th>Conditions</th>
                            <th>Conclusion</th>
                            <th>Confidence</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="tab-content" id="medicines-tab">
                <h3>Manage Medicines Database</h3>
                <button class="btn-add" onclick="openModal('addMedicineModal')">
                    <i class="fas fa-plus"></i> Tambah Obat
                </button>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Dosis</th>
                            <th>Resep</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="tab-content" id="consultations-tab">
                <h3>Consultation History</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Gejala Utama</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div id="addUserModal" class="form-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah User Baru</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="addUserForm">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-input" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-input" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-input" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-select" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Tambah User</button>
            </form>
        </div>
    </div>

    <!-- Add similar modals for rules and medicines -->

    <?php include 'includes/footer.php'; ?>

    <script src="js/admin.js"></script>
</body>
</html>