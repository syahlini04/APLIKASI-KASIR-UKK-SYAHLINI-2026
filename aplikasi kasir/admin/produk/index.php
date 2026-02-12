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

$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY produk_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Aplikasi Kasir</title>
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
                
            </ul>

            <div class="sidebar-footer">
                <a href="../../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Data Produk</div>
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
                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Produk</h3>
                        <a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($produk) > 0) {
                                    while ($p = mysqli_fetch_assoc($produk)) {
                                        $stok = $p['stok'];
                                        $stok_badge = '';
                                        if ($stok > 10) {
                                            $stok_badge = '<span class="badge badge-success">'.$stok.' pcs</span>';
                                        } elseif ($stok > 0) {
                                            $stok_badge = '<span class="badge badge-warning">'.$stok.' pcs</span>';
                                        } else {
                                            $stok_badge = '<span class="badge badge-danger">'.$stok.' pcs</span>';
                                        }
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $p['nama_produk']; ?></td>
                                    <td>Rp <?= number_format($p['harga']); ?></td>
                                    <td><?= $stok_badge; ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $p['produk_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="hapus.php?id=<?= $p['produk_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="5" style="text-align: center; padding: 20px;">Belum ada data produk</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
