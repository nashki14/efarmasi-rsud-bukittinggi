    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Brand Section -->
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="footer-brand-info">
                            <h3>eFarmasi - RSUD Bukittinggi</h3>
                            <p>Platform Konsultasi Obat Online</p>
                        </div>
                    </div>
                    <p class="footer-description">
                        Platform telefarmasi pertama di Indonesia yang menggunakan sistem pakar forward chaining untuk memberikan rekomendasi obat yang tepat dan personal.
                    </p>
                    <div class="footer-contact">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@eFarmasi.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+62 857 1737 9709</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Bukittinggi, Indonesia</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-section">
                    <h4>Menu Utama</h4>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Beranda</a></li>
                        <li><a href="about.html"><i class="fas fa-info-circle"></i> Tentang Kami</a></li>
                        <li><a href="articles.html"><i class="fas fa-newspaper"></i> Artikel Kesehatan</a></li>
                        <li><a href="consultation.php"><i class="fas fa-stethoscope"></i> Konsultasi Obat</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Services -->
                <div class="footer-section">
                    <h4>Layanan Kami</h4>
                    <ul class="footer-links">
                        <li><a href="consultation.php"><i class="fas fa-robot"></i> Sistem Pakar Obat</a></li>
                        <li><a href="#" onclick="startWhatsAppConsultation()"><i class="fab fa-whatsapp"></i> Konsultasi Apoteker</a></li>
                        <li><a href="articles.html"><i class="fas fa-book-medical"></i> Edukasi Obat</a></li>
                        <li><a href="dashboard.php"><i class="fas fa-history"></i> Riwayat Konsultasi</a></li>
                    </ul>
                </div>
                    
                <ul class="social-links">
                    <h3>Follow Kami</h3> 
                    <ul class="footer-links">
                    <div class="social-icons">
                        <a href="https://wa.me/6285717379709" class="social-link whatsapp" target="_blank" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://facebook.com" class="social-link facebook" target="_blank" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://instagram.com" class="social-link instagram" target="_blank" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://twitter.com" class="social-link twitter" target="_blank" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                        </ul>
                </div>
            </div>
        </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2026 eFarmasi. All rights reserved. | Platform Konsultasi Obat Online</p>
                    <div class="footer-bottom-links">
                        <a href="privacy.html">Kebijakan Privasi</a>
                        <a href="terms.html">Syarat & Ketentuan</a>
                        <a href="disclaimer.html">Disclaimer</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>