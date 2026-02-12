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

$error = '';
$success = '';

if (isset($_POST['simpan'])) {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga       = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok        = mysqli_real_escape_string($conn, $_POST['stok']);

    // Validasi input
    if (empty($nama_produk) || empty($harga) || empty($stok)) {
        $error = 'Semua field harus diisi!';
    } elseif ($harga < 0 || $stok < 0) {
        $error = 'Harga dan stok tidak boleh negatif!';
    } else {
        $insert = mysqli_query($conn, "
            INSERT INTO produk (nama_produk, harga, stok)
            VALUES ('$nama_produk', '$harga', '$stok')
        ");

        if ($insert) {
            $success = 'Produk berhasil ditambahkan!';
            catatAktivitas($conn, $_SESSION['user_id'], "Menambah Produk: $nama_produk");
            header("Location: index.php");
            exit;
        } else {
            $error = 'Gagal menambahkan produk!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Aplikasi Kasir</title>
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
                <li><a href="../user/index.php"><span class="menu-icon">👥</span>Data Pengguna</a></li>
                <li><a href="#"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="index.php" class="active"><span class="menu-icon">📦</span>Produk</a></li>
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
                <div class="topbar-title">Tambah Produk</div>
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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Tambah Produk Baru</h3>
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

                    <form method="POST" style="padding: 30px;">
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="text" id="nama_produk" name="nama_produk" placeholder="Masukkan nama produk lengkap" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="harga">Harga (Rp)</label>
                                <input type="number" id="harga" name="harga" placeholder="Contoh: 50000" min="0" required>
                            </div>

                            <div class="form-group">
                                <label for="stok">Stok (Pcs)</label>
                                <input type="number" id="stok" name="stok" placeholder="Contoh: 100" min="0" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div style="display: flex; gap: 15px; width: 100%;">
                                <button type="submit" name="simpan" class="btn btn-primary" style="flex: 1;">Simpan Produk</button>
                                <a href="index.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
