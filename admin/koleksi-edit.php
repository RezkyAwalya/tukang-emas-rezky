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

$stmt = $conn->prepare("SELECT * FROM koleksi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data koleksi tidak ditemukan.");
}

$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    $gambarBaru = $data["gambar"];

    if ($nama === "" || $kategori === "") {
        die("Nama dan kategori wajib diisi.");
    }

    if (
        isset($_FILES["gambar"]) &&
        $_FILES["gambar"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES["gambar"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            die("Upload gambar gagal.");
        }

        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        if (!isset($allowedTypes[$mime])) {
            die("Format gambar harus JPG, PNG, atau WEBP.");
        }

        if ($file["size"] > 5 * 1024 * 1024) {
            die("Ukuran gambar maksimal 5 MB.");
        }

        $folder = "../images/";

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $gambarBaru = uniqid("koleksi_", true)
            . "." . $allowedTypes[$mime];

        if (!move_uploaded_file(
            $file["tmp_name"],
            $folder . $gambarBaru
        )) {
            die("Gambar gagal diupload.");
        }

        // Hapus gambar lama hanya jika file merupakan upload pengguna (berawalan koleksi_)
        $oldFile = basename($data["gambar"] ?? "");
        if (!empty($oldFile) && str_starts_with($oldFile, "koleksi_")) {
            if (is_file("../images/" . $oldFile)) {
                unlink("../images/" . $oldFile);
            } elseif (is_file("../uploads/koleksi/" . $oldFile)) {
                unlink("../uploads/koleksi/" . $oldFile);
            }
        }
    }

    $sql = "UPDATE koleksi
            SET nama = ?, kategori = ?, deskripsi = ?, gambar = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssi",
        $nama,
        $kategori,
        $deskripsi,
        $gambarBaru,
        $id
    );

    if (!$stmt->execute()) {
        die("Data koleksi gagal diperbarui.");
    }

    header("Location: koleksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koleksi - TUKANG EMAS REZKY</title>

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
                    <a class="nav-link active" href="koleksi.php">Koleksi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="galeri.php">Galeri</a>
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
            <h1>Edit Koleksi</h1>
            <p>Ubah informasi koleksi perhiasan.</p>
        </div>

        <div class="custom-form-card">
            <form method="POST" enctype="multipart/form-data">

                <label>Nama Perhiasan</label>
                <input
                    type="text"
                    name="nama"
                    class="form-control"
                    value="<?php echo htmlspecialchars($data["nama"], ENT_QUOTES, "UTF-8"); ?>"
                    required
                >

                <label>Kategori</label>
                <select name="kategori" class="form-control" required>
                    <?php
                    $kategoriList = [
                        "Cincin",
                        "Anting",
                        "Kalung",
                        "Gelang",
                        "Liontin"
                    ];

                    foreach ($kategoriList as $kategoriPilihan):
                    ?>
                        <option
                            value="<?php echo htmlspecialchars($kategoriPilihan, ENT_QUOTES, "UTF-8"); ?>"
                            <?php echo (strcasecmp($data["kategori"], $kategoriPilihan) === 0) ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($kategoriPilihan, ENT_QUOTES, "UTF-8"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Deskripsi</label>
                <textarea
                    name="deskripsi"
                    class="form-control"
                    rows="5"
                ><?php echo htmlspecialchars($data["deskripsi"] ?? "", ENT_QUOTES, "UTF-8"); ?></textarea>

                <?php
                $gambarNama = basename($data["gambar"] ?? "");
                $gambarSrc = "../images/" . htmlspecialchars($gambarNama);
                if (file_exists(__DIR__ . "/../images/" . $gambarNama)) {
                    $gambarSrc = "../images/" . htmlspecialchars($gambarNama);
                } elseif (file_exists(__DIR__ . "/../uploads/koleksi/" . $gambarNama)) {
                    $gambarSrc = "../uploads/koleksi/" . htmlspecialchars($gambarNama);
                } elseif (file_exists(__DIR__ . "/../images/koleksi/" . $gambarNama)) {
                    $gambarSrc = "../images/koleksi/" . htmlspecialchars($gambarNama);
                }
                ?>

                <div class="mb-3 text-center">
                    <label class="form-label fw-bold d-block">Foto Koleksi Saat Ini</label>
                    <img
                        src="<?php echo $gambarSrc; ?>"
                        alt="Gambar koleksi saat ini"
                        style="max-height: 220px; border-radius: 8px; border: 1px solid var(--gold); margin: 8px 0;"
                    >
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
                    <small class="text-secondary d-block mt-1">Kosongkan jika tidak ingin mengganti gambar. Format: JPG, PNG, WEBP (maks. 5 MB).</small>
                </div>

                <div id="newPreviewContainer" class="mb-3 text-center d-none">
                    <p class="small text-secondary mb-1">Pratinjau Foto Baru:</p>
                    <img id="newPreview" src="#" alt="Pratinjau Baru" style="max-height: 200px; border-radius: 8px; border: 1px dashed var(--gold);">
                </div>

                <div class="form-buttons mt-4">
                    <a href="koleksi.php" class="btn-outline-gold">
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
