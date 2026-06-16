<?php 
// Memulai session
session_start();

// Menghapus semua session yang sedang aktif
session_destroy();

// Mengarahkan kembali ke halaman login
header("location:index.php");
?>