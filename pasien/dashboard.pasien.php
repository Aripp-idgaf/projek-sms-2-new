<?php 
session_start();

// Mengecek apakah user sudah login dan benar-benar seorang pasien
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "pasien"){
    // Jika mencoba menyusup, tendang kembali ke halaman login
    header("location:../login/index.php?pesan=belum_login");
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien - MediFlow</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="pasien.css">

    <style>
        .menu-link-wrapper {
            text-decoration: none;
            color: inherit;
            display: block;
        }
    </style>
</head>
<body class="bg-pasien">
    <button class="btn-emergency shadow-lg" onclick="alert('Panggilan Darurat: 119')">
        <i class="bi bi-telephone-fill"></i>
    </button>

    <div class="ashley-container">
        <div class="ash-main" id="main-content-area">
            
            <div id="view-home" class="view-section">
                <div class="d-flex align-items-center mt-2 mb-3">
                    <i class="bi bi-heart-pulse fs-3 text-teal-mediflow me-2"></i>
                    <h4 class="fw-bold text-teal-mediflow mb-0 lh-1">MediFlow</h4>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #2c3e50;">Hello, <span id="ashNameTitle">Memuat...</span></h2>
                        <p class="text-muted small mb-0 fw-medium" id="realtime-datetime">Memuat waktu...</p>
                    </div>
                    
                    <div class="bg-white rounded-pill px-3 py-2 shadow-sm d-flex align-items-center" style="min-width: 260px; border: 1px solid rgba(0,0,0,0.05);">
                        <i class="bi bi-search me-2 text-muted"></i>
                        <input type="text" placeholder="Cari Dokter atau Poli..." class="border-0 bg-transparent w-100 text-dark search-input-header" style="font-size: 0.85rem; outline: none;">
                    </div>
                </div>

                <div class="row g-3 mb-2"> 
                    <div class="col-xl-6 col-md-12">
                        <div class="ash-card-wel shadow-sm position-relative p-4 h-100">
                            
                            <div class="wel-bg-shapes"></div>
                            
                            <div class="wel-text-container" style="width: 55%; z-index: 2; position: relative;">
                                <span class="small text-white opacity-75 mb-1 d-block">Welcome Back!</span>
                                <h4 class="fw-bold text-white mb-4 lh-base">Sudahkah Anda<br>Cek Kesehatan<br>Bulan Ini?</h4>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light btn-sm fw-bold rounded-pill px-4 py-2 shadow-sm" style="color: #117a8b;" onclick="switchView('view-jadwal')">
                                        <i class="bi bi-plus-lg me-1"></i> Buat Janji
                                    </button>
                                </div>
                            </div>
                            
                            <div class="doctor-container">
                                <div class="question-mark">?</div>
                                <img src="../wallpaper/rs7.png" class="wel-doctor-img" alt="Dokter">
                            </div>
                        </div>
                    </div> 

                    <div class="col-xl-6 col-md-12" id="queue-container-area">
                        <div class="bg-white rounded-4 shadow-sm h-100 p-4 d-flex flex-column justify-content-center align-items-center text-center" style="border: 2px dashed #d1e5e5;">
                            <div class="mb-3">
                                <i class="bi bi-calendar2-x text-muted" style="font-size: 3.5rem; opacity: 0.5;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-2">Belum Ada Jadwal Hari Ini</h6>
                            <p class="text-muted small mb-0" style="max-width: 85%;">Anda belum memiliki jadwal periksa. Klik menu <strong>'Jadwal'</strong> untuk mendaftar antrean.</p>
                        </div>
                    </div>
                </div>

                <div class="menu-cards-container">
                    <div class="menu-card" onclick="switchView('view-kamar')">
                        <div class="menu-icon-top"><i class="bi bi-hospital"></i></div>
                        <div class="menu-title">INFORMASI KAMAR</div>
                        <div class="menu-subtitle">Cek ketersediaan kamar.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>

                    <div class="menu-card" onclick="switchView('view-jadwal')">
                        <div class="menu-icon-top"><i class="bi bi-calendar4"></i></div>
                        <div class="menu-title">JADWAL</div>
                        <div class="menu-subtitle">Konfirmasi janji poli.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>

                    <div class="menu-card" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <div class="menu-icon-top"><i class="bi bi-gear"></i></div>
                        <div class="menu-title">SETTING</div>
                        <div class="menu-subtitle">Kelola preferensi akun.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-xl-12"> 
                        <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Riwayat Medis Terbaru</h6>
                                <a href="#" onclick="switchView('view-riwayat')" class="small text-info text-decoration-none fw-medium">Lihat Riwayat Lengkap</a>
                            </div>
                            
                            <div class="d-flex flex-column align-items-center justify-content-center text-center py-4 mt-2" style="border: 2px dashed #f0f0f0; border-radius: 15px; background-color: #fcfcfc;">
                                <i class="bi bi-folder-x text-muted mb-2" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                <h6 class="fw-bold text-dark mb-1 small">Belum Ada Riwayat</h6>
                                <p class="text-muted mb-0" style="font-size: 0.75rem; max-width: 80%;">Rekam medis akan otomatis muncul di sini setelah Anda selesai konsultasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            
            <div id="view-kamar" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Informasi Ketersediaan Kamar</h4>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm h-100 position-relative border-start border-5" style="border-color: #ffc107 !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="fw-bold">Kelas VIP</h5>
                                <i class="bi bi-star-fill text-warning fs-4"></i>
                            </div>
                            <p class="text-muted small mb-3">1 Pasien / Kamar. AC, TV, Kulkas, Sofa Bed, Kamar Mandi Dalam.</p>
                            <div class="d-flex justify-content-between align-items-end mt-auto">
                                <div><span class="small text-muted d-block">Tersedia</span><h4 class="fw-bold text-success mb-0">4 Kamar</h4></div>
                                <div class="text-end"><span class="small text-muted d-block">Tarif per hari</span><h6 class="fw-bold text-dark mb-0">Rp 1.250.000</h6></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm h-100 position-relative border-start border-5" style="border-color: #38c8e6 !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="fw-bold">Kelas 1</h5>
                                <i class="bi bi-hospital text-info fs-4"></i>
                            </div>
                            <p class="text-muted small mb-3">2 Pasien / Kamar. AC, TV Bersama, Kursi Penunggu, Kamar Mandi Dalam.</p>
                            <div class="d-flex justify-content-between align-items-end mt-auto">
                                <div><span class="small text-muted d-block">Tersedia</span><h4 class="fw-bold text-success mb-0">12 Bed</h4></div>
                                <div class="text-end"><span class="small text-muted d-block">Tarif per hari</span><h6 class="fw-bold text-dark mb-0">Rp 750.000</h6></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm h-100 position-relative border-start border-5" style="border-color: #28a745 !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="fw-bold">Kelas 2</h5>
                                <i class="bi bi-hospital text-success fs-4"></i>
                            </div>
                            <p class="text-muted small mb-3">4 Pasien / Kamar. AC, Kamar Mandi Dalam, Tirai Pemisah.</p>
                            <div class="d-flex justify-content-between align-items-end mt-auto">
                                <div><span class="small text-muted d-block">Tersedia</span><h4 class="fw-bold text-warning mb-0">5 Bed</h4></div>
                                <div class="text-end"><span class="small text-muted d-block">Tarif per hari</span><h6 class="fw-bold text-dark mb-0">Rp 450.000</h6></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm h-100 position-relative border-start border-5" style="border-color: #dc3545 !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="fw-bold">Kelas 3</h5>
                                <i class="bi bi-hospital text-danger fs-4"></i>
                            </div>
                            <p class="text-muted small mb-3">6 Pasien / Kamar. Kipas Angin/AC Sentral, Kamar Mandi Dalam.</p>
                            <div class="d-flex justify-content-between align-items-end mt-auto">
                                <div><span class="small text-muted d-block">Tersedia</span><h4 class="fw-bold text-danger mb-0">Penuh (0 Bed)</h4></div>
                                <div class="text-end"><span class="small text-muted d-block">Tarif per hari</span><h6 class="fw-bold text-dark mb-0">Rp 200.000</h6></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-jadwal" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Pendaftaran Janji Temu (Poli)</h4>
                </div>

                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Pilih Spesialisasi / Poliklinik</label>
                            
                            <div class="dropdown w-100">
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dropdown-btn text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textPoli">-- Silakan Pilih Poliklinik --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu">
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; document.getElementById('textPoli').classList.remove('text-muted'); document.getElementById('textPoli').classList.add('text-dark'); return false;">Poliklinik Umum</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; document.getElementById('textPoli').classList.remove('text-muted'); document.getElementById('textPoli').classList.add('text-dark'); return false;">Poliklinik Gigi & Mulut</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; document.getElementById('textPoli').classList.remove('text-muted'); document.getElementById('textPoli').classList.add('text-dark'); return false;">Poliklinik Anak (Pediatri)</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; document.getElementById('textPoli').classList.remove('text-muted'); document.getElementById('textPoli').classList.add('text-dark'); return false;">Poliklinik Penyakit Dalam</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; document.getElementById('textPoli').classList.remove('text-muted'); document.getElementById('textPoli').classList.add('text-dark'); return false;">Poliklinik Kandungan (Obgyn)</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; document.getElementById('textPoli').classList.remove('text-muted'); document.getElementById('textPoli').classList.add('text-dark'); return false;">Poliklinik Mata</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tanggal Rencana Periksa</label>
                            <input type="date" class="form-control rounded-pill border-2" style="padding: 12px 20px; font-size: 0.9rem; color: #2c3e50;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Pilih Dokter Jaga</label>
                            
                            <div class="dropdown w-100">
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dropdown-btn text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textDokter">-- Pilih Dokter --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu">
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textDokter').innerText=this.innerText; document.getElementById('textDokter').classList.remove('text-muted'); document.getElementById('textDokter').classList.add('text-dark'); return false;">Dr. Budi Santoso, Sp.PD</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textDokter').innerText=this.innerText; document.getElementById('textDokter').classList.remove('text-muted'); document.getElementById('textDokter').classList.add('text-dark'); return false;">Dr. Siti Aminah, Sp.A</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textDokter').innerText=this.innerText; document.getElementById('textDokter').classList.remove('text-muted'); document.getElementById('textDokter').classList.add('text-dark'); return false;">Drg. Andi Pratama</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Keluhan Utama (Singkat)</label>
                            <textarea class="form-control rounded-4 border-2" rows="3" placeholder="Deskripsikan keluhan Anda di sini..."></textarea>
                        </div>
                        <div class="col-md-12 mt-4">
                            <button class="btn w-100 rounded-pill py-3 fw-bold shadow-sm btn-teal" onclick="kirimJadwalAlert()">
                                Ajukan Janji Temu Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-riwayat" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Riwayat Medis Lengkap</h4>
                </div>

                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-8">
                        <div class="bg-white rounded-pill px-4 py-2 shadow-sm d-flex align-items-center border h-100" style="border: 1px solid rgba(0,0,0,0.05); min-height: 48px;">
                            <i class="bi bi-search me-2 text-muted"></i>
                            <input type="text" placeholder="Cari diagnosis, nama dokter, atau poliklinik..." class="border-0 bg-transparent w-100 text-dark search-input-header" style="font-size: 0.9rem; outline: none;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dropdown h-100">
                            <button class="btn bg-white border shadow-sm rounded-pill w-100 d-flex justify-content-between align-items-center custom-dropdown-btn text-dark fw-medium h-100" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 10px 20px; font-size: 0.9rem; min-height: 48px;">
                                <span id="textFilterWaktu">Semua Waktu</span>
                                <i class="bi bi-chevron-down text-teal-mediflow fw-bold"></i>
                            </button>
                            <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu">
                                <li><a class="dropdown-item custom-dropdown-item active" href="#" onclick="document.querySelectorAll('#view-riwayat .custom-dropdown-item').forEach(el=>el.classList.remove('active')); this.classList.add('active'); document.getElementById('textFilterWaktu').innerText=this.innerText; return false;">Semua Waktu</a></li>
                                <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.querySelectorAll('#view-riwayat .custom-dropdown-item').forEach(el=>el.classList.remove('active')); this.classList.add('active'); document.getElementById('textFilterWaktu').innerText=this.innerText; return false;">3 Bulan Terakhir</a></li>
                                <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.querySelectorAll('#view-riwayat .custom-dropdown-item').forEach(el=>el.classList.remove('active')); this.classList.add('active'); document.getElementById('textFilterWaktu').innerText=this.innerText; return false;">6 Bulan Terakhir</a></li>
                                <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.querySelectorAll('#view-riwayat .custom-dropdown-item').forEach(el=>el.classList.remove('active')); this.classList.add('active'); document.getElementById('textFilterWaktu').innerText=this.innerText; return false;">Tahun Ini</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <div class="border-bottom pb-4 mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill mb-2 px-3 py-1 small border border-success">Selesai</span>
                                <h6 class="fw-bold mb-1" style="color: #2c3e50;">Poliklinik Penyakit Dalam</h6>
                                <p class="text-muted small mb-0"><i class="bi bi-person me-1"></i> Dr. Budi Santoso, Sp.PD</p>
                            </div>
                            <div class="text-end">
                                <span class="d-block small fw-bold text-dark">15 Mei 2026</span>
                                <span class="text-muted" style="font-size: 0.75rem;">10:30 WIB</span>
                            </div>
                        </div>
                        <div class="bg-light p-3 rounded-3 mt-3 border" style="border-left: 4px solid var(--mediflow-blue) !important;">
                            <p class="small mb-1"><span class="fw-bold text-dark">Keluhan:</span> Demam tinggi lebih dari 3 hari, pusing, dan mual.</p>
                            <p class="small mb-1"><span class="fw-bold text-dark">Diagnosis:</span> Gejala Tifus (Tipes)</p>
                            <p class="small mb-0"><span class="fw-bold text-dark">Tindakan & Obat:</span> Cek darah lengkap, Paracetamol 500mg, Istirahat total.</p>
                        </div>
                    </div>

                    <div class="border-bottom pb-4 mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill mb-2 px-3 py-1 small border border-success">Selesai</span>
                                <h6 class="fw-bold mb-1" style="color: #2c3e50;">Poliklinik Gigi & Mulut</h6>
                                <p class="text-muted small mb-0"><i class="bi bi-person me-1"></i> Drg. Andi Pratama</p>
                            </div>
                            <div class="text-end">
                                <span class="d-block small fw-bold text-dark">02 Feb 2026</span>
                                <span class="text-muted" style="font-size: 0.75rem;">14:15 WIB</span>
                            </div>
                        </div>
                        <div class="bg-light p-3 rounded-3 mt-3 border" style="border-left: 4px solid var(--mediflow-blue) !important;">
                            <p class="small mb-1"><span class="fw-bold text-dark">Keluhan:</span> Gigi geraham bungsu terasa sakit saat mengunyah.</p>
                            <p class="small mb-1"><span class="fw-bold text-dark">Diagnosis:</span> Impaksi gigi M3 bawah kiri.</p>
                            <p class="small mb-0"><span class="fw-bold text-dark">Tindakan & Obat:</span> Observasi foto rontgen panoramik, Asam Mefenamat.</p>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary rounded-pill btn-sm px-4 fw-medium">Muat Lebih Banyak</button>
                    </div>
                </div>
            </div>
        </div> 
        
        <div class="ash-right position-relative" id="rightPanel">
            <button class="toggle-panel-btn" onclick="togglePanel()">
                <i class="bi bi-chevron-right fs-5"></i>
            </button>

            <button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 position-relative">
                <i class="bi bi-bell fs-5 text-muted"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>
            <div class="d-flex gap-2 bg-light p-1 rounded-4 mb-4 mt-2" id="familyTabsContainer">
                <div class="kk-tab active" onclick="switchProfileTab('Anak', this)">Utama</div>
                <div class="kk-tab d-none" id="tabAyah" onclick="switchProfileTab('Ayah', this)">Ayah</div>
                <div class="kk-tab d-none" id="tabIbu" onclick="switchProfileTab('Ibu', this)">Ibu</div>
            </div>
            <div class="text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-medium shadow-sm avatar-circle" style="width: 85px; height: 85px; background-color: #72b9b9; font-size: 2rem;">
                    --
                </div>
                <h5 class="fw-bold mb-0 text-dark mt-2" id="ashNameProfile">Memuat Profil...</h5>
                <p class="small text-muted mb-2" id="ashEmail">memuat email...</p>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mb-3">Pasien Aktif RS Hermina</span>
            </div>
            
            <div class="bg-light p-3 rounded-4 mb-4 small">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">No. RM</span><span class="fw-bold text-dark" id="ashRm">-</span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">NIK</span><span class="fw-bold text-dark" id="ashNik">-</span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">Umur</span><span class="fw-bold text-dark" id="ashUmur">-</span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">Alamat</span><span class="fw-bold text-dark text-end ms-4" id="ashAlamat">-</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">No. BPJS</span><span class="fw-bold text-dark" id="ashBpjs">-</span></div>
            </div>
            
            <div class="d-flex justify-content-between text-center mt-2 mb-4 bg-white p-3 rounded-4 border">
                <div><span class="small text-muted d-block">Blood</span><h6 class="fw-bold mb-0 text-info" id="ashBlood">-</h6></div>
                <div class="border-start border-end px-3"><span class="small text-muted d-block">Height</span><h6 class="fw-bold mb-0 text-dark" id="ashHeight">-</h6></div>
                <div><span class="small text-muted d-block">Weight</span><h6 class="fw-bold mb-0 text-dark" id="ashWeight">-</h6></div>
            </div>
            <div class="card border-0 rounded-4 p-3 bg-pasien shadow-sm mb-3 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-2 opacity-25"><i class="bi bi-wallet2 fs-1 text-success"></i></div>
                <h6 class="fw-bold small mb-3 text-dark position-relative"><i class="bi bi-receipt me-2"></i>Status Tagihan</h6>
                <div class="d-flex justify-content-between align-items-end mb-3 position-relative">
                    <div><span class="small text-muted d-block mb-1">Tagihan Pending</span><h4 class="fw-bold text-success mb-0" id="ashTagihan">Rp 0</h4></div>
                    <span class="badge bg-light text-success border border-success rounded-pill" id="ashTagihanBadge">Lunas</span>
                </div>
                <button class="btn btn-teal w-100 rounded-pill shadow-sm position-relative d-none" id="btnBayarTagihan">Bayar Sekarang</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 position-relative" style="border-radius: 30px; overflow: hidden;">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4 z-3" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;"></button>
                <div class="modal-body p-0">
                    <div class="d-flex flex-column flex-md-row" style="min-height: 550px;">
                        
                        <div class="p-4 d-flex flex-column" style="width: 100%; max-width: 250px; background: #f8fafb; border-right: 1px solid #eee;">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-gear-fill me-2 text-teal-mediflow"></i>Settings</h5>
                            <div class="nav flex-column nav-pills flex-grow-1" id="settings-tabs" role="tablist">
                                <button class="nav-link active small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-account" type="button" role="tab"><i class="bi bi-person-circle me-2"></i> Biodata Akun</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-family" type="button" role="tab" onclick="loadKeluargaSettings()"><i class="bi bi-people me-2"></i> Keluarga</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-payment" type="button" role="tab"><i class="bi bi-credit-card me-2"></i> Pembayaran</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-bpjs-set" type="button" role="tab"><i class="bi bi-shield-check me-2"></i> BPJS</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-privacy-set" type="button" role="tab"><i class="bi bi-lock me-2"></i> Privasi</button>
                            </div>
                            
                            <div class="mt-auto pt-3 border-top">
                                <button class="btn btn-settings-logout w-100 rounded-pill py-2 fw-bold small text-start px-3" onclick="logoutSession()">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </button>
                            </div>
                        </div>

                        <div class="p-4 p-md-5 flex-grow-1 bg-white" style="max-height: 600px; overflow-y: auto;">
                            <div class="tab-content" id="pills-tabContent">
                                
                                <div class="tab-pane fade show active" id="pills-account" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Biodata Akun</h6>
                                    <div class="mb-3"><label class="small fw-bold mb-1">Nama Lengkap</label><input type="text" id="settingsNameInput" class="form-control form-control-sm rounded-3" readonly style="background-color: #f0f0f0;"></div>
                                    <div class="mb-3"><label class="small fw-bold mb-1">Email Saat Ini</label><input type="email" id="settingsEmailInput" class="form-control form-control-sm rounded-3"></div>
                                    <div class="mb-3"><label class="small fw-bold mb-1">Ubah Password</label><input type="password" id="settingsPasswordInput" class="form-control form-control-sm rounded-3" placeholder="Masukkan password baru"></div>
                                    <button class="btn btn-teal w-100 mt-3 shadow-sm py-2" onclick="updateAuthData()">Simpan Perubahan Akun</button>
                                </div>

                                <div class="tab-pane fade" id="pills-family" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Manajemen Keluarga</h6>
                                    <p class="small fw-bold mb-2">Anggota Terdaftar:</p>
                                    <ul class="list-group list-group-flush mb-4" id="settingsFamilyList">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 small text-muted">Memuat data...</li>
                                    </ul>
                                    <button class="btn btn-outline-info w-100 rounded-pill small" onclick="tambahAnggotaKeluarga()">+ Tambah Anggota</button>
                                </div>

                                <div class="tab-pane fade" id="pills-payment" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Metode Pembayaran</h6>
                                    <div class="p-3 border rounded-4 mb-3 d-flex justify-content-between align-items-center bg-light" style="border: 2px solid #6cb7b7 !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-bank fs-3 me-3 text-teal-mediflow"></i>
                                            <div><h6 class="mb-0 small fw-bold">Virtual Account Umum</h6><span class="small text-muted">Akan dihasilkan otomatis</span></div>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    </div>
                                    <p class="text-muted small">Pembayaran akan disinkronkan secara otomatis berdasarkan riwayat janji temu Anda.</p>
                                </div>

                                <div class="tab-pane fade" id="pills-bpjs-set" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Layanan BPJS Kesehatan</h6>
                                    <div class="p-3 border rounded-4 mb-4">
                                        <div class="d-flex justify-content-between mb-2"><span class="small text-muted">Status Sinkronisasi</span><span class="badge bg-secondary rounded-pill" id="statusBpjs">Belum Terhubung</span></div>
                                        <input type="text" id="inputSetBpjs" class="form-control form-control-sm rounded-3 mt-2" placeholder="Masukkan Nomor BPJS (13 Digit)">
                                    </div>
                                    <button class="btn btn-teal w-100 text-white rounded-pill py-2 shadow-sm" onclick="simpanDataBPJS()">Sinkronkan BPJS Sekarang</button>
                                </div>

                                <div class="tab-pane fade" id="pills-privacy-set" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Perangkat & Keamanan</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div><h6 class="mb-0 small fw-bold">Perangkat Ini (Device Login)</h6><span class="text-muted small" id="deviceInfo">Mendeteksi perangkat...</span></div>
                                        <button class="btn btn-sm btn-outline-danger py-0" onclick="logoutSession()">Logout Perangkat</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    
    <script src="pasien.js?v=2"></script>
</body>
</html>