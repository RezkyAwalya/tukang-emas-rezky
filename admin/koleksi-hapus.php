<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("ID tidak valid.");
}


$stmt = $conn->prepare(
    "SELECT gambar FROM koleksi WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Koleksi tidak ditemukan.");
}

$data = $result->fetch_assoc();


$namaFile = basename($data["gambar"] ?? "");

// Hanya hapus file fisik jika file merupakan hasil upload koleksi (berawalan koleksi_)
if (!empty($namaFile) && str_starts_with($namaFile, "koleksi_")) {
    if (file_exists("../images/" . $namaFile)) {
        unlink("../images/" . $namaFile);
    } elseif (file_exists("../uploads/koleksi/" . $namaFile)) {
        unlink("../uploads/koleksi/" . $namaFile);
    }
}


$stmt = $conn->prepare(
    "DELETE FROM koleksi WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();


header("Location: koleksi.php");
exit;

?>