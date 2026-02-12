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

// Default date range
$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : date('Y-m-01');
$tanggal_akhir = isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : date('Y-m-d');

// Query dengan filter tanggal
$transaksi = mysqli_query($conn, "
    SELECT transaksi.*, user.nama 
    FROM transaksi 
    JOIN user ON transaksi.user_id = user.user_id
    WHERE DATE(transaksi.tanggal) >= '$tanggal_mulai' 
    AND DATE(transaksi.tanggal) <= '$tanggal_akhir'
    ORDER BY transaksi.tanggal DESC
");

// Hitung statistik
$stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(total) as total_pendapatan,
        AVG(total) as rata_rata,
        MIN(total) as min_transaksi,
        MAX(total) as max_transaksi
    FROM transaksi
    WHERE DATE(tanggal) >= '$tanggal_mulai' 
    AND DATE(tanggal) <= '$tanggal_akhir'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Aplikasi Kasir</title>
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
                <li><a href="index.php"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="../produk/index.php"><span class="menu-icon">📦</span>Produk</a></li>
                <li><a href="laporan.php" class="active"><span class="menu-icon">📈</span>Laporan</a></li>
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
                <div class="topbar-title">Laporan Transaksi</div>
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
                <!-- Report Header -->
                <div class="report-header">
                    <h1>📊 Laporan Transaksi</h1>
                    <p>Periode: <?php echo date('d F Y', strtotime($tanggal_mulai)); ?> - <?php echo date('d F Y', strtotime($tanggal_akhir)); ?></p>
                </div>

                <!-- Filter Section -->
                <div class="filter-section search-filter">
                    <form method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
                        <div class="filter-group" style="flex: 1;">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>">
                        </div>
                        <div class="filter-group" style="flex: 1;">
                            <label for="tanggal_akhir">Tanggal Akhir</label>
                            <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                        </div>
                        <button type="submit" class="btn-filter">🔍 Filter</button>
                    </form>
                </div>

                <!-- Print Button -->
                <button onclick="window.print();" class="print-btn">🖨️ Cetak</button>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value"><?php echo $stats['total_transaksi']; ?></div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-label">Total Pendapatan</div>
                        <div class="stat-value">Rp <?php echo number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Rata-rata Transaksi</div>
                        <div class="stat-value">Rp <?php echo number_format($stats['rata_rata'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-label">Transaksi Minimum</div>
                        <div class="stat-value">Rp <?php echo number_format($stats['min_transaksi'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-label">Transaksi Maximum</div>
                        <div class="stat-value">Rp <?php echo number_format($stats['max_transaksi'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-section">
                    <div class="table-header">
                        <h3>Detail Transaksi</h3>
                    </div>

                    <?php if (mysqli_num_rows($transaksi) > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Petugas</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Jumlah Barang</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    mysqli_data_seek($transaksi, 0);
                                    $no = 1;
                                    $grand_total = 0;
                                    $jumlah_barang_total = 0;
                                    
                                    while ($t = mysqli_fetch_assoc($transaksi)) {
                                        $transaksi_detail = mysqli_query($conn, "
                                            SELECT SUM(qty) as total_qty FROM transaksi_detail 
                                            WHERE transaksi_id = '{$t['transaksi_id']}'
                                        ");
                                        $detail = mysqli_fetch_assoc($transaksi_detail);
                                        $grand_total += $t['total'];
                                        $jumlah_barang_total += $detail['total_qty'];
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $t['transaksi_id']; ?></strong></td>
                                            <td><?php echo $t['nama']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($t['tanggal'])); ?></td>
                                            <td style="text-align: center;"><?php echo $detail['total_qty']; ?> item</td>
                                            <td><strong>Rp <?php echo number_format($t['total'], 0, ',', '.'); ?></strong></td>
                                            <td>
                                                <a href="detail.php?id=<?php echo $t['transaksi_id']; ?>" style="color: #2563eb; text-decoration: none; font-weight: 600;">📋 Lihat Detail</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Summary -->
                        <div class="footer-summary">
                            <div class="footer-item">
                                <div class="label">Total Transaksi</div>
                                <div class="value"><?php echo $stats['total_transaksi']; ?></div>
                            </div>
                            <div class="footer-item">
                                <div class="label">Total Barang</div>
                                <div class="value"><?php echo $jumlah_barang_total; ?></div>
                            </div>
                            <div class="footer-item">
                                <div class="label">Grand Total</div>
                                <div class="value">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <p>❌ Tidak ada data transaksi untuk periode yang dipilih</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Reset to default date range if needed
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.filter-group input');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value) {
                        this.style.borderColor = '#2563eb';
                    }
                });
            });
        });
    </script>
</body>
</html>
