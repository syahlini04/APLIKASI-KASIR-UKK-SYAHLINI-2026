<?php
session_start();
include "../../config/koneksi.php";

// proteksi login petugas
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Default date range
$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : date('Y-m-01');
$tanggal_akhir = isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : date('Y-m-d');

// Query dengan filter tanggal
$data = mysqli_query($conn, "
    SELECT transaksi.* FROM transaksi 
    WHERE user_id='$user_id'
    AND DATE(transaksi.tanggal) >= '$tanggal_mulai'
    AND DATE(transaksi.tanggal) <= '$tanggal_akhir'
    ORDER BY transaksi.tanggal DESC
");

// Hitung statistik
$stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(total) as total_pendapatan,
        MIN(total) as min_transaksi,
        MAX(total) as max_transaksi
    FROM transaksi
    WHERE user_id='$user_id'
    AND DATE(tanggal) >= '$tanggal_mulai'
    AND DATE(tanggal) <= '$tanggal_akhir'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        @media print {
            .sidebar, .topbar, .card:nth-of-type(1), .card:nth-child(1), .stats-grid > div {
                display: none;
            }
            .dashboard-wrapper {
                display: flex;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .dashboard-content {
                max-width: 100%;
                margin: 0;
                padding: 20px;
            }
            .card {
                box-shadow: none;
                border: none;
            }
            .card-header {
                display: none;
            }
            .print-btn, .btn-filter, .filter-group {
                display: none !important;
            }
            .table-responsive {
                margin: 0;
            }
            .table-responsive table {
                font-size: 12px;
            }
            .table-responsive th, .table-responsive td {
                padding: 8px 10px;
            }
            body {
                background: white;
            }
        }
    </style>
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
                <li><a href="../dashboard.php"><span class="menu-icon">📊</span>Dashboard</a></li>
                <li><a href="../produk/index.php"><span class="menu-icon">📦</span>Produk</a></li>
                <li><a href="../transaksi/index.php"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="index.php" class="active"><span class="menu-icon">📈</span>Laporan</a></li>
            </ul>

            <div class="sidebar-footer">
                <a href="../../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Laporan Transaksi Saya</div>
                <div class="topbar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
                    <div class="user-info">
                        <p class="user-name"><?php echo $_SESSION['nama']; ?></p>
                        <p class="user-role">Petugas</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Report Header & Filter in One Card -->
                <div class="card" style="border: 2px solid #e2e8f0;">
                    <div class="card-header" style="background: white; color: #1e293b; border-radius: 8px 8px 0 0;">
                        <div>
                            <h3 class="card-title" style="color: #1e293b; margin: 0;">📊 Laporan Transaksi Saya</h3>
                            <p style="color: #666; margin: 5px 0 0 0; font-size: 13px;">
                                Periode: <?php echo date('d F Y', strtotime($tanggal_mulai)); ?> - <?php echo date('d F Y', strtotime($tanggal_akhir)); ?>
                            </p>
                        </div>
                        <button onclick="window.print();" class="print-btn" style="margin: 0; background: #16a34a; padding: 10px 20px; border-radius: 6px; transition: all 0.3s;">
                            🖨️ Cetak Laporan
                        </button>
                    </div>

                    <!-- Filter Section -->
                    <div style="padding: 25px; background: white;">
                        <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end;">
                            <div class="filter-group">
                                <label for="tanggal_mulai" style="display: flex; align-items: center; gap: 5px;">
                                    📅 Tanggal Mulai
                                </label>
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>" style="border: 2px solid #e2e8f0; padding: 12px; border-radius: 8px; font-size: 14px; transition: all 0.3s;">
                            </div>
                            <div class="filter-group">
                                <label for="tanggal_akhir" style="display: flex; align-items: center; gap: 5px;">
                                    📅 Tanggal Akhir
                                </label>
                                <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>" style="border: 2px solid #e2e8f0; padding: 12px; border-radius: 8px; font-size: 14px; transition: all 0.3s;">
                            </div>
                            <button type="submit" class="btn-filter" style="padding: 12px 25px; font-size: 14px; font-weight: 600; border-radius: 8px; background: #2563eb; color: white; border: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                                🔍 Terapkan Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
                    <div class="stat-card">
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value"><?php echo $stats['total_transaksi']; ?></div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-label">Total Pendapatan</div>
                        <div class="stat-value">Rp <?php echo number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-label">Minimum</div>
                        <div class="stat-value">Rp <?php echo number_format($stats['min_transaksi'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Transaksi</h3>
                    </div>

                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    mysqli_data_seek($data, 0);
                                    $grand_total = 0;
                                    $jumlah_barang_total = 0;
                                    
                                    while ($t = mysqli_fetch_assoc($data)) {
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
                                            <td><?php echo date('d/m/Y H:i', strtotime($t['tanggal'])); ?></td>
                                            <td style="text-align: center;"><?php echo $detail['total_qty']; ?></td>
                                            <td><strong>Rp <?php echo number_format($t['total'], 0, ',', '.'); ?></strong></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Summary -->
                        <div style="background: #f1f5f9; padding: 15px 20px; border-top: 2px solid #2563eb; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">TOTAL TRANSAKSI</div>
                                <div style="font-size: 18px; color: #1e293b; font-weight: bold;"><?php echo $stats['total_transaksi']; ?></div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">TOTAL BARANG</div>
                                <div style="font-size: 18px; color: #1e293b; font-weight: bold;"><?php echo $jumlah_barang_total; ?></div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">GRAND TOTAL</div>
                                <div style="font-size: 18px; color: #16a34a; font-weight: bold;">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                            <p>❌ Tidak ada data transaksi untuk periode yang dipilih</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced filter interaction
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.filter-group input');
            const printBtn = document.querySelector('.print-btn');
            const filterBtn = document.querySelector('.btn-filter');

            // Input focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = '#2563eb';
                    this.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.1)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.borderColor = '#e2e8f0';
                    this.style.boxShadow = 'none';
                });
                
                input.addEventListener('change', function() {
                    if (this.value) {
                        this.style.borderColor = '#16a34a';
                    }
                });
            });

            // Button hover effects
            if (printBtn) {
                printBtn.addEventListener('mouseenter', function() {
                    this.style.background = '#15803d';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 16px rgba(22, 163, 74, 0.4)';
                });
                printBtn.addEventListener('mouseleave', function() {
                    this.style.background = '#16a34a';
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            }

            if (filterBtn) {
                filterBtn.addEventListener('mouseenter', function() {
                    this.style.background = '#1d4ed8';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 20px rgba(37, 99, 235, 0.4)';
                });
                filterBtn.addEventListener('mouseleave', function() {
                    this.style.background = '#2563eb';
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.3)';
                });
            }
        });
    </script>
</body>
</html>
