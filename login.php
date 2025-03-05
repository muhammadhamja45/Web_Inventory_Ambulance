<?php
session_start();

// Redirect ke dashboard jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: pages/dashboard.php");
    exit;
}

// Cek jika ada session logout message
$logout_message = '';
if (isset($_SESSION['logout_message'])) {
    $logout_message = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']); // Hapus pesan setelah ditampilkan
}

include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM pengguna WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $password === $user['password']) { // Perubahan di sini: Bandingkan password tanpa hashing
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['last_activity'] = time();
        $_SESSION['is_logged_in'] = true;
        
        // Redirect ke dashboard
        header("Location: pages/dashboard.php");
        exit;
    } else {
        $error = "Email atau password yang Anda masukkan tidak valid.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Ambulans</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0a58ca;
            --primary-light: #e7f1ff;
            --secondary: #6c757d;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-light) 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            max-width: 900px;
            width: 100%;
        }
        
        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .login-header {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: none;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .login-image {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
        }
        
        .login-image img {
            max-width: 100%;
            height: auto;
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary);
            opacity: 0.8;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px 20px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background-color: var(--light);
            border-top: none;
        }
        
        .login-brand {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 0;
        }
        
        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: var(--primary);
        }
        
        .password-input {
            border-left: none;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .password-toggle {
            cursor: pointer;
            color: var(--secondary);
            transition: color 0.3s;
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        .login-help {
            color: var(--primary);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .login-help:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        @media (max-width: 767.98px) {
            .login-image {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card login-card">
            <div class="row g-0">
                <div class="col-md-6">
                    <div class="login-header d-md-none">
                        <div class="login-brand">
                            <i class="fas fa-ambulance me-2"></i>SIM Ambulans
                        </div>
                        <p class="login-subtitle">Sistem Informasi Manajemen Ambulans</p>
                    </div>
                    
                    <div class="login-body">
                        <div class="text-center mb-4 d-none d-md-block">
                            <h2 class="fw-bold text-primary mb-2">Selamat Datang</h2>
                            <p class="text-muted">Silakan login untuk melanjutkan</p>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div><?= $error ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <form action="login.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                    <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                                </div>
                                <div class="invalid-feedback">
                                    Silakan masukkan email yang valid.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                </div>
                                <div class="invalid-feedback">
                                    Password tidak boleh kosong.
                                </div>
                                <div class="d-flex justify-content-end mt-1">
                                    <span class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye-slash me-1"></i>Tampilkan password
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">
                                        Ingat saya
                                    </label>
                                </div>
                                <a href="#" class="login-help">Lupa password?</a>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="login-footer">
                        <p class="mb-0 text-muted">&copy; <?= date('Y') ?> Sistem Manajemen Ambulans. All rights reserved.</p>
                    </div>
                </div>
                
                <div class="col-md-6 login-image">
                    <div class="text-center">
                        <div class="login-brand mb-4">
                            <i class="fas fa-ambulance me-2"></i>SIM Ambulans
                        </div>
                        <img src="assets/images/Ambulance-rafiki.svg" alt="Ambulance Illustration" class="img-fluid mb-4";>
                        <h4 class="fw-bold mb-3">Manajemen Peralatan Ambulans</h4>
                        <p class="mb-0">Sistem pencatatan dan pengelolaan peralatan medis untuk unit ambulans</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                this.querySelector('i').classList.toggle('fa-eye-slash');
                this.querySelector('i').classList.toggle('fa-eye');
            });
            
            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
</body>
</html>