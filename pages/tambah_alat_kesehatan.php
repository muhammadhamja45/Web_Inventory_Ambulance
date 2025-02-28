<?php 
require_once '../auth_check.php';
requireLogin();
checkSessionTimeout();

include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0 py-2"><i class="fas fa-plus-circle me-2"></i>Tambah Alat Kesehatan</h3>
                </div>
                <div class="card-body p-4" style="border-top: 4px solid #0d6efd;">
                    <div class="small text-primary mb-4">Silakan lengkapi informasi alat kesehatan baru di bawah ini</div>
                    
                    <form action="../actions/add_item.php" method="POST" enctype="multipart/form-data" id="addItemForm">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-primary" id="nama" name="nama" required>
                                    <label for="nama" class="text-primary">Nama Alat Kesehatan</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control border-primary" id="deskripsi" name="deskripsi" style="height: 120px"></textarea>
                                    <label for="deskripsi" class="text-primary">Deskripsi</label>
                                </div>
                                <div class="form-text text-primary">Berikan deskripsi detail tentang alat kesehatan ini</div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="foto" class="form-label text-primary">Foto Alat Kesehatan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white"><i class="fas fa-image"></i></span>
                                        <input type="file" class="form-control border-primary" id="foto" name="foto" accept="image/*">
                                    </div>
                                    <div class="form-text text-primary">Format yang didukung: JPG, PNG, GIF (Maks. 2MB)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a class="btn btn-outline-primary" href="../pages/inventory.php">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-1"></i> Tambah Alat Kesehatan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3" style="background-color: #e7f1ff; color: #0d6efd;">
                    <div>Data yang ditambahkan akan tersimpan dalam database inventaris</div>
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

.card {
    border-left: 1px solid #e7f1ff;
    border-right: 1px solid #e7f1ff;
    border-bottom: 1px solid #e7f1ff;
}

.form-floating>.form-control:focus~label,
.form-floating>.form-control:not(:placeholder-shown)~label {
    color: #0d6efd;
    opacity: 0.8;
}

/* Preview image style */
#imagePreview {
    max-width: 100%;
    max-height: 200px;
    border: 2px solid #0d6efd;
    border-radius: 5px;
    display: none;
    margin-top: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('addItemForm');
    form.addEventListener('submit', function(event) {
        const nama = document.getElementById('nama').value;
        
        if (!nama.trim()) {
            event.preventDefault();
            alert('Nama alat kesehatan harus diisi');
            return false;
        }
    });
    
    // Image preview
    const fileInput = document.getElementById('foto');
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            // Check if preview element exists, if not create it
            let preview = document.getElementById('imagePreview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'imagePreview';
                this.parentNode.parentNode.appendChild(preview);
            }
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>