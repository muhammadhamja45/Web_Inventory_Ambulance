<?php
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();
include '../config/database.php';

// Default filter untuk bulan dan tahun saat ini
$currentMonth = date('m');
$currentYear = date('Y');

$filterMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;
$filterYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;

// Query dengan filter bulan
$sql = "SELECT t.id, t.tanggal, u.nama as unit, a.nama as alat, td.jumlah 
        FROM transaksi t 
        JOIN transaksi_detail td ON t.id = td.transaksi_id 
        JOIN unit_ambulans u ON t.unit_ambulans_id = u.id 
        JOIN alat_kesehatan a ON td.alat_kesehatan_id = a.id 
        WHERE MONTH(t.tanggal) = :month AND YEAR(t.tanggal) = :year
        ORDER BY t.tanggal DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['month' => $filterMonth, 'year' => $filterYear]);
$history = $stmt->fetchAll();

// Query untuk mendapatkan statistik
$sqlStats = "SELECT 
    COUNT(DISTINCT t.id) as total_transaksi,
    COUNT(DISTINCT t.unit_ambulans_id) as total_unit,
    SUM(td.jumlah) as total_item
    FROM transaksi t 
    JOIN transaksi_detail td ON t.id = td.transaksi_id 
    WHERE MONTH(t.tanggal) = :month AND YEAR(t.tanggal) = :year";
$stmtStats = $pdo->prepare($sqlStats);
$stmtStats->execute(['month' => $filterMonth, 'year' => $filterYear]);
$stats = $stmtStats->fetch();

// Query untuk mendapatkan item terbanyak
$sqlTopItem = "SELECT a.nama as alat, SUM(td.jumlah) as total
    FROM transaksi t 
    JOIN transaksi_detail td ON t.id = td.transaksi_id 
    JOIN alat_kesehatan a ON td.alat_kesehatan_id = a.id
    WHERE MONTH(t.tanggal) = :month AND YEAR(t.tanggal) = :year
    GROUP BY a.id
    ORDER BY total DESC
    LIMIT 1";
$stmtTopItem = $pdo->prepare($sqlTopItem);
$stmtTopItem->execute(['month' => $filterMonth, 'year' => $filterYear]);
$topItem = $stmtTopItem->fetch();

// Query untuk mendapatkan jumlah per unit ambulans
$sqlUnitStats = "SELECT 
    u.nama as unit_nama,
    COUNT(DISTINCT t.id) as total_transaksi,
    SUM(td.jumlah) as total_item
    FROM transaksi t 
    JOIN transaksi_detail td ON t.id = td.transaksi_id 
    JOIN unit_ambulans u ON t.unit_ambulans_id = u.id
    WHERE MONTH(t.tanggal) = :month AND YEAR(t.tanggal) = :year
    GROUP BY u.id
    ORDER BY total_item DESC";
$stmtUnitStats = $pdo->prepare($sqlUnitStats);
$stmtUnitStats->execute(['month' => $filterMonth, 'year' => $filterYear]);
$unitStats = $stmtUnitStats->fetchAll();

// Nama bulan dalam bahasa Indonesia
$monthNames = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-history me-2"></i>Riwayat Transaksi
            </h2>
            <p class="text-muted">Pencatatan penggunaan alat kesehatan pada unit ambulans</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="../pages/record_usage.php" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Catat Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Filter & Stats Section -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="fas fa-filter me-2"></i>Filter Transaksi
                    </h5>
                    <form id="filterForm" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label for="month" class="form-label text-primary">Bulan</label>
                            <select class="form-select border-primary" id="month" name="month">
                                <?php foreach ($monthNames as $num => $name): ?>
                                    <option value="<?= $num ?>" <?= $filterMonth == $num ? 'selected' : '' ?>>
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="year" class="form-label text-primary">Tahun</label>
                            <select class="form-select border-primary" id="year" name="year">
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                    <option value="<?= $y ?>" <?= $filterYear == $y ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary text-white mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-exchange-alt fa-2x"></i>
                            </div>
                            <h3 class="display-5 fw-bold text-primary"><?= $stats['total_transaksi'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Total Transaksi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-info text-white mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-ambulance fa-2x"></i>
                            </div>
                            <h3 class="display-5 fw-bold text-info"><?= $stats['total_unit'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Unit Ambulans</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-success text-white mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-medkit fa-2x"></i>
                            </div>
                            <h3 class="display-5 fw-bold text-success"><?= $stats['total_item'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Total Item</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Ambulans Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-ambulance me-2"></i>
                        Penggunaan Per Unit Ambulans - <?= $monthNames[$filterMonth] ?> <?= $filterYear ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (count($unitStats) > 0): ?>
                        <div class="row">
                            <?php foreach ($unitStats as $unit): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rounded-circle bg-primary text-white p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-ambulance fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0"><?= htmlspecialchars($unit['unit_nama']) ?></h5>
                                                    <small class="text-muted"><?= $unit['total_transaksi'] ?> transaksi</small>
                                                </div>
                                            </div>
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: <?= min(100, ($unit['total_item'] / max(1, $stats['total_item'])) * 100) ?>%;" 
                                                     aria-valuenow="<?= $unit['total_item'] ?>" aria-valuemin="0" aria-valuemax="<?= $stats['total_item'] ?>"></div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold text-primary"><?= $unit['total_item'] ?> item</span>
                                                <span class="text-muted"><?= round(($unit['total_item'] / max(1, $stats['total_item'])) * 100) ?>% dari total</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada data penggunaan unit ambulans pada bulan <?= $monthNames[$filterMonth] ?> <?= $filterYear ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Used Item -->
    <?php if ($topItem && $topItem['total'] > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-blue">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-white text-primary p-3 me-3">
                        <i class="fas fa-award fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">Item Terbanyak Digunakan</h6>
                        <h4 class="text-white mb-0"><?= htmlspecialchars($topItem['alat']) ?> (<?= $topItem['total'] ?> unit)</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Transaction History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>
                        Transaksi Bulan <?= $monthNames[$filterMonth] ?> <?= $filterYear ?>
                    </h5>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-0 text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control bg-transparent border-0 text-white" 
                               placeholder="Cari transaksi...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="transactionTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 text-primary">Tanggal</th>
                                    <th class="text-primary">Unit Ambulans</th>
                                    <th class="text-primary">Alat Kesehatan</th>
                                    <th class="text-primary text-center">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="historyTable">
                                <?php if (count($history) > 0): ?>
                                    <?php foreach ($history as $row): ?>
                                        <tr class="transaction-row">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="date-icon bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <?= date('d', strtotime($row['tanggal'])) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                                                        <small class="text-muted"><?= date('H:i', strtotime($row['tanggal'])) ?> WIB</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-circle bg-info bg-opacity-10 text-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-ambulance"></i>
                                                    </div>
                                                    <?= htmlspecialchars($row['unit']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-circle bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-medkit"></i>
                                                    </div>
                                                    <?= htmlspecialchars($row['alat']) ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                                    <?= htmlspecialchars($row['jumlah']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="empty-state">
                                                <img src="../assets/images/empty-data.svg" alt="No Data" style="width: 120px; height: 120px;" class="mb-3">
                                                <h5 class="text-muted">Belum ada transaksi</h5>
                                                <p class="text-muted">Tidak ada data transaksi untuk bulan <?= $monthNames[$filterMonth] ?> <?= $filterYear ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0 text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                Menampilkan <?= count($history) ?> transaksi
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button class="btn btn-outline-primary" id="exportBtn">
                                <i class="fas fa-file-export me-2"></i>Export Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary: #0d6efd;
    --primary-light: #e7f1ff;
    --primary-dark: #0a58ca;
    --info: #0dcaf0;
    --success: #198754;
}

.bg-gradient-blue {
    background: linear-gradient(45deg, var(--primary-dark), var(--primary));
    color: white;
}

.transaction-row {
    transition: all 0.2s ease;
}

.transaction-row:hover {
    background-color: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

.date-icon {
    font-weight: bold;
    font-size: 0.9rem;
}

.icon-circle {
    transition: all 0.3s ease;
}

.transaction-row:hover .icon-circle {
    transform: scale(1.1);
}

.badge {
    transition: all 0.3s ease;
}

.transaction-row:hover .badge {
    transform: scale(1.1);
    box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.empty-state {
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

#searchInput {
    border-radius: 20px;
}

#searchInput:focus {
    box-shadow: none;
}

.btn-outline-primary {
    color: var(--primary);
    border-color: var(--primary);
}

.btn-outline-primary:hover {
    background-color: var(--primary);
    color: white;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.card-footer {
    border-top: none;
}

.table > :not(:first-child) {
    border-top: none;
}

.table th {
    font-weight: 600;
}

.progress {
    border-radius: 10px;
    background-color: var(--primary-light);
}

.progress-bar {
    border-radius: 10px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.transaction-row {
    animation: fadeIn 0.5s ease forwards;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#historyTable tr.transaction-row');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Auto-submit filter form when selections change
    const monthSelect = document.getElementById('month');
    const yearSelect = document.getElementById('year');
    
    monthSelect.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    yearSelect.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Export functionality (placeholder)
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            alert('Fitur export akan segera tersedia!');
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>