<?php 
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");
if (!$koneksi) { die("Koneksi database gagal."); }

if(!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "dokter"){
    header("location:../login/index.php?pesan=belum_login");
    exit(); 
}

$nama_dokter = $_SESSION['nama'] ?? 'Dokter';

// ==========================================
// MENGAMBIL DATA ANTREAN PASIEN REAL DARI DB
// ==========================================
$query_antrean = "SELECT rb.*, u.nama AS nama_pasien, u.email 
                  FROM riwayat_berobat rb 
                  LEFT JOIN users u ON rb.email_pasien = u.email 
                  WHERE rb.nama_dokter = '$nama_dokter' AND rb.status = 'Menunggu' 
                  ORDER BY rb.tanggal_periksa ASC, rb.waktu_kunjungan ASC";

$q_antrean = mysqli_query($koneksi, $query_antrean);
$antrean_arr = [];

if($q_antrean) {
    while($row = mysqli_fetch_assoc($q_antrean)) {
        $email = $row['email_pasien'];
        $angka_unik = preg_replace("/[^0-9]/", "", md5($email)); 
        $rm_p = "RM-" . substr($angka_unik, 0, 4);
        if(strlen($rm_p) < 7) { $rm_p = "RM-" . rand(1000,9999); }

        $antrean_arr[] = [
            'id' => $row['id'],
            'nama_pasien' => $row['nama_pasien'] ?? 'Pasien Tidak Dikenal',
            'no_rm' => $rm_p,
            'waktu' => $row['waktu_kunjungan'] ?? '-',
            'tanggal' => date('d M Y', strtotime($row['tanggal_periksa'])),
            'keluhan' => $row['keluhan'] ?? '-'
        ];
    }
}

// ==========================================
// MENGAMBIL DATA RIWAYAT PASIEN HARI INI
// ==========================================
$hari_ini = date('Y-m-d');
$query_riwayat = "SELECT rb.*, u.nama AS nama_pasien, u.email 
                  FROM riwayat_berobat rb 
                  LEFT JOIN users u ON rb.email_pasien = u.email 
                  WHERE rb.nama_dokter = '$nama_dokter' AND rb.status = 'Selesai' AND DATE(rb.tanggal_periksa) = '$hari_ini' 
                  ORDER BY rb.id DESC LIMIT 5";
$q_riwayat = mysqli_query($koneksi, $query_riwayat);

// ==========================================
// MENGAMBIL DATA JADWAL PRAKTIK DOKTER
// ==========================================
$jadwal_dokter = ['Senin'=>[], 'Selasa'=>[], 'Rabu'=>[], 'Kamis'=>[], 'Jumat'=>[], 'Sabtu'=>[], 'Minggu'=>[]];
$q_jadwal = @mysqli_query($koneksi, "SELECT * FROM jadwal_praktik WHERE nama_dokter = '$nama_dokter' ORDER BY jam_mulai ASC");
if($q_jadwal) {
    while($j = mysqli_fetch_assoc($q_jadwal)){
        $hari_j = ucfirst(strtolower($j['hari']));
        if(isset($jadwal_dokter[$hari_j])) {
            $jadwal_dokter[$hari_j][] = $j;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter - MediFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dokter.css?v=<?= time(); ?>">
</head>
<body class="bg-dokter">

    <div class="ashley-container">
        <div class="ash-main" id="main-content-area">
            
            <!-- ================= VIEW BERANDA ================= -->
            <div id="view-home" class="view-section">
                
                <!-- HEADER LOGO MEDIFLOW -->
                <div class="d-flex align-items-center mt-2 mb-3">
                    <i class="bi bi-heart-pulse fs-3 text-teal-mediflow me-2"></i>
                    <h4 class="fw-bold text-teal-mediflow mb-0 lh-1">MediFlow Dokter</h4>
                </div>
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #2c3e50;">Hello, <?= htmlspecialchars($nama_dokter); ?></h2>
                        <p class="text-muted small mb-0 fw-medium" id="realtime-datetime">Memuat waktu...</p>
                    </div>
                </div>

                <div class="row g-3 mb-2"> 
                    <!-- BANNER KIRI -->
                    <div class="col-xl-6 col-md-12">
                        <div class="ash-card-wel shadow-sm position-relative p-4 h-100">
                            <div class="wel-bg-shapes"></div>
                            <div class="wel-text-container" style="width: 55%; z-index: 2; position: relative;">
                                <span class="small text-white opacity-75 mb-1 d-block">Selamat Bertugas!</span>
                                <h4 class="fw-bold text-white mb-4 lh-base">Siap Melayani<br>Pasien Anda<br>Hari Ini?</h4>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light btn-sm fw-bold rounded-pill px-4 py-2 shadow-sm" style="color: #117a8b;" onclick="switchView('view-daftar-pasien')">
                                        <i class="bi bi-list-task me-1"></i> Lihat Antrean
                                    </button>
                                </div>
                            </div>
                            
                            <div class="doctor-container">
                                <div class="question-mark">?</div>
                                <img src="../wallpaper/rs7.png" class="wel-doctor-img" alt="Dokter">
                            </div>

                        </div>
                    </div> 

                    <!-- KOTAK PASIEN BERIKUTNYA -->
                    <div class="col-xl-6 col-md-12">
                        <div id="queue-card-wrapper" class="bg-white rounded-4 shadow-sm h-100 p-0 position-relative d-flex border-start border-5" style="border-color: #f39c12; overflow: hidden; transition: all 0.3s ease;">
                            
                            <div id="queue-badge" class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 fw-bold small" style="border-bottom-left-radius: 15px; z-index: 2;">MENUNGGU</div>
                            
                            <div class="flex-grow-1 p-3 d-flex flex-column justify-content-center" id="queue-data-container">
                                <!-- Dirender oleh JavaScript -->
                            </div>

                            <div class="d-flex flex-column gap-3 align-items-center justify-content-center p-3" style="min-width: 85px; background-color: #fff; z-index: 2; border-left: 1px solid #f0f0f0;">
                                <button class="btn btn-sm rounded-circle shadow-sm btn-arrow-queue d-flex align-items-center justify-content-center" onclick="prevQueue()"><i class="bi bi-chevron-up text-primary fw-bold"></i></button>
                                <span id="queue-counter" class="small fw-bold text-muted" style="font-size: 0.85rem;">0/0</span>
                                <button class="btn btn-sm rounded-circle shadow-sm btn-arrow-queue d-flex align-items-center justify-content-center" onclick="nextQueue()"><i class="bi bi-chevron-down text-primary fw-bold"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4 MENU CARDS -->
                <div class="menu-cards-container">
                    <div class="menu-card" onclick="switchView('view-daftar-pasien')">
                        <div class="menu-icon-top"><i class="bi bi-people"></i></div>
                        <div class="menu-title">DAFTAR PASIEN</div>
                        <div class="menu-subtitle">Kelola antrean poli Anda.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>
                    <div class="menu-card" onclick="switchView('view-rekam-medis')">
                        <div class="menu-icon-top"><i class="bi bi-journal-medical"></i></div>
                        <div class="menu-title">REKAM MEDIS</div>
                        <div class="menu-subtitle">Input diagnosis & obat.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>
                    <div class="menu-card" onclick="switchView('view-jadwal')">
                        <div class="menu-icon-top"><i class="bi bi-calendar4"></i></div>
                        <div class="menu-title">JADWAL</div>
                        <div class="menu-subtitle">Kelola jadwal praktik.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>
                    <div class="menu-card" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <div class="menu-icon-top"><i class="bi bi-gear"></i></div>
                        <div class="menu-title">SETTING</div>
                        <div class="menu-subtitle">Kelola profil dokter.</div>
                        <div class="menu-btn-bottom"><i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>

                <!-- RIWAYAT PASIEN TERTANGANI HARI INI -->
                <div class="row g-3 mb-4 mt-2">
                    <div class="col-xl-12"> 
                        <div class="bg-white rounded-4 shadow-sm p-4 h-100 border border-light">
                            <!-- HEADER RIWAYAT + TULISAN LIHAT SELENGKAPNYA -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Riwayat Pasien Tertangani Hari Ini</h6>
                                <a href="javascript:void(0)" class="text-teal-mediflow small text-decoration-none fw-bold" onclick="switchView('view-rekam-medis')">Lihat Selengkapnya <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                            
                            <?php 
                            if($q_riwayat && mysqli_num_rows($q_riwayat) > 0) {
                                while($r = mysqli_fetch_assoc($q_riwayat)) {
                                    $email_r = $r['email_pasien'];
                                    $angka_r = preg_replace("/[^0-9]/", "", md5($email_r)); 
                                    $rm_r = "RM-" . substr($angka_r, 0, 4);
                                    if(strlen($rm_r) < 7) { $rm_r = "RM-" . rand(1000,9999); }
                            ?>
                                <div class="border rounded-3 p-3 mt-2" style="border-left: 4px solid var(--mediflow-blue) !important; background-color: #fafbfc;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill mb-1 px-2 py-1" style="font-size: 0.65rem;"><i class="bi bi-check2-circle me-1"></i>Selesai</span>
                                            <h6 class="fw-bold mb-1 mt-1 text-dark" style="font-size: 0.95rem;"><?= $r['nama_pasien']; ?></h6>
                                            <p class="text-muted small mb-0"><i class="bi bi-person-vcard me-1"></i> <?= $rm_r; ?></p>
                                        </div>
                                        <div class="text-end">
                                            <span class="d-block small text-muted">Ditangani Shift</span>
                                            <span class="d-block fw-bold text-dark"><?= $r['waktu_kunjungan']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                } 
                            } else { 
                            ?>
                                <div class="d-flex flex-column align-items-center justify-content-center py-5 mt-2 text-center" style="border: 2px dashed #f0f0f0; border-radius: 15px; background-color: #fcfcfc;">
                                    <i class="bi bi-journal-x text-muted mb-3" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                    <h6 class="fw-bold text-dark mb-1 small">Belum Ada Riwayat Hari Ini</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem; max-width: 80%;">Pasien yang telah selesai Anda periksa hari ini akan otomatis muncul di sini (Reset besok).</p>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>

            </div> 

            <!-- ================= VIEW DAFTAR PASIEN ================= -->
            <div id="view-daftar-pasien" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Daftar Antrean Pasien</h4>
                </div>
                <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                    <?php if(empty($antrean_arr)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted mb-3 d-block" style="font-size: 3rem; opacity:0.5;"></i>
                            <h5 class="fw-bold">Antrean Kosong</h5>
                            <p class="text-muted small">Saat ini tidak ada pasien dalam antrean poliklinik Anda.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Waktu</th>
                                        <th>Keluhan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($antrean_arr as $antrian): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= $antrian['no_rm'] ?></span></td>
                                        <td class="fw-bold text-dark"><?= $antrian['nama_pasien'] ?></td>
                                        <td class="text-danger fw-bold small"><?= $antrian['waktu'] ?></td>
                                        <td class="small text-muted"><?= $antrian['keluhan'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-teal rounded-pill px-3 shadow-sm text-white" onclick="switchView('view-rekam-medis')">Periksa</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ================= VIEW REKAM MEDIS ================= -->
            <div id="view-rekam-medis" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Input Rekam Medis</h4>
                </div>
                
                <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                    <?php if(empty($antrean_arr)): ?>
                        <div class="alert alert-warning border-0 rounded-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Tidak ada antrean pasien untuk diperiksa hari ini.
                        </div>
                    <?php else: ?>
                        <form action="simpan_rekam_medis.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Pilih Pasien</label>
                                <select name="id_riwayat" class="form-select rounded-3 border-2 shadow-none" required>
                                    <option value="" selected disabled>-- Pilih Pasien dari Antrean --</option>
                                    <?php foreach($antrean_arr as $antrian): ?>
                                        <option value="<?= $antrian['id'] ?>"><?= $antrian['no_rm'] ?> - <?= $antrian['nama_pasien'] ?> (<?= $antrian['waktu'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Diagnosis (Hasil Pemeriksaan)</label>
                                <textarea name="diagnosis" class="form-control rounded-3 border-2 shadow-none" rows="3" placeholder="Deskripsikan hasil pemeriksaan / diagnosis penyakit pasien..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Resep Obat</label>
                                <textarea name="obat" class="form-control rounded-3 border-2 shadow-none" rows="2" placeholder="Sebutkan obat yang diresepkan (Contoh: Paracetamol 3x1)..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-teal w-100 rounded-pill py-2 fw-bold text-white shadow-sm">Simpan & Selesai Pemeriksaan</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ================= VIEW JADWAL (7 KOLOM) ================= -->
            <div id="view-jadwal" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Jadwal Praktik (7 Hari)</h4>
                </div>
                
                <div class="jadwal-wrapper bg-white p-3 rounded-4 shadow-sm border border-light">
                    <div class="jadwal-grid-7">
                        <?php 
                        $hari_angka = date('N'); 
                        $peta_hari = ['Senin'=>1, 'Selasa'=>2, 'Rabu'=>3, 'Kamis'=>4, 'Jumat'=>5, 'Sabtu'=>6, 'Minggu'=>7];
                        
                        foreach($jadwal_dokter as $hari => $slots): 
                            $is_today = ($peta_hari[$hari] == $hari_angka) ? 'active-day' : '';
                        ?>
                            <div class="jadwal-col <?= $is_today ?>">
                                <div class="jadwal-day-header text-dark"><?= strtoupper($hari) ?></div>
                                
                                <?php if(empty($slots)): ?>
                                    <div class="text-center text-muted mt-2 fw-medium" style="font-size: 0.75rem; opacity: 0.6;">
                                        Tidak ada jadwal
                                    </div>
                                <?php else: ?>
                                    <?php foreach($slots as $s): ?>
                                        <div class="jadwal-slot-card">
                                            <div class="slot-time"><i class="bi bi-clock me-1"></i> <?= $s['jam_mulai'] ?> - <?= $s['jam_selesai'] ?></div>
                                            <div class="d-flex justify-content-center align-items-center gap-1 mt-2">
                                                <div class="slot-badge <?= strtolower($s['jenis'] ?? 'umum') ?>"><?= $s['jenis'] ?? 'Umum' ?></div>
                                                <div class="slot-quota"><?= $s['kuota'] ?? 0 ?> Pasien</div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div> 
    </div>

    <!-- ================= MODAL SETTING (UPDATE) ================= -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 position-relative" style="border-radius: 30px; overflow: hidden;">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4 z-3" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;"></button>
                <div class="modal-body p-0">
                    <div class="d-flex flex-column flex-md-row" style="min-height: 550px;">
                        
                        <div class="p-4 d-flex flex-column" style="width: 100%; max-width: 250px; background: #f8fafb; border-right: 1px solid #eee;">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-gear-fill me-2 text-teal-mediflow"></i>Settings</h5>
                            <div class="nav flex-column nav-pills flex-grow-1" id="settings-tabs" role="tablist">
                                <button class="nav-link active small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-account" type="button" role="tab"><i class="bi bi-person-circle me-2"></i> Biodata Dokter</button>
                            </div>
                            <div class="mt-auto pt-3 border-top">
                                <button class="btn btn-settings-logout w-100 rounded-pill py-2 fw-bold small text-start px-3" onclick="window.location.href='../login/logout.php'"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                            </div>
                        </div>

                        <div class="p-4 p-md-5 flex-grow-1 bg-white" style="max-height: 600px; overflow-y: auto;">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-account" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Biodata Akun Dokter</h6>
                                    
                                    <form action="update_profil_dokter.php" method="POST">
                                        <!-- Nama (GABISA DIRUBAH) -->
                                        <div class="mb-3">
                                            <label class="small fw-bold mb-1">Nama Lengkap & Gelar <i class="bi bi-lock-fill text-muted ms-1" style="font-size:0.7rem;"></i></label>
                                            <input type="text" class="form-control form-control-sm rounded-3 py-2 bg-light text-muted" value="<?= htmlspecialchars($_SESSION['nama'] ?? ''); ?>" readonly style="cursor: not-allowed;">
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <!-- Poliklinik (GABISA DIRUBAH) -->
                                            <div class="col-md-6">
                                                <label class="small fw-bold mb-1">Poliklinik / Spesialisasi <i class="bi bi-lock-fill text-muted ms-1" style="font-size:0.7rem;"></i></label>
                                                <input type="text" class="form-control form-control-sm rounded-3 py-2 bg-light text-muted" value="<?= htmlspecialchars($_SESSION['poli'] ?? 'Poliklinik Umum'); ?>" readonly style="cursor: not-allowed;">
                                            </div>
                                            <!-- Jam Kerja Shift PATEN (GABISA DIRUBAH) -->
                                            <div class="col-md-6">
                                                <label class="small fw-bold mb-1">Jam Kerja / Shift <i class="bi bi-lock-fill text-muted ms-1" style="font-size:0.7rem;"></i></label>
                                                <input type="text" class="form-control form-control-sm rounded-3 py-2 bg-light text-muted" value="Pagi (08:00 - 12:00)" readonly style="cursor: not-allowed;">
                                            </div>
                                        </div>

                                        <hr class="my-4" style="opacity: 0.1;">
                                        
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="small fw-bold mb-1">Email Login</label>
                                                <input type="email" name="email" class="form-control form-control-sm rounded-3 py-2" value="<?= htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                                            </div>
                                            <!-- PASSWORD DENGAN ICON MATA -->
                                            <div class="col-md-6">
                                                <label class="small fw-bold mb-1">Password Baru</label>
                                                <div class="position-relative">
                                                    <input type="password" id="settingsPasswordInput" name="password" class="form-control form-control-sm rounded-3 py-2" placeholder="Kosongkan jika tak diubah" style="padding-right: 35px;">
                                                    <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-2" id="toggleSettingsPassword" style="cursor: pointer; color: #a0b8c2; font-size: 1.1rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-teal w-100 rounded-3 shadow-sm py-2 fw-bold text-white">Simpan Perubahan Akun</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lempar Array dari PHP ke JS -->
    <script>
        const antreanData = <?= json_encode($antrean_arr); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dokter.js?v=<?= time(); ?>"></script>
</body>
</html>