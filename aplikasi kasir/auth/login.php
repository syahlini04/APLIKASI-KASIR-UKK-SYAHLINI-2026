<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Aplikasi Kasir</h1>
                <p>Sistem Manajemen Penjualan</p>
            </div>
            
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
            ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php
                unset($_SESSION['error']);
            }
            
            if (isset($_GET['msg']) && $_GET['msg'] == 'registrasi-ditutup') {
            ?>
            <div class="alert alert-info">
                <strong>Informasi:</strong> Registrasi publik telah ditutup. Silakan hubungi administrator untuk pembuatan akun baru.
            </div>
            <?php
            }
            ?>

            <form action="login_proses.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="login-footer">
                <p>&copy; 2026 Aplikasi Kasir. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>