<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Tidak menggunakan password_hash

    $sql = "INSERT INTO pengguna (nama, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama, $email, $password]);

    header("Location: ../pages/manajemen_user.php");
}
?>