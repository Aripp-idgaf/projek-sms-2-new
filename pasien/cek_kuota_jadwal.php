<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");

if (!$koneksi) {
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi database gagal.']);
    exit();
}

$tanggal = isset($_GET['tanggal']) ? mysqli_real_escape_string($koneksi, $_GET['tanggal']) : date('Y-m-d');

// Kuota Maksimal Tiap Shift 
$kuota_max = 15;

// Hitung Pagi (Status Menunggu/Selesai, yang Batal tidak dihitung)
$q_pagi = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM riwayat_berobat WHERE tanggal_periksa='$tanggal' AND waktu_kunjungan LIKE 'Pagi%' AND status != 'Batal'");
$tot_pagi = mysqli_fetch_assoc($q_pagi)['total'];

// Hitung Siang
$q_siang = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM riwayat_berobat WHERE tanggal_periksa='$tanggal' AND waktu_kunjungan LIKE 'Siang%' AND status != 'Batal'");
$tot_siang = mysqli_fetch_assoc($q_siang)['total'];

// Hitung Malam
$q_malam = mysqli_query($koneksi, "SELECT COUNT(id) as total FROM riwayat_berobat WHERE tanggal_periksa='$tanggal' AND waktu_kunjungan LIKE 'Malam%' AND status != 'Batal'");
$tot_malam = mysqli_fetch_assoc($q_malam)['total'];

// Kembalikan sisa kuota ke Javascript
echo json_encode([
    'status' => 'sukses',
    'sisa_pagi' => max(0, $kuota_max - $tot_pagi),
    'sisa_siang' => max(0, $kuota_max - $tot_siang),
    'sisa_malam' => max(0, $kuota_max - $tot_malam)
]);
?>