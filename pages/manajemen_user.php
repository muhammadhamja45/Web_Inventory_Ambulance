<?php
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();

include '../config/database.php';

$sql = "SELECT * FROM pengguna";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="text-primary"><i class="fas fa-users me-2"></i>Manajemen Pengguna</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus me-2"></i>Tambah Pengguna Baru
                </button>
            </div>
            <hr class="bg-primary" style="height: 2px; opacity: 0.5;">
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 bg-primary bg-opacity-10 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white p-3 me-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-primary">Total Pengguna</h5>
                        <h3 class="mb-0"><?= count($users) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-info bg-opacity-10 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info text-white p-3 me-3">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-info">Admin</h5>
                        <h3 class="mb-0"><?= count(array_filter($users, function($user) { return isset($user['role']) && $user['role'] === 'admin'; })) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-success bg-opacity-10 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success text-white p-3 me-3">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-success">Aktif</h5>
                        <h3 class="mb-0"><?= count(array_filter($users, function($user) { return !isset($user['status']) || $user['status'] === 'active'; })) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-primary">#</th>
                                    <th scope="col" class="text-primary">Nama</th>
                                    <th scope="col" class="text-primary">Email</th>
                                    <th scope="col" class="text-primary">Status</th>
                                    <th scope="col" class="text-primary">Tanggal Dibuat</th>
                                    <th scope="col" class="text-primary text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($users) > 0): ?>
                                    <?php foreach ($users as $index => $user): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                                                    </div>
                                                    <?= htmlspecialchars($user['nama']) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <?php if (!isset($user['status']) || $user['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-id="<?= $user['id'] ?>" data-nama="<?= htmlspecialchars($user['nama']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>Belum ada data pengguna
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus me-2"></i>Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../actions/add_user.php" method="POST" id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="text" class="form-control border-primary" id="nama" name="nama" required>
                            <label for="nama" class="text-primary"><i class="fas fa-user me-2"></i>Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="email" class="form-control border-primary" id="email" name="email" required>
                            <label for="email" class="text-primary"><i class="fas fa-envelope me-2"></i>Email</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating">
                            <input type="password" class="form-control border-primary" id="password" name="password" required>
                            <label for="password" class="text-primary"><i class="fas fa-lock me-2"></i>Password</label>
                        </div>
                        <div class="form-text text-primary">
                            Password harus minimal 8 karakter dengan kombinasi huruf dan angka
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating">
                            <select class="form-select border-primary" id="role" name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <label for="role" class="text-primary"><i class="fas fa-user-tag me-2"></i>Role</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengguna <strong id="delete_user_name"></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <a href="#" id="confirmDelete" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.form-floating>.form-control:focus~label,
.form-floating>.form-control:not(:placeholder-shown)~label,
.form-floating>.form-select~label {
    color: #0d6efd;
    opacity: 0.8;
}

.modal-content {
    border: none;
    border-radius: 0.5rem;
    overflow: hidden;
}

.modal-header {
    border-bottom: none;
}

.modal-footer {
    border-top: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password validation
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
                event.preventDefault();
                alert('Password harus minimal 8 karakter dengan kombinasi huruf dan angka');
            }
        });
    }
    
    // Delete user modal
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            const userName = button.getAttribute('data-nama');
            
            document.getElementById('delete_user_name').textContent = userName;
            document.getElementById('confirmDelete').href = '../actions/delete_user.php?id=' + userId;
        });
    }
    
    // Initialize DataTable if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#usersTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>