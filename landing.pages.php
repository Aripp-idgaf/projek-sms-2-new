<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediFlow - Layanan Kesehatan Premium</title>
    
    <link rel="stylesheet" href="pages.css?v=12">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

    <div class="top-bar">
        <div class="top-bar-content">
            <div class="tb-item"><i class="bi bi-envelope-fill"></i> info@mediflow.com</div>
            <div class="tb-item"><i class="bi bi-telephone-fill"></i> +62 811 2345 6789</div>
            <div class="tb-item"><i class="bi bi-geo-alt-fill"></i> Jl. Kesehatan No.12, Semarang</div>
        </div>
    </div>

    <nav class="navbar-modern">
        <div class="logo">
            <i class="bi bi-heart-pulse icon-med"></i>mediflow
        </div>
        <div class="nav-actions">
            <a href="./login/index.php" id="navAuthBtn" class="btn-login-outline">
                <i class="bi bi-person-circle" style="font-size: 1.2rem;"></i> Login / Register
            </a>
        </div>
    </nav>

    <header id="beranda" class="hero-booking">
        <div class="hero-bg-wrapper">
            <img src="wallpaper/rs17.jpeg" alt="Dokter Background" class="hero-bg-image">
            <div class="hero-fade-overlay"></div>
        </div>

        <div class="hero-content-booking reveal-up">
            <h1>Booking Jadwal<br>Dokter Lebih Cepat<br>& Pasti</h1>
            <p>Atur jadwal konsultasi, pilih dokter spesialis, dan dapatkan nomor antrean secara online. Semudah memesan tiket bioskop.</p>
            
            <div class="hero-cta-box" onclick="aksesLayanan('view-jadwal')">
                <div class="cta-text">
                    <h3>Cari Jadwal Dokter Spesialis</h3>
                </div>
                <div class="cta-btn-circle"><i class="bi bi-arrow-right"></i></div>
            </div>

            <div class="trust-badge">
                <div class="avatars">
                    <img src="wallpaper/rs4.jpg" alt="Pasien">
                    <img src="wallpaper/rs18.jpg" alt="Pasien">
                    <img src="wallpaper/rs19.jpg" alt="Pasien">
                </div>
                <span class="trust-text">Dipercaya 100k+ Pasien Aktif</span>
            </div>
        </div>

    </header>

    <section id="statement" class="statement-section">
        <div class="statement-content">
            <h2 class="reveal-up split-heading">
                <span class="split-line">
                    <span class="text-left">KAMI</span>
                    <span class="text-center-hidden">MEDIFLOW</span>
                    <span class="text-right">MEMBERIKAN</span>
                </span><br>
                KEMUDAHAN AKSES KESEHATAN<br>UNTUK ANDA DAN KELUARGA.
            </h2>
            <p class="reveal-up" style="transition-delay: 0.2s;">
                MediFlow adalah platform reservasi kesehatan terpadu. Kami menyediakan layanan 
                penjadwalan pintar untuk memastikan Anda mendapatkan penanganan medis yang presisi, 
                tanpa perlu membuang waktu berharga Anda di ruang tunggu.
            </p>
            <a href="#layanan" class="read-more-link reveal-up" style="transition-delay: 0.4s;">Selengkapnya</a>
        </div>
    </section>

    <section id="layanan" class="lunaria-services">
        <div class="lunaria-header reveal-up">
            <h2>LAYANAN KAMI</h2>
        </div>

        <div class="lunaria-grid">
            <div class="lunaria-card reveal-up" style="transition-delay: 0.1s;" onclick="aksesLayanan('view-jadwal')">
                <div class="l-img-wrapper">
                    <div class="img-mask">
                        <img src="wallpaper/rs4.jpg" alt="Jadwal Dokter">
                    </div>
                    <div class="l-card-icon"><i class="bi bi-calendar3"></i></div>
                </div>
                <div class="l-card-info text-center">
                    <h3>JADWAL DOKTER</h3>
                    <span>Pilih spesialis & atur waktu temu.</span>
                </div>
            </div>

            <div class="lunaria-card reveal-up" style="transition-delay: 0.3s;" onclick="aksesLayanan('view-jadwal')">
                <div class="l-img-wrapper">
                    <div class="img-mask">
                        <img src="wallpaper/rs3.jpg" alt="Daftar Online">
                    </div>
                    <div class="l-card-icon"><i class="bi bi-laptop"></i></div>
                </div>
                <div class="l-card-info text-center">
                    <h3>DAFTAR ONLINE</h3>
                    <span>Ambil nomor antrean dari rumah.</span>
                </div>
            </div>

            <div class="lunaria-card reveal-up" style="transition-delay: 0.5s;" onclick="aksesLayanan('view-kamar')">
                <div class="l-img-wrapper">
                    <div class="img-mask">
                        <img src="wallpaper/rs19.jpg" alt="Kamar Rawat">
                    </div>
                    <div class="l-card-icon"><i class="bi bi-hospital"></i></div>
                </div>
                <div class="l-card-info text-center">
                    <h3>INFORMASI KAMAR</h3>
                    <span>Cek ketersediaan tempat tidur RS.</span>
                </div>
            </div>
        </div>
    </section>

    <section id="dokter-unggulan" class="featured-doctor-section reveal-up">
        <div class="welcome-banner">
            <div class="banner-bg-decor">
                <div class="decor-circle"></div>
                <div class="decor-shape"></div>
            </div>
            
            <div class="banner-text">
                <h2 class="banner-doc-name">Halo, Dr. Cae Soo Bin, Sp.PD!</h2>
                <p class="banner-doc-desc" style="margin-bottom: 0;">
                    Menjabat sebagai Dokter Kepala & Spesialis Penyakit Dalam di RS MediFlow. 
                    Berdedikasi penuh untuk memastikan Anda mendapatkan perawatan medis berstandar internasional dan pelayanan kesehatan terbaik setiap harinya.
                </p>
            </div>

            <div class="hero-doctor-wrapper">
                <div class="bubble-hallo">Siap Melayani Anda!</div>
                <img src="wallpaper/rs14.png" alt="Dr. Cae Soo Bin" class="wel-doctor-img">
            </div>
        </div>
    </section>

    <section id="dokter-profil" class="doctor-section">
        <div class="doctor-container">
            
            <div class="doctor-info-col reveal-up">
                <div class="section-badge"><i class="bi bi-heart-pulse"></i> Tim Medis Kami</div>
                <h2>Dokter Ahli Bersertifikasi</h2>
                <p class="doctor-subtitle">— Menghadirkan solusi medis profesional dengan pendekatan modern yang berfokus pada ketepatan dan kepedulian pasien.</p>

                <div class="doctor-accordion">
                    <div class="accordion-item active" data-img="wallpaper/rs4.jpg" data-badge="Tersertifikasi Dokter Anak">
                        <div class="accordion-header">
                            <h4>Dr. Kyla Salsabila, Sp.A</h4>
                            <i class="bi bi-dash"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Menjabat sebagai Dokter Spesialis Anak di RS MediFlow. Memastikan tumbuh kembang anak Anda terpantau dengan penanganan modern dan lingkungan yang ramah anak.</p>
                        </div>
                    </div>

                    <div class="accordion-item" data-img="wallpaper/rs18.jpg" data-badge="Tersertifikasi Spesialis Mata">
                        <div class="accordion-header">
                            <h4>Dr. Bima Anggara, Sp.M</h4>
                            <i class="bi bi-plus"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Menjabat sebagai Dokter Spesialis Mata (Oftalmologi) di RS MediFlow. Berdedikasi memberikan perawatan optik terbaik untuk menjaga kesehatan dan ketajaman visual Anda.</p>
                        </div>
                    </div>

                    <div class="accordion-item" data-img="wallpaper/rs19.jpg" data-badge="Tersertifikasi Penyakit Dalam">
                        <div class="accordion-header">
                            <h4>Dr. Nabila Putri, Sp.PD</h4>
                            <i class="bi bi-plus"></i>
                        </div>
                        <div class="accordion-body">
                            <p>Menjabat sebagai Dokter Spesialis Penyakit Dalam di RS MediFlow. Fokus pada diagnosa presisi dan perawatan komprehensif bagi pasien dengan keluhan medis mendalam.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="doctor-image-col reveal-up" style="transition-delay: 0.3s;">
                <div class="doc-img-wrapper">
                    <img src="wallpaper/rs1.jpg" alt="Profil Dokter" id="doc-display-img">
                    <div class="doc-badge" id="doc-badge-text">
                        <i class="bi bi-check-circle"></i> Tersertifikasi Dokter Anak
                    </div>
                    <button class="doc-action-btn" onclick="aksesLayanan('view-jadwal')">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </section>

    <footer class="footer-hero">
        <div class="footer-hero-content">
            <div class="fh-text reveal-up">
                <h2>Kesehatan Anda,<br>Prioritas Kami.</h2>
                <div class="btn-footer-wrapper mt-4">
                    <button class="btn-footer" onclick="aksesLayanan('view-home')">
                        <span class="btn-footer-text">Akses Dashboard</span>
                        <div class="btn-footer-icon-circle"><i class="bi bi-arrow-right"></i></div>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom-links reveal-up">
            <div class="f-col">
                <div class="logo footer-logo">
                    <i class="bi bi-heart-pulse icon-med"></i> MediFlow
                </div>
                <p class="text-sm">Platform reservasi dan manajemen klinik digital terpercaya.</p>
            </div>
            <div class="f-col">
                <h4>Perusahaan</h4>
                <a href="#">Tentang Kami</a>
                <a href="#">Karir</a>
            </div>
            <div class="f-col">
                <h4>Bantuan</h4>
                <a href="#">Cara Booking</a>
                <a href="#">Integrasi BPJS</a>
            </div>
        </div>
    </footer>

    <script src="pages.js?v=11"></script>
</body>
</html>