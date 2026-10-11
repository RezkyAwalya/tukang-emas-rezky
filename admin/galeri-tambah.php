<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_FILES["gambar"]) ||
        $_FILES["gambar"]["error"] !== UPLOAD_ERR_OK
    ) {
        $error = "Silakan pilih foto yang valid.";
    } else {
        $file = $_FILES["gambar"];

        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png"  => "png",
            "image/webp" => "webp"
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        if (!isset($allowedTypes[$mime])) {
            $error = "Format foto harus JPG, PNG, atau WEBP.";
        } elseif ($file["size"] > 5 * 1024 * 1024) {
            $error = "Ukuran foto maksimal 5 MB.";
        } else {
            // Menggunakan folder images yang sudah tersedia.
            $folder = "../images/";

            if (!is_dir($folder)) {
                $error = "Folder images tidak ditemukan.";
            } else {
                $namaFile = uniqid("galeri_", true)
                    . "." . $allowedTypes[$mime];

                if (move_uploaded_file(
                    $file["tmp_name"],
                    $folder . $namaFile
                )) {
                    $judul = "";
                    $deskripsi = "";

                    $stmt = $conn->prepare(
                        "INSERT INTO galeri (judul, deskripsi, gambar)
                         VALUES (?, ?, ?)"
                    );

                    $stmt->bind_param(
                        "sss",
                        $judul,
                        $deskripsi,
                        $namaFile
                    );

                    if ($stmt->execute()) {
                        header("Location: galeri.php");
                        exit;
                    }

                    // Hapus file baru jika penyimpanan DB gagal.
                    if (is_file($folder . $namaFile)) {
                        unlink($folder . $namaFile);
                    }

                    $error = "Foto gagal disimpan ke database.";
                } else {
                    $error = "Foto gagal diunggah.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Galeri - TUKANG EMAS REZKY</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.html">
            TUKANG EMAS
            <span class="brand-gold">REZKY</span>
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#adminNavbar"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pesanan.php">Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="koleksi.php">Koleksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="galeri.php">Galeri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="section">
    <div class="container">

        <div class="section-heading">
            <h1>Tambah Galeri</h1>
            <p>Pilih foto yang ingin ditampilkan di galeri.</p>
        </div>

        <div class="custom-form-card" style="max-width: 600px; margin: 0 auto;">

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="gambar" class="form-label fw-bold">Pilih Foto Galeri</label>
                    <input
                        type="file"
                        name="gambar"
                        id="gambar"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                        required
                        onchange="previewImage(this)"
                    >
                    <small class="text-secondary d-block mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran 5 MB.</small>
                </div>

                <div id="imagePreviewContainer" class="mb-4 text-center d-none">
                    <p class="small text-secondary mb-2">Pratinjau Foto:</p>
                    <img id="imagePreview" src="#" alt="Pratinjau" style="max-height: 240px; border-radius: 8px; border: 1px solid var(--gold);">
                </div>

                <div class="form-buttons mt-4">
                    <a href="galeri.php" class="btn-outline-gold">
                        Batal
                    </a>

                    <button type="submit" class="btn-gold">
                        Unggah Foto
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>

<?php include "logout-modal.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(input) {
    const container = document.getElementById('imagePreviewContainer');
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.classList.add('d-none');
    }
}
</script>
</body>
</html>
