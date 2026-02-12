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

$id = $_GET['id'];

// data transaksi
$transaksi = mysqli_query($conn, "
    SELECT transaksi.*, user.nama 
    FROM transaksi 
    JOIN user ON transaksi.user_id = user.user_id
    WHERE transaksi.transaksi_id='$id'
");
$t = mysqli_fetch_assoc($transaksi);

// detail transaksi
$detail = mysqli_query($conn, "
    SELECT transaksi_detail.*, produk.nama_produk
    FROM transaksi_detail
    JOIN produk ON transaksi_detail.produk_id = produk.produk_id
    WHERE transaksi_detail.transaksi_id='$id'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Aplikasi Kasir</title>
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
                <li><a href="index.php" class="active"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="../produk/index.php"><span class="menu-icon">📦</span>Produk</a></li>
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
                <div class="topbar-title">Detail Transaksi #<?= $t['transaksi_id']; ?></div>
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
                <!-- Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Transaksi</h3>
                        <a href="index.php" class="btn btn-secondary">← Kembali</a>
                    </div>

                    <div style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                            <div>
                                <p style="color: #666; font-size: 12px; margin-bottom: 5px;">ID TRANSAKSI</p>
                                <p style="font-size: 18px; font-weight: bold; color: #1e3a8a;">#<?= $t['transaksi_id']; ?></p>
                            </div>
                            <div>
                                <p style="color: #666; font-size: 12px; margin-bottom: 5px;">PETUGAS</p>
                                <p style="font-size: 18px; font-weight: bold; color: #1e3a8a;"><?= $t['nama']; ?></p>
                            </div>
                            <div>
                                <p style="color: #666; font-size: 12px; margin-bottom: 5px;">TANGGAL & JAM</p>
                                <p style="font-size: 18px; font-weight: bold; color: #1e3a8a;"><?= date('d/m/Y H:i', strtotime($t['tanggal'])); ?></p>
                            </div>
                            <div>
                                <p style="color: #666; font-size: 12px; margin-bottom: 5px;">TOTAL PEMBAYARAN</p>
                                <p style="font-size: 18px; font-weight: bold; color: #16a34a;">Rp <?= number_format($t['total']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Items -->
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">Detail Produk</h3>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Harga Satuan</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($detail) > 0) {
                                    while ($d = mysqli_fetch_assoc($detail)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $d['nama_produk']; ?></td>
                                    <td>Rp <?= number_format($d['harga']); ?></td>
                                    <td style="text-align: center;"><?= $d['qty']; ?> pcs</td>
                                    <td>Rp <?= number_format($d['subtotal']); ?></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="5" style="text-align: center; padding: 20px;">Belum ada detail transaksi</td></tr>';
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
