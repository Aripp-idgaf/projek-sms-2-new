<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");

if (!$koneksi || !isset($_SESSION['email'])) { 
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi atau Sesi gagal']); 
    exit(); 
}

$email_utama = $_SESSION['email'];
$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
$tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
$gol_darah = mysqli_real_escape_string($koneksi, $_POST['gol_darah']);
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
$no_bpjs = mysqli_real_escape_string($koneksi, $_POST['bpjs']);
$hubungan = mysqli_real_escape_string($koneksi, $_POST['hubungan']);

// PROSES UPLOAD FOTO KELUARGA
$foto_profil = ''; 
if(isset($_FILES['foto_keluarga']['name']) && $_FILES['foto_keluarga']['name'] != ''){
    if(!is_dir("../uploads/")) { mkdir("../uploads/", 0777, true); }
    
    $nama_file = time() . '_kel_' . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['foto_keluarga']['name']);
    $tmp_file = $_FILES['foto_keluarga']['tmp_name'];
    $path = "../uploads/".$nama_file;
    
    if(move_uploaded_file($tmp_file, $path)){
        $foto_profil = $nama_file;
    }
}

$query = "INSERT INTO keluarga_pasien (email_pasien_utama, nama, nik, tanggal_lahir, gol_darah, alamat, no_bpjs, hubungan, foto_profil) 
          VALUES ('$email_utama', '$nama', '$nik', '$tanggal_lahir', '$gol_darah', '$alamat', '$no_bpjs', '$hubungan', '$foto_profil')";

if(mysqli_query($koneksi, $query)){
    // Jika sukses, kembalikan nama foto agar JS bisa langsung menampilkannya di layar
    echo json_encode(['status' => 'sukses', 'foto' => $foto_profil]);
} else {
    echo json_encode(['status' => 'error', 'pesan' => mysqli_error($koneksi)]);
}
?>