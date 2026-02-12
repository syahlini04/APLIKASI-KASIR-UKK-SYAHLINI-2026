<?php
session_start();
// Redirect semua akses ke halaman ini ke login
// Penambahan user hanya bisa dilakukan melalui admin
header("Location: login.php?msg=registrasi-ditutup");
exit;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Ditutup - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Registrasi Ditutup</h1>
                <p>Penambahan pengguna hanya dilakukan oleh Administrator</p>
            </div>
            
            <div class="alert alert-info" style="text-align: center; margin: 20px 0;">
                <p>Untuk membuat akun baru, silakan hubungi Administrator sistem.</p>
                <p>Pengguna baru akan ditambahkan melalui menu <strong>Data Pengguna</strong> di panel admin.</p>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <p><a href="login.php" class="btn btn-primary">Kembali ke Login</a></p>
            </div>

            <div class="login-footer">
                <p>&copy; 2026 Aplikasi Kasir. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
