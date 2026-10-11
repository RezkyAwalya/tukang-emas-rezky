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

$stmt = $conn->prepare("SELECT * FROM galeri WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: galeri.php");
    exit;
}

$data = $result->fetch_assoc();
$error = "";

// Resolusi path gambar lama
$oldFileName = basename($data["gambar"]);
$currentImgSrc = "../images/" . htmlspecialchars($oldFileName);
if (file_exists(__DIR__ . "/../images/" . $oldFileName)) {
    $currentImgSrc = "../images/" . htmlspecialchars($oldFileName);
} elseif (file_exists(__DIR__ . "/../images/galeri/" . $oldFileName)) {
    $currentImgSrc = "../images/galeri/" . htmlspecialchars($oldFileName);
} elseif (file_exists(__DIR__ . "/../uploads/galeri/" . $oldFileName)) {
    $currentImgSrc = "../uploads/galeri/" . htmlspecialchars($oldFileName);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST["judul"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    $gambarBaru = $data["gambar"];

    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] === UPLOAD_ERR_OK) {
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
            $folder = "../images/";
            $namaFileBaru = uniqid("galeri_", true) . "." . $allowedTypes[$mime];

            if (move_uploaded_file($file["tmp_name"], $folder . $namaFileBaru)) {
                // Hapus aman: Hanya hapus foto lama jika merupakan file galeri upload (berawalan galeri_)
                if (!empty($oldFileName) && str_starts_with($oldFileName, "galeri_")) {
                    $possiblePaths = [
                        $folder . $oldFileName,
                        "../images/galeri/" . $oldFileName,
                        "../uploads/galeri/" . $oldFileName
                    ];
                    foreach ($possiblePaths as $path) {
                        if (is_file($path)) {
                            unlink($path);
                            break;
                        }
                    }
                }
                $gambarBaru = $namaFileBaru;
            } else {
                $error = "Foto baru gagal diunggah.";
            }
        }
    }

    if ($error === "") {
        $updateStmt = $conn->prepare("UPDATE galeri SET judul = ?, deskripsi = ?, gambar = ? WHERE id = ?");
        $updateStmt->bind_param("sssi", $judul, $deskripsi, $gambarBaru, $id);
        if ($updateStmt->execute()) {
            header("Location: galeri.php");
            exit;
        } else {
            $error = "Gagal memperbarui data galeri.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Galeri - TUKANG EMAS REZKY</title>
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
            <h1>Edit Galeri</h1>
            <p>Perbarui foto atau keterangan galeri.</p>
        </div>

        <div class="custom-form-card" style="max-width: 600px; margin: 0 auto;">

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3 text-center">
                    <label class="form-label fw-bold d-block">Foto Saat Ini</label>
                    <img id="currentPhoto" src="<?= $currentImgSrc; ?>" alt="Foto Galeri" style="max-height: 220px; border-radius: 8px; border: 1px solid var(--gold);">
                </div>

                <div class="mb-3">
                    <label for="gambar" class="form-label fw-bold">Ganti Foto (Opsional)</label>
                    <input
                        type="file"
                        name="gambar"
                        id="gambar"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                        onchange="previewNewImage(this)"
                    >
                    <small class="text-secondary d-block mt-1">Kosongkan jika tidak ingin mengganti foto. Format: JPG, PNG, WEBP (maks. 5 MB).</small>
                </div>

                <div id="newPreviewContainer" class="mb-3 text-center d-none">
                    <p class="small text-secondary mb-1">Pratinjau Foto Baru:</p>
                    <img id="newPreview" src="#" alt="Pratinjau Baru" style="max-height: 200px; border-radius: 8px; border: 1px dashed var(--gold);">
                </div>

                <div class="mb-3">
                    <label for="judul" class="form-label fw-bold">Keterangan / Judul (Opsional)</label>
                    <input
                        type="text"
                        name="judul"
                        id="judul"
                        class="form-control"
                        value="<?= htmlspecialchars($data["judul"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                        placeholder="Contoh: Cincin Kawin Custom (opsional)"
                    >
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label fw-bold">Deskripsi Tambahan (Opsional)</label>
                    <textarea
                        name="deskripsi"
                        id="deskripsi"
                        class="form-control"
                        rows="3"
                        placeholder="Catatan pengerjaan atau detail (opsional)"
                    ><?= htmlspecialchars($data["deskripsi"] ?? "", ENT_QUOTES, "UTF-8"); ?></textarea>
                </div>

                <div class="form-buttons">
                    <a href="galeri.php" class="btn-outline-gold">
                        Batal
                    </a>

                    <button type="submit" class="btn-gold">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

    </div>
</section>

<?php include "logout-modal.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewNewImage(input) {
    const container = document.getElementById('newPreviewContainer');
    const preview = document.getElementById('newPreview');
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