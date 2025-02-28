<?php 
// Cek apakah session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tentukan path relatif berdasarkan lokasi file saat ini
$current_path = $_SERVER['PHP_SELF'];
$is_in_pages = strpos($current_path, '/pages/') !== false;
$logout_path = $is_in_pages ? 'logout.php' : 'pages/logout.php';
$login_path = $is_in_pages ? '../login.php' : 'login.php';
$index_path = $is_in_pages ? '../index.php' : 'index.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Ambulans</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #f0f9ff;
            --text-light: #f8fafc;
            --text-dark: #1e293b;
            --accent-color: #3b82f6;
            --danger-color: #ef4444;
            --success-color: #10b981;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Navbar Styling */
        .navbar {
            z-index: 99999;
            padding: 0.75rem 0;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            padding: 0.5rem 0;
            background: rgba(37, 99, 235, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            color: var(--text-light) !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.1);
        }
        
        .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover, .nav-link:focus {
            color: var(--text-light) !important;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: var(--text-light) !important;
            background-color: rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }
        
        .nav-link i {
            font-size: 1.1rem;
            margin-right: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover i {
            transform: translateY(-2px);
        }
        
        /* Dropdown Styling */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 0.75rem;
            margin-top: 0.75rem;
            min-width: 220px;
            animation: fadeInUp 0.3s ease;
        }
        
        .dropdown-item {
            color: var(--text-dark) !important;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: var(--secondary-color) !important;
            color: var(--primary-color) !important;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        /* Login/Logout Button */
        .btn-auth {
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-login {
            background-color: var(--text-light);
            color: var(--primary-color) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-login:hover {
            background-color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-logout {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--danger-color) !important;
        }
        
        .btn-logout:hover {
            background-color: var(--danger-color);
            color: white !important;
            transform: translateY(-3px);
        }
        
        /* Mobile Navbar */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            color: var(--text-light);
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .navbar-collapse {
                background: rgba(37, 99, 235, 0.98);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                padding: 1rem;
                margin-top: 0.75rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                max-height: 80vh;
                overflow-y: auto;
            }
            
            .nav-item {
                margin: 0.5rem 0;
            }
            
            .dropdown-menu {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: none;
                box-shadow: none;
                border: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 0.5rem;
                padding: 0.5rem;
            }
            
            .dropdown-item {
                color: rgba(255, 255, 255, 0.85) !important;
            }
            
            .dropdown-item:hover, .dropdown-item:focus {
                background-color: rgba(255, 255, 255, 0.15) !important;
                color: white !important;
            }
            
            .dropdown-item i {
                color: rgba(255, 255, 255, 0.85);
            }
            
            .btn-auth {
                margin-top: 0.5rem;
                width: 100%;
            }
            
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top animate__animated animate__fadeIn">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="<?php echo $index_path; ?>">
                <img src="<?php echo $is_in_pages ? '../assets/images/logo.png' : 'assets/images/logo.png'; ?>" alt="Logo" width="36" height="36" class="me-2 animate__animated animate__pulse animate__infinite animate__slower">
                <span>Inventaris Ambulans</span>
            </a>

            <!-- Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'dashboard.php' : 'pages/dashboard.php'; ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>

                    <!-- Stok Barang Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['stock_barang.php', 'tambah_stock.php', 'penggunaan_barang.php']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-boxes"></i> Stok Barang
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'stock_barang.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'stock_barang.php' : 'pages/stock_barang.php'; ?>"><i class="fas fa-list"></i> Daftar Stok</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'tambah_stock.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'tambah_stock.php' : 'pages/tambah_stock.php'; ?>"><i class="fas fa-cart-plus"></i> Tambah Stok</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'penggunaan_barang.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'penggunaan_barang.php' : 'pages/penggunaan_barang.php'; ?>"><i class="fas fa-clipboard-list"></i> Penggunaan Barang</a></li>
                        </ul>
                    </li>

                    <!-- Alat Kesehatan Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['tambah_alat_kesehatan.php', 'manajemen_user.php']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-medkit"></i> Alat Kesehatan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'tambah_alat_kesehatan.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'tambah_alat_kesehatan.php' : 'pages/tambah_alat_kesehatan.php'; ?>"><i class="fas fa-plus-square"></i> Tambah Alat</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'manajemen_user.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'manajemen_user.php' : 'pages/manajemen_user.php'; ?>"><i class="fas fa-users-cog"></i> Manajemen User</a></li>
                        </ul>
                    </li>

                    <!-- Laporan Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo basename($_SERVER['PHP_SELF']) == 'riwayat_bulanan.php' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-bar"></i> Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'riwayat_bulanan.php' ? 'active' : ''; ?>" href="<?php echo $is_in_pages ? 'riwayat_bulanan.php' : 'pages/riwayat_bulanan.php'; ?>"><i class="fas fa-calendar-alt"></i> Riwayat Bulanan</a></li>
                        </ul>
                    </li>

                    <!-- Logout Button Only (No Login Button) -->
                    <li class="nav-item ms-lg-2">
    <a class="nav-link btn-auth btn-logout-animated" href="<?php echo $logout_path; ?>">
        <span class="logout-text">Logout</span>
        <span class="logout-icon"><i class="fas fa-arrow-right-from-bracket"></i></span>
    </a>
</li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Add active class to current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = location.pathname.split('/').slice(-1)[0];
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentLocation) {
                    link.classList.add('active');
                }
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>