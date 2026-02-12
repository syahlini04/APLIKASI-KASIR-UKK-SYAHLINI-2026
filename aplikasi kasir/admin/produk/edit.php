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

// Ambil ID produk
$id = $_GET['id'];

// Ambil data produk berdasarkan ID
$data = mysqli_query($conn, "SELECT * FROM produk WHERE produk_id='$id'");
$produk = mysqli_fetch_assoc($data);

$error = '';
$success = '';

// Proses update
if (isset($_POST['update'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok  = mysqli_real_escape_string($conn, $_POST['stok']);

    // Validasi input
    if (empty($nama) || empty($harga) || empty($stok)) {
        $error = 'Semua field harus diisi!';
    } elseif ($harga < 0 || $stok < 0) {
        $error = 'Harga dan stok tidak boleh negatif!';
    } else {
        $update = mysqli_query($conn, "
            UPDATE produk SET
            nama_produk='$nama',
            harga='$harga',
            stok='$stok'
            WHERE produk_id='$id'
        ");

        if ($update) {
            $success = 'Produk berhasil diupdate!';
            catatAktivitas($conn, $_SESSION['user_id'], "Mengedit Produk: $nama (ID: $id)");
            $produk['nama_produk'] = $nama;
            $produk['harga'] = $harga;
            $produk['stok'] = $stok;
        } else {
            $error = 'Gagal mengupdate produk!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Aplikasi Kasir</title>
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
                <li><a href="../transaksi/index.php"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="index.php" class="active"><span class="menu-icon">📦</span>Produk</a></li>
                <li><a href="../laporan/index.php"><span class="menu-icon">📈</span>Laporan</a></li>
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
                <div class="topbar-title">Edit Produk</div>
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
                <!-- Alert Messages -->
                <?php if ($error): ?>
                    <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; color: #991b1b;">
                        <strong>❌ Error:</strong> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div style="background: #dcfce7; border-left: 4px solid #16a34a; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; color: #166534;">
                        <strong>✅ Sukses:</strong> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Data Produk</h3>
                    </div>

                    <form method="POST" style="padding: 25px;">
                        <!-- Nama Produk -->
                        <div class="form-row" style="margin-bottom: 20px;">
                            <div class="form-group" style="width: 100%;">
                                <label for="nama_produk" style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 14px;">
                                    📦 Nama Produk
                                </label>
                                <input type="text" 
                                       id="nama_produk"
                                       name="nama_produk" 
                                       value="<?php echo $produk['nama_produk']; ?>" 
                                       required
                                       style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;">
                            </div>
                        </div>

                        <!-- Harga dan Stok -->
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label for="harga" style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 14px;">
                                    💰 Harga (Rp)
                                </label>
                                <input type="number" 
                                       id="harga"
                                       name="harga" 
                                       value="<?php echo $produk['harga']; ?>" 
                                       required
                                       min="0"
                                       style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;">
                            </div>
                            <div class="form-group">
                                <label for="stok" style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 14px;">
                                    📦 Stok
                                </label>
                                <input type="number" 
                                       id="stok"
                                       name="stok" 
                                       value="<?php echo $produk['stok']; ?>" 
                                       required
                                       min="0"
                                       style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.3s; box-sizing: border-box;">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <a href="index.php" style="display: inline-block; padding: 12px 25px; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; transition: all 0.3s; font-size: 14px;">
                                ← Kembali
                            </a>
                            <button type="submit" name="update" style="padding: 12px 25px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 14px;">
                                ✅ Update Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Input focus effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="number"]');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = '#2563eb';
                    this.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.1)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.borderColor = '#e2e8f0';
                    this.style.boxShadow = 'none';
                });
            });

            // Button hover effects
            const buttons = document.querySelectorAll('button, a[style*="background"]');
            buttons.forEach(btn => {
                if (btn.tagName === 'BUTTON' || (btn.tagName === 'A' && btn.style.background)) {
                    btn.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-2px)';
                        if (this.style.background === 'rgb(37, 99, 235)') {
                            this.style.boxShadow = '0 8px 16px rgba(37, 99, 235, 0.3)';
                        } else {
                            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.2)';
                        }
                    });
                    btn.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    });
                }
            });
        });
    </script>
</body>
</html>
