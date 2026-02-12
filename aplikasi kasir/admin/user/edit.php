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

$id = intval($_GET['id']);
$data = mysqli_query($conn, "SELECT * FROM user WHERE user_id='$id'");

if (mysqli_num_rows($data) == 0) {
    header("Location: index.php");
    exit;
}

$u = mysqli_fetch_assoc($data);

$error = '';
$success = '';

if (isset($_POST['nama'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = $_POST['role'];

    // Validasi input
    if (empty($nama) || empty($username) || empty($role)) {
        $error = 'Semua field harus diisi!';
    } else {
        // Cek username sudah ada (kecuali username user yang sedang diedit)
        $cek = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND user_id!='$id'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            $update = mysqli_query($conn, "
                UPDATE user
                SET nama='$nama', username='$username', role='$role'
                WHERE user_id='$id'
            ");

            if ($update) {
                $success = 'Pengguna berhasil diupdate!';
                $u['nama'] = $nama;
                $u['username'] = $username;
                $u['role'] = $role;
                catatAktivitas($conn, $_SESSION['user_id'], "Mengedit Pengguna: $nama (ID: $id)");
            } else {
                $error = 'Gagal mengupdate pengguna!';
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
    <title>Edit Pengguna - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Kasir Admin</h2>
                <p>Sistem Manajemen</p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="../dashboard.php"><span class="menu-icon">📊</span>Dashboard</a></li>
                <li><a href="index.php" class="active"><span class="menu-icon">👥</span>Data Pengguna</a></li>
                <li><a href="#"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="#"><span class="menu-icon">📦</span>Produk</a></li>
                <li><a href="#"><span class="menu-icon">📈</span>Laporan</a></li>
                <li><a href="#"><span class="menu-icon">⚙️</span>Pengaturan</a></li>
            </ul>

            <div class="sidebar-footer">
                <a href="../../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Edit Pengguna</div>
                <div class="topbar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
                    <div class="user-info">
                        <p class="user-name"><?php echo $_SESSION['nama']; ?></p>
                        <p class="user-role">Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Form Card -->
                <div class="card" style="max-width: 600px;">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Pengguna</h3>
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

                    <form method="POST" style="padding: 20px;">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($u['nama']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?= htmlspecialchars($u['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role/Posisi</label>
                            <select id="role" name="role" required>
                                <option value="admin" <?= $u['role']=='admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="petugas" <?= $u['role']=='petugas' ? 'selected' : ''; ?>>Petugas</option>
                                <option value="user" <?= $u['role']=='user' ? 'selected' : ''; ?>>User</option>
                            </select>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
