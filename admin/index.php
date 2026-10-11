<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

// Fungsi untuk menampilkan teks dengan aman.
function e($value)
{
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Format angka menjadi Rupiah.
function rupiah($value)
{
    return number_format((float)$value, 0, ",", ".");
}

// Format berat emas.
function formatBerat($value)
{
    return number_format((float)$value, 2, ",", ".");
}

// Nilai awal jika query gagal.
$totalPesanan = 0;
$totalMenunggu = 0;
$hargaEmas = null;
$pesananTerbaru = [];
$errorDatabase = false;

// Statistik pesanan.
$queryTotal = $conn->query(
    "SELECT COUNT(*) AS total FROM pesanan"
);

if ($queryTotal) {
    $totalPesanan = (int)$queryTotal->fetch_assoc()["total"];
} else {
    $errorDatabase = true;
}

$queryMenunggu = $conn->query(
    "SELECT COUNT(*) AS total
     FROM pesanan
     WHERE status = 'Menunggu'"
);

if ($queryMenunggu) {
    $totalMenunggu = (int)$queryMenunggu->fetch_assoc()["total"];
} else {
    $errorDatabase = true;
}

// Harga emas terbaru.
$queryHarga = $conn->query(
    "SELECT harga_88, harga_90, sumber, tanggal, waktu_update
     FROM harga_emas
     ORDER BY tanggal DESC, waktu_update DESC
     LIMIT 1"
);

if ($queryHarga) {
    $hargaEmas = $queryHarga->fetch_assoc();
} else {
    $errorDatabase = true;
}

// Lima pesanan terbaru.
$queryPesanan = $conn->query(
    "SELECT id, nama, jenis_perhiasan, kadar, berat,
            estimasi, status, tanggal
     FROM pesanan
     ORDER BY tanggal DESC, id DESC
     LIMIT 5"
);

if ($queryPesanan) {
    while ($row = $queryPesanan->fetch_assoc()) {
        $pesananTerbaru[] = $row;
    }
} else {
    $errorDatabase = true;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - TUKANG EMAS REZKY</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

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
            aria-controls="adminNavbar"
            aria-expanded="false"
            aria-label="Buka navigasi"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="pesanan.php">
                        Pesanan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="koleksi.php">
                        Koleksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="galeri.php">
                        Galeri
                    </a>
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

        <p class="small-title">ADMIN PANEL</p>

        <h1>
            Dashboard
            <span class="brand-gold">Admin</span>
        </h1>

        <p>
            Selamat datang, <?= e($_SESSION["admin"]); ?>.
        </p>

    </div>
</section>

<section class="section">
    <div class="container">

        <?php if ($errorDatabase): ?>
            <div class="alert alert-warning" role="alert">
                Sebagian data tidak dapat dimuat. Periksa koneksi database
                dan struktur tabel yang digunakan.
            </div>
        <?php endif; ?>

        <!-- STATISTIK -->
        <div class="row g-4">

            <div class="col-md-4">
                <div class="home-feature">
                    <i class="bi bi-receipt"></i>

                    <h3><?= $totalPesanan; ?></h3>

                    <p>Total Pesanan</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="home-feature">
                    <i class="bi bi-clock"></i>

                    <h3><?= $totalMenunggu; ?></h3>

                    <p>Pesanan Menunggu</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="home-feature">
                    <i class="bi bi-gem"></i>

                    <h3>Harga Emas</h3>

                    <?php if ($hargaEmas): ?>

                        <p>
                            88%: Rp <?= rupiah($hargaEmas["harga_88"]); ?>
                        </p>

                        <p>
                            90%: Rp <?= rupiah($hargaEmas["harga_90"]); ?>
                        </p>

                        <small><?= e($hargaEmas["sumber"]); ?></small>

                    <?php else: ?>

                        <p>Harga belum tersedia.</p>

                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- PESANAN TERBARU -->
        <div class="section-heading text-center mt-5 mb-4">
            <p class="small-title">DATABASE</p>
            <h2>Pesanan Terbaru</h2>
        </div>

        <div class="custom-form-card">
            <div class="table-responsive">

                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Perhiasan</th>
                            <th>Kadar</th>
                            <th>Berat</th>
                            <th>Estimasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($pesananTerbaru)): ?>

                        <?php foreach ($pesananTerbaru as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>

                                <td><?= e($row["nama"]); ?></td>

                                <td><?= e($row["jenis_perhiasan"]); ?></td>

                                <td><?= e($row["kadar"]); ?></td>

                                <td>
                                    <?= formatBerat($row["berat"]); ?> gram
                                </td>

                                <td>
                                    Rp <?= rupiah($row["estimasi"]); ?>
                                </td>

                                <td><?= e($row["status"]); ?></td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada pesanan atau data pesanan tidak tersedia.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>

            </div>

            <div class="form-buttons mt-4">
                <a href="pesanan.php" class="btn-gold">
                    Kelola Pesanan
                </a>
            </div>
        </div>

        <!-- HARGA EMAS TERKINI -->
        <div class="custom-form-card mt-5">

            <div class="section-heading mb-4">
                <p class="small-title">HARGA EMAS</p>

                <h2>Harga Emas Terkini</h2>

                <p>
                    Harga diambil secara otomatis.
                    Admin tidak dapat mengubah harga emas.
                </p>
            </div>

            <?php if ($hargaEmas): ?>

                <div class="row g-4">

                    <div class="col-md-6">
                        <div class="estimate-box">

                            <div class="estimate-title">
                                KADAR 88%
                            </div>

                            <div class="estimate-result">
                                Rp <?= rupiah($hargaEmas["harga_88"]); ?>
                            </div>

                            <p>per gram</p>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="estimate-box">

                            <div class="estimate-title">
                                KADAR 90%
                            </div>

                            <div class="estimate-result">
                                Rp <?= rupiah($hargaEmas["harga_90"]); ?>
                            </div>

                            <p>per gram</p>

                        </div>
                    </div>

                </div>

                <p class="mt-3">
                    <strong>Sumber:</strong>
                    <?= e($hargaEmas["sumber"]); ?>
                </p>

                <?php if (!empty($hargaEmas["waktu_update"])): ?>
                    <p>
                        <strong>Pembaruan terakhir:</strong>
                        <?= e($hargaEmas["waktu_update"]); ?>
                    </p>
                <?php endif; ?>

            <?php else: ?>

                <p>Harga emas belum tersedia di database.</p>

            <?php endif; ?>

        </div>
    </div>
</section>

<?php include "logout-modal.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>