<?php 
session_start();
if(!isset($_SESSION['status']) || $_SESSION['role'] != "dokter"){
    header("location:../login/index.php?pesan=belum_login");
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter - MediFlow Schedule</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="dokter.css" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        
        <header class="top-navbar">
            <div class="navbar-brand-logo" onclick="switchView('view-dashboard')">
                <i class="bi bi-heart-pulse-fill fs-2"></i> MediFlow
            </div>
            
            <div class="navbar-center-info">
                <i class="bi bi-heart-pulse-fill fs-5" style="color: var(--primary-teal);"></i> 
                <span class="text-dark ms-1 fw-bolder" style="letter-spacing: 1.5px;">MEDIFLOW</span> 
                <span class="text-muted fw-bold">PORTAL</span>
            </div>

            <div class="navbar-right-controls">
                <div class="search-box shadow-sm">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" placeholder="Cari ID/Nama Pasien...">
                </div>
                
                <div class="control-pill shadow-sm">
                    <i class="bi bi-clock text-primary"></i>
                    <span id="realtime-clock">--:-- WIB</span>
                </div>

                <div class="icon-circle shadow-sm" title="Notifikasi" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 5px; margin-left: -5px;"></span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-notif mt-2 shadow border-0">
                    <li class="text-white p-3 fw-bold rounded-top" style="background-color: var(--primary-teal);">Notifikasi Masuk</li>
                    <li class="notif-item p-3 d-flex gap-3 border-bottom">
                        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle h-100"><i class="bi bi-calendar-x-fill"></i></div>
                        <div><h6 class="small fw-bold mb-1 text-dark">Pasien Batal Janji</h6><p class="mb-0 text-muted" style="font-size:0.7rem;">Budi S. membatalkan sesi jam 11:00.</p></div>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center bg-white p-1 pe-3 rounded-pill border shadow-sm cursor-pointer" onclick="switchView('view-settings')">
                    <img src="../wallpaper/rs14.png" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;" alt="Dr. Cae Soo Bin" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3304/3304567.png'; this.style.opacity='0.9';">
                    <span class="fw-bold small text-dark ms-1">Dr. Cae Soo Bin</span>
                </div>
            </div>
        </header>

        <main class="main-content">
            
            <div id="view-dashboard" class="view-section">
                <div class="welcome-banner">
                    <div class="banner-bg-decor">
                        <div class="decor-shape"></div>
                        <div class="decor-circle"></div>
                    </div>
                    <div class="banner-text">
                        <div class="banner-badge" id="dynamic-banner-date"><i class="bi bi-calendar-check me-2"></i>--</div>
                        <h2 class="fw-bold mb-1 fs-1">Hello, Dr. Cae Soo Bin!</h2>
                        <p class="mb-0 opacity-75">You have 5 appointments scheduled today.</p>
                        <div class="mt-4 pt-3 border-top border-light border-opacity-25 d-flex align-items-center gap-4">
                            <span class="text-white fw-bold d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                <span class="pulse-dot-bright me-1"></span> Sistem Sinkronisasi: Aktif
                            </span>
                            <span class="text-white fw-bold d-flex align-items-center gap-2" style="font-size: 0.85rem;" id="dynamic-banner-location-date">
                                <i class="bi bi-cloud-sun fs-5"></i> Semarang, -- | Sesi --
                            </span>
                        </div>
                    </div>
                    <div class="hero-doctor-wrapper" id="hero-doctor-trigger">
                        <div class="bubble-hallo" id="welcome-bubble">Hallo, Dokter Cae Soo Bin 👋</div>
                        <img src="../wallpaper/rs14.png" class="wel-doctor-img" id="doctor-image" alt="Doctor" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3304/3304567.png'; this.style.opacity='0.9'; this.style.filter='none';">
                    </div>
                </div>

                <div class="d-flex flex-wrap nav-card-wrapper">
                    <div class="nav-card-col">
                        <div class="portal-nav-card theme-teal" onclick="switchView('view-pasien')">
                            <div class="card-top-icon"><i class="bi bi-people-fill"></i></div>
                            <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.95rem; letter-spacing: 0.5px;">DATABASE PASIEN</h5>
                            <p class="text-muted small mb-3" style="font-size:0.7rem;">Kelola rekam medis.</p>
                            <span class="badge rounded-pill bg-light border px-3 py-1 text-info fw-bold mb-2">5 Pasien Hari Ini</span>
                            <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                        </div>
                    </div>
                    <div class="nav-card-col">
                        <div class="portal-nav-card theme-teal" onclick="switchView('view-jadwal')">
                            <div class="card-top-icon"><i class="bi bi-calendar-week-fill"></i></div>
                            <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.95rem; letter-spacing: 0.5px;">MANAJEMEN JADWAL</h5>
                            <p class="text-muted small mb-3" style="font-size:0.7rem;">Konfirmasi janji poli.</p>
                            <span class="badge rounded-pill bg-light border px-3 py-1 text-info fw-bold mb-2">84 Antrean</span>
                            <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                        </div>
                    </div>
                    <div class="nav-card-col">
                        <div class="portal-nav-card theme-teal" onclick="switchView('view-riwayat')">
                            <div class="card-top-icon"><i class="bi bi-clock-history"></i></div>
                            <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.95rem; letter-spacing: 0.5px;">RIWAYAT MEDIS</h5>
                            <p class="text-muted small mb-3" style="font-size:0.7rem;">Arsip & resep lampau.</p>
                            <span class="badge rounded-pill bg-light border px-3 py-1 text-info fw-bold mb-2">Database RS</span>
                            <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                        </div>
                    </div>
                    <div class="nav-card-col">
                        <div class="portal-nav-card theme-teal" onclick="switchView('view-settings')">
                            <div class="card-top-icon"><i class="bi bi-gear-fill"></i></div>
                            <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.95rem; letter-spacing: 0.5px;">PENGATURAN</h5>
                            <p class="text-muted small mb-3" style="font-size:0.7rem;">Profil & jam kerja.</p>
                            <span class="badge rounded-pill bg-light border px-3 py-1 text-info fw-bold mb-2">Akun Aktif</span>
                            <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 d-flex flex-column">
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="quick-actions-container">
                                    
                                    <div class="doctor-profile-sm">
                                        <img src="../wallpaper/rs14.png" class="doctor-avatar-sm" alt="Dr. Cae Soo Bin" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3304/3304567.png';">
                                        <div>
                                            <h5 class="doc-name">Dr. Cae Soo Bin, Sp.KK</h5>
                                            <p class="doc-spec">Spesialis Kulit & Kelamin</p>
                                        </div>
                                    </div>

                                    <div class="status-toggle-wrapper shadow-sm">
                                        <div>
                                            <span class="d-block" style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Status Praktik</span>
                                            <span class="status-text status-on" id="statusLabel">Menerima Pasien</span>
                                        </div>
                                        <div class="form-check form-switch fs-4 mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="doctorStatusToggle" checked onchange="toggleDoctorStatus()">
                                        </div>
                                    </div>

                                    <div class="action-divider"></div>

                                    <div class="notice-board shadow-sm">
                                        <div class="notice-header">
                                            <i class="bi bi-megaphone-fill text-danger"></i> PENGUMUMAN INTERNAL
                                        </div>
                                        <p class="notice-text" id="teksPengumuman">"Rapat koordinasi seluruh jajaran staf dan dokter spesialis akan segera dimulai pada pukul 14:00 di Aula Utama Rumah Sakit lantai 3. Pembahasan mencakup penyelarasan SOP baru dan peningkatan akreditasi."</p>
                                        <a href="javascript:void(0)" class="notice-link" onclick="togglePengumuman(this)">...Selengkapnya</a>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="next-patient-card">
                                    <div class="antrean-header">
                                        <span class="antrean-title">ANTREAN SEKARANG</span>
                                        <div class="pulse-dot"></div>
                                    </div>
                                    
                                    <h2 class="antrean-nama">Michael Jordan</h2>
                                    
                                    <div class="antrean-info-row">
                                        <div>
                                            <div class="antrean-info-item">
                                                <i class="bi bi-clock antrean-info-icon"></i>
                                                <span class="antrean-info-text">09:00 AM</span>
                                            </div>
                                            <div class="antrean-info-item mb-0">
                                                <i class="bi bi-paperclip antrean-info-icon"></i>
                                                <span class="antrean-info-text">Skin Allergy</span>
                                            </div>
                                        </div>
                                        <button class="btn-periksa" onclick="periksaPasien('MJ', 'Michael Jordan', 'Skin Allergy', 'Konsultasi Baru')">Periksa Pasien</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-pasien" class="view-section d-none flex-column">
                <button class="btn btn-back-white shadow-sm mb-4 fw-bold text-dark border align-self-start rounded-pill px-4 position-relative" style="z-index: 999;" onclick="switchView('view-dashboard')">
                    <i class="bi bi-arrow-left me-2 text-primary"></i> Kembali ke Menu Utama
                </button>

                <div class="row g-4 h-100">
                    <div class="col-lg-4 col-xl-3 d-flex flex-column">
                        <div class="med-card d-flex flex-column position-relative" style="min-height: 700px;">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="bi bi-list-task me-2 text-primary"></i> Antrean Hari Ini</h5>
                            <div class="pe-2 d-flex flex-column gap-3 mb-4">
                                <div id="queue-MJ" class="queue-item-pasien p-3 rounded-4 cursor-pointer d-flex align-items-center gap-3" onclick="periksaPasien('MJ', 'Michael Jordan', 'Skin Allergy', 'Konsultasi Baru')">
                                    <div class="bg-primary text-white fw-bold rounded-circle d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; font-size: 0.9rem;">MJ</div>
                                    <div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Michael Jordan</div>
                                        <div class="small text-muted fw-bold" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i>09:00 AM</div>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <div class="text-info mb-1" style="font-size: 0.9rem;" title="RSVP Terkonfirmasi"><i class="bi bi-check-all"></i></div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.6rem;">Di Ruangan</span>
                                    </div>
                                </div>
                                <div id="queue-SC" class="queue-item-pasien p-3 rounded-4 cursor-pointer d-flex align-items-center gap-3 opacity-75" onclick="periksaPasien('SC', 'Sarah Connor', 'Routine Check', 'Kontrol')">
                                    <div class="fw-bold rounded-circle d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; font-size: 0.9rem; background-color: #e0f2fe; color: var(--primary-teal);">SC</div>
                                    <div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Sarah Connor</div>
                                        <div class="small fw-bold text-warning" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i>10:30 AM</div>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <div class="text-info mb-1" style="font-size: 0.9rem;"><i class="bi bi-check-all"></i></div>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 d-block mb-1" style="font-size: 0.6rem;">Hadir</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-xl-9 d-flex flex-column">
                        <div id="pasien-empty-state" class="med-card flex-grow-1 d-flex justify-content-center align-items-center flex-column" style="min-height: 700px;">
                            <div class="rounded-circle d-flex justify-content-center align-items-center mb-4" style="width: 120px; height: 120px; background-color: #e0f2fe; border: 2px dashed var(--primary-teal);">
                                <i class="bi bi-folder-plus text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-dark fw-bold mb-2">Rekam Medis Pasien</h3>
                            <p class="text-muted text-center fw-bold" style="max-width: 400px;">Pilih pasien dari daftar antrean di sebelah kiri untuk mengisi rekam medis.</p>
                        </div>

                        <div id="pasien-active-state" class="med-card flex-grow-1 position-relative pb-4 d-none" style="min-height: 700px;">
                            <div class="d-flex align-items-center gap-4 mb-4 border-bottom pb-4">
                                <img src="https://ui-avatars.com/api/?name=Michael+Jordan&background=eef5f5&color=38c8e6" id="emr-avatar" class="rounded-circle shadow-sm" width="90" height="90">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h2 class="fw-bold text-dark mb-0" id="emr-name">Michael Jordan</h2>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-light text-danger fw-bold border px-4 py-2 rounded-pill"><i class="bi bi-skip-forward-fill me-1"></i>Lewati</button>
                                            <button class="btn btn-sm btn-teal fw-bold border px-4 py-2 rounded-pill"><i class="bi bi-megaphone-fill me-1"></i>Panggil</button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end mt-2">
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 fs-6">RM: 1992-04-X4</span>
                                            <span id="emr-kategori" class="detail-tag-primary px-3 py-2 fs-6"><i class="bi bi-tag me-1"></i> Konsultasi Baru</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 p-4 mb-4 d-flex align-items-start gap-3">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                                <div>
                                    <h5 class="text-danger fw-bold mb-1">Peringatan Alergi!</h5>
                                    <p class="text-danger mb-0 opacity-75 fw-bold" id="emr-alergi">Pasien memiliki riwayat alergi terhadap obat golongan <b>Penisilin</b> dan <b>makanan laut (Seafood)</b>.</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold text-dark mb-2 fs-6">Keluhan Hari Ini (Anamnesis)</label>
                                <textarea class="form-emr py-3" rows="3" placeholder="Ketikkan keluhan utama pasien..." id="emr-reason">Pasien mengeluhkan gatal-gatal kemerahan pada area kulit lengan setelah memakan udang tadi malam.</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold text-dark mb-2 fs-6">Diagnosa Kerja (ICD-10)</label>
                                <textarea class="form-emr py-3" rows="2" placeholder="Ketikkan hasil diagnosa dokter..."></textarea>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="fw-bold text-dark mb-0 fs-6">Resep Obat (E-Prescription)</label>
                                    <button class="btn btn-light border text-primary fw-bold px-4 py-2 rounded-pill"><i class="bi bi-plus-lg me-1"></i>Tambah Obat</button>
                                </div>
                                <div class="table-responsive border rounded-4 overflow-hidden">
                                    <table class="table table-borderless align-middle mb-0 text-center">
                                        <thead class="table-header-pastel text-muted border-bottom py-3">
                                            <tr><th class="text-start ps-4 py-3">Nama Obat</th><th class="py-3">Dosis</th><th class="py-3">Jumlah</th><th class="py-3">Aksi</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-4 py-3"><input type="text" class="form-control border-0 bg-transparent fw-bold text-slate-700 fs-6" value="Cetirizine 10mg Tab"></td>
                                                <td><input type="text" class="form-control border-0 bg-transparent text-center text-slate-700 fw-bold fs-6" value="1 x 1 Hari"></td>
                                                <td><input type="text" class="form-control border-0 bg-transparent text-center text-slate-700 fw-bold fs-6" value="10 Caps"></td>
                                                <td><i class="bi bi-trash text-danger fs-5 cursor-pointer"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-end border-top pt-4 mt-3">
                                <button class="btn btn-light border fw-bold me-3 px-5 py-2 rounded-pill" onclick="resetPeriksa()">Batal</button>
                                <button class="btn btn-success fw-bold px-5 py-2 rounded-pill shadow-sm" onclick="selesaiPeriksa()"><i class="bi bi-check-circle me-2"></i>Selesai & Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-jadwal" class="view-section d-none flex-column">
                <button class="btn btn-back-white shadow-sm mb-4 fw-bold text-dark border align-self-start rounded-pill px-4" onclick="switchView('view-dashboard')">
                    <i class="bi bi-arrow-left me-2 text-primary"></i> Kembali ke Menu Utama
                </button>

                <div class="stats-row flex-wrap mb-4">
                    <div class="counter-card" onclick="switchView('view-pasien')">
                        <div class="icon-box-1 shadow-sm"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="counter-number text-dark">84</div>
                            <div class="counter-label">Total Pasien</div>
                        </div>
                    </div>
                    <div class="counter-card" onclick="switchView('view-jadwal')">
                        <div class="icon-box-1 shadow-sm"><i class="bi bi-calendar-day"></i></div>
                        <div>
                            <div class="counter-number text-dark">5</div>
                            <div class="counter-label">Pasien Hari Ini</div>
                        </div>
                    </div>
                    <div class="counter-card" onclick="switchView('view-jadwal')">
                        <div class="icon-box-1 shadow-sm"><i class="bi bi-person-check"></i></div>
                        <div>
                            <div class="counter-number text-dark">2</div>
                            <div class="counter-label">Pasien Tertangani</div>
                        </div>
                    </div>
                </div>

                <div class="med-card d-flex flex-column pb-4" style="min-height: 800px;">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">Manajemen Jadwal & Slot Mingguan</h3>
                            <p class="text-muted small mb-0">Klik pada slot waktu di hari mana saja untuk menambahkan rekam medis / janji temu manual.</p>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="control-pill shadow-sm">
                                <i class="bi bi-calendar-range text-primary"></i>
                                <span id="dynamic-jadwal-date">-- Mei 2026</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-7 mt-2" id="jadwalDaysContainer">
                        <div class="col" data-day="SENIN"><div class="day-column-card"><div class="day-header-title">SENIN</div><div class="d-flex flex-column gap-3"><div class="slot-box" onclick="triggerBlockSmartSimulation()"><div class="slot-time"><i class="bi bi-clock me-1"></i> 08:00 - 09:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">2 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 11:00 - 12:00</div><span class="badge-baru">Umum</span><span class="slot-sisa">1 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 14:00 - 15:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">3 Tersisa</span></div></div></div></div>
                        <div class="col" data-day="SELASA"><div class="day-column-card"><div class="day-header-title">SELASA</div><div class="d-flex flex-column gap-3"><div class="slot-box" onclick="triggerBlockSmartSimulation()"><div class="slot-time"><i class="bi bi-clock me-1"></i> 08:00 - 09:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">2 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 11:00 - 12:00</div><span class="badge-baru">Umum</span><span class="slot-sisa">1 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 14:00 - 15:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">3 Tersisa</span></div></div></div></div>
                        <div class="col" data-day="RABU"><div class="day-column-card"><div class="day-header-title">RABU</div><div class="d-flex flex-column gap-3"><div class="slot-box" onclick="triggerBlockSmartSimulation()"><div class="slot-time"><i class="bi bi-clock me-1"></i> 08:00 - 09:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">2 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 11:00 - 12:00</div><span class="badge-baru">Umum</span><span class="slot-sisa">1 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 14:00 - 15:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">3 Tersisa</span></div></div></div></div>
                        <div class="col" data-day="KAMIS"><div class="day-column-card"><div class="day-header-title">KAMIS</div><div class="d-flex flex-column gap-3"><div class="slot-box" onclick="triggerBlockSmartSimulation()"><div class="slot-time"><i class="bi bi-clock me-1"></i> 08:00 - 09:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">2 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 11:00 - 12:00</div><span class="badge-baru">Umum</span><span class="slot-sisa">1 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 14:00 - 15:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">3 Tersisa</span></div></div></div></div>
                        <div class="col" data-day="JUMAT"><div class="day-column-card"><div class="day-header-title">JUMAT</div><div class="d-flex flex-column gap-3"><div class="slot-box" onclick="triggerBlockSmartSimulation()"><div class="slot-time"><i class="bi bi-clock me-1"></i> 08:00 - 09:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">2 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 11:00 - 12:00</div><span class="badge-baru">Umum</span><span class="slot-sisa">1 Tersisa</span></div><div class="slot-box"><div class="slot-time"><i class="bi bi-clock me-1"></i> 14:00 - 15:00</div><span class="badge-kontrol">Kontrol</span><span class="slot-sisa">3 Tersisa</span></div></div></div></div>
                        <div class="col" data-day="SABTU"><div class="day-column-card"><div class="day-header-title text-muted">SABTU</div><div class="slot-box bg-light" style="border-style:dashed; cursor:not-allowed;"><span class="text-muted small fw-bold py-4">Libur</span></div></div></div>
                        <div class="col" data-day="MINGGU"><div class="day-column-card"><div class="day-header-title text-muted">MINGGU</div><div class="slot-box bg-light" style="border-style:dashed; cursor:not-allowed;"><span class="text-muted small fw-bold py-4">Libur</span></div></div></div>
                    </div>
                </div>
            </div>

            <div id="view-riwayat" class="view-section d-none">
                <button class="btn btn-back-white shadow-sm mb-4 fw-bold text-dark border align-self-start rounded-pill px-4" onclick="switchView('view-dashboard')">
                    <i class="bi bi-arrow-left me-2 text-primary"></i> Kembali ke Menu Utama
                </button>
                
                <div class="med-card pb-5" style="min-height: 700px;">
                    <div class="mb-4 pb-3 border-bottom">
                        <h3 class="text-dark fw-bold mb-1">Riwayat Konsultasi Pasien</h3>
                        <p class="text-muted small fw-bold mb-0">Kelola dan lihat data rekam medis pasien yang sudah selesai ditangani.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-riwayat align-middle text-center">
                            <thead>
                                <tr>
                                    <th class="text-start">NO. REKAM MEDIS</th>
                                    <th class="text-start">PROFIL PASIEN</th>
                                    <th>TANGGAL KUNJUNGAN</th>
                                    <th>JENIS LAYANAN</th>
                                    <th>GOL. DARAH</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr onclick="showDetailRiwayat('Ashley Black', 'RM-882910', 'Anak', '24 Mei 2026', 'Demam tinggi, batuk kering', 'Suspect ISPA (J06.9)', 'Paracetamol Syr, Cefadroxil', 'A+')">
                                    <td class="text-start fw-bold text-info">RM-882910</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-cyan-light">AB</div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">Ashley Black</div>
                                                <span class="tag-umur"><i class="bi bi-person-fill"></i> Anak</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold" style="font-size: 0.9rem;">24 Mei 2026</td>
                                    <td><span class="status-badge status-bpjs"><div class="dot-indicator dot-green"></div> BPJS</span></td>
                                    <td><span class="fw-bold text-danger"><i class="bi bi-droplet-fill"></i> A+</span></td>
                                    <td><div class="btn-action-dot mx-auto"><i class="bi bi-three-dots-vertical"></i></div></td>
                                </tr>
                                
                                <tr onclick="showDetailRiwayat('John Black', 'RM-882911', 'Dewasa', '20 Mei 2026', 'Cek Rutin Bulanan, Kolesterol', 'Hiperkolesterolemia (E78.0)', 'Atorvastatin 20mg', 'O+')">
                                    <td class="text-start fw-bold text-info">RM-882911</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-cyan-light">JB</div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">John Black</div>
                                                <span class="tag-umur"><i class="bi bi-person-fill"></i> Dewasa</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold" style="font-size: 0.9rem;">20 Mei 2026</td>
                                    <td><span class="status-badge status-umum"><div class="dot-indicator dot-orange"></div> Umum</span></td>
                                    <td><span class="fw-bold text-danger"><i class="bi bi-droplet-fill"></i> O+</span></td>
                                    <td><div class="btn-action-dot mx-auto"><i class="bi bi-three-dots-vertical"></i></div></td>
                                </tr>

                                <tr onclick="showDetailRiwayat('Jane Black', 'RM-882912', 'Lansia', '15 Mei 2026', 'Nyeri sendi lutut, sulit berjalan', 'Osteoarthritis (M19.9)', 'Meloxicam 15mg, Fisioterapi', 'B+')">
                                    <td class="text-start fw-bold text-info">RM-882912</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-cyan-light">JB</div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">Jane Black</div>
                                                <span class="tag-umur"><i class="bi bi-person-fill"></i> Lansia</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold" style="font-size: 0.9rem;">15 Mei 2026</td>
                                    <td><span class="status-badge status-bpjs"><div class="dot-indicator dot-green"></div> BPJS</span></td>
                                    <td><span class="fw-bold text-danger"><i class="bi bi-droplet-fill"></i> B+</span></td>
                                    <td><div class="btn-action-dot mx-auto"><i class="bi bi-three-dots-vertical"></i></div></td>
                                </tr>

                                <tr onclick="showDetailRiwayat('Julian Geraldo', 'RM-541337', 'Dewasa', '10 Mei 2026', 'Ruam merah gatal di punggung', 'Dermatitis Kontak Alergi (L23.9)', 'Hidrokortison Krim 1%, Cetirizine', 'A+')">
                                    <td class="text-start fw-bold text-info">RM-541337</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-cyan-light">JG</div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">jgm (Julian G)</div>
                                                <span class="tag-umur"><i class="bi bi-person-fill"></i> Dewasa</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold" style="font-size: 0.9rem;">10 Mei 2026</td>
                                    <td><span class="status-badge status-bpjs"><div class="dot-indicator dot-green"></div> BPJS</span></td>
                                    <td><span class="fw-bold text-danger"><i class="bi bi-droplet-fill"></i> A+</span></td>
                                    <td><div class="btn-action-dot mx-auto"><i class="bi bi-three-dots-vertical"></i></div></td>
                                </tr>

                                <tr onclick="showDetailRiwayat('Halo', 'RM-245629', 'Anak', '02 Mei 2026', 'Diare 3 hari, lemas', 'Gastroenteritis (A09)', 'Oralit, Zinc, Rujukan Dr. Anak', 'A+')">
                                    <td class="text-start fw-bold text-info">RM-245629</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-cyan-light">HA</div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">Halo</div>
                                                <span class="tag-umur"><i class="bi bi-person-fill"></i> Anak</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold" style="font-size: 0.9rem;">02 Mei 2026</td>
                                    <td><span class="status-badge status-bpjs"><div class="dot-indicator dot-green"></div> BPJS</span></td>
                                    <td><span class="fw-bold text-danger"><i class="bi bi-droplet-fill"></i> A+</span></td>
                                    <td><div class="btn-action-dot mx-auto"><i class="bi bi-three-dots-vertical"></i></div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div id="view-settings" class="view-section d-none">
                <button class="btn btn-back-white shadow-sm mb-4 fw-bold text-dark border align-self-start rounded-pill px-4" onclick="switchView('view-dashboard')">
                    <i class="bi bi-arrow-left me-2 text-primary"></i> Kembali ke Menu Utama
                </button>
                <div class="med-card p-5" style="min-height: 700px;">
                    <h3 class="fw-bold mb-5 text-dark"><i class="bi bi-gear-fill me-2 text-primary"></i> Pengaturan Profil & Jam Kerja</h3>
                    <div class="row border-top pt-5">
                        <div class="col-md-3 text-center border-end">
                            <img src="../wallpaper/rs14.png" class="rounded-circle mb-4 border border-4 border-white shadow-sm" width="160" height="160" style="object-fit: cover;" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3304/3304567.png';">
                            <h4 class="fw-bold text-dark">Dr. Cae Soo Bin</h4>
                            <p class="text-muted fw-bold">Dermatologist</p>
                        </div>
                        <div class="col-md-9 ps-md-5">
                            <div class="row g-4 mb-5">
                                <div class="col-md-6"><label class="text-muted fw-bold mb-2">Full Name</label><input type="text" class="form-control bg-light border-0 py-3 fw-bold" value="Dr. Cae Soo Bin"></div>
                                <div class="col-md-6"><label class="text-muted fw-bold mb-2">Specialization</label><input type="text" class="form-control bg-light border-0 py-3 fw-bold" value="Dermatologist"></div>
                            </div>
                            <h5 class="fw-bold text-muted text-uppercase mb-4 border-top pt-5">Default Working Hours</h5>
                            <div class="row g-4">
                                <div class="col-md-6"><label class="text-muted fw-bold mb-2">Jam Buka Praktek</label><input type="time" class="form-control bg-light border-0 py-3 fw-bold fs-5" value="08:00"></div>
                                <div class="col-md-6"><label class="text-muted fw-bold mb-2">Jam Tutup Praktek</label><input type="time" class="form-control bg-light border-0 py-3 fw-bold fs-5" value="17:00"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                                <button class="btn btn-logout px-5 py-3 rounded-pill fw-bold fs-6 shadow-sm" onclick="logout()">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                                <button class="btn btn-teal px-5 py-3 rounded-pill text-white fw-bold fs-6 shadow-sm">Save Changes Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main> 
    </div>

    <div class="modal fade" id="modalDetailRiwayat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-medical-fill text-primary me-2"></i> Detail Rekam Medis</h4>
                        <p class="text-muted small fw-bold mb-0 mt-1">ID Dokumen: <span id="modalDocId" class="text-info">DOC-202605</span></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <div class="row g-3 mb-4 border-bottom pb-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="avatar-circle bg-cyan-light" style="width:60px; height:60px; font-size:1.5rem;" id="modalAvatar">AB</div>
                                <div>
                                    <h4 class="fw-bold text-dark mb-1" id="modalNama">Ashley Black</h4>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-light text-dark border fw-bold" id="modalTipe">Anak</span>
                                        <span class="badge bg-light text-danger border fw-bold"><i class="bi bi-droplet-fill"></i> <span id="modalGolDarah">A+</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-column justify-content-center">
                            <p class="info-label mb-1">NO. REKAM MEDIS</p>
                            <h5 class="fw-bold text-primary mb-0" id="modalRM">RM-882910</h5>
                            <p class="info-label mt-2 mb-0"><i class="bi bi-calendar3 me-1"></i>Tanggal Kunjungan: <span class="text-dark" id="modalTgl">24 Mei 2026</span></p>
                        </div>
                    </div>

                    <div class="bg-grey-box mb-3">
                        <p class="info-label">Keluhan Utama (Anamnesis)</p>
                        <p class="info-value mb-0" id="modalKeluhan">Demam tinggi, batuk kering</p>
                    </div>
                    
                    <div class="bg-grey-box mb-3 border-start border-4 border-warning">
                        <p class="info-label text-warning">Diagnosa Kerja (ICD-10)</p>
                        <p class="info-value mb-0" id="modalDiagnosa">Suspect ISPA (J06.9)</p>
                    </div>

                    <div class="bg-grey-box mb-3 border-start border-4 border-success">
                        <p class="info-label text-success">Terapi / Tindakan (E-Prescription)</p>
                        <p class="info-value mb-0" id="modalTerapi">Paracetamol Syr, Cefadroxil</p>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div>
                            <p class="info-label mb-0">Dokter Penanggung Jawab</p>
                            <p class="fw-bold text-dark mb-0"><i class="bi bi-check-circle-fill text-success me-1"></i>Dr. Cae Soo Bin</p>
                        </div>
                        <img src="../wallpaper/rs14.png" width="40" height="40" class="rounded-circle shadow-sm" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3304/3304567.png';">
                    </div>

                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4 py-3 px-4">
                    <button type="button" class="btn btn-light border fw-bold px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-teal fw-bold px-4 rounded-pill shadow-sm"><i class="bi bi-printer-fill me-2"></i>Cetak Dokumen</button>
                </div>
            </div>
        </div>
    </div>

    <script src="dokter.js"></script>
</body>
</html>