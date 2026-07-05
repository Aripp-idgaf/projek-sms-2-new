<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");

if (!$koneksi || !isset($_SESSION['email'])) { 
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal koneksi.']); 
    exit(); 
}

$email = $_SESSION['email'];
$poli = mysqli_real_escape_string($koneksi, $_POST['poli']);
$tgl = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
$waktu = mysqli_real_escape_string($koneksi, $_POST['waktu']);
$dokter = mysqli_real_escape_string($koneksi, $_POST['dokter']);
$keluhan = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
$bed = mysqli_real_escape_string($koneksi, $_POST['bed']);

// Jika ada bed yang dipesan, ubah status bed di tabel kamar menjadi 'Terisi'
if($bed != '') {
    mysqli_query($koneksi, "UPDATE bed_kamar SET status='Terisi' WHERE nomor_bed='$bed'");
}

$query = "INSERT INTO riwayat_berobat (email_pasien, poli, tanggal_periksa, waktu_kunjungan, nama_dokter, keluhan, nomor_bed, status) 
        VALUES ('$email', '$poli', '$tgl', '$waktu', '$dokter', '$keluhan', '$bed', 'Menunggu')";

if(mysqli_query($koneksi, $query)){
    echo json_encode(['status' => 'sukses']);
} else {
    echo json_encode(['status' => 'error', 'pesan' => mysqli_error($koneksi)]);
}
?>