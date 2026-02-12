<?php
session_start();
include "../../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil ID dari URL
$id = $_GET['id'];

// Hapus produk
mysqli_query($conn, "DELETE FROM produk WHERE produk_id='$id'");

// Kembali ke index
header("Location: index.php");
