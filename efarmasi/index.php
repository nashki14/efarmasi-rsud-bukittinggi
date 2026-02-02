<?php
session_start();
include_once 'includes/auth.php';

// Jika user sudah login, ambil data terbaru
if (isLoggedIn()) {
    include_once 'includes/config.php';
    $database = new Database();
    $db = $database->getConnection();
    
    $user_id = $_SESSION['user_id'];
    
    // Get user's latest consultations
    $query = "SELECT * FROM consultations WHERE user_id = ? ORDER BY created_at DESC LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $recent_consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get consultation count
    $query = "SELECT COUNT(*) as total FROM consultations WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $consultation_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eFarmasi - RSUD Bukittinggi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1 class="hero-title">
                <?php if (isLoggedIn()): ?>
                    Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋
                <?php else: ?>
                    Konsultasi dan Rekomendasi Obat Menggunakan Metode Forward Chaining
                <?php endif; ?>
            </h1>
            <p class="hero-description">
                <?php if (isLoggedIn()): ?>
                    Platform telefarmasi pertama di Indonesia yang menggunakan sistem pakar untuk memberikan rekomendasi obat yang tepat dan personal. Siap membantu kesehatan Anda hari ini!
                <?php else: ?>
                    Platform telefarmasi pertama di Indonesia yang menggunakan sistem pakar untuk memberikan rekomendasi obat yang tepat dan personal.
                <?php endif; ?>
            </p>
            <div class="hero-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="consultation.php" class="btn-primary">
                        <i class="fas fa-stethoscope"></i> Mulai Konsultasi Baru
                    </a>
                    <a href="dashboard.php" class="btn-secondary">
                        <i class="fas fa-tachometer-alt"></i> Lihat Dashboard
                    </a>
                <?php else: ?>
                    <a href="register.html" class="btn-primary">Mulai Konsultasi</a>
                    <a href="#features" class="btn-secondary">Pelajari Fitur</a>
                <?php endif; ?>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <div class="user-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $consultation_count; ?></h3>
                        <p>Total Konsultasi</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="stat-info">
                        <h3>95%</h3>
                        <p>Akurasi Sistem</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24/7</h3>
                        <p>Konsultasi Apoteker</p>
                    </div>
                </div>
            </div>

            <?php if (!empty($recent_consultations)): ?>
            <div class="recent-consultations">
                <h3>Konsultasi Terbaru</h3>
                <div class="consultation-list">
                    <?php foreach($recent_consultations as $consultation): ?>
                        <div class="consultation-item">
                            <div class="consultation-info">
                                <h4><?php echo htmlspecialchars($consultation['main_symptom']); ?></h4>
                                <p><?php echo date('d M Y', strtotime($consultation['created_at'])); ?></p>
                            </div>
                            <a href="result.php?consultation_id=<?php echo $consultation['id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> Lihat Hasil
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
        <div class="hero-image">
            <div class="image-placeholder">
                <i class="fas fa-heartbeat"></i>
                <h3>eFarmasi - RSUD Bukittinggi</h3>
                <p>Telefarmasi Modern</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title">Tentang eFarmasi</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>eFarmasi adalah platform telefarmasi inovatif yang menggabungkan teknologi sistem pakar dengan layanan konsultasi obat online. Kami menggunakan metode <strong>Forward Chaining</strong> untuk menganalisis gejala dan memberikan rekomendasi obat yang tepat.</p>
                    <div class="about-features">
                        <div class="feature-item">
                            <i class="fas fa-brain"></i>
                            <h3>Sistem Pakar Forward Chaining</h3>
                            <p>Menggunakan metode inferensi cerdas untuk analisis gejala berdasarkan aturan medis terverifikasi</p>
                        </div>
                        <div class="feature-item">
                            <i class="fab fa-whatsapp"></i>
                            <h3>Konsultasi dengan Apoteker</h3>
                            <p>Terintegrasi langsung dengan WhatsApp untuk konsultasi real-time dengan apoteker profesional</p>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-database"></i>
                            <h3>Database Obat Lengkap</h3>
                            <p>Rekomendasi obat berdasarkan evidence-based medicine dan formularium nasional</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title">Fitur Unggulan</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3>Sistem Pakar Cerdas</h3>
                    <p>Analisis gejala menggunakan metode Forward Chaining dengan database aturan medis terpercaya</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-prescription-bottle"></i>
                    </div>
                    <h3>Rekomendasi Obat Tepat</h3>
                    <p>Rekomendasi obat OTC yang tepat berdasarkan gejala, usia, dan kondisi kesehatan</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3>Konsultasi Apoteker</h3>
                    <p>Konsultasi langsung via WhatsApp dengan apoteker profesional 24/7</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>Riwayat Konsultasi</h3>
                    <p>Simpan dan kelola riwayat konsultasi kesehatan Anda secara aman dan privat</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Peringatan Interaksi</h3>
                    <p>Deteksi interaksi obat dan kontraindikasi berdasarkan riwayat pengobatan</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book-medical"></i>
                    </div>
                    <h3>Artikel Kesehatan</h3>
                    <p>Informasi kesehatan dan obat terpercaya yang ditulis oleh tenaga medis profesional</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">Apa Kata Pengguna?</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Sangat membantu! Sistem bisa memberikan rekomendasi obat yang tepat untuk demam anak saya. Apotekernya juga responsif banget di WhatsApp."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">SD</div>
                        <div class="author-info">
                            <h4>Sari Dewi</h4>
                            <p>Ibu Rumah Tangga, Jakarta</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Sebagai karyawan yang sibuk, fitur konsultasi online ini sangat praktis. Tidak perlu antri di apotek, langsung dapat rekomendasi yang akurat."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">BS</div>
                        <div class="author-info">
                            <h4>Budi Santoso</h4>
                            <p>Karyawan Swasta, Bandung</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Artikel-artikel kesehatannya sangat informatif. Membantu saya memahami cara penggunaan obat yang benar dan aman untuk keluarga."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">AW</div>
                        <div class="author-info">
                            <h4>Ahmad Wijaya</h4>
                            <p>Mahasiswa, Surabaya</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Consultation CTA -->
    <section id="consultation" class="consultation-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Siap Konsultasi Kesehatan Anda?</h2>
                <p>Dapatkan rekomendasi obat yang tepat dalam hitungan menit dengan sistem pakar kami</p>
                <?php if (isLoggedIn()): ?>
                    <a href="consultation.php" class="btn-primary btn-large">
                        <i class="fas fa-stethoscope"></i> Mulai Konsultasi Sekarang
                    </a>
                <?php else: ?>
                    <a href="register.html" class="btn-primary btn-large">
                        <i class="fas fa-user-plus"></i> Daftar dan Mulai Konsultasi
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>