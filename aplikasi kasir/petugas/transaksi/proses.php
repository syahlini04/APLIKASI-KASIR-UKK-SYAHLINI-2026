<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
include "../../config/koneksi.php";

header('Content-Type: application/json; charset=utf-8');

// Helper function untuk logging error
function logError($message) {
    $log_file = __DIR__ . '/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Validasi session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesi login anda telah berakhir. Silakan login kembali']);
    exit;
}

if ($_SESSION['role'] != 'petugas') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses untuk melakukan transaksi']);
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Method request tidak valid']);
    exit;
}

try {
    // Validasi input
    $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : null;
    $keranjang_json = isset($_POST['keranjang']) ? trim($_POST['keranjang']) : null;
    
    if (!$customer_id || !$keranjang_json) {
        throw new Exception('Data pelanggan atau keranjang tidak ditemukan');
    }

    // Decode JSON keranjang
    $keranjang = json_decode($keranjang_json, true);
    if ($keranjang === null) {
        throw new Exception('Format data keranjang tidak valid: ' . json_last_error_msg());
    }

    $petugas_id = $_SESSION['user_id'];

    // Validasi keranjang tidak kosong
    if (empty($keranjang)) {
        throw new Exception('Keranjang tidak boleh kosong');
    }

    // Validasi customer ID adalah numeric
    if (!is_numeric($customer_id)) {
        throw new Exception('ID pelanggan tidak valid');
    }

    // Validasi customer ada di database
    $cek_customer = mysqli_query($conn, "SELECT user_id FROM user WHERE user_id='$customer_id' AND role='user'");
    if (!$cek_customer) {
        throw new Exception('Error database: ' . mysqli_error($conn));
    }
    if (mysqli_num_rows($cek_customer) === 0) {
        throw new Exception('Pelanggan tidak ditemukan');
    }

    // Hitung total
    $total = 0;
    foreach ($keranjang as $item) {
        if (!isset($item['id'], $item['harga'], $item['qty'])) {
            throw new Exception('Format item keranjang tidak valid');
        }
        if (!is_numeric($item['harga']) || !is_numeric($item['qty'])) {
            throw new Exception('Harga atau jumlah item tidak valid');
        }
        $total += intval($item['harga']) * intval($item['qty']);
    }

    if ($total <= 0) {
        throw new Exception('Total transaksi harus lebih dari 0');
    }

    // Mulai transaksi database
    if (!mysqli_begin_transaction($conn)) {
        throw new Exception('Gagal memulai transaksi database: ' . mysqli_error($conn));
    }

    try {
        // SIMPAN TRANSAKSI MASTER
        $insert = mysqli_query($conn, "
            INSERT INTO transaksi (user_id, customer_id, total, tanggal)
            VALUES ('$petugas_id', '$customer_id', '$total', NOW())
        ");

        if (!$insert) {
            throw new Exception('Gagal menyimpan transaksi: ' . mysqli_error($conn));
        }

        $transaksi_id = mysqli_insert_id($conn);
        if (!$transaksi_id) {
            throw new Exception('Gagal mendapatkan ID transaksi');
        }

        // SIMPAN DETAIL TRANSAKSI & UPDATE STOK
        foreach ($keranjang as $item) {
            $produk_id = mysqli_real_escape_string($conn, $item['id']);
            $qty = intval($item['qty']);
            $harga = intval($item['harga']);
            $subtotal = $harga * $qty;

            // Cek stok
            $stok_query = mysqli_query($conn, "SELECT stok, nama_produk FROM produk WHERE produk_id='$produk_id'");
            if (!$stok_query) {
                throw new Exception('Error query stok: ' . mysqli_error($conn));
            }

            $produk = mysqli_fetch_assoc($stok_query);
            if (!$produk) {
                throw new Exception("Produk (ID: $produk_id) tidak ditemukan");
            }

            if ($qty > $produk['stok']) {
                throw new Exception("Stok {$produk['nama_produk']} tidak cukup. Stok tersedia: {$produk['stok']}");
            }

            // SIMPAN DETAIL TRANSAKSI
            $detail = mysqli_query($conn, "
                INSERT INTO transaksi_detail 
                (transaksi_id, produk_id, qty, harga, subtotal)
                VALUES 
                ('$transaksi_id', '$produk_id', '$qty', '$harga', '$subtotal')
            ");

            if (!$detail) {
                throw new Exception('Gagal menyimpan detail transaksi: ' . mysqli_error($conn));
            }

            // UPDATE STOK
            $update_stok = mysqli_query($conn, "
                UPDATE produk 
                SET stok = stok - $qty 
                WHERE produk_id='$produk_id'
            ");

            if (!$update_stok) {
                throw new Exception('Gagal update stok produk: ' . mysqli_error($conn));
            }
        }

        // Commit transaksi
        if (!mysqli_commit($conn)) {
            throw new Exception('Gagal melakukan commit transaksi: ' . mysqli_error($conn));
        }

        // Catat aktivitas (dengan error handling)
        if (function_exists('catatAktivitas')) {
            $deskripsi = "Membuat transaksi penjualan #$transaksi_id dengan total Rp " . number_format($total);
            catatAktivitas($conn, $petugas_id, $deskripsi);
        }

        // Return success response
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Transaksi berhasil disimpan',
            'transaksi_id' => $transaksi_id,
            'total' => $total
        ]);

    } catch (Exception $e) {
        // Rollback jika ada error
        @mysqli_rollback($conn);
        
        $error_msg = $e->getMessage();
        logError("Checkout Error: " . $error_msg . " | User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => $error_msg
        ]);
        exit;
    }

} catch (Throwable $e) {
    $error_msg = $e->getMessage();
    logError("Unexpected Error: " . $error_msg);
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan sistem. Silakan hubungi administrator.'
    ]);
}

exit;