<?php
session_start();

// Security check for user login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Session timeout management (30 minutes)
$session_timeout = 30 * 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Clear all session variables
    $_SESSION = array();
    
    // Delete session cookie if exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    
    // Start new session for logout message
    session_start();
    $_SESSION['logout_message'] = "Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.";
    
    header("Location: ../login.php");
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

include '../config/database.php';

// Get filter parameters
$currentMonth = date('m');
$currentYear = date('Y');
$filterMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;
$filterYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;
$filterUnit = isset($_GET['unit']) ? $_GET['unit'] : 'all';

// Query for equipment usage by ambulance unit with time filter
$sql = "SELECT u.nama as unit, a.nama as alat, SUM(td.jumlah) as total
        FROM transaksi t
        JOIN unit_ambulans u ON t.unit_ambulans_id = u.id
        JOIN transaksi_detail td ON t.id = td.transaksi_id
        JOIN alat_kesehatan a ON td.alat_kesehatan_id = a.id
        WHERE MONTH(t.tanggal) = :month AND YEAR(t.tanggal) = :year
        GROUP BY u.nama, a.nama";
$stmt = $pdo->prepare($sql);
$stmt->execute(['month' => $filterMonth, 'year' => $filterYear]);
$riwayat_bulanan = $stmt->fetchAll();

// Query for ambulance unit list
$sqlUnits = "SELECT DISTINCT u.id, u.nama FROM unit_ambulans u 
            JOIN transaksi t ON u.id = t.unit_ambulans_id
            ORDER BY u.nama";
$stmtUnits = $pdo->query($sqlUnits);
$unitList = $stmtUnits->fetchAll();

// Get available years for filter
$sqlYears = "SELECT DISTINCT YEAR(tanggal) as year FROM transaksi ORDER BY year DESC";
$stmtYears = $pdo->query($sqlYears);
$yearList = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

// Organize data for charts
$chartData = [];
$colors = [
    'rgba(45, 152, 218, 0.7)', 'rgba(52, 172, 224, 0.7)', 
    'rgba(26, 188, 156, 0.7)', 'rgba(22, 160, 133, 0.7)',
    'rgba(241, 196, 15, 0.7)', 'rgba(243, 156, 18, 0.7)',
    'rgba(231, 76, 60, 0.7)', 'rgba(192, 57, 43, 0.7)',
    'rgba(155, 89, 182, 0.7)', 'rgba(142, 68, 173, 0.7)'
];

foreach ($riwayat_bulanan as $row) {
    if (!isset($chartData[$row['unit']])) {
        $chartData[$row['unit']] = 0;
    }
    $chartData[$row['unit']] += $row['total'];
}

// Get total usage count
$totalUsage = array_sum(array_values($chartData));

// Get month name in Indonesian
$bulanIndonesia = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
$namaBulan = $bulanIndonesia[$filterMonth];
?>

<?php include '../includes/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="card-title fw-bold text-primary mb-4">Dashboard Penggunaan Alat Kesehatan</h2>
                    
                    <!-- Filter Controls -->
                    <form method="get" action="" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="month" class="form-label text-secondary fw-medium">Bulan</label>
                            <select name="month" id="month" class="form-select shadow-sm">
                                <?php foreach ($bulanIndonesia as $key => $bulan): ?>
                                    <option value="<?= $key ?>" <?= $filterMonth == $key ? 'selected' : '' ?>><?= $bulan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="form-label text-secondary fw-medium">Tahun</label>
                            <select name="year" id="year" class="form-select shadow-sm">
                                <?php foreach ($yearList as $year): ?>
                                    <option value="<?= $year ?>" <?= $filterYear == $year ? 'selected' : '' ?>><?= $year ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="unitFilter" class="form-label text-secondary fw-medium">Unit Ambulans</label>
                            <select name="unit" id="unitFilter" class="form-select shadow-sm">
                                <option value="all">Semua Unit</option>
                                <?php foreach ($unitList as $unit): ?>
                                    <option value="<?= htmlspecialchars($unit['nama']) ?>" <?= $filterUnit == $unit['nama'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($unit['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-filter"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Penggunaan</h6>
                            <h3 class="mb-0 fw-bold"><?= number_format($totalUsage) ?> item</h3>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="bi bi-box-seam fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>Periode: <?= $namaBulan ?> <?= $filterYear ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Jumlah Unit Aktif</h6>
                            <h3 class="mb-0 fw-bold"><?= count($chartData) ?> unit</h3>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="bi bi-truck fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>Unit dengan aktivitas penggunaan alat</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Rata-rata Per Unit</h6>
                            <h3 class="mb-0 fw-bold"><?= count($chartData) > 0 ? number_format($totalUsage / count($chartData), 1) : 0 ?></h3>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="bi bi-calculator fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small>Rata-rata penggunaan per unit ambulans</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-primary mb-0 fw-bold">Penggunaan Per Unit</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary chart-type" data-type="bar">
                            <i class="bi bi-bar-chart"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary chart-type" data-type="pie">
                            <i class="bi bi-pie-chart"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary chart-type" data-type="doughnut">
                            <i class="bi bi-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:350px;">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title text-primary mb-0 fw-bold">Detail Penggunaan Alat</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:350px;">
                        <canvas id="detailChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title text-primary mb-0 fw-bold">Riwayat Penggunaan</h5>
            <button class="btn btn-sm btn-outline-primary" id="exportBtn">
                <i class="bi bi-download"></i> Export Excel
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usageTable" class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Unit Ambulans</th>
                            <th class="border-0">Alat Kesehatan</th>
                            <th class="border-0 text-end">Total Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($riwayat_bulanan) > 0): ?>
                            <?php foreach ($riwayat_bulanan as $row): ?>
                                <tr data-unit="<?= htmlspecialchars($row['unit']) ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-truck text-primary"></i>
                                            </div>
                                            <?= htmlspecialchars($row['unit']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['alat']) ?></td>
                                    <td class="text-end fw-medium"><?= htmlspecialchars($row['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Tidak ada data penggunaan untuk periode yang dipilih</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Include SheetJS for Excel export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data for charts
    const chartData = <?= json_encode($chartData) ?>;
    const riwayatBulanan = <?= json_encode($riwayat_bulanan) ?>;
    const colorPalette = <?= json_encode($colors) ?>;
    const filterMonth = '<?= $namaBulan ?>';
    const filterYear = '<?= $filterYear ?>';
    
    // Chart instances
    let usageChart;
    let detailChart;
    
    // Create main chart
    function createMainChart(type = 'bar') {
        const ctx = document.getElementById('usageChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (usageChart) {
            usageChart.destroy();
        }
        
        const labels = Object.keys(chartData);
        const data = Object.values(chartData);
        const backgroundColors = colorPalette.slice(0, labels.length);
        
        usageChart = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Penggunaan',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: type === 'bar' ? 'top' : 'right',
                        display: true
                    },
                    title: {
                        display: true,
                        text: `Penggunaan Per Unit (${filterMonth} ${filterYear})`,
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y || context.parsed || 0;
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        display: type === 'bar',
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        display: type === 'bar',
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Create detail chart
    function createDetailChart(unitName) {
        const ctx = document.getElementById('detailChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (detailChart) {
            detailChart.destroy();
        }
        
        // Filter data based on selected unit
        let filteredData;
        if (unitName === 'all') {
            // Aggregate data per medical equipment for all units
            filteredData = riwayatBulanan.reduce((acc, item) => {
                if (!acc[item.alat]) {
                    acc[item.alat] = 0;
                }
                acc[item.alat] += parseInt(item.total);
                return acc;
            }, {});
        } else {
            // Filter only for selected unit
            filteredData = riwayatBulanan
                .filter(item => item.unit === unitName)
                .reduce((acc, item) => {
                    acc[item.alat] = parseInt(item.total);
                    return acc;
                }, {});
        }
        
        // Sort data by usage (descending)
        const sortedData = Object.entries(filteredData)
            .sort((a, b) => b[1] - a[1])
            .reduce((acc, [key, value]) => {
                acc[key] = value;
                return acc;
            }, {});
        
        const labels = Object.keys(sortedData);
        const data = Object.values(sortedData);
        const backgroundColors = colorPalette.slice(0, labels.length);
        
        detailChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penggunaan Alat',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'right',
                        display: true
                    },
                    title: {
                        display: true,
                        text: unitName === 'all' ? 'Detail Penggunaan Alat (Semua Unit)' : `Detail Penggunaan Alat (${unitName})`,
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    }
    
    // Filter table based on selected unit
    function filterTable(unitName) {
        const rows = document.querySelectorAll('#usageTable tbody tr');
        
        rows.forEach(row => {
            if (unitName === 'all' || row.getAttribute('data-unit') === unitName) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Event listener for unit filter change
    document.getElementById('unitFilter').addEventListener('change', function() {
        const selectedUnit = this.value;
        filterTable(selectedUnit);
        createDetailChart(selectedUnit);
    });
    
    // Event listeners for chart type buttons
    document.querySelectorAll('.chart-type').forEach(button => {
        button.addEventListener('click', function() {
            const chartType = this.getAttribute('data-type');
            document.querySelectorAll('.chart-type').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('active', 'btn-primary');
            createMainChart(chartType);
        });
    });
    
    // Export to Excel functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const table = document.getElementById('usageTable');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Penggunaan Alat"});
        XLSX.writeFile(wb, `Laporan_Penggunaan_Alat_${filterMonth}_${filterYear}.xlsx`);
    });
    
    // Initialize charts and table
    createMainChart('bar');
    createDetailChart('all');
    
    // Set first chart type button as active
    document.querySelector('.chart-type[data-type="bar"]').classList.add('active', 'btn-primary');
    document.querySelector('.chart-type[data-type="bar"]').classList.remove('btn-outline-primary');
});
</script>

<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --success-color: #4cc9f0;
    --info-color: #4895ef;
    --warning-color: #f72585;
    --danger-color: #e63946;
    --light-color: #f8f9fa;
    --dark-color: #212529;
}

body {
    background-color: #f5f7fa;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.card {
    border-radius: 0.75rem;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
}

.btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.text-primary {
    color: var(--primary-color) !important;
}

.bg-primary {
    background-color: var(--primary-color) !important;
}

.form-select, .form-control {
    border-radius: 0.5rem;
    padding: 0.6rem 1rem;
    border: 1px solid #e0e0e0;
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
}

.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table th {
    font-weight: 600;
    color: #495057;
}

.table td, .table th {
    padding: 1rem;
}

.avatar-sm {
    width: 36px;
    height: 36px;
}

.chart-type {
    padding: 0.25rem 0.5rem;
}

.chart-type i {
    font-size: 1rem;
}

@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    .table td, .table th {
        padding: 0.75rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>