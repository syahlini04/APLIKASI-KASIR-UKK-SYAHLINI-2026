# Panduan Menggunakan Sidebar Layout

## Struktur File
- `assets/sidebar.css` - CSS untuk sidebar dan layout
- `admin/includes/sidebar.php` - Sidebar HTML template

## Cara Menggunakan di Halaman Admin

### 1. Tambahkan Link ke CSS Sidebar
```html
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nama Halaman - Admin Kasir</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/sidebar.css?v=<?php echo time(); ?>">
    <style>
        /* CSS spesifik halaman di sini */
    </style>
</head>
```

### 2. Include Sidebar di Body
```html
<body>
    <!-- Sidebar Navigation -->
    <?php include '../../admin/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h2>📦 Judul Halaman</h2>
            <div class="header-info">
                <span>👤 <?= htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
            </div>
        </div>
        
        <!-- Konten halaman di sini -->
    </div>
</body>
```

## Styling Classes

### Layout Utama
- `.sidebar` - Container sidebar
- `.main-content` - Area konten utama
- `.page-header` - Header halaman

### Menu Sidebar
- `.menu-section` - Setiap section menu
- `.menu-item` - Item menu individual
- `.menu-item.active` - Item menu yang aktif (current page)
- `.logout-btn` - Tombol logout

### Navigasi
Menu Sidebar otomatis mendeteksi halaman aktif berdasarkan path file PHP.

## Responsive
Layout sudah fully responsive:
- **Desktop (1024px+)**: Sidebar fixed di kiri, konten di kanan
- **Tablet (768px - 1024px)**: Sidebar lebih kecil
- **Mobile (<768px)**: Sidebar berubah menjadi horizontal

## Catatan
- CSS menggunakan CSS Variables untuk warna dan styling konsisten
- Font Awesome 6.0.0 untuk icons
- Sidebar sticky dan navigasi otomatis menyesuaikan dengan halaman saat ini
