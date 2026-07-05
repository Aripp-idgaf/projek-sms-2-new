<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal.");
}

// Menangkap data yang dikirim dari form login
$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// Menyeleksi data user dengan email dan password yang sesuai
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email' AND password='$password'");
$cek = mysqli_num_rows($query);

if($cek > 0){
    // Jika data ditemukan, ambil datanya
    $data = mysqli_fetch_assoc($query);

    // Menyimpan data ke dalam Session agar bisa dipakai di halaman lain
    $_SESSION['email'] = $data['email'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role']; // Ini yang paling penting
    $_SESSION['nik'] = $data['nik'];
    $_SESSION['tanggal_lahir'] = $data['tanggal_lahir'];
    $_SESSION['gol_darah'] = $data['gol_darah'];
    $_SESSION['alamat'] = $data['alamat'];
    $_SESSION['no_bpjs'] = $data['no_bpjs'];
    $_SESSION['foto_profil'] = $data['foto_profil'];
    $_SESSION['status'] = "login";

    // ==========================================================
    // LOGIKA PERCABANGAN (MENGARAHKAN USER SESUAI ROLE/HAK AKSES)
    // ==========================================================
    if($data['role'] == "admin"){
        // Jika yang login adalah admin, arahkan ke folder admin
        header("location:../admin/dashboard.admin.php");
        
    } else if($data['role'] == "dokter"){
        // Jika yang login adalah dokter, arahkan ke folder dokter
        header("location:../dokter/dashboard.dokter.php");
        
    } else if($data['role'] == "pasien"){
        // Jika yang login adalah pasien, arahkan ke folder pasien
        header("location:../pasien/dashboard.pasien.php");
        
    } else {
        // Jika role tidak dikenali (berjaga-jaga), lempar balik ke login
        header("location:index.php?pesan=gagal");
    }
} else {
    // Jika email atau password salah, lempar balik ke halaman login dengan pesan gagal
    header("location:index.php?pesan=gagal");
}
?>