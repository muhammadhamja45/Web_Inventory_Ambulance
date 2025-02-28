<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Redirect ke halaman login
    header("Location: ../login.php");
    exit;
}

// Cek session timeout (opsional, set timeout 30 menit)
$session_timeout = 30 * 60; // 30 menit dalam detik
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Jika session timeout, lakukan logout
    
    // Hapus semua variabel session
    $_SESSION = array();
    
    // Hapus cookie session jika ada
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Hancurkan session
    session_destroy();
    
    // Mulai session baru untuk pesan logout
    session_start();
    $_SESSION['logout_message'] = "Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.";
    
    header("Location: ../login.php");
    exit;
}

// Update waktu aktivitas terakhir
$_SESSION['last_activity'] = time();

include '../config/database.php';

// Query untuk mengambil total penggunaan barang per unit ambulans
$sql = "SELECT u.nama as unit, a.nama as alat, SUM(td.jumlah) as total
        FROM transaksi t
        JOIN unit_ambulans u ON t.unit_ambulans_id = u.id
        JOIN transaksi_detail td ON t.id = td.transaksi_id
        JOIN alat_kesehatan a ON td.alat_kesehatan_id = a.id
        GROUP BY u.nama, a.nama";
$stmt = $pdo->query($sql);
$riwayat_bulanan = $stmt->fetchAll();

// Query untuk mendapatkan daftar unit ambulans untuk filter
$sqlUnits = "SELECT DISTINCT u.nama FROM unit_ambulans u 
            JOIN transaksi t ON u.id = t.unit_ambulans_id
            ORDER BY u.nama";
$stmtUnits = $pdo->query($sqlUnits);
$unitList = $stmtUnits->fetchAll(PDO::FETCH_COLUMN);

// Mengorganisasi data untuk chart
$chartData = [];
$colors = [
    'rgba(75, 192, 192, 0.6)', 'rgba(54, 162, 235, 0.6)', 
    'rgba(255, 99, 132, 0.6)', 'rgba(255, 206, 86, 0.6)',
    'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)',
    'rgba(199, 199, 199, 0.6)', 'rgba(83, 102, 255, 0.6)'
];

foreach ($riwayat_bulanan as $row) {
    if (!isset($chartData[$row['unit']])) {
        $chartData[$row['unit']] = 0;
    }
    $chartData[$row['unit']] += $row['total'];
}
?>

<?php include '../includes/header.php'; ?>

<div class="container py-4">
    <h2 class="text-center mb-4">Riwayat Penggunaan Alat Kesehatan</h2>
    
    <!-- Filter dan Kontrol -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="form-group mb-md-0 mb-3">
                        <label for="unitFilter" class="form-label">Filter Unit Ambulans:</label>
                        <select id="unitFilter" class="form-select">
                            <option value="all">Semua Unit</option>
                            <?php foreach ($unitList as $unit): ?>
                                <option value="<?= htmlspecialchars($unit) ?>"><?= htmlspecialchars($unit) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-md-0 mb-3">
                        <label for="chartType" class="form-label">Jenis Grafik:</label>
                        <select id="chartType" class="form-select">
                            <option value="bar">Bar Chart</option>
                            <option value="pie">Pie Chart</option>
                            <option value="doughnut">Doughnut Chart</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title h5 mb-0">Grafik Penggunaan Per Unit</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:350px;">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grafik Detail Per Alat -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title h5 mb-0">Detail Penggunaan Alat</h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:350px;">
                        <canvas id="detailChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabel Data -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title h5 mb-0">Tabel Riwayat Penggunaan</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usageTable" class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Unit Ambulans</th>
                            <th>Alat Kesehatan</th>
                            <th>Total Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat_bulanan as $row): ?>
                            <tr data-unit="<?= htmlspecialchars($row['unit']) ?>">
                                <td><?= htmlspecialchars($row['unit']) ?></td>
                                <td><?= htmlspecialchars($row['alat']) ?></td>
                                <td><?= htmlspecialchars($row['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data untuk grafik
    const chartData = <?= json_encode($chartData) ?>;
    const riwayatBulanan = <?= json_encode($riwayat_bulanan) ?>;
    const colorPalette = <?= json_encode($colors) ?>;
    
    // Chart instances
    let usageChart;
    let detailChart;
    
    // Fungsi untuk membuat grafik utama
    function createMainChart(type) {
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
                    borderColor: backgroundColors.map(color => color.replace('0.6', '1')),
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
                        text: 'Total Penggunaan Per Unit Ambulans'
                    }
                },
                scales: {
                    y: {
                        display: type === 'bar',
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Fungsi untuk membuat grafik detail
    function createDetailChart(unitName) {
        const ctx = document.getElementById('detailChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (detailChart) {
            detailChart.destroy();
        }
        
        // Filter data berdasarkan unit yang dipilih
        let filteredData;
        if (unitName === 'all') {
            // Agregasi data per alat kesehatan untuk semua unit
            filteredData = riwayatBulanan.reduce((acc, item) => {
                if (!acc[item.alat]) {
                    acc[item.alat] = 0;
                }
                acc[item.alat] += parseInt(item.total);
                return acc;
            }, {});
        } else {
            // Filter hanya untuk unit yang dipilih
            filteredData = riwayatBulanan
                .filter(item => item.unit === unitName)
                .reduce((acc, item) => {
                    acc[item.alat] = parseInt(item.total);
                    return acc;
                }, {});
        }
        
        const labels = Object.keys(filteredData);
        const data = Object.values(filteredData);
        const backgroundColors = colorPalette.slice(0, labels.length);
        
        detailChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penggunaan Alat',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.6', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        display: true
                    },
                    title: {
                        display: true,
                        text: unitName === 'all' ? 'Detail Penggunaan Alat (Semua Unit)' : `Detail Penggunaan Alat (${unitName})`
                    }
                }
            }
        });
    }
    
    // Filter tabel berdasarkan unit yang dipilih
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
    
    // Event listener untuk perubahan filter unit
    document.getElementById('unitFilter').addEventListener('change', function() {
        const selectedUnit = this.value;
        filterTable(selectedUnit);
        createDetailChart(selectedUnit);
    });
    
    // Event listener untuk perubahan jenis grafik
    document.getElementById('chartType').addEventListener('change', function() {
        createMainChart(this.value);
    });
    
    // Inisialisasi grafik dan tabel
    createMainChart('bar');
    createDetailChart('all');
});
</script>

<style>
/* Styling tambahan */
body {
    background-color: #f8f9fa;
}
.card {
    border: none;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.card-header {
    padding: 0.75rem 1.25rem;
}
.table th {
    background-color: #1a237e;
    color: white;
    border-color: #0d1b42;
}
h2 {
    color: #1a237e;
    font-weight: 600;
}
.form-select:focus, .form-control:focus {
    border-color: #1a237e;
    box-shadow: 0 0 0 0.25rem rgba(26, 35, 126, 0.25);
}
@media (max-width: 767.98px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    .card-body {
        padding: 1rem;
    }
    h2 {
        font-size: 1.5rem;
    }
    .table {
        font-size: 0.9rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>  