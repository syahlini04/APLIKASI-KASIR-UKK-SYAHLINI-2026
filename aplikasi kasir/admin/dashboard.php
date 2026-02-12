<?php
session_start();
include "../config/koneksi.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Get statistics from database
// Total Pengguna
$total_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM user");
$users_data = mysqli_fetch_assoc($total_users);
$total_users_count = $users_data['count'];

// Total Transaksi
$total_transactions = mysqli_query($conn, "SELECT COUNT(*) as count FROM transaksi");
$trans_data = mysqli_fetch_assoc($total_transactions);
$total_trans_count = $trans_data['count'];

// Total Pendapatan (SUM dari semua transaksi)
$total_revenue_query = mysqli_query($conn, "SELECT SUM(total) as total_revenue FROM transaksi");
$revenue_data = mysqli_fetch_assoc($total_revenue_query);
$total_revenue = $revenue_data['total_revenue'] ?? 0;

// Get recent activities
$aktivitas = mysqli_query($conn, "
    SELECT * FROM aktivitas 
    ORDER BY tanggal DESC 
    LIMIT 10
");

// Get recent users for table
$users = mysqli_query($conn, "SELECT * FROM user ORDER BY user_id DESC LIMIT 5");
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../assets/style.css">
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
                <li><a href="dashboard.php" class="active"><span class="menu-icon">📊</span>Dashboard</a></li>
                <li><a href="user/index.php"><span class="menu-icon">👥</span>Data Pengguna</a></li>
                <li><a href="transaksi/index.php"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="produk/index.php"><span class="menu-icon">📦</span>Produk</a></li>
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
                <div class="topbar-title">Dashboard Admin</div>
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
                <!-- Welcome Message -->
                <div class="alert alert-success">
                    Selamat datang kembali, <strong><?php echo $_SESSION['nama']; ?></strong>! 👋
                </div>

                <!-- Statistics Cards -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-card-icon" style="background-color: #eff6ff; color: #2563eb;">👥</div>
                        <div class="stat-card-title">Total Pengguna</div>
                        <div class="stat-card-value"><?php echo $total_users_count; ?></div>
                        <div class="stat-card-footer">Pengguna aktif</div>
                    </div>

                    <div class="stat-card stat-success">
                        <div class="stat-card-icon" style="background-color: #f0fdf4; color: #16a34a;">💳</div>
                        <div class="stat-card-title">Total Transaksi</div>
                        <div class="stat-card-value"><?php echo $total_trans_count; ?></div>
                        <div class="stat-card-footer">Transaksi tercatat</div>
                    </div>

                    <div class="stat-card stat-warning">
                        <div class="stat-card-icon" style="background-color: #fffbeb; color: #d97706;">💰</div>
                        <div class="stat-card-title">Total Pendapatan</div>
                        <div class="stat-card-value">Rp <?php echo number_format($total_revenue); ?></div>
                        <div class="stat-card-footer">Total penjualan</div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Pengguna Terbaru</h3>
                        <a href="user/tambah.php" class="btn btn-primary">+ Tambah Pengguna</a>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($users) > 0) { ?>
                                    <?php while ($u = mysqli_fetch_assoc($users)) { ?>
                                        <tr>
                                            <td><?= $u['user_id']; ?></td>
                                            <td><?= $u['nama']; ?></td>
                                            <td><?= $u['username']; ?></td>
                                            <td><?= $u['role']; ?></td>
                                            <td>
                                                <span class="badge badge-success">Aktif</span>
                                            </td>
                                            <td>
                                                <a href="user/edit.php?id=<?= $u['user_id']; ?>">✏️</a>
                                                <a href="user/hapus.php?id=<?= $u['user_id']; ?>"
                                                    onclick="return confirm('Yakin hapus user?')">🗑️</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">Belum ada data pengguna</td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>

                </div>

                <!-- Activity Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aktivitas Terbaru</h3>
                    </div>

                    <div style="padding: 20px;">
                        <?php 
                        if (mysqli_num_rows($aktivitas) > 0) {
                            while ($a = mysqli_fetch_assoc($aktivitas)) {
                                // Tentukan icon berdasarkan jenis aktivitas
                                $icon = '📝';
                                if (strpos($a['deskripsi'], 'Menambah') !== false) $icon = '➕';
                                if (strpos($a['deskripsi'], 'Mengedit') !== false) $icon = '✏️';
                                if (strpos($a['deskripsi'], 'Menghapus') !== false) $icon = '🗑️';
                                if (strpos($a['deskripsi'], 'Transaksi') !== false) $icon = '💳';
                        ?>
                        <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #eee; align-items: flex-start;">
                            <div style="font-size: 20px;"><?= $icon; ?></div>
                            <div style="flex: 1;">
                                <p style="margin: 0 0 5px 0; color: #333; font-weight: 500;">
                                    <?= $a['deskripsi']; ?>
                                </p>
                                <p style="margin: 0; color: #999; font-size: 12px;">
                                    <?= date('d/m/Y H:i', strtotime($a['tanggal'])); ?>
                                </p>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<p style="color: #95a5a6; text-align: center; margin: 20px 0;">📋 Belum ada aktivitas yang tercatat</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>