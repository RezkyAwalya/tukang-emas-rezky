<?php
require_once "../config/db.php";

header("Content-Type: application/json; charset=UTF-8");

$sql = "
    SELECT id, judul, deskripsi, gambar
    FROM galeri
    ORDER BY tanggal_dibuat DESC, id DESC
";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data galeri."
    ]);

    exit;
}

$galeri = [];

while ($row = $result->fetch_assoc()) {
    $namaFile = basename($row["gambar"]);
    
    // Deteksi lokasi file jika ada file lama di subfolder
    $relativePath = "images/" . $namaFile;
    if (file_exists("../images/" . $namaFile)) {
        $relativePath = "images/" . $namaFile;
    } elseif (file_exists("../images/galeri/" . $namaFile)) {
        $relativePath = "images/galeri/" . $namaFile;
    } elseif (file_exists("../uploads/galeri/" . $namaFile)) {
        $relativePath = "uploads/galeri/" . $namaFile;
    }

    $galeri[] = [
        "id" => (int) $row["id"],
        "judul" => $row["judul"] ?? "",
        "deskripsi" => $row["deskripsi"] ?? "",
        "gambar" => $relativePath
    ];
}

echo json_encode([
    "status" => "success",
    "data" => $galeri
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>
