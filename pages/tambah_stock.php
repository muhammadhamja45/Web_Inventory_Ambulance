<?php
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();
include '../config/database.php';

// Ambil data alat kesehatan dari database
$sql_alat = "SELECT id, nama FROM alat_kesehatan";
$stmt_alat = $pdo->query($sql_alat);
$alat_kesehatan = $stmt_alat->fetchAll();

// Ambil data stok saat ini untuk ditampilkan
$sql_stok = "SELECT ak.id, ak.nama, COALESCE(s.jumlah, 0) as stok 
             FROM alat_kesehatan ak
             LEFT JOIN stok s ON ak.id = s.alat_kesehatan_id
             ORDER BY ak.nama";
$stmt_stok = $pdo->query($sql_stok);
$stok_data = $stmt_stok->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <!-- Form Tambah Stok -->
        <div class="col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 py-2">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Stok
                    </h3>
                    <span class="badge bg-white text-primary">Form Input</span>
                </div>
                <div class="card-body p-4" style="border-top: 4px solid #0d6efd;">
                    <div class="alert alert-info bg-light border-start border-5 border-primary">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading text-primary">Petunjuk Pengisian</h5>
                                <p class="mb-0">Silakan pilih alat kesehatan dan masukkan jumlah stok yang akan ditambahkan.</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="../actions/update_stock.php" method="POST" id="stockForm">
                        <div class="mb-4">
                            <div class="form-floating">
                                <select class="form-select border-primary" id="alat_kesehatan_id" name="alat_kesehatan_id" required>
                                    <option value="">Pilih Alat Kesehatan</option>
                                    <?php foreach ($alat_kesehatan as $alat): ?>
                                        <option value="<?= htmlspecialchars($alat['id']) ?>"><?= htmlspecialchars($alat['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="alat_kesehatan_id" class="text-primary">
                                    <i class="fas fa-medkit me-1"></i> Nama Alat Kesehatan
                                </label>
                            </div>
                            <div id="stockInfo" class="form-text text-primary mt-2 d-none">
                                <i class="fas fa-info-circle me-1"></i> Stok saat ini: <span id="currentStock">0</span> unit
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="number" class="form-control border-primary" id="jumlah" name="jumlah" min="1" required>
                                <label for="jumlah" class="text-primary">
                                    <i class="fas fa-cubes me-1"></i> Jumlah
                                </label>
                            </div>
                            <div class="form-text text-primary">
                                <i class="fas fa-lightbulb me-1"></i> Masukkan jumlah stok yang akan ditambahkan
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="btn btn-outline-primary btn-lg" href="../pages/inventory.php">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Tambah Stok
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3" style="background-color: #e7f1ff;">
                    <div class="text-primary">
                        <i class="fas fa-check-circle me-1"></i> Pastikan data yang dimasukkan sudah benar
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Stok Saat Ini -->
        <div class="col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 py-2">
                        <i class="fas fa-clipboard-list me-2"></i>Stok Saat Ini
                    </h3>
                    <span class="badge bg-white text-primary">Informasi</span>
                </div>
                <div class="card-body p-0" style="border-top: 4px solid #0d6efd; max-height: 500px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-primary py-3">Nama Alat</th>
                                    <th class="text-primary py-3 text-center">Stok</th>
                                    <th class="text-primary py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_data as $item): ?>
                                    <tr data-id="<?= htmlspecialchars($item['id']) ?>" data-stock="<?= htmlspecialchars($item['stok']) ?>">
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-medkit text-primary me-2"></i>
                                                <?= htmlspecialchars($item['nama']) ?>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="fw-bold"><?= htmlspecialchars($item['stok']) ?></span> unit
                                        </td>
                                        <td class="py-3 text-center">
                                            <?php if ($item['stok'] <= 0): ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php elseif ($item['stok'] <= 5): ?>
                                                <span class="badge bg-warning text-dark">Menipis</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center py-3" style="background-color: #e7f1ff;">
                    <div class="text-primary">
                        <i class="fas fa-info-circle me-1"></i> Klik pada baris untuk memilih alat kesehatan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* General Styling */
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 50px;
    padding: 0.6rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    border-color: #0d6efd;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7, #0a58ca);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(13, 110, 253, 0.35);
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
    background: rgba(13, 110, 253, 0.05);
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
}

/* Card Styling */
.card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}

.card-header {
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    padding: 1rem 1.5rem;
    border-bottom: none;
}

.card-title {
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Form Styling */
.form-floating label {
    font-weight: 500;
}

.form-floating>.form-control:focus~label,
.form-floating>.form-control:not(:placeholder-shown)~label,
.form-floating>.form-select~label {
    color: #0d6efd;
    opacity: 0.8;
    font-weight: 600;
}

.form-control, .form-select {
    border-width: 2px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.form-control:hover, .form-select:hover {
    border-color: #0d6efd;
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    font-weight: 600;
    border-top: none;
    border-bottom: 2px solid #e7f1ff;
}

.table tbody tr {
    cursor: pointer;
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.table tbody tr.selected {
    background-color: rgba(13, 110, 253, 0.1);
}

/* Alert Styling */
.alert {
    border-radius: 10px;
    padding: 1rem;
}

.alert-info {
    background-color: rgba(13, 110, 253, 0.05) !important;
}

/* Badge Styling */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 500;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.5s ease forwards;
}

.card:nth-child(2) {
    animation-delay: 0.2s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store stock data
    const stockData = {};
    document.querySelectorAll('tbody tr').forEach(row => {
        const id = row.getAttribute('data-id');
        const stock = parseInt(row.getAttribute('data-stock'));
        stockData[id] = stock;
    });
    
    // Form validation
    const form = document.getElementById('stockForm');
    const alatSelect = document.getElementById('alat_kesehatan_id');
    const jumlahInput = document.getElementById('jumlah');
    const stockInfo = document.getElementById('stockInfo');
    const currentStockSpan = document.getElementById('currentStock');
    
    // Update stock info when alat is selected
    alatSelect.addEventListener('change', function() {
        const selectedId = this.value;
        if (selectedId && stockData[selectedId] !== undefined) {
            currentStockSpan.textContent = stockData[selectedId];
            stockInfo.classList.remove('d-none');
        } else {
            stockInfo.classList.add('d-none');
        }
    });
    
    // Click on table row to select item
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const stock = this.getAttribute('data-stock');
            
            // Remove selected class from all rows
            document.querySelectorAll('tbody tr').forEach(r => {
                r.classList.remove('selected');
            });
            
            // Add selected class to clicked row
            this.classList.add('selected');
            
            // Set the select value
            alatSelect.value = id;
            
            // Trigger change event to update stock info
            const event = new Event('change');
            alatSelect.dispatchEvent(event);
            
            // Focus on jumlah input
            jumlahInput.focus();
            
            // If using Select2, update it
            if (typeof $.fn.select2 !== 'undefined') {
                $(alatSelect).trigger('change.select2');
            }
        });
    });
    
    // Form validation
    form.addEventListener('submit', function(event) {
        const alatId = alatSelect.value;
        const jumlah = jumlahInput.value;
        
        if (!alatId) {
            event.preventDefault();
            showToast('Silakan pilih alat kesehatan terlebih dahulu', 'warning');
            return false;
        }
        
        if (!jumlah || jumlah < 1) {
            event.preventDefault();
            showToast('Jumlah harus diisi dengan angka positif', 'warning');
            return false;
        }
    });
    
    // Toast notification function (if you have Bootstrap 5)
    function showToast(message, type = 'info') {
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Toast !== 'undefined') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            toastContainer.appendChild(toastEl);
            
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            
            toast.show();
            
            // Remove toast after it's hidden
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        } else {
            // Fallback to alert if Bootstrap Toast is not available
            alert(message);
        }
    }
    
    // Select2 initialization (if you have Select2 library)
    if (typeof $.fn.select2 !== 'undefined') {
        $('#alat_kesehatan_id').select2({
            placeholder: 'Pilih Alat Kesehatan',
            width: '100%',
            theme: 'bootstrap-5'
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>