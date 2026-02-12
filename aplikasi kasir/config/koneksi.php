<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "aplikasi kasir";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// Function untuk mencatat aktivitas
function catatAktivitas($conn, $user_id, $deskripsi) {
    $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
    $tanggal = date('Y-m-d H:i:s');
    
    mysqli_query($conn, "
        INSERT INTO aktivitas (user_id, deskripsi, tanggal)
        VALUES ('$user_id', '$deskripsi', '$tanggal')
    ");
}

?>