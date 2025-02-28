<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alat_kesehatan_id = $_POST['alat_kesehatan_id'];
    $jumlah = $_POST['jumlah'];

    // Periksa apakah alat kesehatan sudah ada di tabel stok
    $sql_check = "SELECT * FROM stok WHERE alat_kesehatan_id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$alat_kesehatan_id]);
    $stok = $stmt_check->fetch();

    if ($stok) {
        // Jika sudah ada, update jumlah stok
        $sql_update = "UPDATE stok SET jumlah = jumlah + ? WHERE alat_kesehatan_id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$jumlah, $alat_kesehatan_id]);
    } else {
        // Jika belum ada, insert baru ke tabel stok
        $sql_insert = "INSERT INTO stok (alat_kesehatan_id, jumlah) VALUES (?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$alat_kesehatan_id, $jumlah]);
    }

    header("Location: ../pages/stock_barang.php");
    exit();
}
?>
