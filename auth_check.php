<?php
// File: auth_check.php
session_start();

// Fungsi untuk mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
}

// Fungsi untuk memastikan user sudah login
function requireLogin() {
    if (!isLoggedIn()) {
        // Cek apakah file ini dipanggil dari folder pages atau tidak
        $script_path = $_SERVER['SCRIPT_NAME'];
        
        if (strpos($script_path, '/pages/') !== false) {
            header("Location: ../login.php");
        } else {
            header("Location: login.php");
        }
        exit;
    }
}

// Fungsi untuk mengecek timeout session
function checkSessionTimeout($timeout = 1800) { // Default 30 menit
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Jika session timeout, lakukan logout
        session_unset();
        session_destroy();
        
        // Mulai session baru untuk pesan logout
        session_start();
        $_SESSION['logout_message'] = "Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.";
        
        // Cek apakah file ini dipanggil dari folder pages atau tidak
        $script_path = $_SERVER['SCRIPT_NAME'];
        
        if (strpos($script_path, '/pages/') !== false) {
            header("Location: ../login.php");
        } else {
            header("Location: login.php");
        }
        exit;
    }
    
    // Update waktu aktivitas terakhir
    $_SESSION['last_activity'] = time();
}
?>