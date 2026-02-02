// Articles Management
let allArticles = [];
let filteredArticles = [];
let currentCategory = 'all';
let searchTerm = '';

document.addEventListener('DOMContentLoaded', function() {
    loadArticles();
    setupEventListeners();
});

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function(e) {
        searchTerm = e.target.value.toLowerCase();
        filterArticles();
    });

    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentCategory = this.dataset.category;
            filterArticles();
        });
    });
}

function loadArticles() {
    showLoading(true);
    
    // Simulate API call
    setTimeout(() => {
        // Sample articles data
        allArticles = [
            {
                id: 1,
                title: 'Cara Mengatasi Demam di Rumah',
                excerpt: 'Demam adalah kondisi ketika suhu tubuh meningkat di atas normal. Pelajari cara mengatasi demam dengan tepat di rumah.',
                category: 'Umum',
                author: 'Dr. Ahmad Wijaya',
                date: '2024-01-15',
                content: `
                    <h2>Memahami Demam</h2>
                    <p>Demam adalah mekanisme pertahanan tubuh terhadap infeksi. Suhu tubuh normal berkisar antara 36.5°C hingga 37.5°C.</p>
                    
                    <h3>Penyebab Demam</h3>
                    <ul>
                        <li>Infeksi virus atau bakteri</li>
                        <li>Dehidrasi</li>
                        <li>Imunisasi</li>
                        <li>Penyakit autoimun</li>
                    </ul>
                    
                    <h3>Penanganan di Rumah</h3>
                    <p>Berikut langkah-langkah yang dapat dilakukan:</p>
                    <ol>
                        <li>Istirahat yang cukup</li>
                        <li>Minum air putih yang banyak</li>
                        <li>Kompres dengan air hangat</li>
                        <li>Gunakan pakaian yang nyaman</li>
                    </ol>
                    
                    <blockquote>
                        Jika demam berlangsung lebih dari 3 hari atau suhu melebihi 39°C, segera konsultasi ke dokter.
                    </blockquote>
                `
            },
            {
                id: 2,
                title: 'Memahami Jenis Batuk dan Penanganannya',
                excerpt: 'Batuk merupakan mekanisme pertahanan tubuh. Kenali jenis batuk dan penanganan yang tepat.',
                category: 'Pernapasan',
                author: 'Apt. Maria Santoso',
                date: '2024-01-12',
                content: `
                    <h2>Memahami Jenis Batuk dan Penanganannya yang Tepat</h2>
                    <p>Batuk adalah refleks alami tubuh untuk membersihkan saluran pernapasan dari iritan, lendir, atau benda asing. Meski sering dianggap sepele, batuk bisa menjadi gejala dari berbagai kondisi kesehatan. Mengenali jenis batuk adalah langkah pertama untuk melakukan penanganan yang tepat.</p>
                    
                    <h3>Kategori Berdasarkan Durasi</h3>
                    <ul>
                        <li><strong>Batuk Akut:</strong> Berlangsung kurang dari 3 minggu. Biasanya disebabkan oleh infeksi seperti pilek atau flu.</li>
                        <li><strong>Batuk Sub-Akut:</strong> Berlangsung 3 hingga 8 minggu. Sering terjadi setelah infeksi saluran pernapasan.</li>
                        <li><strong>Batuk Kronis:</strong> Berlangsung lebih dari 8 minggu. Dapat mengindikasikan kondisi medis yang perlu pemeriksaan lebih lanjut.</li>
                    </ul>
                    
                    <h3>Jenis-Jenis Batuk dan Penanganannya</h3>
                    <p>Berikut adalah jenis batuk berdasarkan gejalanya:</p>
                    
                    <h4>1. Batuk Berdahak (Batuk Produktif)</h4>
                    <p>Batuk jenis ini menghasilkan dahak atau lendir dari saluran pernapasan.</p>
                    <ul>
                        <li><strong>Ciri-ciri:</strong> Terdengar berat dan bergemericik, disertai sensasi ada yang mengganjal di dada.</li>
                        <li><strong>Penyebab Umum:</strong> Infeksi (pilek, flu, bronkitis), penyakit paru kronis (PPOK).</li>
                        <li><strong>Penanganan:</strong>
                            <ul>
                                <li>Gunakan obat <strong>ekspektoran</strong> yang mengandung Guaifenesin</li>
                                <li>Perbanyak minum air putih untuk mengencerkan dahak</li>
                                <li>Menghirup uap hangat dengan minyak kayu putih</li>
                                <li>Hindari penekan batuk karena dapat membuat infeksi tertahan</li>
                            </ul>
                        </li>
                    </ul>
                    
                    <h4>2. Batuk Kering (Batuk Tidak Produktif)</h4>
                    <p>Batuk ini tidak menghasilkan dahak dan sering terasa menggelitik di tenggorokan.</p>
                    <ul>
                        <li><strong>Ciri-ciri:</strong> Suara batuknya keras dan menggonggong, terasa kering dan gatal.</li>
                        <li><strong>Penyebab Umum:</strong> Iritasi (asap rokok, debu), infeksi virus, asma, GERD.</li>
                        <li><strong>Penanganan:</strong>
                            <ul>
                                <li>Gunakan obat <strong>antitusif</strong> yang mengandung Dextromethorphan</li>
                                <li>Konsumsi madu dan lemon untuk menenangkan tenggorokan</li>
                                <li>Gunakan lozenges (permen batuk)</li>
                                <li>Hindari pemicu iritan seperti asap rokok</li>
                            </ul>
                        </li>
                    </ul>

                    <h3>Kapan Harus ke Dokter?</h3>
                    <p>Segera konsultasi ke dokter jika batuk disertai dengan:</p>
                    <ul>
                        <li>Demam tinggi yang tidak kunjung turun</li>
                        <li>Sesak napas atau nyeri dada</li>
                        <li>Dahak berwarna hijau, kuning pekat, atau berdarah</li>
                        <li>Batuk berlangsung lebih dari 3 minggu tanpa perbaikan</li>
                        <li>Penurunan berat badan tanpa sebab yang jelas</li>
                    </ul>

                    <blockquote>
                        <p><strong>Peringatan:</strong> Artikel ini hanya untuk tujuan informasi. Konsultasikan dengan tenaga medis profesional untuk diagnosis dan pengobatan yang tepat.</p>
                    </blockquote>
                `
            },
            {
                id: 3,
                title: 'Panduan Penggunaan Obat yang Aman',
                excerpt: 'Penggunaan obat yang tepat sangat penting untuk kesembuhan. Pelajari cara menggunakan obat dengan benar.',
                category: 'Edukasi',
                author: 'Dr. Sari Dewi',
                date: '2024-01-10',
                content: `
                    <h2>Panduan Penggunaan Obat yang Aman</h2>
                    <p>Penggunaan obat yang tepat merupakan kunci keberhasilan pengobatan dan pencegahan efek samping yang tidak diinginkan.</p>
                    
                    <h3>Prinsip Dasar Penggunaan Obat</h3>
                    <ul>
                        <li><strong>5 Tepat:</strong> Tepat pasien, tepat obat, tepat dosis, tepat cara, tepat waktu</li>
                        <li><strong>Baca Label:</strong> Selalu baca informasi pada kemasan obat</li>
                        <li><strong>Ikuti Anjuran:</strong> Patuhi dosis dan durasi penggunaan</li>
                        <li><strong>Konsultasi:</strong> Tanyakan pada apoteker jika ragu</li>
                    </ul>
                    
                    <h3>Jenis-Jenis Obat dan Cara Penggunaan</h3>
                    
                    <h4>1. Obat Oral (Diminum)</h4>
                    <ul>
                        <li>Tablet dan Kapsul: Telan utuh dengan air putih</li>
                        <li>Sirup: Kocok dahulu, gunakan takaran yang tersedia</li>
                        <li>Bubuk: Larutkan dalam air sesuai petunjuk</li>
                    </ul>
                    
                    <h4>2. Obat Topikal (Luar)</h4>
                    <ul>
                        <li>Krim/Salep: Oleskan tipis-tipis pada area bersih</li>
                        <li>Obat Tetes: Ikuti petunjuk jumlah tetes</li>
                        <li>Semprot: Jaga kebersihan nozzle</li>
                    </ul>
                    
                    <h3>Hal yang Harus Dihindari</h3>
                    <ul>
                        <li>Jangan menggandakan dosis jika lupa minum obat</li>
                        <li>Jangan menghentikan obat tanpa konsultasi dokter</li>
                        <li>Jangan membagi obat dengan orang lain</li>
                        <li>Jangan menyimpan obat di tempat lembab</li>
                    </ul>
                    
                    <blockquote>
                        <p>Selalu simpan obat di tempat yang aman, jauh dari jangkauan anak-anak dan hewan peliharaan.</p>
                    </blockquote>
                `
            },
            {
                id: 4,
                title: 'Mengatasi Diare pada Anak dan Dewasa',
                excerpt: 'Diare dapat menyebabkan dehidrasi. Ketahui cara penanganan yang tepat untuk berbagai usia.',
                category: 'Pencernaan',
                author: 'Dr. Budi Santoso',
                date: '2024-01-08',
                content: `
                    <h2>Mengatasi Diare pada Anak dan Dewasa</h2>
                    <p>Diare adalah kondisi dimana frekuensi buang air besar meningkat dengan konsistensi tinja yang cair. Penanganan yang tepat dapat mencegah dehidrasi.</p>
                    
                    <h3>Penyebab Diare</h3>
                    <ul>
                        <li>Infeksi bakteri atau virus</li>
                        <li>Keracunan makanan</li>
                        <li>Alergi makanan</li>
                        <li>Efek samping obat</li>
                        <li>Stres dan kecemasan</li>
                    </ul>
                    
                    <h3>Penanganan di Rumah</h3>
                    
                    <h4>Untuk Dewasa:</h4>
                    <ul>
                        <li>Minum oralit atau cairan elektrolit</li>
                        <li>Konsumsi makanan hambar (bubur, pisang, nasi)</li>
                        <li>Hindari makanan pedas dan berlemak</li>
                        <li>Istirahat yang cukup</li>
                    </ul>
                    
                    <h4>Untuk Anak:</h4>
                    <ul>
                        <li>Lanjutkan pemberian ASI atau susu formula</li>
                        <li>Berikan oralit khusus anak</li>
                        <li>Hindari jus buah dan minuman bersoda</li>
                        <li>Pantau tanda-tanda dehidrasi</li>
                    </ul>
                    
                    <h3>Tanda Dehidrasi yang Perlu Diwaspadai</h3>
                    <ul>
                        <li>Mulut dan lidah kering</li>
                        <li>Mata cekung</li>
                        <li>Frekuensi buang air kecil berkurang</li>
                        <li>Lesu dan lemas</li>
                        <li>Pada bayi: ubun-ubun cekung</li>
                    </ul>
                    
                    <blockquote>
                        <p>Segera bawa ke dokter jika diare disertai darah, demam tinggi, atau tanda dehidrasi berat.</p>
                    </blockquote>
                `
            },
            {
                id: 5,
                title: 'Tips Menjaga Kesehatan di Musim Hujan',
                excerpt: 'Musim hujan meningkatkan risiko penyakit. Lakukan pencegahan dengan tips berikut.',
                category: 'Umum',
                author: 'Dr. Anita Sari',
                date: '2024-01-05',
                content: `
                    <h2>Tips Menjaga Kesehatan di Musim Hujan</h2>
                    <p>Musim hujan membawa tantangan kesehatan tersendiri. Dengan persiapan yang tepat, kita dapat terhindar dari berbagai penyakit.</p>
                    
                    <h3>Penyakit yang Sering Muncul di Musim Hujan</h3>
                    <ul>
                        <li>Influenza dan common cold</li>
                        <li>Demam berdarah dengue (DBD)</li>
                        <li>Leptospirosis (kencing tikus)</li>
                        <li>Diare dan tifus</li>
                        <li>Infeksi kulit</li>
                    </ul>
                    
                    <h3>Tips Pencegahan</h3>
                    
                    <h4>1. Tingkatkan Daya Tahan Tubuh</h4>
                    <ul>
                        <li>Konsumsi makanan bergizi seimbang</li>
                        <li>Perbanyak vitamin C dan D</li>
                        <li>Tidur yang cukup 7-8 jam per hari</li>
                        <li>Olahraga rutin meski di dalam ruangan</li>
                    </ul>
                    
                    <h4>2. Jaga Kebersihan Lingkungan</h4>
                    <ul>
                        <li>Bersihkan genangan air sekitar rumah</li>
                        <li>Pastikan ventilasi udara cukup</li>
                        <li>Gunakan alas kaki saat keluar rumah</li>
                        <li>Buang sampah pada tempatnya</li>
                    </ul>
                    
                    <h4>3. Perlindungan Diri</h4>
                    <ul>
                        <li>Sedia payung atau jas hujan</li>
                        <li>Ganti pakaian basah segera</li>
                        <li>Konsumsi makanan dan minuman hangat</li>
                        <li>Cuci tangan dengan sabun secara rutin</li>
                    </ul>
                    
                    <blockquote>
                        <p>Siapkan selalu kotak P3K di rumah dan pastikan obat-obatan dasar tersedia untuk mengantisipasi keadaan darurat.</p>
                    </blockquote>
                `
            },
            {
                id: 6,
                title: 'Memahami Antibiotik dan Penggunaannya',
                excerpt: 'Antibiotik harus digunakan dengan tepat untuk menghindari resistensi. Pelajari aturan penggunaannya.',
                category: 'Edukasi',
                author: 'Apt. Rina Wijaya',
                date: '2024-01-03',
                content: `
                    <h2>Memahami Antibiotik dan Penggunaannya</h2>
                    <p>Antibiotik adalah obat yang digunakan untuk mengobati infeksi bakteri. Penggunaan yang tidak tepat dapat menyebabkan resistensi antibiotik.</p>
                    
                    <h3>Kapan Antibiotik Diperlukan?</h3>
                    <ul>
                        <li>Infeksi bakteri yang terbukti</li>
                        <li>Radang tenggorokan karena bakteri Streptococcus</li>
                        <li>Infeksi saluran kemih</li>
                        <li>Pneumonia bakteri</li>
                        <li>Infeksi kulit karena bakteri</li>
                    </ul>
                    
                    <h3>Kapan Antibiotik Tidak Diperlukan?</h3>
                    <ul>
                        <li>Common cold (pilek biasa)</li>
                        <li>Flu (influenza)</li>
                        <li>Kebanyakan batuk dan bronkitis</li>
                        <li>Infeksi virus lainnya</li>
                    </ul>
                    
                    <h3>Aturan Penggunaan Antibiotik</h3>
                    <ul>
                        <li><strong>Habiskan:</strong> Minum antibiotik sampai habis meski gejala sudah membaik</li>
                        <li><strong>Tepat Waktu:</strong> Minum sesuai jadwal yang ditentukan</li>
                        <li><strong>Tepat Dosis:</strong> Ikuti dosis yang diresepkan dokter</li>
                        <li><strong>Jangan Berbagi:</strong> Jangan berikan antibiotik Anda kepada orang lain</li>
                    </ul>
                    
                    <h3>Efek Samping yang Mungkin Terjadi</h3>
                    <ul>
                        <li>Gangguan pencernaan (mual, diare)</li>
                        <li>Reaksi alergi</li>
                        <li>Kandidiasis (infeksi jamur)</li>
                        <li>Fotosensitivitas</li>
                    </ul>
                    
                    <blockquote>
                        <p><strong>Peringatan:</strong> Resistensi antibiotik adalah ancaman global. Gunakan antibiotik hanya dengan resep dokter dan sesuai indikasi.</p>
                    </blockquote>
                `
            }
        ];

        filteredArticles = [...allArticles];
        renderArticles();
        showLoading(false);
    }, 1000);
}

function filterArticles() {
    filteredArticles = allArticles.filter(article => {
        const matchesSearch = article.title.toLowerCase().includes(searchTerm) ||
                            article.excerpt.toLowerCase().includes(searchTerm) ||
                            article.author.toLowerCase().includes(searchTerm);
        
        const matchesCategory = currentCategory === 'all' || article.category === currentCategory;
        
        return matchesSearch && matchesCategory;
    });
    
    renderArticles();
}

function renderArticles() {
    const grid = document.getElementById('articlesGrid');
    const noResults = document.getElementById('noResults');
    
    if (filteredArticles.length === 0) {
        grid.innerHTML = '';
        noResults.style.display = 'block';
        return;
    }
    
    noResults.style.display = 'none';
    
    grid.innerHTML = filteredArticles.map(article => `
        <div class="article-card" onclick="openArticle(${article.id})">
            <div class="article-image">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div class="article-content">
                <span class="article-category">${article.category}</span>
                <h3 class="article-title">${article.title}</h3>
                <p class="article-excerpt">${article.excerpt}</p>
                <div class="article-meta">
                    <div class="article-author">
                        <i class="fas fa-user"></i>
                        ${article.author}
                    </div>
                    <div class="article-date">
                        <i class="fas fa-calendar"></i>
                        ${formatDate(article.date)}
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function openArticle(articleId) {
    const article = allArticles.find(a => a.id === articleId);
    if (article) {
        // Create article detail page URL
        const url = `article-detail.html?id=${articleId}&title=${encodeURIComponent(article.title)}&category=${encodeURIComponent(article.category)}&author=${encodeURIComponent(article.author)}&date=${article.date}&content=${encodeURIComponent(article.content)}`;
        window.location.href = url;
    }
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function showLoading(show) {
    const loadingState = document.getElementById('loadingState');
    const grid = document.getElementById('articlesGrid');
    
    if (show) {
        loadingState.style.display = 'block';
        grid.style.display = 'none';
    } else {
        loadingState.style.display = 'none';
        grid.style.display = 'grid';
    }
}

// Function to handle article detail page
function loadArticleDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const articleId = urlParams.get('id');
    
    if (articleId && window.location.pathname.includes('article-detail.html')) {
        // In a real application, you would fetch the article from the server
        // For now, we'll use the URL parameters to display the article
        displayArticleDetail({
            id: articleId,
            title: urlParams.get('title'),
            category: urlParams.get('category'),
            author: urlParams.get('author'),
            date: urlParams.get('date'),
            content: decodeURIComponent(urlParams.get('content') || '')
        });
    }
}

function displayArticleDetail(article) {
    document.title = `${article.title} - PHARMEDICE`;
    
    const container = document.querySelector('.article-detail');
    if (container) {
        container.innerHTML = `
            <article>
                <header class="article-detail-header">
                    <span class="article-detail-category">${article.category}</span>
                    <h1 class="article-detail-title">${article.title}</h1>
                    <div class="article-detail-meta">
                        <span><i class="fas fa-user"></i> ${article.author}</span>
                        <span><i class="fas fa-calendar"></i> ${formatDate(article.date)}</span>
                    </div>
                </header>
                <div class="article-detail-content">
                    ${article.content}
                </div>
            </article>
        `;
    }
}

// Initialize article detail page if needed
if (window.location.pathname.includes('article-detail.html')) {
    loadArticleDetail();
}