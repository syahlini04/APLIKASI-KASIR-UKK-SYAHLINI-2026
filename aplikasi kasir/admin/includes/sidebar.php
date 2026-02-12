<!-- Sidebar Navigation -->
<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-store"></i>
        <div>
            <div class="sidebar-title">KASIR APP</div>
            <div class="sidebar-subtitle">Admin Panel</div>
        </div>
    </div>
    
    <div class="menu-section">
        <div class="menu-title">Menu Utama</div>
        <a href="<?php echo isset($baseUrl) ? $baseUrl : '../../'; ?>admin/dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </div>
    
    <div class="menu-section">
        <div class="menu-title">Manajemen</div>
        <a href="<?php echo isset($baseUrl) ? $baseUrl : '../../'; ?>admin/produk/index.php" class="menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'produk') !== false ? 'active' : ''; ?>">
            <i class="fas fa-box"></i>
            <span>Produk</span>
        </a>
        <a href="<?php echo isset($baseUrl) ? $baseUrl : '../../'; ?>admin/user/index.php" class="menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'user') !== false ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>User</span>
        </a>
    </div>
    
    <div class="menu-section">
        <div class="menu-title">Laporan</div>
        <a href="#" class="menu-item">
            <i class="fas fa-chart-bar"></i>
            <span>Penjualan</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-chart-pie"></i>
            <span>Inventori</span>
        </a>
    </div>
    
    <div class="menu-section">
        <div class="menu-title">Pengaturan</div>
        <a href="#" class="menu-item">
            <i class="fas fa-cog"></i>
            <span>Konfigurasi</span>
        </a>
    </div>
    
    <a href="<?php echo isset($baseUrl) ? $baseUrl : '../../'; ?>auth/logout.php" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</div>
