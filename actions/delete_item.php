<?php
include '../config/database.php';

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    $sql = "DELETE FROM alat_kesehatan WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item_id]);

    header("Location: ../pages/stok_barang.php");
}
?>
