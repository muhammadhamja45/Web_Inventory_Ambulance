<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $foto = $_FILES['foto'];

    $target_dir = "../assets/images/uploads/";
    $target_file = $target_dir . basename($foto["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (move_uploaded_file($foto["tmp_name"], $target_file)) {
        $foto_url = $target_file;

        $sql = "INSERT INTO alat_kesehatan (nama, deskripsi, foto_url) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $deskripsi, $foto_url]);

        header("Location: ../pages/stock_barang.php");
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
