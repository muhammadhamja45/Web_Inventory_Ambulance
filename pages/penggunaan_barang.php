<?php
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();

include '../config/database.php';

$sql = "SELECT * FROM unit_ambulans";
$stmt = $pdo->query($sql);
$units = $stmt->fetchAll();

// Mengambil data alat kesehatan beserta stoknya
$sql = "SELECT ak.id, ak.nama, COALESCE(s.jumlah, 0) as stok 
        FROM alat_kesehatan ak
        LEFT JOIN stok s ON ak.id = s.alat_kesehatan_id
        ORDER BY ak.nama";
$stmt = $pdo->query($sql);
$items = $stmt->fetchAll();

// Menyimpan data stok dalam format JSON untuk digunakan di JavaScript
$stockData = [];
foreach ($items as $item) {
    $stockData[$item['id']] = (int)$item['stok'];
}
$stockDataJson = json_encode($stockData);
?>

<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 py-2">
                        <i class="fas fa-clipboard-list me-2"></i>Pencatatan Penggunaan Barang
                    </h3>
                    <span class="badge bg-light text-primary">Form Penggunaan</span>
                </div>
                
                <div class="card-body p-4" style="border-top: 4px solid #0d6efd;">
                    <div class="alert alert-info bg-light text-primary border-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Silakan isi form berikut untuk mencatat penggunaan alat kesehatan pada unit ambulans. 
                        <strong>Catatan:</strong> Hanya alat dengan stok tersedia yang dapat dipilih.
                    </div>
                    
                    <form action="../actions/record_usage.php" method="POST" id="usageForm">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select border-primary" id="unitAmbulans" name="unitAmbulans" required>
                                        <option value="" selected disabled>Pilih unit ambulans</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?= htmlspecialchars($unit['id']) ?>"><?= htmlspecialchars($unit['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="unitAmbulans" class="text-primary">Unit Ambulans</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control border-primary" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                                    <label for="tanggal" class="text-primary">Tanggal Penggunaan</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-light text-primary">
                                <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Daftar Alat Kesehatan</h5>
                            </div>
                            <div class="card-body p-3">
                                <div id="itemContainer">
                                    <div class="item-row mb-3 p-3 border rounded border-primary bg-light">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-2">
                                                    <select class="form-select border-primary item-select" name="alatKesehatan[]" required data-row="0">
                                                        <option value="" selected disabled>Pilih alat kesehatan</option>
                                                        <?php foreach ($items as $item): ?>
                                                            <option value="<?= htmlspecialchars($item['id']) ?>" 
                                                                <?= $item['stok'] <= 0 ? 'disabled' : '' ?> 
                                                                data-stock="<?= htmlspecialchars($item['stok']) ?>">
                                                                <?= htmlspecialchars($item['nama']) ?> 
                                                                (Stok: <?= htmlspecialchars($item['stok']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label class="text-primary">Alat Kesehatan</label>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-floating mb-2">
                                                    <input type="number" class="form-control border-primary jumlah-input" name="jumlah[]" min="1" required data-row="0">
                                                    <label class="text-primary">Jumlah</label>
                                                    <div class="stock-info text-primary mt-1" style="font-size: 0.8rem;"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center justify-content-center">
                                                <button type="button" class="btn btn-outline-danger remove-item" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-outline-primary" id="addItem">
                                        <i class="fas fa-plus-circle me-2"></i>Tambah Item
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="../pages/usage_history.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Penggunaan
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center py-3" style="background-color: #e7f1ff; color: #0d6efd;">
                    <div>Pastikan data yang dimasukkan sudah benar sebelum menyimpan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.item-row {
    transition: all 0.3s ease;
}

.item-row:hover {
    background-color: #f8f9ff !important;
}

.remove-item {
    transition: all 0.2s ease;
}

.remove-item:hover {
    transform: scale(1.1);
}

.alert-info {
    background-color: #f0f7ff !important;
    border-left: 4px solid #0d6efd !important;
}

.form-floating>.form-control:focus~label,
.form-floating>.form-control:not(:placeholder-shown)~label,
.form-floating>.form-select~label {
    color: #0d6efd;
    opacity: 0.8;
}

/* Styling untuk opsi yang dinonaktifkan */
select option:disabled {
    color: #dc3545;
    background-color: #f8d7da;
    font-style: italic;
}

.stock-warning {
    color: #dc3545;
    font-weight: bold;
}

.stock-ok {
    color: #198754;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemContainer = document.getElementById('itemContainer');
    const addItemBtn = document.getElementById('addItem');
    
    // Data stok dari PHP
    const stockData = <?= $stockDataJson ?>;
    let rowCounter = 1; // Mulai dari 1 karena row 0 sudah ada di HTML
    
    // Fungsi untuk memperbarui informasi stok
    function updateStockInfo(selectElement, inputElement, stockInfoElement) {
        const selectedItemId = selectElement.value;
        if (!selectedItemId) {
            stockInfoElement.textContent = '';
            return;
        }
        
        const availableStock = stockData[selectedItemId];
        const requestedAmount = parseInt(inputElement.value) || 0;
        
        // Perbarui atribut max pada input jumlah
        inputElement.setAttribute('max', availableStock);
        
        // Tampilkan informasi stok
        if (requestedAmount > availableStock) {
            stockInfoElement.textContent = `Stok tidak cukup! Tersedia: ${availableStock}`;
            stockInfoElement.className = 'stock-info stock-warning mt-1';
            inputElement.classList.add('is-invalid');
        } else if (requestedAmount === availableStock) {
            stockInfoElement.textContent = `Menggunakan semua stok yang tersedia (${availableStock})`;
            stockInfoElement.className = 'stock-info text-warning mt-1';
            inputElement.classList.remove('is-invalid');
        } else {
            stockInfoElement.textContent = `Stok tersedia: ${availableStock}`;
            stockInfoElement.className = 'stock-info stock-ok mt-1';
            inputElement.classList.remove('is-invalid');
        }
    }
    
    // Inisialisasi event listener untuk baris pertama
    const firstSelect = document.querySelector('.item-select[data-row="0"]');
    const firstInput = document.querySelector('.jumlah-input[data-row="0"]');
    const firstStockInfo = firstInput.parentElement.querySelector('.stock-info');
    
    firstSelect.addEventListener('change', function() {
        updateStockInfo(firstSelect, firstInput, firstStockInfo);
    });
    
    firstInput.addEventListener('input', function() {
        updateStockInfo(firstSelect, firstInput, firstStockInfo);
    });
    
    // Add new item row
    addItemBtn.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.classList.add('item-row', 'mb-3', 'p-3', 'border', 'rounded', 'border-primary', 'bg-light');
        
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-2">
                        <select class="form-select border-primary item-select" name="alatKesehatan[]" required data-row="${rowCounter}">
                            <option value="" selected disabled>Pilih alat kesehatan</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= htmlspecialchars($item['id']) ?>" 
                                    <?= $item['stok'] <= 0 ? 'disabled' : '' ?> 
                                    data-stock="<?= htmlspecialchars($item['stok']) ?>">
                                    <?= htmlspecialchars($item['nama']) ?> 
                                    (Stok: <?= htmlspecialchars($item['stok']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="text-primary">Alat Kesehatan</label>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control border-primary jumlah-input" name="jumlah[]" min="1" required data-row="${rowCounter}">
                        <label class="text-primary">Jumlah</label>
                        <div class="stock-info text-primary mt-1" style="font-size: 0.8rem;"></div>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-outline-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        itemContainer.appendChild(newItem);
        
        // Enable all remove buttons if we have more than one item
        if (itemContainer.querySelectorAll('.item-row').length > 1) {
            const removeButtons = itemContainer.querySelectorAll('.remove-item');
            removeButtons.forEach(button => {
                button.disabled = false;
            });
        }
        
        // Add event listener to the new remove button
        const removeButton = newItem.querySelector('.remove-item');
        removeButton.addEventListener('click', function() {
            newItem.remove();
            
            // If only one item remains, disable its remove button
            if (itemContainer.querySelectorAll('.item-row').length === 1) {
                const lastRemoveButton = itemContainer.querySelector('.remove-item');
                lastRemoveButton.disabled = true;
            }
        });
        
        // Add event listeners for stock validation
        const newSelect = newItem.querySelector('.item-select');
        const newInput = newItem.querySelector('.jumlah-input');
        const newStockInfo = newInput.parentElement.querySelector('.stock-info');
        
        newSelect.addEventListener('change', function() {
            updateStockInfo(newSelect, newInput, newStockInfo);
        });
        
        newInput.addEventListener('input', function() {
            updateStockInfo(newSelect, newInput, newStockInfo);
        });
        
        // Initialize Select2 for the new select if available
        if (typeof $.fn.select2 !== 'undefined') {
            $(newItem).find('.item-select').select2({
                placeholder: 'Pilih alat kesehatan',
                width: '100%'
            });
        }
        
        rowCounter++;
    });
    
    // Form validation
    const form = document.getElementById('usageForm');
    form.addEventListener('submit', function(event) {
        const unitAmbulans = document.getElementById('unitAmbulans').value;
        const tanggal = document.getElementById('tanggal').value;
        const alatSelects = document.querySelectorAll('select[name="alatKesehatan[]"]');
        const jumlahInputs = document.querySelectorAll('input[name="jumlah[]"]');
        
        let isValid = true;
        
        if (!unitAmbulans) {
            alert('Silakan pilih unit ambulans');
            isValid = false;
        }
        
        if (!tanggal) {
            alert('Silakan pilih tanggal penggunaan');
            isValid = false;
        }
        
        // Check for duplicate items and stock availability
        const selectedItems = [];
        alatSelects.forEach((select, index) => {
            if (!select.value) {
                alert('Silakan pilih semua alat kesehatan');
                isValid = false;
                return;
            }
            
            if (selectedItems.includes(select.value)) {
                alert('Terdapat alat kesehatan yang duplikat. Silakan gabungkan jumlahnya.');
                isValid = false;
                return;
            }
            
            selectedItems.push(select.value);
            
            const requestedAmount = parseInt(jumlahInputs[index].value) || 0;
            if (!requestedAmount || requestedAmount < 1) {
                alert('Jumlah harus diisi dengan angka positif');
                isValid = false;
                return;
            }
            
            const availableStock = stockData[select.value];
            if (requestedAmount > availableStock) {
                alert(`Stok tidak cukup untuk ${select.options[select.selectedIndex].text}. Tersedia: ${availableStock}, Diminta: ${requestedAmount}`);
                isValid = false;
                return;
            }
        });
        
        if (!isValid) {
            event.preventDefault();
        }
    });
    
    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('#unitAmbulans').select2({
            placeholder: 'Pilih unit ambulans',
            width: '100%'
        });
        
        $('.item-select').select2({
            placeholder: 'Pilih alat kesehatan',
            width: '100%'
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>