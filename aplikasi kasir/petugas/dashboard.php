<?php
session_start();
include "../config/koneksi.php";

// cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// cek role petugas
if ($_SESSION['role'] != 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

// Get statistics
$total_produk = mysqli_query($conn, "SELECT COUNT(*) as count FROM produk");
$produk_data = mysqli_fetch_assoc($total_produk);
$total_produk_count = $produk_data['count'];

$total_stok = mysqli_query($conn, "SELECT SUM(stok) as total FROM produk");
$stok_data = mysqli_fetch_assoc($total_stok);
$total_stok_count = $stok_data['total'] ?? 0;

$produk_habis = mysqli_query($conn, "SELECT COUNT(*) as count FROM produk WHERE stok <= 0");
$habis_data = mysqli_fetch_assoc($produk_habis);
$produk_habis_count = $habis_data['count'];

$last_produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY produk_id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Kasir Petugas</h2>
                <p>Sistem Manajemen</p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><span class="menu-icon">📊</span>Dashboard</a></li>
                <li><a href="produk/index.php"><span class="menu-icon">📦</span>Data Produk</a></li>
                <li><a href="transaksi/index.php"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="laporan/index.php"><span class="menu-icon">📈</span>Laporan</a></li>
            </ul>

            <div class="sidebar-footer">
                <a href="../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Dashboard Petugas</div>
                <div class="topbar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
                    <div class="user-info">
                        <p class="user-name"><?php echo $_SESSION['nama']; ?></p>
                        <p class="user-role">Petugas Kasir</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Message -->
                <div class="alert alert-success">
                    Selamat datang kembali, <strong><?php echo $_SESSION['nama']; ?></strong>! 👋
                </div>

                <!-- Statistics Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-card-icon" style="background-color: #eff6ff; color: #2563eb;">📦</div>
                        <div class="stat-card-title">Total Produk</div>
                        <div class="stat-card-value"><?php echo $total_produk_count; ?></div>
                        <div class="stat-card-footer">Produk terdaftar</div>
                    </div>

                    <div class="stat-card stat-success">
                        <div class="stat-card-icon" style="background-color: #f0fdf4; color: #16a34a;">📊</div>
                        <div class="stat-card-title">Total Stok</div>
                        <div class="stat-card-value"><?php echo number_format($total_stok_count); ?></div>
                        <div class="stat-card-footer">Unit tersedia</div>
                    </div>

                    <div class="stat-card stat-warning">
                        <div class="stat-card-icon" style="background-color: #fffbeb; color: #d97706;">⚠️</div>
                        <div class="stat-card-title">Produk Habis</div>
                        <div class="stat-card-value"><?php echo $produk_habis_count; ?></div>
                        <div class="stat-card-footer">Perlu restok</div>
                    </div>
                </div>

                <!-- Recent Products -->
                <div style="margin-top: 30px;">
                    <h3 style="color: #1e3a8a; margin-bottom: 20px; font-size: 18px; font-weight: 600;">📋 Produk Terbaru</h3>
                    <div class="card">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if (mysqli_num_rows($last_produk) > 0) {
                                        while ($p = mysqli_fetch_assoc($last_produk)) {
                                            $stok_badge = '';
                                            if ($p['stok'] > 10) {
                                                $stok_badge = '<span class="badge badge-success">'.$p['stok'].' pcs</span>';
                                            } elseif ($p['stok'] > 0) {
                                                $stok_badge = '<span class="badge badge-warning">'.$p['stok'].' pcs</span>';
                                            } else {
                                                $stok_badge = '<span class="badge badge-danger">'.$p['stok'].' pcs</span>';
                                            }
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $p['nama_produk']; ?></td>
                                        <td>Rp <?= number_format($p['harga']); ?></td>
                                        <td><?= $stok_badge; ?></td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" style="text-align: center; padding: 20px;">Belum ada produk</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
