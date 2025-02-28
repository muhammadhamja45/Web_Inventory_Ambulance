<?php 
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();

include '../includes/header.php'; ?>

<div class="page-wrapper">
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-9 col-lg-10">
                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">Tambah Alat Kesehatan</h2>
                        <p class="text-muted">Tambahkan data alat kesehatan baru ke dalam sistem inventaris</p>
                    </div>
                    <a href="../pages/inventory.php" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Inventaris
                    </a>
                </div>
                
                <!-- Main Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Progress Indicator -->
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;" id="formProgress"></div>
                    </div>
                    
                    <!-- Card Header -->
                    <div class="card-header bg-white p-0">
                        <div class="row g-0">
                            <div class="col-md-4 bg-primary text-white p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <h3 class="fw-bold mb-2">Tambah Alat</h3>
                                    <p class="opacity-75 mb-4">Lengkapi informasi untuk menambahkan alat kesehatan baru</p>
                                </div>
                                
                                <div class="steps-container">
                                    <div class="step active" data-step="1">
                                        <div class="step-icon">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6 class="mb-0 fw-bold">Informasi Dasar</h6>
                                            <small class="opacity-75">Nama dan deskripsi</small>
                                        </div>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="step-icon">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6 class="mb-0 fw-bold">Media</h6>
                                            <small class="opacity-75">Foto alat kesehatan</small>
                                        </div>
                                    </div>
                                    <div class="step" data-step="3">
                                        <div class="step-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6 class="mb-0 fw-bold">Konfirmasi</h6>
                                            <small class="opacity-75">Tinjau dan simpan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8 p-4 d-flex align-items-center">
                                <div class="w-100">
                                    <div class="alert alert-primary border-0 d-flex align-items-center" role="alert">
                                        <div class="alert-icon me-3">
                                            <i class="fas fa-info-circle fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="alert-heading mb-1 fw-bold">Panduan Pengisian</h6>
                                            <p class="mb-0 small">Pastikan data yang dimasukkan sudah benar dan lengkap. Data yang telah disimpan dapat diubah melalui menu edit.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body p-4 p-lg-5">
                        <form action="../actions/add_item.php" method="POST" enctype="multipart/form-data" id="addItemForm">
                            <!-- Multi-step form content -->
                            <div class="form-steps">
                                <!-- Step 1: Basic Information -->
                                <div class="form-step active" data-step="1">
                                    <div class="mb-4 pb-2">
                                        <h4 class="fw-bold text-primary mb-3">Informasi Dasar</h4>
                                        <p class="text-muted">Masukkan informasi dasar tentang alat kesehatan</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="nama" class="form-label fw-semibold">Nama Alat Kesehatan <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg mb-2">
                                            <span class="input-group-text bg-white text-primary border-end-0">
                                                <i class="fas fa-medkit"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-lg border-start-0" 
                                                id="nama" name="nama" 
                                                placeholder="Masukkan nama alat kesehatan" required>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1 text-primary"></i>
                                            Gunakan nama yang spesifik dan mudah diidentifikasi
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi Alat</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-white text-primary border-end-0">
                                                <i class="fas fa-align-left"></i>
                                            </span>
                                            <textarea class="form-control border-start-0" 
                                                id="deskripsi" name="deskripsi" 
                                                rows="5" 
                                                placeholder="Berikan deskripsi detail tentang alat kesehatan ini"></textarea>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1 text-primary"></i>
                                            Jelaskan fungsi, spesifikasi, dan informasi penting lainnya
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-5">
                                        <button type="button" class="btn btn-primary btn-lg px-5 rounded-pill next-step">
                                            Lanjutkan <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Step 2: Media Upload -->
                                <div class="form-step" data-step="2">
                                    <div class="mb-4 pb-2">
                                        <h4 class="fw-bold text-primary mb-3">Media</h4>
                                        <p class="text-muted">Unggah foto alat kesehatan untuk dokumentasi visual</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Foto Alat Kesehatan</label>
                                        
                                        <div class="upload-container">
                                            <div class="upload-area" id="uploadArea">
                                                <input type="file" class="d-none" id="foto" name="foto" accept="image/*">
                                                
                                                <div class="upload-content text-center" id="uploadContent">
                                                    <div class="upload-icon mb-3">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Unggah Foto Alat Kesehatan</h5>
                                                    <p class="text-muted mb-4">Seret dan lepas file di sini, atau klik untuk memilih</p>
                                                    <button type="button" class="btn btn-primary px-4 rounded-pill" id="browseButton">
                                                        <i class="fas fa-folder-open me-2"></i>Pilih File
                                                    </button>
                                                </div>
                                                
                                                <div class="preview-content d-none" id="previewContent">
                                                    <div class="preview-header d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="mb-0 fw-bold">Preview Foto</h6>
                                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" id="removeImage">
                                                            <i class="fas fa-trash-alt me-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                    <div class="preview-image-container">
                                                        <img src="" alt="Preview" id="imagePreview" class="img-fluid rounded-3">
                                                    </div>
                                                    <div class="preview-info mt-3">
                                                        <div class="d-flex align-items-center text-muted small">
                                                            <i class="fas fa-file-image me-2"></i>
                                                            <span id="fileName">filename.jpg</span>
                                                            <span class="mx-2">•</span>
                                                            <span id="fileSize">0 KB</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="upload-info mt-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                                    <span class="small fw-semibold">Persyaratan Foto:</span>
                                                </div>
                                                <ul class="small text-muted ps-4 mb-0">
                                                    <li>Format yang didukung: JPG, PNG, GIF</li>
                                                    <li>Ukuran maksimal: 2MB</li>
                                                    <li>Resolusi yang direkomendasikan: minimal 800x600 piksel</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 rounded-pill prev-step">
                                            <i class="fas fa-arrow-left me-2"></i> Kembali
                                        </button>
                                        <button type="button" class="btn btn-primary btn-lg px-5 rounded-pill next-step">
                                            Lanjutkan <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Step 3: Review and Submit -->
                                <div class="form-step" data-step="3">
                                    <div class="mb-4 pb-2">
                                        <h4 class="fw-bold text-primary mb-3">Konfirmasi Data</h4>
                                        <p class="text-muted">Tinjau informasi yang telah dimasukkan sebelum menyimpan</p>
                                    </div>
                                    
                                    <div class="review-container bg-light p-4 rounded-4 mb-4">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <h6 class="fw-bold text-primary mb-3">
                                                    <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                                                </h6>
                                                <div class="review-item mb-3">
                                                    <div class="review-label text-muted small">Nama Alat Kesehatan</div>
                                                    <div class="review-value fw-semibold" id="reviewNama">-</div>
                                                </div>
                                                <div class="review-item">
                                                    <div class="review-label text-muted small">Deskripsi</div>
                                                    <div class="review-value" id="reviewDeskripsi">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <h6 class="fw-bold text-primary mb-3">
                                                    <i class="fas fa-image me-2"></i>Media
                                                </h6>
                                                <div class="review-image text-center p-3 bg-white rounded-3 border">
                                                    <div id="reviewImageContainer">
                                                        <div class="no-image-placeholder text-muted">
                                                            <i class="fas fa-image fa-3x mb-2"></i>
                                                            <p>Tidak ada foto yang diunggah</p>
                                                        </div>
                                                    </div>
                                                    <img src="" alt="Review" id="reviewImage" class="img-fluid rounded-3 d-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning border-0 d-flex" role="alert">
                                        <div class="alert-icon me-3">
                                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0">Pastikan semua informasi sudah benar sebelum menyimpan. Data yang telah disimpan dapat diubah melalui menu edit.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 rounded-pill prev-step">
                                            <i class="fas fa-arrow-left me-2"></i> Kembali
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill">
                                            <i class="fas fa-save me-2"></i> Simpan Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Help Card -->
                <div class="card border-0 shadow-sm rounded-4 mt-4 bg-light">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="help-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold mb-1">Butuh bantuan?</h5>
                                <p class="mb-0">Jika Anda mengalami kesulitan dalam menambahkan data alat kesehatan, silakan hubungi tim dukungan teknis atau lihat panduan pengguna.</p>
                            </div>
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
    --primary-dark: #0b5ed7;
    --primary-light: #e7f1ff;
    --primary-lighter: #f0f7ff;
    --success: #198754;
    --warning: #ffc107;
    --danger: #dc3545;
    --secondary: #6c757d;
    --light: #f8f9fa;
    --dark: #212529;
    --white: #ffffff;
    --border-radius: 0.5rem;
    --border-radius-lg: 1rem;
    --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
    --box-shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.12);
    --transition: all 0.3s ease;
}

/* General Styles */
body {
    background-color: #f9fbfd;
    color: var(--dark);
}

.page-wrapper {
    min-height: 100vh;
}

.rounded-4 {
    border-radius: var(--border-radius-lg) !important;
}

.form-control, .input-group-text {
    border-color: #e0e6ed;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.form-label {
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.form-text {
    color: var(--secondary);
    font-size: 0.875rem;
}

/* Buttons */
.btn {
    font-weight: 500;
    padding: 0.6rem 1.2rem;
    transition: var(--transition);
}

.btn-lg {
    padding: 0.8rem 1.5rem;
    font-size: 1rem;
}

.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
    box-shadow: 0 4px 6px rgba(13, 110, 253, 0.1);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(13, 110, 253, 0.2);
}

.btn-success {
    background-color: var(--success);
    border-color: var(--success);
    box-shadow: 0 4px 6px rgba(25, 135, 84, 0.1);
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(25, 135, 84, 0.2);
}

.btn-outline-secondary {
    color: var(--secondary);
    border-color: #d0d6dc;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: var(--dark);
    border-color: #c6ccd3;
}

.rounded-pill {
    border-radius: 50rem !important;
}

/* Alert Styles */
.alert {
    border-radius: var(--border-radius);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.alert-primary {
    background-color: var(--primary-lighter);
    color: var(--primary-dark);
}

.alert-warning {
    background-color: #fff8e1;
    color: #856404;
}

.alert-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    color: inherit;
}

/* Steps Navigation */
.steps-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.step {
    display: flex;
    align-items: center;
    gap: 1rem;
    opacity: 0.6;
    transition: var(--transition);
}

.step.active {
    opacity: 1;
}

.step-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.2);
    color: var(--white);
    font-size: 1rem;
}

.step.active .step-icon {
    background-color: var(--white);
    color: var(--primary);
}

.step-content {
    flex: 1;
}

/* Form Steps */
.form-steps {
    position: relative;
}

.form-step {
    display: none;
}

.form-step.active {
    display: block;
}

/* Upload Area */
.upload-container {
    margin-bottom: 1.5rem;
}

.upload-area {
    border: 2px dashed #d0d6dc;
    border-radius: var(--border-radius);
    padding: 2rem;
    background-color: var(--white);
    transition: var(--transition);
    cursor: pointer;
}

.upload-area:hover {
    border-color: var(--primary);
    background-color: var(--primary-lighter);
}

.upload-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: var(--primary-light);
    color: var(--primary);
    font-size: 2rem;
    transition: var(--transition);
}

.upload-area:hover .upload-icon {
    transform: translateY(-5px);
    background-color: var(--primary);
    color: var(--white);
}

.preview-image-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: var(--border-radius);
    overflow: hidden;
    height: 250px;
}

#imagePreview {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

/* Review Section */
.review-item {
    margin-bottom: 1rem;
}

.review-label {
    margin-bottom: 0.25rem;
}

.review-value {
    word-break: break-word;
}

.no-image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    background-color: #f8f9fa;
    border-radius: var(--border-radius);
}

/* Help Section */
.help-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: var(--primary-light);
    color: var(--primary);
    font-size: 1.5rem;
}

/* Progress Bar Animation */
@keyframes progress {
    0% { width: 0%; }
    33% { width: 33%; }
    66% { width: 66%; }
    100% { width: 100%; }
}

/* Responsive Adjustments */
@media (max-width: 767.98px) {
    .steps-container {
        flex-direction: row;
        justify-content: space-between;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }
    
    .step-content {
        display: none;
    }
    
    .step-icon {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }
    
    .upload-area {
        padding: 1.5rem;
    }
    
    .upload-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Multi-step form navigation
    const formSteps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const progressBar = document.getElementById('formProgress');
    
    let currentStep = 1;
    const totalSteps = formSteps.length;
    
    // Update progress bar
    function updateProgress() {
        const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = `${progressPercentage}%`;
    }
    
    // Go to specific step
    function goToStep(step) {
        formSteps.forEach(formStep => {
            formStep.classList.remove('active');
        });
        
        stepIndicators.forEach(indicator => {
            indicator.classList.remove('active');
        });
        
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
        document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
        
        currentStep = step;
        updateProgress();
    }
    
    // Next step button click
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Validate current step
            if (currentStep === 1) {
                const nama = document.getElementById('nama').value;
                if (!nama.trim()) {
                    showToast('Nama alat kesehatan harus diisi', 'danger');
                    return;
                }
                
                // Update review data
                document.getElementById('reviewNama').textContent = nama;
                
                const deskripsi = document.getElementById('deskripsi').value;
                document.getElementById('reviewDeskripsi').textContent = deskripsi.trim() ? deskripsi : 'Tidak ada deskripsi';
            }
            
            if (currentStep === 2) {
                // Update review image
                const imagePreview = document.getElementById('imagePreview');
                const reviewImage = document.getElementById('reviewImage');
                const reviewImageContainer = document.getElementById('reviewImageContainer');
                
                if (imagePreview.src) {
                    reviewImage.src = imagePreview.src;
                    reviewImage.classList.remove('d-none');
                    reviewImageContainer.classList.add('d-none');
                } else {
                    reviewImage.classList.add('d-none');
                    reviewImageContainer.classList.remove('d-none');
                }
            }
            
            goToStep(currentStep + 1);
        });
    });
    
    // Previous step button click
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            goToStep(currentStep - 1);
        });
    });
    
    // File upload functionality
    const fileInput = document.getElementById('foto');
    const browseButton = document.getElementById('browseButton');
    const uploadArea = document.getElementById('uploadArea');
    const uploadContent = document.getElementById('uploadContent');
    const previewContent = document.getElementById('previewContent');
    const imagePreview = document.getElementById('imagePreview');
    const removeButton = document.getElementById('removeImage');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    // Open file dialog when browse button is clicked
    browseButton.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });
    
    // Open file dialog when upload area is clicked
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });
    
    // Handle file selection
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        uploadArea.classList.add('highlight');
        uploadArea.style.borderColor = 'var(--primary)';
        uploadArea.style.backgroundColor = 'var(--primary-lighter)';
    }
    
    function unhighlight() {
        uploadArea.classList.remove('highlight');
        uploadArea.style.borderColor = '';
        uploadArea.style.backgroundColor = '';
    }
    
    uploadArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            
            // Check if file is an image
            if (!file.type.match('image.*')) {
                showToast('Hanya file gambar yang diperbolehkan', 'danger');
                return;
            }
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showToast('Ukuran file maksimal 2MB', 'danger');
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                uploadContent.classList.add('d-none');
                previewContent.classList.remove('d-none');
                
                // Update file info
            // Update file info
            fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
            }
            
            reader.readAsDataURL(file);
        }
    }
    
    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Remove image
    removeButton.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.value = '';
        imagePreview.src = '';
        previewContent.classList.add('d-none');
        uploadContent.classList.remove('d-none');
    });
    
    // Form validation
    const form = document.getElementById('addItemForm');
    const namaInput = document.getElementById('nama');
    
    form.addEventListener('submit', function(event) {
        if (!namaInput.value.trim()) {
            event.preventDefault();
            showToast('Nama alat kesehatan harus diisi', 'danger');
            goToStep(1);
            namaInput.focus();
            return false;
        }
    });
    
    // Toast notification
    function showToast(message, type) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '5';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type} border-0 mb-2`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        // Toast content
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Auto-remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
        
        // Close button functionality
        const closeButton = toast.querySelector('.btn-close');
        closeButton.addEventListener('click', function() {
            toast.remove();
        });
    }
    
    // Input animations
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.input-group').classList.add('shadow-sm');
            this.closest('.input-group').style.borderColor = 'var(--primary)';
        });
        
        input.addEventListener('blur', function() {
            this.closest('.input-group').classList.remove('shadow-sm');
            this.closest('.input-group').style.borderColor = '';
        });
    });
    
    // Initialize
    updateProgress();
});
</script>

<?php include '../includes/footer.php'; ?>