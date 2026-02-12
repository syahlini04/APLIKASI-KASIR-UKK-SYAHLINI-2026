<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data transaksi terbaru
$transaksi_terbaru = mysqli_query($conn, "
    SELECT * FROM transaksi 
    WHERE customer_id='$user_id'
    ORDER BY transaksi_id DESC
    LIMIT 5
");

// Hitung statistik
$stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(total) as total_belanja
    FROM transaksi
    WHERE customer_id='$user_id'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .user-dashboard {
            min-height: 100vh;
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
            padding: 40px 20px;
        }
        
        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
        }
        
        .welcome-card h1 {
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        
        .welcome-card p {
            font-size: 16px;
            margin: 0;
            opacity: 0.95;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .stat-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 13px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 28px;
            color: #1e293b;
            font-weight: bold;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .menu-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            border-color: #2563eb;
        }
        
        .menu-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 15px;
        }
        
        .menu-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .menu-desc {
            font-size: 13px;
            color: #64748b;
        }
        
        .recent-transactions {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .recent-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .transaction-item:last-child {
            border-bottom: none;
        }
        
        .transaction-date {
            font-size: 13px;
            color: #64748b;
        }
        
        .transaction-amount {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        
        .empty-state p {
            margin: 10px 0;
        }
        
        @media (max-width: 768px) {
            .welcome-card {
                padding: 25px;
            }
            
            .welcome-card h1 {
                font-size: 24px;
            }
            
            .stats-grid, .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="user-dashboard">
        <div class="dashboard-container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h1>👋 Halo, <?php echo $_SESSION['nama']; ?>!</h1>
                <p>Selamat datang kembali di Aplikasi Kasir</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-icon">💳</div>
                    <div class="stat-label">Total Transaksi</div>
                    <div class="stat-value"><?php echo $stats['total_transaksi'] ?? 0; ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">💰</div>
                    <div class="stat-label">Total Belanja</div>
                    <div class="stat-value">Rp <?php echo number_format($stats['total_belanja'] ?? 0, 0, ',', '.'); ?></div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <?php if (mysqli_num_rows($transaksi_terbaru) > 0): ?>
                <div class="recent-transactions">
                    <h3 class="recent-title">📜 Transaksi Terbaru</h3>
                    <?php
                    mysqli_data_seek($transaksi_terbaru, 0);
                    while ($t = mysqli_fetch_assoc($transaksi_terbaru)):
                    ?>
                        <div class="transaction-item">
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">Transaksi #<?php echo $t['transaksi_id']; ?></div>
                                <div class="transaction-date"><?php echo date('d M Y H:i', strtotime($t['tanggal'])); ?></div>
                            </div>
                            <div class="transaction-amount">Rp <?php echo number_format($t['total'], 0, ',', '.'); ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <!-- Menu Cards -->
            <div class="menu-grid">
                <a href="riwayat.php" class="menu-card">
                    <div class="menu-icon">📋</div>
                    <div class="menu-title">Riwayat Lengkap</div>
                    <div class="menu-desc">Lihat semua transaksi Anda</div>
                </a>
                <a href="../auth/logout.php" class="menu-card" style="border-color: #dc2626;">
                    <div class="menu-icon">🚪</div>
                    <div class="menu-title">Logout</div>
                    <div class="menu-desc">Keluar dari akun</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
