<?php 
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");
if (!$koneksi) { die("Koneksi database gagal."); }

if(!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['role'] != "pasien"){
    header("location:../login/index.php?pesan=belum_login");
    exit(); 
}

$email_user = $_SESSION['email'];

$tanggal_lahir = isset($_SESSION['tanggal_lahir']) ? $_SESSION['tanggal_lahir'] : '2000-01-01';
$lahir = new DateTime($tanggal_lahir);
$hari_ini = new DateTime("today");
$umur = $lahir->diff($hari_ini)->y; 

$hash_email = md5($email_user ?? 'pasien');
$angka_unik = preg_replace("/[^0-9]/", "", $hash_email); 
$no_rm = "RM-" . substr($angka_unik, 0, 4); 
if(strlen($no_rm) < 7) { $no_rm = "RM-" . rand(1000,9999); } 

// ==========================================
// DETEKSI DEVICE REAL UNTUK TAB PRIVASI
// ==========================================
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$os_platform  = "Unknown OS";
$os_array = array(
    '/windows nt 10/i'      =>  'Windows',
    '/windows nt 6.3/i'     =>  'Windows',
    '/windows nt 6.2/i'     =>  'Windows',
    '/macintosh|mac os x/i' =>  'Mac OS',
    '/linux/i'              =>  'Linux',
    '/iphone/i'             =>  'iOS',
    '/android/i'            =>  'Android'
);
foreach ($os_array as $regex => $value) { 
    if (preg_match($regex, $user_agent)) { $os_platform = $value; break; } 
}

$browser = "Unknown Browser";
$browser_array = array(
    '/edg/i'       => 'Edge',
    '/chrome/i'    => 'Google Chrome',
    '/firefox/i'   => 'Firefox',
    '/safari/i'    => 'Safari',
    '/opera/i'     => 'Opera'
);
foreach ($browser_array as $regex => $value) { 
    if (preg_match($regex, $user_agent)) { $browser = $value; break; } 
}
$device_icon = in_array($os_platform, ['Android', 'iOS']) ? 'bi-phone' : 'bi-laptop';

// ==========================================
// MENGAMBIL DATA KELUARGA DARI DATABASE
// ==========================================
$q_keluarga = mysqli_query($koneksi, "SELECT * FROM keluarga_pasien WHERE email_pasien_utama='$email_user'");
$array_keluarga = [];
$html_list_keluarga = "";
$html_tab_keluarga = "";
$index_keluarga = 1; 

while($kel = mysqli_fetch_assoc($q_keluarga)){
    $lahir_kel = new DateTime($kel['tanggal_lahir']);
    $umur_kel = $lahir_kel->diff($hari_ini)->y . " Tahun";
    
    $foto_path = ($kel['foto_profil'] != '') ? '../uploads/'.$kel['foto_profil'] : '';
    $inisial = strtoupper(substr($kel['nama'], 0, 1));
    $nama_panggilan = explode(" ", $kel['nama'])[0];
    $id_kel = $kel['id'];

    $array_keluarga[] = [
        'id' => $id_kel, 'nama' => $kel['nama'], 'nik' => $kel['nik'], 'tanggal_lahir' => $kel['tanggal_lahir'], 
        'hubungan' => $kel['hubungan'], 'umur' => $umur_kel, 'rm' => "RM-K" . rand(1000,9999), 
        'darah' => $kel['gol_darah'], 'alamat' => $kel['alamat'], 'bpjs' => $kel['no_bpjs'], 'foto' => $foto_path
    ];

    $html_list_keluarga .= "
        <li class='list-group-item d-flex justify-content-between align-items-center px-2 py-3 rounded-3 mb-2 border' id='list-kel-{$id_kel}'>
            <div class='d-flex align-items-center'>
                <div class='rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3 overflow-hidden' style='width: 40px; height: 40px; font-weight: bold;'>";
    if($foto_path != ''){ $html_list_keluarga .= "<img src='$foto_path' id='list-img-{$id_kel}' style='width:100%; height:100%; object-fit:cover;'>"; } 
    else { $html_list_keluarga .= "<span id='list-init-{$id_kel}'>{$inisial}</span>"; }
    $html_list_keluarga .= "</div>
                <div><h6 class='mb-0 small fw-bold text-dark' id='list-nama-{$id_kel}'>{$kel['nama']}</h6><span class='small text-muted' id='list-hub-{$id_kel}' style='font-size: 0.75rem;'>{$kel['hubungan']}</span></div>
            </div>
            <div>
                <i class='bi bi-pencil-square text-primary fs-5 me-2' style='cursor:pointer;' onclick='bukaFormEditKeluarga({$id_kel}, {$index_keluarga})'></i>
                <i class='bi bi-trash text-danger fs-5' style='cursor:pointer;' onclick='hapusKeluarga({$id_kel}, {$index_keluarga})'></i>
            </div>
        </li>";

    $html_tab_keluarga .= "<div class='kk-tab flex-shrink-0' id='tab-kel-{$id_kel}' onclick='switchProfileTab({$index_keluarga}, this)'>{$nama_panggilan}</div>";
    $index_keluarga++;
}

$q_jadwal_aktif = mysqli_query($koneksi, "SELECT * FROM riwayat_berobat WHERE email_pasien='$email_user' AND status='Menunggu' ORDER BY created_at DESC LIMIT 1");
$jadwal_aktif = mysqli_fetch_assoc($q_jadwal_aktif);
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
    <link rel="stylesheet" href="pasien.css?v=<?= time(); ?>">
</head>
<body class="bg-pasien">
    <button class="btn-emergency shadow-lg" onclick="alert('Panggilan Darurat: 119')"><i class="bi bi-telephone-fill"></i></button>

    <div class="ashley-container">
        <div class="ash-main" id="main-content-area">
            
            <!-- VIEW HOME -->
            <div id="view-home" class="view-section">
                <div class="d-flex align-items-center mt-2 mb-3">
                    <i class="bi bi-heart-pulse fs-3 text-teal-mediflow me-2"></i>
                    <h4 class="fw-bold text-teal-mediflow mb-0 lh-1">MediFlow</h4>
                </div>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: #2c3e50;">Hello, <span id="ashNameTitle"><?= htmlspecialchars(explode(" ", $_SESSION['nama'] ?? 'Pasien')[0]); ?></span></h2>
                        <p class="text-muted small mb-0 fw-medium" id="realtime-datetime">Memuat waktu...</p>
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
                        <?php if($jadwal_aktif) { 
                            $tgl_j = date('d M Y', strtotime($jadwal_aktif['tanggal_periksa']));
                        ?>
                            <div class="bg-white rounded-4 shadow-sm h-100 p-4 position-relative overflow-hidden border-start border-5" style="border-color: #f39c12 !important;">
                                <div class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 fw-bold small" style="border-bottom-left-radius: 15px;">MENUNGGU</div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-check text-teal-mediflow me-2"></i>Jadwal Anda Berikutnya</h6>
                                <hr class="my-2" style="opacity:0.1">
                                <div class="row g-2 mt-1 small">
                                    <div class="col-6"><span class="text-muted d-block" style="font-size:0.7rem;">Poliklinik</span><span class="fw-bold"><?= $jadwal_aktif['poli']; ?></span></div>
                                    <div class="col-6"><span class="text-muted d-block" style="font-size:0.7rem;">Dokter</span><span class="fw-bold"><?= $jadwal_aktif['nama_dokter']; ?></span></div>
                                    <div class="col-6 mt-3"><span class="text-muted d-block" style="font-size:0.7rem;">Tanggal & Waktu</span><span class="fw-bold text-danger"><?= $tgl_j; ?><br><?= $jadwal_aktif['waktu_kunjungan']; ?></span></div>
                                    <div class="col-6 mt-3"><span class="text-muted d-block" style="font-size:0.7rem;">Bed Inap</span><span class="fw-bold"><?= ($jadwal_aktif['nomor_bed']) ? 'Bed '.$jadwal_aktif['nomor_bed'] : 'Rawat Jalan'; ?></span></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="bg-white rounded-4 shadow-sm h-100 p-4 d-flex flex-column justify-content-center align-items-center text-center" style="border: 2px dashed #d1e5e5;">
                                <div class="mb-3"><i class="bi bi-calendar2-x text-muted" style="font-size: 3.5rem; opacity: 0.5;"></i></div>
                                <h6 class="fw-bold text-dark mb-2">Belum Ada Jadwal Hari Ini</h6>
                                <p class="text-muted small mb-0" style="max-width: 85%;">Anda belum memiliki jadwal periksa. Klik menu <strong>'Jadwal'</strong> untuk mendaftar antrean.</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="menu-cards-container">
                    <div class="menu-card" onclick="switchView('view-kamar')">
                        <div class="menu-icon-top"><i class="bi bi-hospital"></i></div>
                        <div class="menu-title">INFORMASI KAMAR</div>
                        <div class="menu-subtitle">Pilih slot rawat inap.</div>
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
                                <a href="javascript:void(0)" class="text-teal-mediflow small text-decoration-none fw-bold" onclick="switchView('view-riwayat')">Lihat Selengkapnya <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                            
                            <?php 
                            $q_riwayat_1 = mysqli_query($koneksi, "SELECT * FROM riwayat_berobat WHERE email_pasien='$email_user' AND status='Selesai' ORDER BY tanggal_periksa DESC LIMIT 1");
                            if($q_riwayat_1 && mysqli_num_rows($q_riwayat_1) > 0) {
                                $data_r = mysqli_fetch_assoc($q_riwayat_1);
                                $tgl = date('d M Y', strtotime($data_r['tanggal_periksa']));
                            ?>
                                <div class="border rounded-3 p-3 mt-2" style="border-left: 4px solid var(--mediflow-blue) !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill mb-1 px-2 py-1" style="font-size: 0.7rem;"><?= $data_r['status']; ?></span>
                                            <h6 class="fw-bold mb-1" style="color: #2c3e50;"><?= $data_r['poli']; ?></h6>
                                            <p class="text-muted small mb-0"><i class="bi bi-person me-1"></i> <?= $data_r['nama_dokter']; ?></p>
                                        </div>
                                        <div class="text-end">
                                            <span class="d-block small fw-bold text-dark"><?= $tgl; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="d-flex flex-column align-items-center justify-content-center text-center py-4 mt-2" style="border: 2px dashed #f0f0f0; border-radius: 15px; background-color: #fcfcfc;">
                                    <i class="bi bi-folder-x text-muted mb-2" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                    <h6 class="fw-bold text-dark mb-1 small">Belum Ada Riwayat</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem; max-width: 80%;">Rekam medis akan otomatis muncul di sini setelah Anda selesai konsultasi.</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div> 
            
            <!-- VIEW PEMILIHAN SLOT KAMAR -->
            <div id="view-kamar" class="view-section d-none position-relative">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Pilih Slot Rawat Inap</h4>
                </div>
                
                <div class="alert border-0 rounded-4 mb-4 small shadow-sm d-flex align-items-center" style="background-color: #e2f5fa; color: #1e2f3a;">
                    <i class="bi bi-info-circle-fill text-teal-mediflow fs-4 me-3"></i> 
                    <div>Pilih slot (warna biru gelap) pada daftar di bawah ini. Slot yang terisi (abu-abu) tidak dapat dipilih.</div>
                </div>

                <div class="tix-legend">
                    <div class="tix-legend-item"><div class="tix-box avail"></div> Tersedia</div>
                    <div class="tix-legend-item"><div class="tix-box unavail"></div> Terisi</div>
                    <div class="tix-legend-item"><div class="tix-box yourseat"></div> Pilihan Anda</div>
                </div>

                <div class="row g-4 mb-5 pb-5">
                    <?php
                    $q_kamar = mysqli_query($koneksi, "SELECT * FROM kamar");
                    if($q_kamar && mysqli_num_rows($q_kamar) > 0) {
                        while($kamar = mysqli_fetch_assoc($q_kamar)) {
                            $id_kamar = $kamar['id'];
                    ?>
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm position-relative border-start border-5" style="border-color: <?= $kamar['warna_hex']; ?> !important;">
                                <div class="d-flex align-items-center mb-1">
                                    <h5 class="fw-bold mb-0 me-2"><?= $kamar['nama_kelas']; ?></h5>
                                    <i class="bi <?= $kamar['icon_bi']; ?> fs-5"></i>
                                </div>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($kamar['deskripsi']); ?></p>
                                
                                <div class="mt-3 pt-3 border-top">
                                    <div class="bed-grid">
                                        <?php 
                                        $q_bed = mysqli_query($koneksi, "SELECT * FROM bed_kamar WHERE id_kamar='$id_kamar'");
                                        if(mysqli_num_rows($q_bed) > 0){
                                            while($bed = mysqli_fetch_assoc($q_bed)){
                                                $status_class = strtolower($bed['status']); 
                                        ?>
                                            <div class="tix-seat <?= $status_class; ?>" onclick="pilihBed(this, '<?= $bed['nomor_bed']; ?>', '<?= $kamar['nama_kelas']; ?>')">
                                                <?= $bed['nomor_bed']; ?>
                                            </div>
                                        <?php } } else { echo "<p class='small text-muted w-100 text-center mt-3'>Belum ada data slot.</p>"; } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } } ?>
                </div>

                <div id="bookingBar" class="floating-booking-bar d-none">
                    <div>
                        <span class="small text-muted d-block mb-1">Bed Dipilih:</span>
                        <h5 class="fw-bold text-dark mb-0" id="txtSelectedBed">-</h5>
                    </div>
                    <button class="btn btn-teal rounded-pill px-4 fw-bold shadow-sm" onclick="prosesPesanKamar()">Lanjutkan <i class="bi bi-arrow-right ms-2"></i></button>
                </div>
            </div>

            <!-- VIEW JADWAL -->
            <div id="view-jadwal" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Pendaftaran Janji Temu</h4>
                </div>
                
                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <div class="row g-4">
                        
                        <div class="col-md-12 mb-2">
                            <div class="p-3 rounded-4 bg-light" style="border: 2px dashed #d1e5e5;">
                                <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-hospital text-teal-mediflow me-2"></i>Bed Rawat Inap (Opsional)</h6>
                                <div id="infoKamarTerpilih">
                                    <div class="text-muted small">Belum ada kamar/bed yang dipilih. (Abaikan jika hanya butuh Rawat Jalan).</div>
                                    <button type="button" class="btn btn-sm btn-outline-teal mt-2" onclick="switchView('view-kamar')"><i class="bi bi-search me-1"></i> Cari / Pilih Kamar</button>
                                </div>
                                <input type="hidden" id="inputKamarTerpilih" name="kamar_terpilih" value="">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Pilih Spesialisasi / Poliklinik</label>
                            <div class="dropdown w-100">
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dd-trigger text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textPoli">-- Silakan Pilih Poliklinik --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu" id="listPoli" style="max-height: 250px; overflow-y: auto;">
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Poliklinik Umum (Dokter Umum)">Poliklinik Umum (Dokter Umum)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header fw-bold text-primary">Layanan Utama</h6></li>
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Spesialis Penyakit Dalam (Sp.PD)">Spesialis Penyakit Dalam (Sp.PD)</a></li>
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Spesialis Anak (Sp.A)">Spesialis Anak (Sp.A)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header fw-bold text-primary">Layanan Khusus</h6></li>
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Spesialis Jantung & Pembuluh Darah (Sp.JP)">Spesialis Jantung & Pembuluh Darah (Sp.JP)</a></li>
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Spesialis Mata (Sp.M)">Spesialis Mata (Sp.M)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header fw-bold text-primary">Penunjang & Khusus</h6></li>
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Spesialis Anestesiologi (Sp.An)">Spesialis Anestesiologi (Sp.An)</a></li>
                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Spesialis Radiologi (Sp.Rad)">Spesialis Radiologi (Sp.Rad)</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tanggal Rencana Periksa</label>
                            <input type="date" id="inputTanggal" onchange="cekKuotaJadwal(this.value)" class="form-control rounded-pill border-2" style="padding: 12px 20px; font-size: 0.9rem;" min="<?= date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Pilih Dokter Jaga</label>
                            <div class="dropdown w-100">
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dd-trigger text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textDokter">-- Pilih Poliklinik Terlebih Dahulu --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu" id="listDokter">
                                    <li><a class="dropdown-item custom-dd-item disabled text-muted" href="#">-- Pilih Poliklinik --</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Waktu Kunjungan / Shift (Terisi Otomatis)</label>
                            <input type="text" id="textJam" class="form-control rounded-pill border-2 bg-light text-muted fw-bold" style="padding: 12px 20px; font-size: 0.9rem; cursor: not-allowed;" value="-- Pilih Dokter Terlebih Dahulu --" readonly>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold small">Keluhan Utama (Singkat)</label>
                            <textarea id="inputKeluhan" class="form-control rounded-4 border-2" rows="3" placeholder="Deskripsikan keluhan Anda secara singkat..."></textarea>
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <button id="btnBukaKonfirmasi" class="btn w-100 rounded-pill py-3 fw-bold shadow-sm btn-teal" onclick="bukaKonfirmasiJadwal()">Ajukan Janji Temu Sekarang</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW RIWAYAT MEDIS LENGKAP -->
            <div id="view-riwayat" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Riwayat Medis Lengkap</h4>
                </div>
                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <?php 
                    $q_riwayat_all = mysqli_query($koneksi, "SELECT * FROM riwayat_berobat WHERE email_pasien='$email_user' AND status='Selesai' ORDER BY tanggal_periksa DESC");
                    if($q_riwayat_all && mysqli_num_rows($q_riwayat_all) > 0) {
                        while($row = mysqli_fetch_assoc($q_riwayat_all)) {
                            $tgl_all = date('d M Y', strtotime($row['tanggal_periksa']));
                    ?>
                        <div class="border-bottom pb-4 mb-4 riwayat-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill mb-2 px-3 py-1 small border border-success"><?= $row['status']; ?></span>
                                    <h6 class="fw-bold mb-1" style="color: #2c3e50;"><?= $row['poli']; ?></h6>
                                    <p class="text-muted small mb-0"><i class="bi bi-person me-1"></i> <?= $row['nama_dokter']; ?></p>
                                </div>
                                <div class="text-end">
                                    <span class="d-block small fw-bold text-dark"><?= $tgl_all; ?></span>
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded-3 mt-3 border" style="border-left: 4px solid var(--mediflow-blue) !important;">
                                <p class="small mb-1"><span class="fw-bold text-dark">Diagnosis:</span> <?= htmlspecialchars($row['diagnosis']); ?></p>
                                <p class="small mb-0"><span class="fw-bold text-dark">Obat:</span> <?= htmlspecialchars($row['obat']); ?></p>
                            </div>
                        </div>
                    <?php } } else { ?>
                        <div class="d-flex flex-column align-items-center justify-content-center text-center py-5">
                            <i class="bi bi-inbox text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="fw-bold text-dark mb-2">Belum Ada Riwayat</h5>
                            <p class="text-muted">Riwayat medis lengkap Anda akan tersimpan otomatis setelah berkonsultasi.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div> 
        
        <div class="ash-right position-relative" id="rightPanel">
            <button class="toggle-panel-btn" onclick="togglePanel()"><i class="bi bi-chevron-right fs-5"></i></button>

            <div class="d-flex gap-2 bg-light p-1 rounded-4 mb-4 mt-2" id="familyTabsContainer" style="overflow-x: auto;">
                <div class="kk-tab active flex-shrink-0" onclick="switchProfileTab(0, this)">Utama</div>
                <?= $html_tab_keluarga; ?> 
            </div>

            <div class="text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-medium shadow-sm avatar-circle overflow-hidden position-relative" style="width: 85px; height: 85px; background-color: #72b9b9; font-size: 2rem;">
                    <img id="mainProfilePic" src="<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '../uploads/'.$_SESSION['foto_profil'] : ''; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; <?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '' : 'display: none;'; ?>">
                    <span id="mainProfileInitials" style="<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? 'display: none;' : ''; ?>"><?= substr($_SESSION['nama'] ?? 'P', 0, 1); ?></span>
                </div>
                <h5 class="fw-bold mb-0 text-dark mt-2" id="sidebarName"><?= htmlspecialchars($_SESSION['nama'] ?? 'Nama Pasien'); ?></h5>
                <p class="small text-muted mb-2" id="sidebarEmail"><?= htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                <span id="sidebarStatusBadge" class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mb-3">Utama (Anda)</span>
            </div>
            
            <div class="bg-light p-3 rounded-4 mb-4 small">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">No. RM</span><span class="fw-bold text-dark" id="sidebarRm"><?= $no_rm; ?></span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">NIK</span><span class="fw-bold text-dark" id="sidebarNik"><?= htmlspecialchars($_SESSION['nik'] ?? '-'); ?></span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">Umur</span><span class="fw-bold text-dark" id="sidebarUmur"><?= $umur; ?> Tahun</span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">Gol. Darah</span><span class="fw-bold text-danger" id="sidebarDarah"><?= htmlspecialchars($_SESSION['gol_darah'] ?? '-'); ?></span></div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span class="text-muted">Alamat</span><span class="fw-bold text-dark text-end ms-4" id="sidebarAlamat"><?= htmlspecialchars($_SESSION['alamat'] ?? '-'); ?></span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">No. BPJS</span><span class="fw-bold text-dark" id="sidebarBpjs"><?= htmlspecialchars($_SESSION['no_bpjs'] ?? '-'); ?></span></div>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI JADWAL (BARU) -->
    <div class="modal fade" id="konfirmasiJadwalModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 p-4 shadow-lg">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px; background-color: rgba(56, 200, 230, 0.1);">
                            <i class="bi bi-question-circle-fill text-teal-mediflow" style="font-size: 3.5rem;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Konfirmasi Janji Temu</h5>
                    <p class="text-muted small mb-4">Pastikan data pilihan dokter dan jadwal Anda sudah benar sebelum melanjutkan.</p>
                    
                    <div class="bg-light rounded-4 p-3 text-start small mb-4" style="border: 2px dashed #d1e5e5;">
                        <div class="row g-2">
                            <div class="col-5 text-muted">Poliklinik</div><div class="col-7 fw-bold text-dark" id="konfPoli">-</div>
                            <div class="col-5 text-muted">Dokter Jaga</div><div class="col-7 fw-bold text-dark" id="konfDokter">-</div>
                            <div class="col-5 text-muted mt-2">Tanggal Periksa</div><div class="col-7 fw-bold text-danger mt-2" id="konfTgl">-</div>
                            <div class="col-5 text-muted">Shift Waktu</div><div class="col-7 fw-bold text-primary" id="konfWaktu">-</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border w-50 rounded-pill fw-bold py-2 text-muted" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-teal w-50 rounded-pill fw-bold py-2" onclick="kirimJadwalAlert()" id="btnProsesKonfirmasi">Ya, Konfirmasi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP SUKSES JADWAL -->
    <div class="modal fade" id="successJadwalModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 text-center p-4 shadow-lg">
                <div class="modal-body">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3 text-dark">Jadwal Berhasil Dikirim!</h5>
                    <p class="text-muted small">Pendaftaran janji temu Anda telah berhasil disimpan dan sedang menunggu antrean. Silakan cek statusnya di halaman utama.</p>
                    <button type="button" class="btn btn-teal rounded-pill px-5 fw-bold mt-2" data-bs-dismiss="modal">Tutup & Kembali ke Beranda</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SETTINGS (TERMASUK PRIVASI BARU) -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 position-relative" style="border-radius: 30px; overflow: hidden;">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4 z-3" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;"></button>
                <div class="modal-body p-0">
                    <div class="d-flex flex-column flex-md-row" style="min-height: 550px;">
                        
                        <!-- SIDEBAR KIRI MODAL -->
                        <div class="p-4 d-flex flex-column" style="width: 100%; max-width: 250px; background: #f8fafb; border-right: 1px solid #eee;">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-gear-fill me-2 text-teal-mediflow"></i>Settings</h5>
                            <div class="nav flex-column nav-pills flex-grow-1" id="settings-tabs" role="tablist">
                                <button class="nav-link active small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-account" type="button" role="tab"><i class="bi bi-person-circle me-2"></i> Biodata Akun</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-family" type="button" role="tab"><i class="bi bi-people me-2"></i> Keluarga</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-privacy-set" type="button" role="tab"><i class="bi bi-lock me-2"></i> Privasi</button>
                            </div>
                            <div class="mt-auto pt-3 border-top">
                                <button class="btn btn-settings-logout w-100 rounded-pill py-2 fw-bold small text-start px-3" onclick="logoutSession()"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                            </div>
                        </div>

                        <!-- KONTEN KANAN MODAL -->
                        <div class="p-4 p-md-5 flex-grow-1 bg-white" style="max-height: 600px; overflow-y: auto;">
                            <div class="tab-content" id="pills-tabContent">
                                
                                <!-- ======================= TAB BIODATA AKUN ======================= -->
                                <div class="tab-pane fade show active" id="pills-account" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-3">Biodata Akun</h6>
                                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                        <div class="position-relative me-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-medium shadow-sm overflow-hidden" style="width: 75px; height: 75px; background-color: #72b9b9; font-size: 2rem;">
                                                <img id="settingsProfilePic" src="<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '../uploads/'.$_SESSION['foto_profil'] : ''; ?>" style="width: 100%; height: 100%; object-fit: cover; <?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '' : 'display: none;'; ?>">
                                                <span id="settingsProfileInitials" style="<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? 'display: none;' : ''; ?>"><?= substr($_SESSION['nama'] ?? 'P', 0, 1); ?></span>
                                            </div>
                                            <label for="uploadProfile" class="position-absolute bottom-0 end-0 bg-teal-mediflow text-white rounded-circle shadow" style="cursor: pointer; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; transform: translate(10%, 10%);"><i class="bi bi-camera-fill" style="font-size: 0.75rem;"></i></label>
                                            <input type="file" id="uploadProfile" class="d-none" accept="image/png, image/jpeg, image/jpg">
                                        </div>
                                        <div><h6 class="fw-bold mb-1 small text-dark">Foto Profil</h6><p class="small text-muted mb-0" style="font-size: 0.75rem;">Format: JPG, PNG, JPEG.</p></div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4"><label class="small fw-bold mb-1">No. RM</label><input type="text" class="form-control form-control-sm rounded-3 fw-bold text-secondary" readonly style="background-color: #f0f0f0;" value="<?= $no_rm; ?>"></div>
                                        <div class="col-md-8"><label class="small fw-bold mb-1">Nama Lengkap</label><input type="text" id="settingsNameInput" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($_SESSION['nama'] ?? ''); ?>"></div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6"><label class="small fw-bold mb-1">NIK</label><input type="number" id="settingsNikInput" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($_SESSION['nik'] ?? ''); ?>" placeholder="16 Digit NIK"></div>
                                        <div class="col-md-6"><label class="small fw-bold mb-1">Tanggal Lahir</label><input type="date" id="settingsTglLahirInput" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($_SESSION['tanggal_lahir'] ?? ''); ?>"></div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small fw-bold mb-1">Golongan Darah</label>
                                            <div class="dropdown dropdown-custom-container w-100">
                                                <select id="settingsGolDarahInput" class="d-none">
                                                    <option value="-" <?= (!isset($_SESSION['gol_darah']) || $_SESSION['gol_darah'] == '-' || $_SESSION['gol_darah'] == '') ? 'selected' : ''; ?>>Pilih...</option>
                                                    <option value="A" <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'A') ? 'selected' : ''; ?>>A</option>
                                                    <option value="B" <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'B') ? 'selected' : ''; ?>>B</option>
                                                    <option value="AB" <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'AB') ? 'selected' : ''; ?>>AB</option>
                                                    <option value="O" <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'O') ? 'selected' : ''; ?>>O</option>
                                                </select>
                                                <div class="form-control form-control-sm rounded-3 dropdown-toggle d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" style="cursor: pointer; background-color: #fff; height: 31px;">
                                                    <span><?= isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] != '' && $_SESSION['gol_darah'] != '-' ? $_SESSION['gol_darah'] : 'Pilih...'; ?></span><i class="bi bi-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu">
                                                    <li><a class="dropdown-item custom-dd-item <?= (!isset($_SESSION['gol_darah']) || $_SESSION['gol_darah'] == '-' || $_SESSION['gol_darah'] == '') ? 'active' : ''; ?>" href="#" data-value="-">Pilih...</a></li>
                                                    <li><a class="dropdown-item custom-dd-item <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'A') ? 'active' : ''; ?>" href="#" data-value="A">A</a></li>
                                                    <li><a class="dropdown-item custom-dd-item <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'B') ? 'active' : ''; ?>" href="#" data-value="B">B</a></li>
                                                    <li><a class="dropdown-item custom-dd-item <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'AB') ? 'active' : ''; ?>" href="#" data-value="AB">AB</a></li>
                                                    <li><a class="dropdown-item custom-dd-item <?= (isset($_SESSION['gol_darah']) && $_SESSION['gol_darah'] == 'O') ? 'active' : ''; ?>" href="#" data-value="O">O</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6"><label class="small fw-bold mb-1">No. BPJS</label><input type="number" id="settingsBpjsInput" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($_SESSION['no_bpjs'] ?? ''); ?>"></div>
                                    </div>
                                    <div class="mb-3"><label class="small fw-bold mb-1">Alamat</label><textarea id="settingsAlamatInput" class="form-control form-control-sm rounded-3" rows="2"><?= htmlspecialchars($_SESSION['alamat'] ?? ''); ?></textarea></div>
                                    <hr class="my-4" style="opacity: 0.1;">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6"><label class="small fw-bold mb-1">Email Login</label><input type="email" id="settingsEmailInput" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($_SESSION['email'] ?? ''); ?>"></div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold mb-1">Password Baru</label>
                                            <div class="position-relative">
                                                <input type="password" id="settingsPasswordInput" class="form-control form-control-sm rounded-3" placeholder="Kosongkan jika tak diubah" style="padding-right: 35px;">
                                                <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-2" id="toggleSettingsPassword" style="cursor: pointer; color: #a0b8c2;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="btnSimpanAkun" class="btn btn-teal w-100 shadow-sm py-2 rounded-3" onclick="updateAuthData()">Simpan Perubahan Akun</button>
                                </div>
                                
                                <!-- ======================= TAB KELUARGA ======================= -->
                                <div class="tab-pane fade" id="pills-family" role="tabpanel" tabindex="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">Anggota Keluarga</h6>
                                        <button id="btnBukaFormKeluarga" class="btn btn-sm btn-outline-teal rounded-pill px-3 fw-bold" onclick="toggleFormKeluarga()"><i class="bi bi-plus-lg me-1"></i> Tambah</button>
                                    </div>
                                    
                                    <div class="alert alert-info border-0 rounded-4 small py-2 px-3 mb-3 d-flex align-items-center" style="background-color: #e2f5fa; color: #1e2f3a;">
                                        <i class="bi bi-info-circle-fill text-teal-mediflow fs-5 me-2"></i> Klik nama keluarga untuk beralih profil.
                                    </div>

                                    <ul class="list-group list-group-flush mb-4" id="settingsFamilyList">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-3 rounded-3 mb-2 border">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-teal-mediflow text-white d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; font-weight: bold; overflow: hidden;">
                                                    <img src="<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '../uploads/'.$_SESSION['foto_profil'] : ''; ?>" style="width: 100%; height: 100%; object-fit: cover; <?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '' : 'display: none;'; ?>">
                                                    <span style="<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? 'display: none;' : ''; ?>"><?= substr($_SESSION['nama'] ?? 'P', 0, 1); ?></span>
                                                </div>
                                                <div><h6 class="mb-0 small fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama'] ?? 'Pasien Utama'); ?></h6><span class="small text-muted" style="font-size: 0.75rem;">Utama (Anda)</span></div>
                                            </div>
                                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size:0.6rem;">Default</span>
                                        </li>
                                        <?= $html_list_keluarga; ?>
                                    </ul>

                                    <!-- FORM TAMBAH KELUARGA -->
                                    <div id="formTambahKeluarga" class="bg-light p-3 rounded-4 border d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0 text-primary">Tambah Anggota Keluarga</h6>
                                            <button type="button" class="btn-close" aria-label="Close" style="font-size:0.7rem;" onclick="toggleFormKeluarga()"></button>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="position-relative me-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white fw-medium shadow-sm overflow-hidden" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                                    <img id="previewFotoKel" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                    <i class="bi bi-person-fill" id="iconDefaultKel"></i>
                                                </div>
                                                <label for="kelFoto" class="position-absolute bottom-0 end-0 bg-teal-mediflow text-white rounded-circle shadow" style="cursor: pointer; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; transform: translate(10%, 10%);"><i class="bi bi-camera-fill" style="font-size: 0.5rem;"></i></label>
                                                <input type="file" id="kelFoto" class="d-none" accept="image/png, image/jpeg, image/jpg" onchange="document.getElementById('previewFotoKel').src = window.URL.createObjectURL(this.files[0]); document.getElementById('previewFotoKel').style.display='block'; document.getElementById('iconDefaultKel').style.display='none';">
                                            </div>
                                            <div class="small text-muted" style="font-size:0.75rem;">Unggah foto keluarga (Opsional)</div>
                                        </div>

                                        <div class="mb-2"><input type="text" id="kelNama" class="form-control form-control-sm rounded-3 py-2" placeholder="Nama Lengkap Keluarga" required></div>
                                        <div class="mb-2"><input type="number" id="kelNik" class="form-control form-control-sm rounded-3 py-2" placeholder="16 Digit NIK" required></div>
                                        
                                        <div class="row g-2 mb-2">
                                            <div class="col-6"><input type="date" id="kelTglLahir" class="form-control form-control-sm rounded-3 py-2 text-muted" required title="Tanggal Lahir"></div>
                                            <div class="col-6">
                                                <div class="dropdown dropdown-custom-container w-100">
                                                    <select id="kelGolDarah" class="d-none"><option value="-" selected>-</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select>
                                                    <div class="form-control form-control-sm rounded-3 py-2 dropdown-toggle d-flex align-items-center justify-content-between bg-white text-muted" data-bs-toggle="dropdown" style="cursor: pointer; height: 32px; font-size:0.875rem;">
                                                        <span>Gol. Darah</span><i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                                                    </div>
                                                    <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu">
                                                        <li><a class="dropdown-item custom-dd-item active" href="#" data-value="-">Pilih Gol...</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="A">A</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="B">B</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="AB">AB</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="O">O</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div class="dropdown dropdown-custom-container w-100" id="displayKelHubungan">
                                                <select id="kelHubungan" class="d-none" required><option value="" selected disabled></option><option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option><option value="Orang Tua">Orang Tua</option><option value="Saudara">Saudara</option><option value="Lainnya">Lainnya</option></select>
                                                <div class="form-control form-control-sm rounded-3 py-2 dropdown-toggle d-flex align-items-center justify-content-between bg-white text-muted" data-bs-toggle="dropdown" style="cursor: pointer; height: 32px; font-size:0.875rem;">
                                                    <span>Pilih Hubungan...</span><i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu">
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Suami">Suami</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Istri">Istri</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Anak">Anak</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Orang Tua">Orang Tua</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Saudara">Saudara</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Lainnya">Lainnya</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2"><input type="number" id="kelBpjs" class="form-control form-control-sm rounded-3 py-2" placeholder="No. BPJS (Opsional)"></div>
                                        <div class="mb-3"><textarea id="kelAlamat" class="form-control form-control-sm rounded-3" rows="2" placeholder="Alamat Domisili (Opsional)"></textarea></div>

                                        <button id="btnSimpanKel" class="btn btn-primary w-100 rounded-3 shadow-sm py-2 fw-bold text-white small" onclick="simpanKeluarga()">Simpan Data Keluarga</button>
                                    </div>

                                    <!-- FORM EDIT KELUARGA -->
                                    <div id="formEditKeluarga" class="bg-light p-3 rounded-4 border d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0 text-primary">Edit Data Keluarga</h6>
                                            <button type="button" class="btn-close" aria-label="Close" style="font-size:0.7rem;" onclick="batalEditKeluarga()"></button>
                                        </div>
                                        
                                        <input type="hidden" id="editKelId">
                                        <input type="hidden" id="editKelIndex">

                                        <div class="d-flex align-items-center mb-3">
                                            <div class="position-relative me-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white fw-medium shadow-sm overflow-hidden" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                                    <img id="previewEditFotoKel" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                    <i class="bi bi-person-fill" id="iconEditDefaultKel"></i>
                                                </div>
                                                <label for="editKelFoto" class="position-absolute bottom-0 end-0 bg-teal-mediflow text-white rounded-circle shadow" style="cursor: pointer; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; transform: translate(10%, 10%);"><i class="bi bi-camera-fill" style="font-size: 0.5rem;"></i></label>
                                                <input type="file" id="editKelFoto" class="d-none" accept="image/png, image/jpeg, image/jpg" onchange="document.getElementById('previewEditFotoKel').src = window.URL.createObjectURL(this.files[0]); document.getElementById('previewEditFotoKel').style.display='block'; document.getElementById('iconEditDefaultKel').style.display='none';">
                                            </div>
                                        </div>

                                        <div class="mb-2"><label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">Nama Lengkap</label><input type="text" id="editKelNama" class="form-control form-control-sm rounded-3 py-2" required></div>
                                        <div class="mb-2"><label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">NIK</label><input type="number" id="editKelNik" class="form-control form-control-sm rounded-3 py-2" required></div>
                                        
                                        <div class="row g-2 mb-2">
                                            <div class="col-6"><label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">Tgl Lahir</label><input type="date" id="editKelTglLahir" class="form-control form-control-sm rounded-3 py-2 text-muted" required></div>
                                            <div class="col-6">
                                                <label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">Gol. Darah</label>
                                                <div class="dropdown dropdown-custom-container w-100">
                                                    <select id="editKelGolDarah" class="d-none"><option value="-">-</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select>
                                                    <div class="form-control form-control-sm rounded-3 py-2 dropdown-toggle d-flex align-items-center justify-content-between bg-white text-dark" data-bs-toggle="dropdown" style="cursor: pointer; height: 32px; font-size:0.875rem;">
                                                        <span>-</span><i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                                                    </div>
                                                    <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu">
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="-">Pilih Gol...</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="A">A</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="B">B</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="AB">AB</a></li>
                                                        <li><a class="dropdown-item custom-dd-item" href="#" data-value="O">O</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">Hubungan Keluarga</label>
                                            <div class="dropdown dropdown-custom-container w-100">
                                                <select id="editKelHubungan" class="d-none" required><option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option><option value="Orang Tua">Orang Tua</option><option value="Saudara">Saudara</option><option value="Lainnya">Lainnya</option></select>
                                                <div class="form-control form-control-sm rounded-3 py-2 dropdown-toggle d-flex align-items-center justify-content-between bg-white text-dark" data-bs-toggle="dropdown" style="cursor: pointer; height: 32px; font-size:0.875rem;">
                                                    <span>Suami</span><i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu">
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Suami">Suami</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Istri">Istri</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Anak">Anak</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Orang Tua">Orang Tua</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Saudara">Saudara</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Lainnya">Lainnya</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2"><label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">No. BPJS</label><input type="number" id="editKelBpjs" class="form-control form-control-sm rounded-3 py-2"></div>
                                        <div class="mb-3"><label class="small fw-bold mb-1 text-muted" style="font-size:0.7rem;">Alamat</label><textarea id="editKelAlamat" class="form-control form-control-sm rounded-3" rows="2"></textarea></div>

                                        <div class="d-flex gap-2">
                                            <button class="btn border bg-white w-50 rounded-3 py-2 text-muted fw-bold small shadow-sm" onclick="batalEditKeluarga()">Batal</button>
                                            <button id="btnSimpanEditKel" class="btn btn-primary w-50 rounded-3 shadow-sm py-2 fw-bold text-white small" onclick="simpanEditKeluarga()">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- ======================= TAB PRIVASI & KEAMANAN ======================= -->
                                <div class="tab-pane fade" id="pills-privacy-set" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Privasi & Keamanan</h6>
                                    
                                    <div class="bg-light p-3 rounded-4 border mb-4">
                                        <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                            <i class="bi bi-shield-check text-success fs-4 me-3"></i>
                                            <div>
                                                <h6 class="mb-0 fw-bold small text-dark">Autentikasi Dua Faktor (2FA)</h6>
                                                <span class="small text-muted" style="font-size: 0.75rem;">Tingkatkan keamanan akun Anda.</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="form-check-label small fw-medium text-dark" for="switch2fa">Aktifkan 2FA via Email</label>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input fs-5 m-0" type="checkbox" role="switch" id="switch2fa" style="cursor:pointer;" onchange="alert('Fitur OTP Email akan dikirimkan ke '+document.getElementById('settingsEmailInput').value+' saat login.')">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-4 border mb-4">
                                        <h6 class="fw-bold small text-dark mb-3 border-bottom pb-2">Riwayat Login Terakhir</h6>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <span class="d-block fw-bold small text-dark"><i class="bi <?= $device_icon ?> me-2"></i><?= $os_platform ?> - <?= $browser ?></span>
                                                <span class="text-muted" style="font-size:0.75rem; margin-left: 22px;"><i class="bi bi-geo-alt me-1"></i>Semarang, Indonesia</span>
                                            </div>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size:0.65rem;">Saat Ini</span>
                                        </div>
                                        
                                        <button class="btn btn-outline-danger w-100 rounded-pill py-2 small fw-bold" style="font-size:0.8rem;" onclick="alert('Semua sesi di perangkat lain telah dikeluarkan.')">Keluarkan Semua Perangkat Lain</button>
                                    </div>

                                    <div class="border border-danger border-opacity-25 bg-danger bg-opacity-10 p-3 rounded-4">
                                        <h6 class="fw-bold small text-danger mb-1">Hapus Akun</h6>
                                        <p class="text-muted mb-3" style="font-size: 0.75rem;">Semua data rekam medis dan antrean Anda akan terhapus secara permanen.</p>
                                        <button class="btn btn-danger w-100 rounded-pill py-2 small fw-bold shadow-sm" style="font-size:0.8rem;" onclick="if(confirm('Tindakan ini tidak bisa dibatalkan. Yakin hapus akun?')) { alert('Permintaan penghapusan akun telah dikirim ke Admin.'); }">Minta Penghapusan Akun</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PENGGABUNGAN DATA JAVASCRIPT PHP KE VARIABEL JS -->
    <script>
        let dataKeluarga = [
            {
                id: 0,
                nama: "<?= htmlspecialchars($_SESSION['nama'] ?? 'Pasien Utama'); ?>",
                nik: "<?= htmlspecialchars($_SESSION['nik'] ?? '-'); ?>",
                tanggal_lahir: "<?= htmlspecialchars($_SESSION['tanggal_lahir'] ?? ''); ?>",
                hubungan: "Utama (Anda)",
                umur: "<?= $umur; ?> Tahun",
                rm: "<?= $no_rm; ?>",
                darah: "<?= htmlspecialchars($_SESSION['gol_darah'] ?? '-'); ?>",
                alamat: "<?= htmlspecialchars($_SESSION['alamat'] ?? '-'); ?>",
                bpjs: "<?= htmlspecialchars($_SESSION['no_bpjs'] ?? '-'); ?>",
                foto: "<?= isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '' ? '../uploads/'.$_SESSION['foto_profil'] : ''; ?>" 
            },
            <?php foreach($array_keluarga as $kel_json){ echo json_encode($kel_json) . ","; } ?>
        ];
    </script>

    <!-- FUNGSI JS UNTUK TOGGLE EYE ICON PASSWORD DI DALAM MODAL -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePwd = document.getElementById('toggleSettingsPassword');
            if(togglePwd) {
                togglePwd.addEventListener('click', function() {
                    const pwdInput = document.getElementById('settingsPasswordInput');
                    if (pwdInput.type === 'password') {
                        pwdInput.type = 'text';
                        this.classList.remove('bi-eye-slash');
                        this.classList.add('bi-eye');
                        this.style.color = '#38c8e6'; // teal color when visible
                    } else {
                        pwdInput.type = 'password';
                        this.classList.remove('bi-eye');
                        this.classList.add('bi-eye-slash');
                        this.style.color = '#a0b8c2'; // muted color when hidden
                    }
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="pasien.js?v=<?= time(); ?>"></script>
</body>
</html>