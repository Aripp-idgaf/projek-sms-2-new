<?php
session_start();

// Koneksi Database
$koneksi = mysqli_connect("localhost", "root", "", "db_mediflow");
if (!$koneksi) {
    die("Koneksi database gagal.");
}

// Cek apakah data diposting dari form setting
if(isset($_POST['email'])) {
    
    $email_lama = $_SESSION['email'];
    $email_baru = mysqli_real_escape_string($koneksi, $_POST['email']);
    // Ambil password baru tanpa di-hash/enkripsi agar tersimpan sebagai teks biasa
    $password_baru = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Jika password diisi, update password dan email
    if(!empty($password_baru)) {
        $query = "UPDATE users SET email='$email_baru', password='$password_baru' WHERE email='$email_lama'";
    } 
    // Jika password dikosongkan, HANYA update email
    else {
        $query = "UPDATE users SET email='$email_baru' WHERE email='$email_lama'";
    }

    // Eksekusi Query
    if(mysqli_query($koneksi, $query)) {
        // Perbarui Session Email
        $_SESSION['email'] = $email_baru;
        // Redirect tanpa pop-up
        header("Location: dashboard.dokter.php");
        exit();
    } else {
        // Redirect tanpa pop-up (bisa tambahkan parameter ?error=1 di link jika ingin ada penanda error nantinya)
        header("Location: dashboard.dokter.php");
        exit();
    }

} else {
    // Jika file ini diakses langsung tanpa lewat form
    header("Location: dashboard.dokter.php");
    exit();
}
?>