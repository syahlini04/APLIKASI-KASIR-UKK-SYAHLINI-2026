<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

include "../../config/koneksi.php";

$id = $_GET['id'];
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT nama FROM user WHERE user_id='$id'"));
$nama = $user['nama'];

mysqli_query($conn, "DELETE FROM user WHERE user_id='$id'");
catatAktivitas($conn, $_SESSION['user_id'], "Menghapus Pengguna: $nama (ID: $id)");

header("Location: index.php");
