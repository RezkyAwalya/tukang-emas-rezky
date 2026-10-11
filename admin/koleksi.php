<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$sql = "SELECT * FROM koleksi ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Koleksi - TUKANG EMAS REZKY</title>

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

<section class="page-header">
    <div class="container text-center">
        <p class="small-title">KATALOG</p>
        <h1>
            Kelola <span class="brand-gold">Koleksi</span>
        </h1>
        <p>Kelola daftar produk perhiasan yang ditampilkan pada halaman publik.</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="mb-1">Daftar Produk Koleksi</h2>
                <p class="text-secondary mb-0">Total: <strong><?= $result->num_rows; ?></strong> produk terdaftar</p>
            </div>

            <div class="d-flex gap-2">
                <a href="index.php" class="btn-outline-gold">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <a href="koleksi-tambah.php" class="btn-gold">
                    <i class="bi bi-plus-lg"></i> + Tambah Koleksi
                </a>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>

            <div class="search-box mb-4" style="max-width: 420px;">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="adminSearchKoleksi"
                    class="form-control"
                    placeholder="Cari nama perhiasan atau kategori..."
                >
            </div>

            <div class="row g-4" id="adminKoleksiGrid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $gambarNama = basename($row["gambar"]);
                    $gambarPath = "../images/" . $gambarNama;
                    if (is_file("../images/" . $gambarNama)) {
                        $gambarPath = "../images/" . $gambarNama;
                    } elseif (is_file("../uploads/koleksi/" . $gambarNama)) {
                        $gambarPath = "../uploads/koleksi/" . $gambarNama;
                    } elseif (is_file("../images/koleksi/" . $gambarNama)) {
                        $gambarPath = "../images/koleksi/" . $gambarNama;
                    }
                    ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3 koleksi-card-wrapper">
                        <div class="product-card d-flex flex-column h-100">
                            <div class="product-image">
                                <img
                                    src="<?= htmlspecialchars($gambarPath); ?>"
                                    alt="<?= htmlspecialchars($row["nama"]); ?>"
                                    loading="lazy"
                                >
                                <span class="product-category">
                                    <?= htmlspecialchars($row["kategori"]); ?>
                                </span>
                            </div>

                            <div class="product-content d-flex flex-column flex-grow-1 p-3">
                                <h4 class="mb-2 fs-5" title="<?= htmlspecialchars($row["nama"]); ?>">
                                    <?= htmlspecialchars($row["nama"]); ?>
                                </h4>

                                <p class="text-secondary flex-grow-1 small mb-3" style="min-height: 48px; line-height: 1.5;">
                                    <?= htmlspecialchars(mb_strimwidth($row["deskripsi"] ?? "", 0, 90, "...")); ?>
                                </p>

                                <div class="d-flex gap-2 mt-auto pt-2 border-top border-dark">
                                    <a
                                        href="koleksi-edit.php?id=<?= $row["id"]; ?>"
                                        class="btn-gold btn-sm flex-fill text-center py-2"
                                    >
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <a
                                        href="koleksi-hapus.php?id=<?= $row["id"]; ?>"
                                        class="btn-outline-gold btn-sm flex-fill text-center py-2"
                                        onclick="return confirm('Yakin ingin menghapus koleksi ini?')"
                                    >
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="noMatchMessage" class="custom-form-card text-center py-5 d-none mt-3">
                <i class="bi bi-search display-6 text-gold mb-3 d-block"></i>
                <h5>Tidak ada produk yang cocok</h5>
                <p class="text-secondary mb-0">Coba gunakan kata kunci pencarian yang lain.</p>
            </div>

        <?php else: ?>

            <div class="custom-form-card text-center py-5">
                <i class="bi bi-gem display-4 text-gold mb-3 d-block"></i>
                <h4>Belum Ada Data Koleksi</h4>
                <p class="text-secondary mb-4">Tambahkan koleksi produk perhiasan emas Anda untuk mulai ditampilkan pada katalog publik.</p>
                <a href="koleksi-tambah.php" class="btn-gold">
                    <i class="bi bi-plus-lg"></i> + Tambah Koleksi Baru
                </a>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php include "logout-modal.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("adminSearchKoleksi");
    const cards = document.querySelectorAll(".koleksi-card-wrapper");
    const noMatch = document.getElementById("noMatchMessage");

    if (searchInput) {
        searchInput.addEventListener("input", function (e) {
            const query = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(function (card) {
                const text = card.textContent.toLowerCase();
                if (text.includes(query)) {
                    card.style.display = "";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });

            if (noMatch) {
                if (visibleCount === 0 && query !== "") {
                    noMatch.classList.remove("d-none");
                } else {
                    noMatch.classList.add("d-none");
                }
            }
        });
    }
});
</script>
</body>
</html>