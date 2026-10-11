<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$sql = "SELECT * FROM galeri ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - TUKANG EMAS REZKY</title>

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

<section class="page-header">
    <div class="container text-center">
        <p class="small-title">DOKUMENTASI</p>
        <h1>
            Kelola <span class="brand-gold">Galeri</span>
        </h1>
        <p>Kelola dokumentasi foto hasil karya perhiasan untuk ditampilkan pada website.</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="mb-1">Daftar Foto Galeri</h2>
                <p class="text-secondary mb-0">Total: <strong><?= $result->num_rows; ?></strong> foto di galeri</p>
            </div>

            <div class="d-flex gap-2">
                <a href="index.php" class="btn-outline-gold">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <a href="galeri-tambah.php" class="btn-gold">
                    <i class="bi bi-plus-lg"></i> + Tambah Foto Galeri
                </a>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>

            <div class="row g-4" id="adminGaleriGrid">
                <?php while ($row = $result->fetch_assoc()): 
                    $namaFile = basename($row["gambar"]);
                    $imgSrc = "../images/" . htmlspecialchars($namaFile);
                    if (file_exists(__DIR__ . "/../images/" . $namaFile)) {
                        $imgSrc = "../images/" . htmlspecialchars($namaFile);
                    } elseif (file_exists(__DIR__ . "/../images/galeri/" . $namaFile)) {
                        $imgSrc = "../images/galeri/" . htmlspecialchars($namaFile);
                    } elseif (file_exists(__DIR__ . "/../uploads/galeri/" . $namaFile)) {
                        $imgSrc = "../uploads/galeri/" . htmlspecialchars($namaFile);
                    }
                    $judulTampil = !empty($row["judul"]) ? htmlspecialchars($row["judul"]) : "Foto Dokumentasi #" . $row["id"];
                ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 galeri-card-wrapper">
                        <div class="product-card d-flex flex-column h-100">
                            <div class="product-image" style="height: 250px;">
                                <img
                                    src="<?= $imgSrc; ?>"
                                    alt="<?= $judulTampil; ?>"
                                    loading="lazy"
                                >
                                <span class="product-category">
                                    <i class="bi bi-camera"></i> #<?= $row["id"]; ?>
                                </span>
                            </div>

                            <div class="product-content d-flex flex-column flex-grow-1 p-3">
                                <h5 class="mb-2 fs-6 text-truncate" title="<?= $judulTampil; ?>">
                                    <?= $judulTampil; ?>
                                </h5>

                                <?php if (!empty($row["deskripsi"])): ?>
                                    <p class="text-secondary flex-grow-1 small mb-3" style="min-height: 38px; line-height: 1.4;">
                                        <?= htmlspecialchars(mb_strimwidth($row["deskripsi"], 0, 75, "...")); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-secondary flex-grow-1 small mb-3 fst-italic" style="min-height: 38px; line-height: 1.4; color: #666 !important;">
                                        Hasil karya perhiasan
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex gap-2 mt-auto pt-2 border-top border-dark">
                                    <a
                                        href="galeri-edit.php?id=<?= $row["id"]; ?>"
                                        class="btn-gold btn-sm flex-fill text-center py-2"
                                    >
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <a
                                        href="galeri-hapus.php?id=<?= $row["id"]; ?>"
                                        class="btn-outline-gold btn-sm flex-fill text-center py-2"
                                        onclick="return confirm('Yakin ingin menghapus foto ini?')"
                                    >
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>

            <div class="custom-form-card text-center py-5">
                <i class="bi bi-images display-4 text-gold mb-3 d-block"></i>
                <h4>Belum Ada Foto Galeri</h4>
                <p class="text-secondary mb-4">Mulai unggah foto dokumentasi karya perhiasan untuk ditampilkan pada halaman galeri pengunjung.</p>
                <a href="galeri-tambah.php" class="btn-gold">
                    <i class="bi bi-plus-lg"></i> + Tambah Foto Galeri
                </a>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php include "logout-modal.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>