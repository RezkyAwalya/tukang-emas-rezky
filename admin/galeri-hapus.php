<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: galeri.php");
    exit;
}

$stmt = $conn->prepare("SELECT gambar FROM galeri WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $fileName = basename($data["gambar"] ?? "");

    // Proteksi: Hanya hapus fisik file jika dibuat melalui sistem upload galeri (berawalan galeri_)
    // Jangan hapus gambar jika merupakan aset produk bersama atau aset tetap
    if (!empty($fileName) && str_starts_with($fileName, "galeri_")) {
        $pathsToCheck = [
            __DIR__ . "/../images/" . $fileName,
            __DIR__ . "/../images/galeri/" . $fileName,
            __DIR__ . "/../uploads/galeri/" . $fileName
        ];
        foreach ($pathsToCheck as $path) {
            if (is_file($path)) {
                unlink($path);
                break;
            }
        }
    }

    $deleteStmt = $conn->prepare("DELETE FROM galeri WHERE id = ?");
    $deleteStmt->bind_param("i", $id);
    $deleteStmt->execute();
}

header("Location: galeri.php");
exit;
?>