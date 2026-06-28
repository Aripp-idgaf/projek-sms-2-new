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
        'id' => $id_kel,
        'nama' => $kel['nama'],
        'nik' => $kel['nik'],
        'tanggal_lahir' => $kel['tanggal_lahir'], 
        'hubungan' => $kel['hubungan'],
        'umur' => $umur_kel,
        'rm' => "RM-K" . rand(1000,9999), 
        'darah' => $kel['gol_darah'],
        'alamat' => $kel['alamat'],
        'bpjs' => $kel['no_bpjs'],
        'foto' => $foto_path
    ];

    $html_list_keluarga .= "
        <li class='list-group-item d-flex justify-content-between align-items-center px-2 py-3 rounded-3 mb-2 border' id='list-kel-{$id_kel}'>
            <div class='d-flex align-items-center'>
                <div class='rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3 overflow-hidden' style='width: 40px; height: 40px; font-weight: bold;'>";
    if($foto_path != ''){
        $html_list_keluarga .= "<img src='$foto_path' id='list-img-{$id_kel}' style='width:100%; height:100%; object-fit:cover;'>";
    } else {
        $html_list_keluarga .= "<span id='list-init-{$id_kel}'>{$inisial}</span>";
    }
    $html_list_keluarga .= "</div>
                <div>
                    <h6 class='mb-0 small fw-bold text-dark' id='list-nama-{$id_kel}'>{$kel['nama']}</h6>
                    <span class='small text-muted' id='list-hub-{$id_kel}' style='font-size: 0.75rem;'>{$kel['hubungan']}</span>
                </div>
            </div>
            <div>
                <i class='bi bi-pencil-square text-primary fs-5 me-2' style='cursor:pointer;' onclick='bukaFormEditKeluarga({$id_kel}, {$index_keluarga})' title='Edit Anggota'></i>
                <i class='bi bi-trash text-danger fs-5' style='cursor:pointer;' onclick='hapusKeluarga({$id_kel}, {$index_keluarga})' title='Hapus Anggota'></i>
            </div>
        </li>";

    $html_tab_keluarga .= "<div class='kk-tab flex-shrink-0' id='tab-kel-{$id_kel}' onclick='switchProfileTab({$index_keluarga}, this)'>{$nama_panggilan}</div>";
    $index_keluarga++;
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
    <link rel="stylesheet" href="pasien.css?v=<?= time(); ?>">
</head>
<body class="bg-pasien">
    <button class="btn-emergency shadow-lg" onclick="alert('Panggilan Darurat: 119')"><i class="bi bi-telephone-fill"></i></button>

    <div class="ashley-container">
        <div class="ash-main" id="main-content-area">
            
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
                    <div class="bg-white rounded-pill px-3 py-2 shadow-sm d-flex align-items-center" style="min-width: 260px; border: 1px solid rgba(0,0,0,0.05);">
                        <i class="bi bi-search me-2 text-muted"></i>
                        <input type="text" id="searchInput" placeholder="Cari Riwayat, Dokter, atau Menu..." class="border-0 bg-transparent w-100 text-dark search-input-header" style="font-size: 0.85rem; outline: none;">
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
                            <div class="mb-3"><i class="bi bi-calendar2-x text-muted" style="font-size: 3.5rem; opacity: 0.5;"></i></div>
                            <h6 class="fw-bold text-dark mb-2">Belum Ada Jadwal Hari Ini</h6>
                            <p class="text-muted small mb-0" style="max-width: 85%;">Anda belum memiliki jadwal periksa. Klik menu <strong>'Jadwal'</strong> untuk mendaftar antrean.</p>
                        </div>
                    </div>
                </div>

                <div class="menu-cards-container">
                    <div class="menu-card" onclick="switchView('view-kamar')">
                        <div class="menu-icon-top"><i class="bi bi-hospital"></i></div>
                        <div class="menu-title">INFORMASI KAMAR</div>
                        <div class="menu-subtitle">Pilih kasur rawat inap.</div>
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
                                <a href="#" onclick="switchView('view-riwayat')" class="small text-info text-decoration-none fw-medium" style="cursor:pointer;">Lihat Riwayat Lengkap</a>
                            </div>
                            
                            <?php 
                            $q_riwayat_1 = mysqli_query($koneksi, "SELECT * FROM riwayat_berobat WHERE email_pasien='$email_user' ORDER BY tanggal_periksa DESC LIMIT 1");
                            if($q_riwayat_1 && mysqli_num_rows($q_riwayat_1) > 0) {
                                $data_r = mysqli_fetch_assoc($q_riwayat_1);
                                $tgl = date('d M Y', strtotime($data_r['tanggal_periksa']));
                                $jam = date('H:i', strtotime($data_r['tanggal_periksa']));
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
                                            <span class="text-muted" style="font-size: 0.75rem;"><?= $jam; ?> WIB</span>
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
            
            <!-- VIEW KAMAR ALA TIX ID -->
            <div id="view-kamar" class="view-section d-none position-relative">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Pilih Kamar & Bed Rawat Inap</h4>
                </div>
                
                <div class="alert border-0 rounded-4 mb-4 small shadow-sm d-flex align-items-center" style="background-color: #e2f5fa; color: #1e2f3a;">
                    <i class="bi bi-info-circle-fill text-teal-mediflow fs-4 me-3"></i> 
                    <div>Ketuk kotak bed berwarna biru gelap untuk memilih kasur. Lanjutkan pemesanan untuk melengkapi jadwal.</div>
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
                            $tarif_rp = "Rp " . number_format($kamar['tarif'], 0, ',', '.');
                    ?>
                        <div class="col-12">
                            <div class="bg-white p-4 rounded-4 shadow-sm position-relative border-start border-5" style="border-color: <?= $kamar['warna_hex']; ?> !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2 border-bottom pb-3">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <h5 class="fw-bold mb-0 me-2"><?= $kamar['nama_kelas']; ?></h5>
                                            <i class="bi <?= $kamar['icon_bi']; ?> fs-5"></i>
                                        </div>
                                        <p class="text-muted small mb-0"><?= htmlspecialchars($kamar['deskripsi']); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="small text-muted d-block">Tarif per hari</span>
                                        <h6 class="fw-bold text-dark mb-0"><?= $tarif_rp; ?></h6>
                                    </div>
                                </div>
                                
                                <div class="bed-grid">
                                    <?php 
                                    $q_bed = mysqli_query($koneksi, "SELECT * FROM bed_kamar WHERE id_kamar='$id_kamar'");
                                    if(mysqli_num_rows($q_bed) > 0){
                                        while($bed = mysqli_fetch_assoc($q_bed)){
                                            $status_class = strtolower($bed['status']); 
                                    ?>
                                        <div class="tix-seat <?= $status_class; ?>" onclick="pilihBed(this, '<?= $bed['nomor_bed']; ?>', '<?= $kamar['nama_kelas']; ?>', '<?= $tarif_rp; ?>')">
                                            <?= $bed['nomor_bed']; ?>
                                        </div>
                                    <?php } } else { echo "<p class='small text-muted w-100 text-center mt-3'>Belum ada data kasur.</p>"; } ?>
                                </div>

                                <div class="tix-curve-container">
                                    <div class="tix-curve-line"></div>
                                    <span class="tix-curve-text">NURSE STATION</span>
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

            <!-- VIEW JADWAL (DENGAN TOMBOL PENCARIAN/UBAH KASUR) -->
            <div id="view-jadwal" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Pendaftaran Janji Temu</h4>
                </div>
                
                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <div class="row g-4">
                        
                        <!-- KOTAK INFO KAMAR (DIPERBARUI) -->
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
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dropdown-btn text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textPoli">-- Silakan Pilih Poliklinik --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu">
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; return false;">Poliklinik Umum</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textPoli').innerText=this.innerText; return false;">Poliklinik Gigi & Mulut</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tanggal Rencana Periksa</label>
                            <input type="date" class="form-control rounded-pill border-2" style="padding: 12px 20px; font-size: 0.9rem;">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Pilih Waktu Kunjungan</label>
                            <div class="dropdown w-100">
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dropdown-btn text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textJam">-- Pilih Jam Periksa --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu">
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textJam').innerText=this.innerText; return false;">Pagi (08:00 - 12:00)</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textJam').innerText=this.innerText; return false;">Siang (13:00 - 16:00)</a></li>
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textJam').innerText=this.innerText; return false;">Malam (18:00 - 21:00)</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Pilih Dokter Jaga</label>
                            <div class="dropdown w-100">
                                <button class="btn border border-2 w-100 rounded-pill d-flex justify-content-between align-items-center custom-dropdown-btn text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 12px 20px; font-size: 0.9rem;">
                                    <span id="textDokter">-- Pilih Dokter --</span>
                                    <i class="bi bi-chevron-down text-teal-mediflow"></i>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2 custom-dropdown-menu">
                                    <li><a class="dropdown-item custom-dropdown-item" href="#" onclick="document.getElementById('textDokter').innerText=this.innerText; return false;">Dr. Budi Santoso, Sp.PD</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Keluhan Utama (Singkat)</label>
                            <textarea class="form-control rounded-4 border-2" rows="3" placeholder="Deskripsikan keluhan Anda secara singkat..."></textarea>
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <button class="btn w-100 rounded-pill py-3 fw-bold shadow-sm btn-teal" onclick="kirimJadwalAlert()">Ajukan Janji Temu Sekarang</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-riwayat" class="view-section d-none">
                <div class="d-flex align-items-center mt-3 mb-4">
                    <button class="btn btn-light rounded-circle shadow-sm me-3" onclick="switchView('view-home')"><i class="bi bi-arrow-left fs-5"></i></button>
                    <h4 class="fw-bold text-dark mb-0 lh-1">Riwayat Medis Lengkap</h4>
                </div>
                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <?php 
                    $q_riwayat_all = mysqli_query($koneksi, "SELECT * FROM riwayat_berobat WHERE email_pasien='$email_user' ORDER BY tanggal_periksa DESC");
                    if($q_riwayat_all && mysqli_num_rows($q_riwayat_all) > 0) {
                        while($row = mysqli_fetch_assoc($q_riwayat_all)) {
                            $tgl_all = date('d M Y', strtotime($row['tanggal_periksa']));
                            $jam_all = date('H:i', strtotime($row['tanggal_periksa']));
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
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= $jam_all; ?> WIB</span>
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
        
        <!-- PANEL KANAN -->
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

    <!-- MODAL SETTINGS FULL (DIPERBAIKI) -->
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
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-family" type="button" role="tab"><i class="bi bi-people me-2"></i> Keluarga</button>
                                <button class="nav-link small text-start mb-2" data-bs-toggle="tab" data-bs-target="#pills-privacy-set" type="button" role="tab"><i class="bi bi-lock me-2"></i> Privasi</button>
                            </div>
                            <div class="mt-auto pt-3 border-top">
                                <button class="btn btn-settings-logout w-100 rounded-pill py-2 fw-bold small text-start px-3" onclick="logoutSession()"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                            </div>
                        </div>

                        <div class="p-4 p-md-5 flex-grow-1 bg-white" style="max-height: 600px; overflow-y: auto;">
                            <div class="tab-content" id="pills-tabContent">
                                
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
                                        <div class="col-md-6"><label class="small fw-bold mb-1">Email</label><input type="email" id="settingsEmailInput" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($_SESSION['email'] ?? ''); ?>"></div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold mb-1">Password</label>
                                            <div class="position-relative"><input type="password" id="settingsPasswordInput" class="form-control form-control-sm rounded-3" placeholder="Kosongkan jika tak diubah" style="padding-right: 35px;"><i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-2" id="toggleSettingsPassword" style="cursor: pointer; color: #a0b8c2;"></i></div>
                                        </div>
                                    </div>
                                    <button id="btnSimpanAkun" class="btn btn-teal w-100 shadow-sm py-2 rounded-3" onclick="updateAuthData()">Simpan Perubahan Akun</button>
                                </div>

                                <div class="tab-pane fade" id="pills-family" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Manajemen Keluarga</h6>
                                    <p class="small fw-bold mb-2 text-dark">Anggota Terdaftar:</p>
                                    <ul class="list-group list-group-flush mb-3" id="settingsFamilyList">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-3 rounded-3 mb-2 border">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex justify-content-center align-items-center me-3 overflow-hidden shadow-sm" style="width: 40px; height: 40px; font-weight: bold; background-color: #72b9b9; color: white;">
                                                    <?php if(isset($_SESSION['foto_profil']) && $_SESSION['foto_profil'] != '') { ?>
                                                        <img id="familyMainPic" src="../uploads/<?= $_SESSION['foto_profil']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                        <span id="familyMainInitials" style="display:none;"><?= substr($_SESSION['nama'] ?? 'P', 0, 1); ?></span>
                                                    <?php } else { ?>
                                                        <img id="familyMainPic" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                        <span id="familyMainInitials"><?= substr($_SESSION['nama'] ?? 'P', 0, 1); ?></span>
                                                    <?php } ?>
                                                </div>
                                                <div><h6 class="mb-0 small fw-bold text-dark"><?= htmlspecialchars($_SESSION['nama'] ?? 'Pasien Utama'); ?></h6><span class="small text-muted" style="font-size: 0.75rem;">Utama (Anda)</span></div>
                                            </div>
                                        </li>
                                        <?= $html_list_keluarga; ?> 
                                    </ul>
                                    <button id="btnBukaFormKeluarga" class="btn btn-outline-teal w-100 mt-2 shadow-sm py-2 rounded-3 fw-medium" style="border: 1.5px dashed #38c8e6; color: #38c8e6;" onclick="toggleFormKeluarga()">+ Tambah Anggota Keluarga Baru</button>

                                    <div id="formTambahKeluarga" class="d-none mt-3 p-3 rounded-4 bg-light border" style="border-color: rgba(56, 200, 230, 0.3) !important;">
                                        <h6 class="fw-bold small mb-3 text-teal-mediflow"><i class="bi bi-person-plus me-2"></i>Data Anggota Keluarga</h6>
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center overflow-hidden shadow-sm" style="width: 50px; height: 50px;" id="previewFotoKelContainer">
                                                <i class="bi bi-person fs-3" id="iconDefaultKel"></i>
                                                <img id="previewFotoKel" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                            </div>
                                            <div>
                                                <label for="kelFoto" class="btn btn-sm btn-outline-teal rounded-pill px-3 py-1">Pilih Foto</label>
                                                <input type="file" id="kelFoto" class="d-none" accept="image/png, image/jpeg, image/jpg">
                                            </div>
                                        </div>
                                        <div class="mb-2"><input type="text" id="kelNama" class="form-control form-control-sm rounded-3" placeholder="Nama Lengkap Keluarga"></div>
                                        <div class="mb-2"><input type="number" id="kelNik" class="form-control form-control-sm rounded-3" placeholder="NIK (16 Digit)"></div>
                                        <div class="mb-2"><label class="small text-muted mb-1" style="font-size: 0.75rem;">Tanggal Lahir</label><input type="date" id="kelTglLahir" class="form-control form-control-sm rounded-3"></div>
                                        <div class="mb-2">
                                            <div class="dropdown dropdown-custom-container w-100">
                                                <select id="kelGolDarah" class="d-none">
                                                    <option value="-" selected>Pilih...</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                                                </select>
                                                <div class="form-control form-control-sm rounded-3 dropdown-toggle d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" style="cursor: pointer; background-color: #fff; height: 31px;"><span class="text-muted">Pilih Golongan Darah...</span><i class="bi bi-chevron-down text-muted" style="font-size: 0.8rem;"></i></div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu shadow-sm">
                                                    <li><a class="dropdown-item custom-dd-item active" href="#" data-value="-">Pilih Golongan Darah...</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="A">A</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="B">B</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="AB">AB</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="O">O</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mb-2"><input type="text" id="kelAlamat" class="form-control form-control-sm rounded-3" placeholder="Alamat Lengkap"></div>
                                        <div class="mb-2"><input type="number" id="kelBpjs" class="form-control form-control-sm rounded-3" placeholder="No. BPJS (Opsional)"></div>
                                        <div class="mb-3">
                                            <div class="dropdown dropdown-custom-container w-100">
                                                <select id="kelHubungan" class="d-none">
                                                    <option value="" selected>Pilih...</option>
                                                    <option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option><option value="Kakak">Kakak</option><option value="Adik">Adik</option><option value="Kakek">Kakek</option><option value="Nenek">Nenek</option><option value="Ayah">Ayah</option><option value="Ibu">Ibu</option><option value="Saudara">Saudara</option>
                                                </select>
                                                <div class="form-control form-control-sm rounded-3 dropdown-toggle d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" style="cursor: pointer; background-color: #fff; height: 31px;"><span class="text-muted">Pilih Hubungan...</span><i class="bi bi-chevron-down text-muted" style="font-size: 0.8rem;"></i></div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu shadow-lg">
                                                    <li><a class="dropdown-item custom-dd-item active" href="#" data-value="">Pilih Hubungan...</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Suami">Suami</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Istri">Istri</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Anak">Anak</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Kakak">Kakak</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Adik">Adik</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Kakek">Kakek</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Nenek">Nenek</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Ayah">Ayah</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Ibu">Ibu</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Saudara">Saudara</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button id="btnSimpanKel" class="btn btn-sm btn-teal w-50 rounded-pill py-2 fw-medium" onclick="simpanKeluarga()">Simpan Data</button>
                                            <button class="btn btn-sm btn-light border w-50 rounded-pill py-2 fw-medium text-muted" onclick="toggleFormKeluarga()">Batal</button>
                                        </div>
                                    </div>
                                    
                                    <div id="formEditKeluarga" class="d-none mt-3 p-3 rounded-4 bg-light border" style="border-color: rgba(56, 200, 230, 0.3) !important;">
                                        <h6 class="fw-bold small mb-3 text-teal-mediflow"><i class="bi bi-pencil-square me-2"></i>Edit Data Keluarga</h6>
                                        <input type="hidden" id="editKelId">
                                        <input type="hidden" id="editKelIndex">
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center overflow-hidden shadow-sm" style="width: 50px; height: 50px;">
                                                <i class="bi bi-person fs-3" id="iconEditDefaultKel"></i>
                                                <img id="previewEditFotoKel" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                            </div>
                                            <div>
                                                <label for="editKelFoto" class="btn btn-sm btn-outline-teal rounded-pill px-3 py-1">Ganti Foto</label>
                                                <input type="file" id="editKelFoto" class="d-none" accept="image/png, image/jpeg, image/jpg">
                                            </div>
                                        </div>
                                        <div class="mb-2"><input type="text" id="editKelNama" class="form-control form-control-sm rounded-3"></div>
                                        <div class="mb-2"><input type="number" id="editKelNik" class="form-control form-control-sm rounded-3"></div>
                                        <div class="mb-2"><label class="small text-muted mb-1" style="font-size: 0.75rem;">Tanggal Lahir</label><input type="date" id="editKelTglLahir" class="form-control form-control-sm rounded-3"></div>
                                        <div class="mb-2">
                                            <div class="dropdown dropdown-custom-container w-100">
                                                <select id="editKelGolDarah" class="d-none">
                                                    <option value="-">Pilih...</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                                                </select>
                                                <div class="form-control form-control-sm rounded-3 dropdown-toggle d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" style="cursor: pointer; background-color: #fff; height: 31px;" id="displayEditKelGolDarah">
                                                    <span class="text-muted">Pilih...</span><i class="bi bi-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu shadow-sm">
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="-">Pilih...</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="A">A</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="B">B</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="AB">AB</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="O">O</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mb-2"><input type="text" id="editKelAlamat" class="form-control form-control-sm rounded-3" placeholder="Alamat Lengkap"></div>
                                        <div class="mb-2"><input type="number" id="editKelBpjs" class="form-control form-control-sm rounded-3" placeholder="No. BPJS"></div>
                                        <div class="mb-3">
                                            <div class="dropdown dropdown-custom-container w-100">
                                                <select id="editKelHubungan" class="d-none">
                                                    <option value="">Pilih...</option>
                                                    <option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option>
                                                    <option value="Kakak">Kakak</option><option value="Adik">Adik</option><option value="Kakek">Kakek</option>
                                                    <option value="Nenek">Nenek</option><option value="Ayah">Ayah</option><option value="Ibu">Ibu</option><option value="Saudara">Saudara</option>
                                                </select>
                                                <div class="form-control form-control-sm rounded-3 dropdown-toggle d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" style="cursor: pointer; background-color: #fff; height: 31px;" id="displayEditKelHubungan">
                                                    <span class="text-muted">Pilih...</span><i class="bi bi-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <ul class="dropdown-menu w-100 border-0 custom-dropdown-menu shadow-lg">
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="">Pilih...</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Suami">Suami</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Istri">Istri</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Anak">Anak</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Kakak">Kakak</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Adik">Adik</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Kakek">Kakek</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Nenek">Nenek</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Ayah">Ayah</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Ibu">Ibu</a></li>
                                                    <li><a class="dropdown-item custom-dd-item" href="#" data-value="Saudara">Saudara</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button id="btnSimpanEditKel" class="btn btn-sm btn-teal w-50 rounded-pill py-2 fw-medium" onclick="simpanEditKeluarga()">Update Data</button>
                                            <button class="btn btn-sm btn-light border w-50 rounded-pill py-2 fw-medium text-muted" onclick="batalEditKeluarga()">Batal</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="pills-privacy-set" role="tabpanel" tabindex="0">
                                    <h6 class="fw-bold mb-4">Perangkat & Keamanan</h6>
                                    <div class="p-3 border rounded-4 bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-laptop fs-3 me-3 text-teal-mediflow"></i>
                                                <div>
                                                    <h6 class="mb-0 small fw-bold">Sesi Saat Ini</h6>
                                                    <span class="text-muted small" id="deviceInfo" style="font-size: 0.75rem;">Mendeteksi perangkat...</span>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="logoutSession()"><i class="bi bi-power me-1"></i> Logout</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            <?php 
            foreach($array_keluarga as $kel_json){
                echo json_encode($kel_json) . ",";
            }
            ?>
        ];
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="pasien.js?v=<?= time(); ?>"></script>
</body>
</html>