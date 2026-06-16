<?php
session_start();
include 'koneksi.php';

// Menangkap data dari form login
$email = $_POST['email'];
$password = $_POST['password'];

// Mengambil data user berdasarkan email
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
$cek = mysqli_num_rows($query);

if($cek > 0) {
    $user = mysqli_fetch_assoc($query);
    
    // Cek password langsung dengan membandingkan teks
    if($password == $user['password']) {
        
        // Simpan session
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['status'] = "login";

        // Arahkan ke dashboard sesuai role
        if($user['role'] == "admin") {
            header("location:../admin/dashboard.admin.php");
        } else if($user['role'] == "dokter") {
            header("location:../dokter/dashboard.dokter.php");
        } else if($user['role'] == "pasien") {
            header("location:../pasien/dashboard.pasien.php");
        }
        
    } else {
        // Password salah
        header("location:index.php?pesan=gagal");
    }
} else {
    // Email tidak ditemukan
    header("location:index.php?pesan=gagal");
}
?>