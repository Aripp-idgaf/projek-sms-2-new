<?php
session_start();
// Jangan tampilkan error HTML agar tidak merusak sistem AJAX JavaScript
error_reporting(E_ALL);
ini_set('display_errors', 0); 

$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");

if (!$koneksi) { echo "Error: Database tidak terhubung!"; exit(); }
if (!isset($_SESSION['email'])) { echo "Error: Sesi login hilang, silakan login ulang."; exit(); }

$email_lama = $_SESSION['email'];

$nama = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
$nik = mysqli_real_escape_string($koneksi, $_POST['nik'] ?? '');
$tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir'] ?? '');
$gol_darah = mysqli_real_escape_string($koneksi, $_POST['gol_darah'] ?? '');
$no_bpjs = mysqli_real_escape_string($koneksi, $_POST['no_bpjs'] ?? '');
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat'] ?? '');
$email_baru = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
$password = mysqli_real_escape_string($koneksi, $_POST['password'] ?? '');

// Mencegah Error MySQL Strict Mode (Jika tanggal tidak diisi, jadikan NULL)
$tanggal_lahir_sql = ($tanggal_lahir == "") ? "NULL" : "'$tanggal_lahir'";
// Mencegah NIK kosong menjadi error
$nik_sql = ($nik == "") ? "NULL" : "'$nik'";

$foto_profil = $_SESSION['foto_profil'] ?? ''; 

// ==========================================
// HANDLE UPLOAD FOTO (Bebas Ukuran MB)
// ==========================================
if(isset($_FILES['foto_profil']['name']) && $_FILES['foto_profil']['name'] != ''){
    
    // PERBAIKAN: Otomatis buat folder 'uploads' jika belum ada!
    if(!is_dir("../uploads/")){
        mkdir("../uploads/", 0777, true);
    }

    $nama_file = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['foto_profil']['name']);
    $tmp_file = $_FILES['foto_profil']['tmp_name'];
    $path = "../uploads/".$nama_file;
    
    if(move_uploaded_file($tmp_file, $path)){
        $foto_profil = $nama_file;
    } else {
        echo "Error: Gagal memindahkan file gambar ke folder uploads.";
        exit();
    }
}

// ==========================================
// QUERY UPDATE KE DATABASE
// ==========================================
// Jika kolom password di form diisi, maka update passwordnya
if ($password != "") {
    $query = "UPDATE users SET nama='$nama', email='$email_baru', password='$password', nik=$nik_sql, tanggal_lahir=$tanggal_lahir_sql, gol_darah='$gol_darah', no_bpjs='$no_bpjs', alamat='$alamat', foto_profil='$foto_profil' WHERE email='$email_lama'";
} else {
    // Jika tidak diisi, biarkan password lama
    $query = "UPDATE users SET nama='$nama', email='$email_baru', nik=$nik_sql, tanggal_lahir=$tanggal_lahir_sql, gol_darah='$gol_darah', no_bpjs='$no_bpjs', alamat='$alamat', foto_profil='$foto_profil' WHERE email='$email_lama'";
}

if(mysqli_query($koneksi, $query)){
    // PERBARUI SESSION AGAR LANGSUNG BERUBAH DI DASHBOARD
    $_SESSION['nama'] = $nama;
    $_SESSION['email'] = $email_baru;
    $_SESSION['nik'] = $nik;
    $_SESSION['tanggal_lahir'] = $tanggal_lahir;
    $_SESSION['gol_darah'] = $gol_darah;
    $_SESSION['alamat'] = $alamat;
    $_SESSION['no_bpjs'] = $no_bpjs;
    $_SESSION['foto_profil'] = $foto_profil;
    
    echo "sukses";
} else {
    // TAMPILKAN PESAN ERROR ASLI DARI DATABASE JIKA GAGAL
    echo "Error Database: " . mysqli_error($koneksi); 
}
?>