<?php 
// Mengaktifkan session pada php
session_start();

// Menghubungkan php dengan koneksi database
include 'koneksi.php';

// Menangkap data yang dikirim dari form login
$email = $_POST['email'];
$password = $_POST['password'];

// Menyeleksi data user dengan email dan password yang sesuai
$login = mysqli_query($koneksi,"SELECT * FROM users WHERE email='$email' AND password='$password'");

// Menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

// Cek apakah email dan password di temukan pada database
if($cek > 0){

    // Ambil semua data (baris) pengguna tersebut dari database
    $data = mysqli_fetch_assoc($login);

    // ========================================================
    // PROSES SINKRONISASI KE DASHBOARD (MEMASUKKAN KE SESSION)
    // ========================================================
    $_SESSION['email'] = $email;
    $_SESSION['status'] = "login";
    $_SESSION['role'] = "pasien"; // atau sesuai role di DB Anda

    // Bawa data lainnya dari database ke Session
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['nik'] = $data['nik'];
    $_SESSION['tanggal_lahir'] = $data['tanggal_lahir']; // Format wajib: YYYY-MM-DD
    $_SESSION['gol_darah'] = $data['gol_darah'];
    $_SESSION['alamat'] = $data['alamat'];

    // Alihkan ke halaman dashboard pasien
    header("location:../pasien/dashboard.pasien.php");

} else {
    // Jika gagal, kembalikan ke form login
    header("location:index.php?pesan=gagal");
}
?>