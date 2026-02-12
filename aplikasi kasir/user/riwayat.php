<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Default date range
$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : date('Y-m-01');
$tanggal_akhir = isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : date('Y-m-d');

// Query dengan filter tanggal
$data = mysqli_query($conn, "
    SELECT * FROM transaksi
    WHERE customer_id='$user_id'
    AND DATE(tanggal) >= '$tanggal_mulai'
    AND DATE(tanggal) <= '$tanggal_akhir'
    ORDER BY transaksi_id DESC
");


// Hitung statistik
$stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(total) as total_belanja,
        AVG(total) as rata_rata
    FROM transaksi
    WHERE customer_id='$user_id'
    AND DATE(tanggal) >= '$tanggal_mulai'
    AND DATE(tanggal) <= '$tanggal_akhir'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .user-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
            padding: 40px 20px;
        }
        
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }
        
        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr 120px;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        
        .filter-group input {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .filter-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .filter-btn {
            padding: 12px 25px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .filter-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .stat-item .label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .stat-item .value {
            font-size: 22px;
            color: #1e293b;
            font-weight: bold;
        }
        
        .transactions-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .card-header {
            background: #f1f5f9;
            padding: 20px 25px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .card-header h3 {
            margin: 0;
            font-size: 16px;
            color: #1e293b;
            font-weight: 600;
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .transaction-table thead {
            background: #f8fafc;
        }
        
        .transaction-table th {
            padding: 15px 25px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .transaction-table td {
            padding: 15px 25px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        
        .transaction-table tbody tr:hover {
            background: #f8fafc;
        }
        
        .amount {
            font-weight: bold;
            color: #16a34a;
            font-size: 15px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        
        .footer-summary {
            background: #f1f5f9;
            padding: 20px 25px;
            border-top: 2px solid #2563eb;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        
        .summary-item {
            font-size: 13px;
            color: #64748b;
        }
        
        .summary-item .label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 16px;
            color: #1e293b;
            font-weight: bold;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .footer-summary {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="user-page">
        <div class="page-container">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title">📜 Riwayat Transaksi</h1>
                <a href="dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <form method="POST" class="filter-form">
                    <div class="filter-group">
                        <label for="tanggal_mulai">📅 Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>">
                    </div>
                    <div class="filter-group">
                        <label for="tanggal_akhir">📅 Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                    </div>
                    <button type="submit" class="filter-btn">🔍 Filter</button>
                </form>
            </div>

            <!-- Statistics -->
            <div class="stats-row">
                <div class="stat-item">
                    <div class="label">Total Transaksi</div>
                    <div class="value"><?php echo $stats['total_transaksi'] ?? 0; ?></div>
                </div>
                <div class="stat-item">
                    <div class="label">Total Belanja</div>
                    <div class="value">Rp <?php echo number_format($stats['total_belanja'] ?? 0, 0, ',', '.'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="label">Rata-rata</div>
                    <div class="value">Rp <?php echo number_format($stats['rata_rata'] ?? 0, 0, ',', '.'); ?></div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="transactions-card">
                <div class="card-header">
                    <h3>Detail Transaksi</h3>
                </div>

                <?php if (mysqli_num_rows($data) > 0): ?>
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal & Waktu</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($data, 0);
                            $no = 1;
                            $grand_total = 0;
                            while ($d = mysqli_fetch_assoc($data)):
                                $grand_total += $d['total'];
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($d['tanggal'])); ?></td>
                                    <td class="amount">Rp <?php echo number_format($d['total'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Footer Summary -->
                    <div class="footer-summary">
                        <div class="summary-item">
                            <div class="label">TOTAL TRANSAKSI</div>
                            <div class="value"><?php echo $stats['total_transaksi']; ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="label">GRAND TOTAL</div>
                            <div class="value">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="label">RATA-RATA</div>
                            <div class="value">Rp <?php echo number_format($stats['rata_rata'] ?? 0, 0, ',', '.'); ?></div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="date"]');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value) {
                        this.style.borderColor = '#16a34a';
                    }
                });
            });
        });
    </script>
</body>
</html>
