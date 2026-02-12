<?php
session_start();
include "../../config/koneksi.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['nama'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Validasi input
    if (empty($nama) || empty($username) || empty($password) || empty($role)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek username sudah ada
        $cek = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "
                INSERT INTO user (nama, username, password, role)
                VALUES ('$nama', '$username', '$password_hash', '$role')
            ");

            if ($insert) {
                $success = 'Pengguna berhasil ditambahkan!';
                // Catat aktivitas
                catatAktivitas($conn, $_SESSION['user_id'], "Menambah Pengguna: $nama ($role)");
                header("Location: index.php");
                exit;
            } else {
                $error = 'Gagal menambahkan pengguna!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Registrasi Pengguna</h1>
                <p>Tambah Akun Baru Aplikasi Kasir</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= $error; ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success; ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST" class="login-form">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password (min 6 karakter)" required>
                </div>

                <div class="form-group">
                    <label for="role">Role/Posisi</label>
                    <select id="role" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="user">User</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>

            <div class="login-footer">
                <p><a href="index.php" style="color: #2563eb; text-decoration: none;">← Kembali ke Daftar Pengguna</a></p>
            </div>
        </div>
    </div>
</body>
</html>
