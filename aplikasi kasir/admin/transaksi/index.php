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

$transaksi = mysqli_query($conn, "
    SELECT transaksi.*, user.nama 
    FROM transaksi 
    JOIN user ON transaksi.user_id = user.user_id
    ORDER BY transaksi.transaksi_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi - Aplikasi Kasir</title>
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
                
            </ul>

            <div class="sidebar-footer">
                <a href="../../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Data Transaksi</div>
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
                        <h3 class="card-title">Daftar Transaksi</h3>
                        <div style="flex: 1;"></div>
                        <input type="text" id="searchInput" placeholder="🔍 Cari transaksi..." style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; min-width: 250px;">
                    </div>

                    <div class="table-responsive">
                        <table id="transaksiTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Transaksi</th>
                                    <th>Petugas</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($transaksi) > 0) {
                                    while ($t = mysqli_fetch_assoc($transaksi)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>#<?= $t['transaksi_id']; ?></td>
                                    <td><?= $t['nama']; ?></td>
                                    <td>Rp <?= number_format($t['total']); ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($t['tanggal'])); ?></td>
                                    <td>
                                        <a href="detail.php?id=<?= $t['transaksi_id']; ?>" class="btn btn-primary btn-sm">Detail</a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6" style="text-align: center; padding: 20px;">Belum ada data transaksi</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi pencarian transaksi
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            let filter = e.target.value.toLowerCase();
            let table = document.getElementById('transaksiTable');
            let rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let text = rows[i].textContent.toLowerCase();
                rows[i].style.display = text.includes(filter) ? '' : 'none';
            }
        });
    </script>

</body>
</html>
