<?php
session_start();
include_once 'includes/auth.php';
requireLogin();

include_once 'includes/config.php';
$database = new Database();
$db = $database->getConnection();

// Get user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get patient profile
$query = "SELECT * FROM patient_profiles WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Get consultation history
$query = "SELECT * FROM consultations WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - eFarmasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard">
        <div class="container">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <h1 class="dashboard-welcome">Halo, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
                <p class="dashboard-subtitle">Selamat datang di dashboard eFarmasi</p>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3 class="card-title"><i class="fas fa-stethoscope"></i> Konsultasi Cepat</h3>
                    <div class="quick-actions">
                        <a href="consultation.php" class="action-btn">
                            <i class="fas fa-comment-medical"></i>
                            <span>Konsultasi Obat</span>
                        </a>
                        <a href="#" onclick="startWhatsAppConsultation()" class="action-btn">
                            <i class="fab fa-whatsapp"></i>
                            <span>Chat Apoteker</span>
                        </a>
                        <a href="articles.html" class="action-btn">
                            <i class="fas fa-book-medical"></i>
                            <span>Baca Artikel</span>
                        </a>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="dashboard-card">
                    <h3 class="card-title"><i class="fas fa-user-circle"></i> Informasi Profil</h3>
                    <div class="profile-info">
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telepon</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></span>
                        </div>
						<div class="info-item">
                            <span class="info-label">No Rekam Medis</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['norm'] ?? '-'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Jenis Kelamin</span>
                            <span class="info-value">
                                <?php 
                                if ($user['gender'] === 'male') echo 'Laki-laki';
                                elseif ($user['gender'] === 'female') echo 'Perempuan';
                                else echo '-';
                                ?>
                            </span>
                        </div>
                        <?php if ($profile): ?>
                        <div class="info-item">
                            <span class="info-label">Alergi</span>
                            <span class="info-value"><?php echo htmlspecialchars($profile['allergies'] ?? 'Tidak ada'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Riwayat Penyakit</span>
                            <span class="info-value"><?php echo htmlspecialchars($profile['chronic_diseases'] ?? 'Tidak ada'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Consultation History -->
            <div class="dashboard-card">
                <h3 class="card-title"><i class="fas fa-history"></i> Riwayat Konsultasi Terbaru</h3>
                <div class="consultation-history">
                    <?php if (!empty($consultations)): ?>
                        <ul class="history-list">
                            <?php foreach($consultations as $consultation): ?>
                                <li class="history-item">
                                    <div>
                                        <div class="history-symptoms"><?php echo htmlspecialchars($consultation['main_symptom']); ?></div>
                                        <div class="history-date"><?php echo date('d M Y H:i', strtotime($consultation['created_at'])); ?></div>
                                    </div>
                                    <span class="history-status status-completed">Selesai</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-file-medical"></i>
                            <p>Belum ada riwayat konsultasi</p>
                            <a href="consultation.php" class="btn-primary" style="margin-top: 1rem;">Mulai Konsultasi Pertama</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>