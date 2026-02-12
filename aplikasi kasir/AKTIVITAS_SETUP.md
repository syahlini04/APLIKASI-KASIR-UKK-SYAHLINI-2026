# Setup Tabel Aktivitas & Logging System

## 1. Buat Tabel Aktivitas di Database

Jalankan query SQL berikut di phpMyAdmin atau command line:

```sql
CREATE TABLE aktivitas (
    aktivitas_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    deskripsi VARCHAR(255),
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);
```

## 2. Function untuk Mencatat Aktivitas

Tambahkan function ini di file `config/koneksi.php`:

```php
function catatAktivitas($conn, $user_id, $deskripsi) {
    $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
    mysqli_query($conn, "
        INSERT INTO aktivitas (user_id, deskripsi, tanggal)
        VALUES ('$user_id', '$deskripsi', NOW())
    ");
}
```

## 3. Penggunaan di Setiap Halaman

### Contoh di admin/user/tambah.php (setelah insert user):
```php
catatAktivitas($conn, $_SESSION['user_id'], "Menambah Pengguna: $nama");
```

### Contoh di admin/user/edit.php (setelah update):
```php
catatAktivitas($conn, $_SESSION['user_id'], "Mengedit Pengguna: $nama");
```

### Contoh di admin/user/hapus.php (setelah delete):
```php
catatAktivitas($conn, $_SESSION['user_id'], "Menghapus Pengguna dengan ID: $id");
```

### Contoh di admin/produk/tambah.php:
```php
catatAktivitas($conn, $_SESSION['user_id'], "Menambah Produk: $nama_produk");
```

### Contoh di petugas/transaksi/proses.php:
```php
catatAktivitas($conn, $_SESSION['user_id'], "Transaksi Baru - Total: Rp " . number_format($total));
```

## 4. Aktivitas yang Dicatat

Dashboard akan menampilkan aktivitas dengan icon otomatis berdasarkan keyword:
- ➕ **Menambah** - Icon untuk penambahan data
- ✏️ **Mengedit** - Icon untuk perubahan data
- 🗑️ **Menghapus** - Icon untuk penghapusan data
- 💳 **Transaksi** - Icon untuk aktivitas transaksi
- 📝 **Default** - Icon untuk aktivitas lainnya

## 5. Dashboard Display

Dashboard admin akan menampilkan:
- **10 aktivitas terbaru** dalam order desc (terbaru di atas)
- **Timestamp** dengan format: dd/mm/yyyy HH:mm
- **Icon otomatis** sesuai jenis aktivitas
- **Deskripsi lengkap** aktivitas yang dilakukan

Setiap kali ada aktivitas baru, dashboard otomatis terupdate saat di-refresh.
