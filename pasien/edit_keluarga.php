<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");

if (!$koneksi || !isset($_SESSION['email'])) { 
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi gagal']); 
    exit(); 
}

$email_utama = $_SESSION['email'];
$id = mysqli_real_escape_string($koneksi, $_POST['id']);
$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
$tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
$gol_darah = mysqli_real_escape_string($koneksi, $_POST['gol_darah']);
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
$no_bpjs = mysqli_real_escape_string($koneksi, $_POST['bpjs']);
$hubungan = mysqli_real_escape_string($koneksi, $_POST['hubungan']);

// Jika upload foto baru, maka update fotonya. Jika tidak, biarkan foto lama.
$foto_query = "";
$foto_profil = ''; 
if(isset($_FILES['foto_keluarga']['name']) && $_FILES['foto_keluarga']['name'] != ''){
    if(!is_dir("../uploads/")) { mkdir("../uploads/", 0777, true); }
    $nama_file = time() . '_kel_' . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['foto_keluarga']['name']);
    $tmp_file = $_FILES['foto_keluarga']['tmp_name'];
    $path = "../uploads/".$nama_file;
    if(move_uploaded_file($tmp_file, $path)){
        $foto_profil = $nama_file;
        $foto_query = ", foto_profil='$foto_profil'";
    }
}

// Update ke database
$query = "UPDATE keluarga_pasien SET nama='$nama', nik='$nik', tanggal_lahir='$tanggal_lahir', gol_darah='$gol_darah', alamat='$alamat', no_bpjs='$no_bpjs', hubungan='$hubungan' $foto_query WHERE id='$id' AND email_pasien_utama='$email_utama'";

if(mysqli_query($koneksi, $query)){
    echo json_encode(['status' => 'sukses', 'foto' => $foto_profil]);
} else {
    echo json_encode(['status' => 'error', 'pesan' => mysqli_error($koneksi)]);
}
?>