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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - Aplikasi Kasir</title>
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
                
            </ul>

            <div class="sidebar-footer">
                <a href="../../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Data Pengguna</div>
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
                        <h3 class="card-title">Daftar Pengguna</h3>
                        <a href="tambah.php" class="btn btn-primary">+ Tambah Pengguna</a>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $data = mysqli_query($conn, "SELECT * FROM user");

                                if (mysqli_num_rows($data) > 0) {
                                    while ($u = mysqli_fetch_assoc($data)) {
                                        $role_badge = '';
                                        if ($u['role'] == 'admin') {
                                            $role_badge = '<span class="badge badge-success">Admin</span>';
                                        } elseif ($u['role'] == 'petugas') {
                                            $role_badge = '<span class="badge badge-warning">Petugas</span>';
                                        } else {
                                            $role_badge = '<span class="badge badge-info" style="background-color: #d6eaf8; color: #2563eb;">User</span>';
                                        }
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $u['nama']; ?></td>
                                    <td><?= $u['username']; ?></td>
                                    <td><?= $role_badge; ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $u['user_id']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                        <a href="hapus.php?id=<?= $u['user_id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px;">
                                        <p style="color: #6b7280; font-size: 14px;">📋 Belum ada data pengguna</p>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
