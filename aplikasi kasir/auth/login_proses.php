<?php
session_start();
include "../config/koneksi.php";

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validasi input
if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Username dan password harus diisi!';
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
$data = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {

    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['nama']    = $data['nama'];
    $_SESSION['role']    = $data['role'];

    if ($data['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } elseif ($data['role'] == 'petugas') {
        header("Location: ../petugas/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit;

} else {
    $_SESSION['error'] = 'Username atau password salah!';
    header("Location: login.php");
    exit;
}
?>