<?php
require_once "../config/db.php";

header("Content-Type: application/json; charset=UTF-8");

$sql = "
    SELECT id, nama, kategori, deskripsi, gambar
    FROM koleksi
    ORDER BY id ASC
";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data koleksi."
    ]);
    exit;
}

$koleksi = [];

while ($row = $result->fetch_assoc()) {
    $namaFile = basename($row["gambar"]);

    $relativePath = "images/" . $namaFile;
    if (file_exists("../images/" . $namaFile)) {
        $relativePath = "images/" . $namaFile;
    } elseif (file_exists("../uploads/koleksi/" . $namaFile)) {
        $relativePath = "uploads/koleksi/" . $namaFile;
    } elseif (file_exists("../images/koleksi/" . $namaFile)) {
        $relativePath = "images/koleksi/" . $namaFile;
    }

    $koleksi[] = [
        "id" => (int) $row["id"],
        "nama" => $row["nama"],
        "kategori" => $row["kategori"],
        "deskripsi" => $row["deskripsi"] ?? "",
        "gambar" => $relativePath
    ];
}

echo json_encode([
    "status" => "success",
    "data" => $koleksi
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>
