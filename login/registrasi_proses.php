<?php
include 'koneksi.php';

// Menangkap data dari form register
$nama = $_POST['nama'];
$email = $_POST['email'];
// Mengacak password agar aman di database (hashing)
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
$role = 'pasien'; // Otomatis mendaftar sebagai pasien

// Cek apakah email sudah pernah dipakai sebelumnya
$cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
if(mysqli_num_rows($cek_email) > 0) {
    // Jika email sudah ada, kembalikan ke halaman login dengan pesan error
    header("location:index.php?pesan=email_terdaftar");
} else {
    // Jika email belum ada, simpan data ke database
    $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
    
    if(mysqli_query($koneksi, $query)){
        // Jika sukses, kembalikan ke halaman login dengan pesan sukses
        header("location:index.php?pesan=daftar_sukses");
    } else {
        header("location:index.php?pesan=gagal");
    }
}
?>