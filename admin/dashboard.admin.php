<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MediFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <div class="main-wrapper">
        <header class="top-navbar">
            <div class="navbar-brand-logo" onclick="switchView('view-home')">
                <i class="bi bi-heart-pulse-fill fs-2"></i> MediFlow
            </div>
            
            <div class="navbar-center-info">
                <i class="bi bi-shield-lock-fill fs-5" style="color: var(--primary-teal);"></i> 
                <span class="text-dark ms-1 fw-bolder" style="letter-spacing: 1.5px;">MEDIFLOW</span> 
                <span class="text-muted fw-bold">PORTAL</span>
            </div>

            <div class="navbar-right-controls">
                <div class="search-box shadow-sm d-none d-md-flex">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" placeholder="Cari ID Pasien, Dokter...">
                </div>
                
                <div class="control-pill shadow-sm">
                    <i class="bi bi-clock text-primary"></i>
                    <span id="realtime-clock">--:-- WIB</span>
                </div>

                <div class="icon-circle shadow-sm position-relative" title="Notifikasi">
                    <i class="bi bi-bell-fill"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </div>
                
                <div class="d-flex align-items-center bg-white p-1 pe-3 rounded-pill shadow-sm border cursor-pointer" onclick="switchView('view-settings')">
                    <img src="../wallpaper/rs14.png" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Admin&background=eef5f5&color=38c8e6';">
                    <span class="fw-bold small text-dark d-none d-md-inline">Super Admin</span>
                </div>

                <i class="bi bi-box-arrow-right logout-btn text-danger" onclick="logout()" title="Keluar"></i>
            </div>
        </header>

        <main class="ash-main">
            <div id="view-home" class="view-section">
                <div class="welcome-banner">
                    <div class="banner-bg-decor">
                        <div class="decor-circle"></div>
                    </div>

                    <div class="banner-text">
                        <div class="banner-badge" id="dynamic-banner-date"><i class="bi bi-calendar-check me-2"></i>--</div>
                        <h2 class="fw-bold mb-1 fs-1">Hello, Admin!</h2>
                        <p class="mb-0 opacity-75">Kelola rekam medis, penjadwalan dokter, dan operasional RS hari ini.</p>
                        
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
                        <div class="bubble-hallo" id="welcome-bubble">Hallo, Admin 👋</div>
                        <img src="../wallpaper/rs14.png" class="wel-doctor-img" id="doctor-image" alt="Doctor" onerror="this.src='https://cdni.iconscout.com/illustration/premium/thumb/female-doctor-4985226-4155163.png'">
                    </div>
                </div>

                <div class="nav-card-grid">
                    <div class="portal-nav-card" onclick="switchView('view-pasien')">
                        <div class="card-top-icon"><i class="bi bi-people-fill"></i></div>
                        <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">DATABASE PASIEN</h5>
                        <p class="text-muted small mb-3" style="font-size:0.65rem;">Kelola rekam medis.</p>
                        <span class="badge rounded-pill bg-light border px-3 py-1 text-info fw-bold mb-2" id="rswnTotalPasien">0 Pasien</span>
                        <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                    </div>

                    <div class="portal-nav-card" onclick="switchView('view-dokter')">
                        <div class="card-top-icon"><i class="bi bi-heart-pulse-fill"></i></div>
                        <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">DATA DOKTER</h5>
                        <p class="text-muted small mb-3" style="font-size:0.65rem;">Pantau shift & cuti.</p>
                        <span class="badge rounded-pill bg-light border px-3 py-1 text-success fw-bold mb-2" id="rswnTotalDokter">0 Dokter</span>
                        <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                    </div>

                    <div class="portal-nav-card" onclick="switchView('view-booking')">
                        <div class="card-top-icon"><i class="bi bi-door-open-fill"></i></div>
                        <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">BOOKING KAMAR</h5>
                        <p class="text-muted small mb-3" style="font-size:0.65rem;">Alokasi ranjang inap.</p>
                        <span class="badge rounded-pill bg-light border px-3 py-1 text-warning fw-bold mb-2" id="rswnTotalBooking">0 Reservasi</span>
                        <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                    </div>

                    <div class="portal-nav-card" onclick="switchView('view-riwayat')">
                        <div class="card-top-icon"><i class="bi bi-clock-history"></i></div>
                        <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">REKAM MEDIS</h5>
                        <p class="text-muted small mb-3" style="font-size:0.65rem;">Arsip riwayat kesehatan.</p>
                        <span class="badge rounded-pill bg-light border px-3 py-1 text-secondary fw-bold mb-2">Database RS</span>
                        <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                    </div>

                    <div class="portal-nav-card" onclick="switchView('view-settings')">
                        <div class="card-top-icon"><i class="bi bi-gear-fill"></i></div>
                        <h5 class="fw-bold text-dark mt-2 mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">PENGATURAN</h5>
                        <p class="text-muted small mb-3" style="font-size:0.65rem;">Konfigurasi sistem.</p>
                        <span class="badge rounded-pill bg-light border px-3 py-1 text-danger fw-bold mb-2">Akses Penuh</span>
                        <div class="card-bottom-icon"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>

                <div class="row g-4 mb-2">
                    <div class="col-xl-4 col-lg-4">
                        <div class="med-card d-flex flex-column h-100">
                            <h6 class="fw-bold mb-3 small text-dark"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Statistik Kunjungan RS (2026)</h6>
                            <div class="flex-grow-1" style="position: relative; min-height: 180px; width: 100%;"><canvas id="adminBarChart"></canvas></div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4">
                        <div class="med-card d-flex flex-column h-100">
                            <h6 class="fw-bold text-dark mb-3 small"><i class="bi bi-door-open text-info me-2"></i>Status Ranjang Inap</h6>
                            <div class="flex-grow-1 d-flex flex-column justify-content-center" style="font-size: 0.85rem;">
                                <div class="d-flex justify-content-between border-bottom pb-3 mb-2"><span class="text-muted fw-semibold">Suite / VIP</span><span class="fw-bold text-dark" id="bedVipDash">0 / 15 Bed</span></div>
                                <div class="d-flex justify-content-between border-bottom pb-3 mb-2"><span class="text-muted fw-semibold">Kelas I</span><span class="fw-bold text-dark" id="bedKelas1Dash">0 / 40 Bed</span></div>
                                <div class="d-flex justify-content-between border-bottom pb-3 mb-2"><span class="text-muted fw-semibold">Kelas II</span><span class="fw-bold text-dark" id="bedKelas2Dash">0 / 50 Bed</span></div>
                                <div class="d-flex justify-content-between pb-1"><span class="text-muted fw-semibold">Kelas III</span><span class="fw-bold text-dark" id="bedKelas3Dash">0 / 80 Bed</span></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-lg-4">
                        <div class="med-card bg-white border border-info border-2 d-flex flex-column h-100">
                            <h6 class="fw-bold text-primary mb-2 small"><i class="bi bi-shield-check me-2"></i>Verifikasi Cepat BPJS</h6>
                            <p class="text-muted mb-3" style="font-size: 0.75rem;">Cek status kepesertaan BPJS secara realtime.</p>
                            <div class="input-group mb-3">
                                <input type="text" id="adminInput" class="form-control bg-light border-0 ps-3" placeholder="Ketik: John / Jane / Ashley" style="font-size:0.8rem;">
                                <button class="btn btn-info text-white fw-bold" onclick="cekBpjsAdmin()"><i class="bi bi-search"></i></button>
                            </div>
                            <div id="hasilAdmin" class="bg-light text-dark p-3 rounded-3 d-none border flex-grow-1" style="font-size:0.8rem;">
                                <h6 class="fw-bold text-dark mb-1" id="resNamaAdmin">-</h6>
                                <div id="resStatusAdmin"></div>
                                <div id="btnAksiAdmin" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-pasien" class="view-section d-none">
                <button class="btn back-btn mb-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left text-primary me-2"></i>Kembali ke Dashboard</button>
                <div class="med-card d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Database Pasien Terdaftar</h5>
                            <p class="small text-muted mb-0">Kelola dan perbarui rujukan dokter penanggung jawab rekam medis.</p>
                        </div>
                        <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPasien" onclick="isiDropdownDokterPilihan('inputDokterPasien')">
                            <i class="bi bi-person-plus-fill me-2"></i>Pasien Baru
                        </button>
                    </div>
                    <div class="table-custom-wrapper">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>No. RM</th>
                                    <th>Profil Pasien</th>
                                    <th>Dokter Rujukan</th>
                                    <th>Jenis Layanan</th>
                                    <th>Gol. Darah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelPasienBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="view-dokter" class="view-section d-none">
                <button class="btn back-btn mb-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left text-primary me-2"></i>Kembali ke Dashboard</button>
                <div class="med-card d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Database Dokter Terdaftar</h5>
                            <p class="small text-muted mb-0">Kelola jadwal shift praktek dokter & penanganan pasien secara real-time.</p>
                        </div>
                        <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahDokter">
                            <i class="bi bi-person-plus-fill me-2"></i>Tambah Dokter
                        </button>
                    </div>
                    <div class="table-custom-wrapper">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Dokter</th>
                                    <th>Profil Dokter</th>
                                    <th>Spesialis & Ruangan</th>
                                    <th>Jadwal & Jam Praktek</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelDokterBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="view-booking" class="view-section d-none">
                <button class="btn back-btn mb-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left text-primary me-2"></i>Kembali ke Dashboard</button>
                <div class="med-card d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Database Booking Kamar Rawat Inap</h5>
                            <p class="small text-muted mb-0">Kelola alokasi kamar inap berdasarkan rujukan jenis asuransi kepesertaan pasien.</p>
                        </div>
                        <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahBooking" onclick="isiDropdownPasienBooking()">
                            <i class="bi bi-door-open-fill me-2"></i>Booking Kamar
                        </button>
                    </div>
                    <div class="table-custom-wrapper">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Profil Pasien</th>
                                    <th>Kelas & No. Kamar</th>
                                    <th>Durasi Inap</th>
                                    <th>Status & Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelBookingBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="view-riwayat" class="view-section d-none">
                <button class="btn back-btn mb-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left text-primary me-2"></i>Kembali ke Dashboard</button>
                <div class="med-card d-flex justify-content-center align-items-center flex-column py-5 h-100">
                    <i class="bi bi-inbox text-muted" style="font-size: 6rem;"></i>
                    <h3 class="text-dark fw-bold mt-4 mb-2">Arsip Riwayat Rekam Medis</h3>
                    <p class="text-muted fw-bold">Database seluruh histori pasien yang telah selesai dirawat atau keluar.</p>
                </div>
            </div>

            <div id="view-settings" class="view-section d-none">
                <button class="btn back-btn mb-3" onclick="switchView('view-home')">
                    <i class="bi bi-arrow-left text-primary me-2"></i>Kembali ke Dashboard
                </button>
                
                <div class="med-card p-5 h-100" style="min-height: 700px;">
                    <h3 class="fw-bold mb-5 text-dark"><i class="bi bi-gear-fill me-2 text-primary"></i> Pengaturan Sistem & Profil Admin</h3>
                    <div class="row border-top pt-5">
                        <div class="col-md-3 text-center border-end">
                            <img src="../../wallpaper/rs14.png" class="rounded-circle mb-4 border border-4 border-white shadow-sm" style="object-fit:cover;" width="160" height="160" onerror="this.src='https://ui-avatars.com/api/?name=Admin+RS&background=eef5f5&color=38c8e6'">
                            <h4 class="fw-bold text-dark">Super Admin</h4>
                            <p class="text-muted fw-bold">Administrator IT</p>
                        </div>
                        <div class="col-md-9 ps-md-5">
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="text-muted fw-bold mb-2">Nama Pengguna</label>
                                    <input type="text" class="form-control bg-light border-0 py-3 fw-bold" value="Administrator Pusat">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted fw-bold mb-2">Peran (Role)</label>
                                    <input type="text" class="form-control bg-light border-0 py-3 fw-bold text-danger" value="Akses Penuh (Full Access)" disabled>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold text-muted text-uppercase mb-4 border-top pt-5">Pengaturan Notifikasi</h5>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label fw-bold ms-2">Terima Notifikasi Pendaftaran Pasien Baru</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label fw-bold ms-2">Peringatan Kamar Inap Penuh</label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                                <button class="btn btn-light border text-danger px-5 py-3 rounded-pill fw-bold fs-6 shadow-sm" onclick="logout()">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                                <button class="btn btn-primary px-5 py-3 rounded-pill text-white fw-bold fs-6 shadow-sm" onclick="alert('Pengaturan berhasil disimpan!')">
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main> 
    </div>

    <div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="modal-header border-0 bg-light p-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-person-badge-fill text-primary me-2"></i>Registrasi Pasien Baru</h5>
                    </div>
                    <button type="button" class="btn-close mb-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                        <div class="input-group-custom"><i class="bi bi-person"></i><input type="text" id="inputNamaPasien" placeholder="Contoh: Budi Santoso"></div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Dokter Rujukan / Pemeriksa <span class="text-danger">*</span></label>
                        <div class="input-group-custom"><i class="bi bi-heart-pulse-fill text-info"></i><select id="inputDokterPasien"></select></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Golongan Darah</label>
                            <div class="input-group-custom"><i class="bi bi-droplet-half text-danger"></i>
                                <select id="inputGolDarah"><option value="A+">A+</option><option value="B+">B+</option><option value="AB+">AB+</option><option value="O+">O+</option></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Jenis Layanan</label>
                            <div class="input-group-custom"><i class="bi bi-shield-check text-success"></i>
                                <select id="inputJenisLayanan"><option value="BPJS">BPJS</option><option value="Umum">Umum</option></select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="small text-muted fw-bold mb-2">Kategori Usia</label>
                            <div class="input-group-custom"><i class="bi bi-people text-info"></i>
                                <select id="inputKategoriUsia"><option value="Anak">Anak</option><option value="Dewasa">Dewasa</option><option value="Lansia">Lansia</option></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" onclick="simpanPasienBaru()">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditPasien" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="modal-header border-0 bg-light p-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Rujukan Medis Pasien</h5>
                    </div>
                    <button type="button" class="btn-close mb-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Nama Pasien</label>
                        <div class="input-group-custom bg-light"><i class="bi bi-person text-muted"></i><input type="text" id="editNamaPasien" disabled></div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Pindahkan ke Dokter Rujukan Baru <span class="text-danger">*</span></label>
                        <div class="input-group-custom"><i class="bi bi-heart-pulse-fill text-primary"></i><select id="editDokterPasien"></select></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" onclick="simpanPerubahanPasien()">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahDokter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-heart-pulse-fill text-primary me-2"></i>Registrasi Dokter Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Nama Lengkap Dokter <span class="text-danger">*</span></label>
                        <div class="input-group-custom"><i class="bi bi-person-badge"></i><input type="text" id="inputNamaDokter" placeholder="Contoh: Dr. Budi Santoso, Sp.A"></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Spesialisasi</label>
                            <div class="input-group-custom"><i class="bi bi-award text-warning"></i>
                                <select id="inputSpesialis"><option value="Dokter Umum">Dokter Umum</option><option value="Spesialis Kulit">Spesialis Kulit</option><option value="Spesialis Jantung">Spesialis Jantung</option><option value="Spesialis Anak">Spesialis Anak</option></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Ruangan / Poli</label>
                            <div class="input-group-custom"><i class="bi bi-door-open text-primary"></i><input type="text" id="inputRuangan" placeholder="Contoh: Poli R.03"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Jadwal Praktek</label>
                            <div class="input-group-custom"><i class="bi bi-calendar-check text-success"></i>
                                <select id="inputJadwal"><option value="Senin - Jumat">Senin - Jumat</option><option value="Senin - Rabu">Senin - Rabu</option></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Jam Kerja</label>
                            <div class="input-group-custom"><i class="bi bi-clock-history text-info"></i><input type="text" id="inputJam" placeholder="08:00 - 15:00"></div>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="small text-muted fw-bold mb-2">Status Saat Ini</label>
                        <div class="input-group-custom"><i class="bi bi-broadcast text-danger"></i>
                            <select id="inputStatusDokter"><option value="Aktif">Aktif / Tersedia</option><option value="Cuti">Cuti / Libur</option></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" onclick="simpanDokterBaru()">Simpan Dokter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditDokter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Jadwal & Data Dokter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Nama Lengkap Dokter</label>
                        <div class="input-group-custom"><i class="bi bi-person-badge"></i><input type="text" id="editNamaDokter"></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Spesialisasi</label>
                            <div class="input-group-custom"><i class="bi bi-award text-warning"></i>
                                <select id="editSpesialis"><option value="Dokter Umum">Dokter Umum</option><option value="Spesialis Kulit">Spesialis Kulit</option><option value="Spesialis Jantung">Spesialis Jantung</option></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Ruangan / Poli</label>
                            <div class="input-group-custom"><i class="bi bi-door-open text-primary"></i><input type="text" id="editRuangan"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Jadwal</label>
                            <div class="input-group-custom"><i class="bi bi-calendar-check text-success"></i><select id="editJadwal"><option value="Senin - Jumat">Senin - Jumat</option></select></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Jam Kerja</label>
                            <div class="input-group-custom"><i class="bi bi-clock-history text-info"></i><input type="text" id="editJam"></div>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="small text-muted fw-bold mb-2">Status</label>
                        <div class="input-group-custom"><i class="bi bi-broadcast text-danger"></i><select id="editStatusDokter"><option value="Aktif">Aktif</option><option value="Cuti">Cuti</option></select></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" onclick="simpanPerubahanDokter()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahBooking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-door-open-fill text-primary me-2"></i>Reservasi Kamar Inap Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Pilih Pasien Terdaftar <span class="text-danger">*</span></label>
                        <div class="input-group-custom"><i class="bi bi-person"></i><select id="inputPasienBooking" onchange="filterKelasKamarBerdasarLayanan('inputPasienBooking', 'inputKelasKamar')"></select></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Kelas Kamar <span class="text-danger">*</span></label>
                            <div class="input-group-custom"><i class="bi bi-layer-forward text-warning"></i><select id="inputKelasKamar"></select></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">No. Kamar <span class="text-danger">*</span></label>
                            <div class="input-group-custom"><i class="bi bi-hash text-primary"></i><input type="text" id="inputNomorKamar" placeholder="Kamar 302"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Check-In <span class="text-danger">*</span></label>
                            <div class="input-group-custom"><i class="bi bi-calendar-plus text-success"></i><input type="date" id="inputCheckIn"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Check-Out <span class="text-danger">*</span></label>
                            <div class="input-group-custom"><i class="bi bi-calendar-minus text-danger"></i><input type="date" id="inputCheckOut"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Status</label>
                        <div class="input-group-custom"><i class="bi bi-toggle-on text-info"></i><select id="inputStatusBooking"><option value="Waiting">Waiting</option><option value="Checked-In">Checked-In</option><option value="Checked-Out">Checked-Out</option></select></div>
                    </div>
                    <div class="mb-1">
                        <label class="small text-muted fw-bold mb-2">Catatan Medis</label>
                        <div class="input-group-custom"><i class="bi bi-journal-text text-muted"></i><input type="text" id="inputKeteranganBooking" placeholder="Catatan tambahan"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" onclick="simpanBookingBaru()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditBooking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Ubah Reservasi Kamar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Nama Pasien</label>
                        <div class="input-group-custom bg-light"><i class="bi bi-person text-muted"></i><input type="text" id="editNamaPasienBooking" disabled></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Kelas Kamar</label>
                            <div class="input-group-custom"><i class="bi bi-layer-forward text-warning"></i><select id="editKelasKamar"></select></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Nomor Kamar</label>
                            <div class="input-group-custom"><i class="bi bi-hash text-primary"></i><input type="text" id="editNomorKamar"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Check-In</label>
                            <div class="input-group-custom"><i class="bi bi-calendar-plus text-success"></i><input type="date" id="editCheckIn"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted fw-bold mb-2">Check-Out</label>
                            <div class="input-group-custom"><i class="bi bi-calendar-minus text-danger"></i><input type="date" id="editCheckOut"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold mb-2">Status</label>
                        <div class="input-group-custom"><i class="bi bi-toggle-on text-info"></i><select id="editStatusBooking"><option value="Waiting">Waiting</option><option value="Checked-In">Checked-In</option><option value="Checked-Out">Checked-Out</option></select></div>
                    </div>
                    <div class="mb-1">
                        <label class="small text-muted fw-bold mb-2">Catatan</label>
                        <div class="input-group-custom"><i class="bi bi-journal-text text-muted"></i><input type="text" id="editKeteranganBooking"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 shadow-sm" onclick="simpanPerubahanBooking()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapusPasien" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow-lg text-center p-4" style="border-radius: 25px;"><div class="mx-auto bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;"><i class="bi bi-trash3-fill fs-2"></i></div><h5 class="fw-bold text-dark mb-2">Hapus Pasien?</h5><p class="small text-muted mb-4"><span id="namaHapus" class="fw-bold text-dark"></span> akan dihapus permanen.</p><div class="d-flex gap-2 w-100"><button type="button" class="btn btn-light w-50 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger w-50 rounded-pill fw-bold shadow-sm" onclick="eksekusiHapus()">Ya, Hapus</button></div></div></div></div>
    <div class="modal fade" id="modalHapusDokter" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow-lg text-center p-4" style="border-radius: 25px;"><div class="mx-auto bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;"><i class="bi bi-person-x-fill fs-2"></i></div><h5 class="fw-bold text-dark mb-2">Hapus Dokter?</h5><p class="small text-muted mb-4"><span id="namaHapusDokter" class="fw-bold text-dark"></span> akan dihapus permanen.</p><div class="d-flex gap-2 w-100"><button type="button" class="btn btn-light w-50 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger w-50 rounded-pill fw-bold shadow-sm" onclick="eksekusiHapusDokter()">Ya, Hapus</button></div></div></div></div>
    <div class="modal fade" id="modalHapusBooking" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content border-0 shadow-lg text-center p-4" style="border-radius: 25px;"><div class="mx-auto bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;"><i class="bi bi-exclamation-triangle-fill fs-2"></i></div><h5 class="fw-bold text-dark mb-2">Batalkan Booking?</h5><p class="small text-muted mb-4"><span id="namaHapusBooking" class="fw-bold text-dark"></span> akan dihapus permanen.</p><div class="d-flex gap-2 w-100"><button type="button" class="btn btn-light w-50 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger w-50 rounded-pill fw-bold shadow-sm" onclick="eksekusiHapusBooking()">Ya, Hapus</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="admin.js"></script>

</body>
</html>