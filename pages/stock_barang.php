<?php
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();
include '../config/database.php';

// Query to get all medical equipment with their stock
$sql = "SELECT a.*, s.jumlah FROM alat_kesehatan a LEFT JOIN stok s ON a.id = s.alat_kesehatan_id ORDER BY a.nama ASC";
$stmt = $pdo->query($sql);
$items = $stmt->fetchAll();

// Get count of low stock items (less than 5)
$lowStockCount = 0;
foreach ($items as $item) {
    if (($item['jumlah'] ?? 0) < 5) {
        $lowStockCount++;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container py-4">
    <!-- Dashboard Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="mb-1">Inventaris Alat Kesehatan</h2>
            <p class="text-muted">Manajemen stok dan inventaris peralatan medis</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="tambah_alat_kesehatan.php" class="btn btn-primary d-flex align-items-center">
                <i class="fas fa-plus-circle me-2"></i> Tambah Alat Baru
            </a>
            <a href="laporan_stok.php" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="fas fa-file-export me-2"></i> Ekspor Laporan
            </a>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-box text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Total Jenis Alat</h6>
                        <h3 class="mb-0"><?= count($items) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-cubes text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Total Stok</h6>
                        <h3 class="mb-0"><?= array_sum(array_column($items, 'jumlah')) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Stok Menipis</h6>
                        <h3 class="mb-0"><?= $lowStockCount ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="fas fa-sync text-info fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Update Terakhir</h6>
                        <p class="mb-0"><?= date('d M Y, H:i') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari nama atau deskripsi alat...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="stockFilter" class="form-select">
                        <option value="all">Semua Status Stok</option>
                        <option value="low">Stok Menipis (< 5)</option>
                        <option value="empty">Stok Kosong</option>
                        <option value="available">Stok Tersedia</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo me-2"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="inventoryTable" class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Nama Alat</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Foto</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): 
                            $stockLevel = $item['jumlah'] ?? 0;
                            $stockStatus = '';
                            $statusClass = '';
                            
                            if ($stockLevel == 0) {
                                $stockStatus = 'Kosong';
                                $statusClass = 'bg-danger';
                            } elseif ($stockLevel < 5) {
                                $stockStatus = 'Menipis';
                                $statusClass = 'bg-warning';
                            } else {
                                $stockStatus = 'Tersedia';
                                $statusClass = 'bg-success';
                            }
                        ?>
                            <tr data-stock="<?= $stockLevel ?>" data-status="<?= strtolower($stockStatus) ?>">
                                <td class="px-4 py-3">
                                    <div class="fw-semibold"><?= htmlspecialchars($item['nama']) ?></div>
                                    <div class="small text-muted">ID: <?= htmlspecialchars($item['id']) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php 
                                    // Limit description to 100 characters
                                    $description = htmlspecialchars($item['deskripsi']);
                                    echo (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description;
                                    ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($item['foto_url']): ?>
                                        <img src="<?= htmlspecialchars($item['foto_url']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" 
                                            class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;" 
                                            data-bs-toggle="modal" data-bs-target="#imageModal" 
                                            data-img-src="<?= htmlspecialchars($item['foto_url']) ?>" 
                                            data-img-title="<?= htmlspecialchars($item['nama']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                            style="width: 70px; height: 70px;">
                                            <i class="fas fa-image text-muted fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center fw-bold"><?= $stockLevel ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2"><?= $stockStatus ?></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group">
                                        <a href="tambah_stock.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i> Stok
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="edit_item.php?id=<?= $item['id'] ?>">
                                                <i class="fas fa-edit me-2"></i> Edit
                                            </a></li>
                                            <li><a class="dropdown-item" href="item_history.php?id=<?= $item['id'] ?>">
                                                <i class="fas fa-history me-2"></i> Riwayat
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="javascript:void(0);" 
                                                onclick="confirmDelete(<?= $item['id'] ?>, '<?= htmlspecialchars($item['nama']) ?>')">
                                                <i class="fas fa-trash-alt me-2"></i> Hapus
                                            </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($items) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="py-5">
                                        <i class="fas fa-box-open fs-1 text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">Belum ada alat kesehatan yang terdaftar</p>
                                        <a href="tambah_barang.php" class="btn btn-primary mt-3">Tambah Alat Baru</a>
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

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus <strong id="deleteItemName"></strong>?</p>
                <p class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image modal functionality
    document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#imageModal"]').forEach(img => {
        img.addEventListener('click', function() {
            document.getElementById('modalImage').src = this.getAttribute('data-img-src');
            document.getElementById('imageModalLabel').textContent = 'Foto: ' + this.getAttribute('data-img-title');
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const stockFilter = document.getElementById('stockFilter');
    const resetFilters = document.getElementById('resetFilters');
    const tableRows = document.querySelectorAll('#inventoryTable tbody tr');
    
    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const stockFilterValue = stockFilter.value;
        
        tableRows.forEach(row => {
            if (row.querySelector('td:first-child')) { // Skip "no data" row
                const itemName = row.querySelector('td:first-child').textContent.toLowerCase();
                const itemDesc = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const stockStatus = row.getAttribute('data-status');
                const stockLevel = parseInt(row.getAttribute('data-stock'));
                
                const matchesSearch = itemName.includes(searchValue) || itemDesc.includes(searchValue);
                let matchesStockFilter = true;
                
                if (stockFilterValue === 'low') {
                    matchesStockFilter = stockLevel < 5 && stockLevel > 0;
                } else if (stockFilterValue === 'empty') {
                    matchesStockFilter = stockLevel === 0;
                } else if (stockFilterValue === 'available') {
                    matchesStockFilter = stockLevel >= 5;
                }
                
                row.style.display = matchesSearch && matchesStockFilter ? '' : 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    stockFilter.addEventListener('change', filterTable);
    
    resetFilters.addEventListener('click', function() {
        searchInput.value = '';
        stockFilter.value = 'all';
        filterTable();
    });
    
    // Delete confirmation
    window.confirmDelete = function(id, name) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = '../actions/delete_item.php?id=' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };
});
</script>

<style>
/* Custom Styles */
body {
    background-color: #f8f9fa;
}

.container {
    max-width: 1320px;
}

h2 {
    color: #1a237e;
    font-weight: 600;
}

.table thead th {
    font-weight: 600;
    border-top: 0;
    border-bottom: 1px solid #e0e0e0;
}

.table td {
    vertical-align: middle;
}

.btn-primary {
    background-color: #1a237e;
    border-color: #1a237e;
}

.btn-primary:hover, .btn-primary:focus {
    background-color: #0d1b42;
    border-color: #0d1b42;
}

.btn-outline-secondary:hover, .btn-outline-secondary:focus {
    background-color: #f8f9fa;
    color: #1a237e;
    border-color: #1a237e;
}

.card {
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.img-thumbnail {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.img-thumbnail:hover {
    transform: scale(1.05);
}

/* For small screens, make the buttons stack */
@media (max-width: 576px) {
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .dropdown-toggle-split {
        display: none;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin-bottom: 0.25rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Add Font Awesome if not already included in header */
@media screen and (max-width: 767px) {
    .d-flex.gap-2 {
        flex-wrap: wrap;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>