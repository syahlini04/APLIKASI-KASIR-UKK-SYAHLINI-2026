<?php
session_start();
include "../../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'petugas') {
    header("Location: ../../auth/login.php");
    exit;
}

$transaksi_id = isset($_GET['transaksi_id']) ? $_GET['transaksi_id'] : null;

if (!$transaksi_id) {
    die('ID Transaksi tidak ditemukan');
}

// Ambil data transaksi
$transaksi = mysqli_query($conn, "
    SELECT t.*, 
           u.nama as petugas_nama,
           c.nama as customer_nama
    FROM transaksi t
    LEFT JOIN user u ON t.user_id = u.user_id
    LEFT JOIN user c ON t.customer_id = c.user_id
    WHERE t.transaksi_id = '$transaksi_id'
");

$trans = mysqli_fetch_assoc($transaksi);

if (!$trans) {
    die('Transaksi tidak ditemukan');
}

// Ambil detail transaksi
$detail = mysqli_query($conn, "
    SELECT td.*, p.nama_produk
    FROM transaksi_detail td
    LEFT JOIN produk p ON td.produk_id = p.produk_id
    WHERE td.transaksi_id = '$transaksi_id'
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Transaksi #<?= $transaksi_id; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
        }

        .struk-container {
            width: 400px;
            background: white;
            margin: 20px auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }

        .struk-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
        }

        .struk-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .struk-header p {
            font-size: 12px;
            color: #666;
            margin: 3px 0;
        }

        .struk-info {
            font-size: 12px;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .struk-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .struk-info-label {
            font-weight: bold;
            width: 40%;
        }

        .struk-info-value {
            width: 60%;
            text-align: right;
        }

        .struk-items {
            border-top: 2px dashed #333;
            border-bottom: 2px dashed #333;
            padding: 15px 0;
            margin-bottom: 15px;
        }

        .struk-item {
            font-size: 12px;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .struk-item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .struk-item-detail {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 11px;
        }

        .struk-item-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding-top: 5px;
            border-top: 1px dotted #ccc;
        }

        .struk-summary {
            font-size: 12px;
            margin-bottom: 15px;
        }

        .struk-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .struk-summary-row.subtotal {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .struk-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            background: #f0f0f0;
            padding: 10px;
            margin: -10px 0 0 0;
            border-radius: 5px;
        }

        .struk-total-label {
            flex: 1;
        }

        .struk-total-value {
            text-align: right;
            min-width: 120px;
        }

        .struk-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #333;
            font-size: 11px;
            color: #666;
        }

        .struk-footer p {
            margin: 5px 0;
        }

        .struk-message {
            text-align: center;
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-top: 15px;
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
        }

        .print-button button {
            padding: 12px 30px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .print-button button:hover {
            background: #2563eb;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .struk-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }

            .print-button {
                display: none;
            }

            @page {
                margin: 0;
                size: 80mm auto;
            }
        }

        @media (max-width: 480px) {
            .struk-container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <!-- Header -->
        <div class="struk-header">
            <h1>🧾 STRUK PENJUALAN</h1>
            <p>Aplikasi Kasir</p>
            <p>Transaksi #<?= str_pad($trans['transaksi_id'], 6, '0', STR_PAD_LEFT); ?></p>
        </div>

        <!-- Info Transaksi -->
        <div class="struk-info">
            <div class="struk-info-row">
                <span class="struk-info-label">Tanggal:</span>
                <span class="struk-info-value"><?= date('d/m/Y H:i', strtotime($trans['tanggal'])); ?></span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Petugas:</span>
                <span class="struk-info-value"><?= $trans['petugas_nama']; ?></span>
            </div>
            <div class="struk-info-row">
                <span class="struk-info-label">Customer:</span>
                <span class="struk-info-value"><?= $trans['customer_nama']; ?></span>
            </div>
        </div>

        <!-- Items -->
        <div class="struk-items">
            <?php 
            $total_items = 0;
            while ($d = mysqli_fetch_assoc($detail)) {
                $total_items += $d['qty'];
            ?>
                <div class="struk-item">
                    <div class="struk-item-name"><?= substr($d['nama_produk'], 0, 30); ?></div>
                    <div class="struk-item-detail">
                        <span><?= $d['qty']; ?> x Rp <?= number_format($d['harga']); ?></span>
                        <span>Rp <?= number_format($d['subtotal']); ?></span>
                    </div>
                </div>
            <?php } 

            // Reset result pointer
            mysqli_data_seek($detail, 0);
            ?>
        </div>

        <!-- Summary -->
        <div class="struk-summary">
            <div class="struk-summary-row subtotal">
                <span>Subtotal</span>
                <span>Rp <?= number_format($trans['total']); ?></span>
            </div>
            <div class="struk-total-row">
                <span class="struk-total-label">TOTAL</span>
                <span class="struk-total-value">Rp <?= number_format($trans['total']); ?></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="struk-footer">
            <p>════════════════════════════</p>
            <p>Terima kasih atas pembelian Anda</p>
            <p>Semoga sentosa bersama kami</p>
            <p>════════════════════════════</p>
            <p style="margin-top: 10px; font-size: 10px;">
                Dicetak: <?= date('d/m/Y H:i:s'); ?>
            </p>
        </div>

        <div class="struk-message">
            Mohon simpan struk ini sebagai bukti pembayaran
        </div>
    </div>

    <div class="print-button">
        <button onclick="window.print()">🖨️ Cetak Struk</button>
    </div>

    <script>
        // Auto print untuk versi pertama
        // window.print();
    </script>
</body>
</html>
