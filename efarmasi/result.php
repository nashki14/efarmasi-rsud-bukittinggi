<?php
session_start();
include_once 'includes/auth.php';
requireLogin();

include_once 'includes/config.php';
$database = new Database();
$db = $database->getConnection();

$consultation_id = $_GET['consultation_id'] ?? 0;
$is_admin = isset($_GET['admin']);

// Get consultation data
$query = "SELECT c.*, u.full_name 
          FROM consultations c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$consultation_id]);
$consultation = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if consultation exists and user has access
if (!$consultation || (!$is_admin && $consultation['user_id'] != $_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$recommendations = json_decode($consultation['recommendation'], true) ?: [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Konsultasi - eFarmasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/result.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="result-container">
        <div class="container">
            <!-- Header -->
            <div class="result-header">
                <div class="result-title">
                    <h1><i class="fas fa-file-medical"></i> Hasil Konsultasi</h1>
                    <p>Konsultasi ID: #<?php echo $consultation['id']; ?> • <?php echo date('d F Y H:i', strtotime($consultation['created_at'])); ?></p>
                </div>
                <div class="result-actions">
                    <button onclick="window.print()" class="btn-secondary">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button onclick="startWhatsAppConsultation()" class="btn-primary">
                        <i class="fab fa-whatsapp"></i> Konsultasi Apoteker
                    </button>
                </div>
            </div>

            <!-- Patient Info -->
            <div class="info-card">
                <h3><i class="fas fa-user"></i> Informasi Pasien</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value"><?php echo htmlspecialchars($consultation['full_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Konsultasi</span>
                        <span class="info-value"><?php echo date('d F Y H:i', strtotime($consultation['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gejala Utama</span>
                        <span class="info-value"><?php echo htmlspecialchars($consultation['main_symptom']); ?></span>
                    </div>
                    <?php if ($consultation['additional_symptoms']): ?>
                    <div class="info-item">
                        <span class="info-label">Gejala Tambahan</span>
                        <span class="info-value"><?php echo htmlspecialchars($consultation['additional_symptoms']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="recommendations-section">
                <h2><i class="fas fa-pills"></i> Rekomendasi Pengobatan</h2>
                
                <?php if (!empty($recommendations) && isset($recommendations[0]['medicine']) && !empty($recommendations[0]['medicine'])): ?>
                    <?php foreach($recommendations as $index => $rec): ?>
                        <div class="recommendation-card">
                            <div class="rec-header">
                                <h3>Rekomendasi <?php echo $index + 1; ?></h3>
                                <span class="confidence-badge">
                                    <?php echo round(($rec['confidence'] ?? 0.8) * 100); ?>% Akurat
                                </span>
                            </div>
                            
                            <?php if (isset($rec['medicine']) && !empty($rec['medicine'])): ?>
                            <div class="medicine-info">
                                <h4><i class="fas fa-capsules"></i> Obat yang Direkomendasikan</h4>
                                <div class="medicine-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Obat</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($rec['medicine']); ?></span>
                                    </div>
                                    <?php if (isset($rec['dosage']) && !empty($rec['dosage'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Dosis</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($rec['dosage']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (isset($rec['frequency']) && !empty($rec['frequency'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Frekuensi</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($rec['frequency']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (isset($rec['max_daily']) && !empty($rec['max_daily'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Maksimal per Hari</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($rec['max_daily']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (isset($rec['duration']) && !empty($rec['duration'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Durasi Penggunaan</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($rec['duration']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($rec['indications']) && !empty($rec['indications'])): ?>
                            <div class="info-section">
                                <h4><i class="fas fa-info-circle"></i> Kegunaan Obat</h4>
                                <div class="info-content">
                                    <?php echo htmlspecialchars($rec['indications']); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($rec['contraindications']) && !empty($rec['contraindications'])): ?>
                            <div class="warning-section mild">
                                <h4><i class="fas fa-ban"></i> Kontraindikasi</h4>
                                <div class="warning-content">
                                    <?php echo htmlspecialchars($rec['contraindications']); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($rec['side_effects']) && !empty($rec['side_effects'])): ?>
                            <div class="info-section">
                                <h4><i class="fas fa-exclamation-triangle"></i> Efek Samping</h4>
                                <div class="info-content">
                                    <?php echo htmlspecialchars($rec['side_effects']); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($rec['warning']) && !empty($rec['warning'])): ?>
                            <div class="warning-section important">
                                <h4><i class="fas fa-exclamation-circle"></i> Peringatan Penting</h4>
                                <div class="warning-content">
                                    <?php echo htmlspecialchars($rec['warning']); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="rec-footer">
                                <small>Berdasarkan aturan sistem pakar "<?php echo htmlspecialchars($rec['rule_name'] ?? 'Sistem Pakar'); ?>" dengan confidence level <?php echo round(($rec['confidence'] ?? 0.8) * 100); ?>%</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-recommendation">
                        <i class="fas fa-stethoscope"></i>
                        <h3>Konsultasi dengan Tenaga Medis</h3>
                        <p>Berdasarkan gejala yang Anda alami, disarankan untuk berkonsultasi langsung dengan dokter atau apoteker untuk penanganan yang lebih tepat dan akurat.</p>
                        <div class="consultation-options">
                            <button onclick="startWhatsAppConsultation()" class="btn-primary">
                                <i class="fab fa-whatsapp"></i> Konsultasi dengan Apoteker
                            </button>
                            <p class="consultation-note">
                                Konsultasi langsung memungkinkan diagnosis yang lebih akurat dan rekomendasi pengobatan yang tepat.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Important Notes -->
            <div class="notes-section">
                <div class="note-card important">
                    <h4><i class="fas fa-exclamation-circle"></i> Catatan Penting</h4>
                    <ul>
                        <li>Rekomendasi ini berdasarkan informasi yang diberikan dan sistem pakar forward chaining</li>
                        <li>Selalu baca aturan pakai dan peringatan pada kemasan obat</li>
                        <li>Jika gejala berlanjut atau memburuk, segera konsultasi ke dokter</li>
                        <li>Informasikan alergi obat sebelum mengonsumsi obat baru</li>
                        <li>Simpan obat di tempat yang sesuai dan jauh dari jangkauan anak-anak</li>
                    </ul>
                </div>

                <div class="note-card disclaimer">
                    <h4><i class="fas fa-shield-alt"></i> Disclaimer</h4>
                    <p>Rekomendasi yang diberikan adalah saran berdasarkan sistem pakar dan tidak menggantikan konsultasi langsung dengan tenaga medis profesional. Selalu konsultasikan dengan apoteker atau dokter sebelum mengonsumsi obat.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="consultation.php" class="btn-primary">
                    <i class="fas fa-redo"></i> Konsultasi Baru
                </a>
                <a href="dashboard.php" class="btn-secondary">
                    <i class="fas fa-tachometer-alt"></i> Kembali ke Dashboard
                </a>
                <?php if (isAdmin()): ?>
                <a href="admin.php" class="btn-secondary">
                    <i class="fas fa-cog"></i> Admin Panel
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function startWhatsAppConsultation() {
            const phoneNumber = "6285717379709";
            const message = `Halo, saya ingin konsultasi mengenai hasil rekomendasi obat dari PHARMEDICE.\nKonsultasi ID: #<?php echo $consultation['id']; ?>\nGejala: <?php echo $consultation['main_symptom']; ?>`;
            const encodedMessage = encodeURIComponent(message);
            const whatsappURL = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
            
            window.open(whatsappURL, '_blank');
        }
    </script>
</body>
</html>
