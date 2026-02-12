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

$produk = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Kasir - Aplikasi Kasir</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .transaksi-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 25px;
            margin-top: 20px;
        }

        .produk-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .produk-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .produk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .produk-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .produk-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
        }

        .produk-card.selected {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .produk-card-image {
            width: 100%;
            height: 150px;
            background: #f5f5f5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin-bottom: 10px;
            color: #d1d5db;
        }

        .produk-card-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .produk-card-harga {
            color: #10b981;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .produk-card-stok {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .produk-card-btn {
            width: 100%;
            padding: 8px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .produk-card-btn:hover {
            background: #2563eb;
        }

        /* Keranjang Styles */
        .keranjang-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .keranjang-header {
            padding: 20px;
            border-bottom: 2px solid #e2e8f0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .keranjang-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .keranjang-header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .keranjang-body {
            padding: 20px;
            flex: 1;
            max-height: 450px;
            overflow-y: auto;
            min-height: 200px;
        }

        .keranjang-kosong {
            text-align: center;
            color: #9ca3af;
            padding: 40px 20px;
            font-size: 14px;
        }

        .keranjang-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .keranjang-item-info {
            flex: 1;
        }

        .keranjang-item-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .keranjang-item-harga {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .keranjang-item-qty-control {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 6px;
        }

        .keranjang-item-qty-btn {
            width: 22px;
            height: 22px;
            border: 1px solid #d1d5db;
            background: white;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .keranjang-item-qty-btn:hover {
            background: #f3f4f6;
        }

        .keranjang-item-qty {
            width: 35px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
        }

        .keranjang-item-subtotal {
            color: #10b981;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .keranjang-item-delete {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
        }

        .keranjang-item-delete:hover {
            background: #dc2626;
        }

        /* Footer Keranjang */
        .keranjang-footer {
            padding: 20px;
            border-top: 2px solid #e2e8f0;
            background: #f9fafb;
            border-radius: 0 0 12px 12px;
        }

        .keranjang-summary {
            margin-bottom: 15px;
        }

        .keranjang-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .keranjang-summary-row.total {
            border-top: 2px solid #e5e7eb;
            padding-top: 8px;
            font-weight: 700;
            font-size: 16px;
            color: #10b981;
        }

        .keranjang-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .keranjang-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-checkout:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-checkout:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        .btn-cetak {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .btn-cetak:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-cetak:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        .form-header {
            margin-bottom: 20px;
        }

        .form-header-select {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        @media (max-width: 1200px) {
            .transaksi-container {
                grid-template-columns: 1fr;
            }

            .keranjang-section {
                position: static;
            }

            .produk-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
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
                <li><a href="../produk/index.php"><span class="menu-icon">📦</span>Data Produk</a></li>
                <li><a href="index.php" class="active"><span class="menu-icon">💳</span>Transaksi</a></li>
                <li><a href="../laporan/index.php"><span class="menu-icon">📈</span>Laporan</a></li>
            </ul>

            <div class="sidebar-footer">
                <a href="../../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-title">Transaksi Kasir - Sistem Keranjang Belanja</div>
                <div class="topbar-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
                    <div class="user-info">
                        <p class="user-name"><?php echo $_SESSION['nama']; ?></p>
                        <p class="user-role">Petugas Kasir</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="form-header-select">
                        <div class="form-group">
                            <label for="customer_id">
                                👤 Pilih Customer (Pembeli)
                            </label>
                            <select id="customer_id">
                                <option value="">-- Pilih Customer --</option>
                                <?php 
                                $customers = mysqli_query($conn, "SELECT user_id, nama FROM user WHERE role='user' ORDER BY nama ASC");
                                while ($c = mysqli_fetch_assoc($customers)) { ?>
                                    <option value="<?= $c['user_id']; ?>">
                                        <?= $c['nama']; ?> (ID: <?= $c['user_id']; ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Main Transaction Area -->
                <div class="transaksi-container">
                    <!-- Bagian Produk -->
                    <div class="produk-section">
                        <!-- Header Produk -->
                        <div class="produk-header">
                            <h3>📦 Daftar Produk Tersedia</h3>
                            <input type="text" id="searchProduk" class="search-box" placeholder="🔍 Cari produk..." style="width: 250px;">
                        </div>

                        <!-- Grid Produk -->
                        <div class="produk-grid" id="produkGrid">
                            <?php 
                            $produk_reset = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
                            while ($p = mysqli_fetch_assoc($produk_reset)) { 
                            ?>
                                <div class="produk-card" 
                                     data-id="<?= $p['produk_id']; ?>" 
                                     data-nama="<?= $p['nama_produk']; ?>"
                                     data-harga="<?= $p['harga']; ?>"
                                     data-stok="<?= $p['stok']; ?>"
                                     onclick="tambahKeKeranjang(this)">
                                    <div class="produk-card-image">📦</div>
                                    <div class="produk-card-name"><?= $p['nama_produk']; ?></div>
                                    <div class="produk-card-harga">Rp <?= number_format($p['harga']); ?></div>
                                    <div class="produk-card-stok">Stok: <?= $p['stok']; ?> pcs</div>
                                    <button type="button" class="produk-card-btn">➕ Tambah</button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Bagian Keranjang -->
                    <div class="keranjang-section">
                        <div class="keranjang-header">
                            <h3>🛒 Keranjang Belanja</h3>
                            <p id="jumlahItem">0 item di keranjang</p>
                        </div>

                        <div class="keranjang-body" id="keranjangBody">
                            <div class="keranjang-kosong">
                                <p>Keranjang masih kosong</p>
                                <p style="font-size: 11px; margin-top: 10px;">Klik produk untuk menambahkan item</p>
                            </div>
                        </div>

                        <div class="keranjang-footer">
                            <div class="keranjang-summary">
                                <div class="keranjang-summary-row">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">Rp 0</span>
                                </div>
                                <div class="keranjang-summary-row">
                                    <span>Diskon:</span>
                                    <span id="diskon">Rp 0</span>
                                </div>
                                <div class="keranjang-summary-row total">
                                    <span>Total Bayar:</span>
                                    <span id="totalBayar">Rp 0</span>
                                </div>
                            </div>

                            <div class="keranjang-actions">
                                <button type="button" class="keranjang-btn btn-checkout" id="btnCheckout" onclick="prosesCheckout()" disabled>
                                    ✓ Selesaikan Transaksi
                                </button>
                                <button type="button" class="keranjang-btn btn-cetak" id="btnCetak" onclick="cetakStruk()" disabled>
                                    🖨️ Cetak Struk
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data keranjang
        let keranjang = [];
        let transaksiSelesai = null;

        // Tambah ke keranjang
        function tambahKeKeranjang(element) {
            const produkId = element.getAttribute('data-id');
            const produkNama = element.getAttribute('data-nama');
            const produkHarga = parseInt(element.getAttribute('data-harga'));
            const produkStok = parseInt(element.getAttribute('data-stok'));

            // Cek apakah produk sudah ada di keranjang
            const itemExist = keranjang.find(item => item.id === produkId);

            if (itemExist) {
                // Jika ada, tambah qty
                if (itemExist.qty < produkStok) {
                    itemExist.qty++;
                } else {
                    alert('Stok tidak cukup!');
                    return;
                }
            } else {
                // Jika belum ada, tambah item baru
                keranjang.push({
                    id: produkId,
                    nama: produkNama,
                    harga: produkHarga,
                    stok: produkStok,
                    qty: 1
                });
            }

            // Mark element as selected
            element.classList.add('selected');

            updateKeranjangUI();
        }

        // Update Qty di keranjang
        function updateQty(produkId, change) {
            const item = keranjang.find(i => i.id === produkId);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) {
                    hapusItem(produkId);
                    return;
                }
                if (item.qty > item.stok) {
                    item.qty = item.stok;
                }
                updateKeranjangUI();
            }
        }

        // Hapus item dari keranjang
        function hapusItem(produkId) {
            keranjang = keranjang.filter(item => item.id !== produkId);
            document.querySelector(`[data-id="${produkId}"]`).classList.remove('selected');
            updateKeranjangUI();
        }

        // Update UI Keranjang
        function updateKeranjangUI() {
            const keranjangBody = document.getElementById('keranjangBody');
            const jumlahItem = document.getElementById('jumlahItem');
            const jumlahTotal = keranjang.reduce((sum, item) => sum + item.qty, 0);

            if (keranjang.length === 0) {
                keranjangBody.innerHTML = '<div class="keranjang-kosong"><p>Keranjang masih kosong</p><p style="font-size: 11px; margin-top: 10px;">Klik produk untuk menambahkan item</p></div>';
                jumlahItem.textContent = '0 item di keranjang';
                document.getElementById('btnCheckout').disabled = true;
                document.getElementById('btnCetak').disabled = true;
                return;
            }

            let html = '';
            keranjang.forEach(item => {
                const subtotal = item.harga * item.qty;
                html += `
                    <div class="keranjang-item">
                        <div class="keranjang-item-info">
                            <div class="keranjang-item-name">${item.nama}</div>
                            <div class="keranjang-item-harga">Rp ${parseInt(item.harga).toLocaleString('id-ID')} × ${item.qty}</div>
                            <div class="keranjang-item-qty-control">
                                <button type="button" class="keranjang-item-qty-btn" onclick="updateQty('${item.id}', -1)">−</button>
                                <input type="number" class="keranjang-item-qty" value="${item.qty}" readonly>
                                <button type="button" class="keranjang-item-qty-btn" onclick="updateQty('${item.id}', 1)">+</button>
                            </div>
                            <div class="keranjang-item-subtotal">Rp ${subtotal.toLocaleString('id-ID')}</div>
                        </div>
                        <button type="button" class="keranjang-item-delete" onclick="hapusItem('${item.id}')">Hapus</button>
                    </div>
                `;
            });

            keranjangBody.innerHTML = html;
            jumlahItem.textContent = `${jumlahTotal} item di keranjang`;

            // Update summary
            const subtotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('totalBayar').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');

            // Enable checkout button
            document.getElementById('btnCheckout').disabled = false;
            document.getElementById('btnCetak').disabled = transaksiSelesai === null;
        }

        // Proses Checkout
        function prosesCheckout() {
            const customerId = document.getElementById('customer_id').value;

            if (!customerId) {
                alert('Pilih customer terlebih dahulu!');
                return;
            }

            if (keranjang.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }

            // Kirim data ke backend
            const formData = new FormData();
            formData.append('customer_id', customerId);
            formData.append('keranjang', JSON.stringify(keranjang));

            fetch('proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Cek jika response bukan JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.error('Response text:', text);
                        throw new Error('Response bukan JSON: ' + text);
                    });
                }
            })
            .then(data => {
                console.log('Data response:', data);
                if (data.success) {
                    transaksiSelesai = data;
                    alert('✓ Transaksi berhasil disimpan!');
                    // Reset keranjang
                    keranjang = [];
                    document.getElementById('customer_id').value = '';
                    document.querySelectorAll('.produk-card').forEach(el => el.classList.remove('selected'));
                    updateKeranjangUI();
                    document.getElementById('btnCetak').disabled = false;
                } else {
                    alert('❌ Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error detail:', error);
                alert('❌ Terjadi kesalahan: ' + error.message);
            });
        }

        // Cetak Struk
        function cetakStruk() {
            if (!transaksiSelesai) {
                alert('Lakukan transaksi terlebih dahulu!');
                return;
            }

            window.open('cetak_struk.php?transaksi_id=' + transaksiSelesai.transaksi_id, '_blank');
        }

        // Search Produk
        document.getElementById('searchProduk').addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase();
            const cards = document.querySelectorAll('.produk-card');
            
            cards.forEach(card => {
                const nama = card.getAttribute('data-nama').toLowerCase();
                if (nama.includes(keyword)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>
