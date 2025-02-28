<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_ambulans_id = $_POST['unitAmbulans'];
    $tanggal = $_POST['tanggal'];
    $alat_kesehatan_ids = $_POST['alatKesehatan'];
    $jumlahs = $_POST['jumlah'];

    $sql = "INSERT INTO transaksi (unit_ambulans_id, tanggal) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$unit_ambulans_id, $tanggal]);
    $transaksi_id = $pdo->lastInsertId();

    for ($i = 0; $i < count($alat_kesehatan_ids); $i++) {
        $alat_kesehatan_id = $alat_kesehatan_ids[$i];
        $jumlah = $jumlahs[$i];

        $sql = "INSERT INTO transaksi_detail (transaksi_id, alat_kesehatan_id, jumlah) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$transaksi_id, $alat_kesehatan_id, $jumlah]);

        $sql = "UPDATE stok SET jumlah = jumlah - ? WHERE alat_kesehatan_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$jumlah, $alat_kesehatan_id]);
    }

    header("Location: ../pages/penggunaan_barang.php");
}
?>
