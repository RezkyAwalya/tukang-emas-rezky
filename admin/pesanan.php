<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

// Ambil statistik pesanan
$stats = [
    "total" => 0,
    "menunggu" => 0,
    "diproses" => 0,
    "selesai" => 0
];

$statQuery = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Menunggu' THEN 1 ELSE 0 END) as menunggu,
        SUM(CASE WHEN status = 'Diproses' THEN 1 ELSE 0 END) as diproses,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai
    FROM pesanan
");

if ($statQuery && $statRow = $statQuery->fetch_assoc()) {
    $stats["total"] = (int) $statRow["total"];
    $stats["menunggu"] = (int) ($statRow["menunggu"] ?? 0);
    $stats["diproses"] = (int) ($statRow["diproses"] ?? 0);
    $stats["selesai"] = (int) ($statRow["selesai"] ?? 0);
}

// Ambil semua daftar pesanan
$sql = "SELECT * FROM pesanan ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - TUKANG EMAS REZKY</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .stat-card {
            background: #151515;
            border: 1px solid #282828;
            border-radius: 8px;
            padding: 20px;
            transition: 0.3s;
        }
        .stat-card:hover {
            border-color: rgba(212, 175, 55, 0.5);
            transform: translateY(-3px);
        }
        .stat-icon {
            font-size: 2rem;
            color: #d4af37;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            font-family: 'Playfair Display', serif;
        }
        .table th {
            background-color: #1a1a1a;
            color: #d4af37;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 14px 12px;
            border-bottom: 2px solid #333;
        }
        .table td {
            padding: 14px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #222;
        }
        .badge-status {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-filter.active {
            background: #d4af37;
            color: #111;
            border-color: #d4af37;
        }
    </style>
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
                    <a class="nav-link active" href="pesanan.php">Pesanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="koleksi.php">Koleksi</a>
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
        <p class="small-title">DATABASE</p>
        <h1>
            Kelola <span class="brand-gold">Pesanan</span>
        </h1>
        <p>Kelola data pesanan custom dan pemesanan perhiasan pelanggan.</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <!-- KARTU STATISTIK PESANAN -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small mb-1">TOTAL PESANAN</p>
                        <h3 class="stat-number"><?= $stats["total"]; ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small mb-1">MENUNGGU</p>
                        <h3 class="stat-number text-warning"><?= $stats["menunggu"]; ?></h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small mb-1">DIPROSES</p>
                        <h3 class="stat-number text-info"><?= $stats["diproses"]; ?></h3>
                    </div>
                    <div class="stat-icon text-info">
                        <i class="bi bi-gear-wide-connected"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small mb-1">SELESAI</p>
                        <h3 class="stat-number text-success"><?= $stats["selesai"]; ?></h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- HEADER KELOLA & FILTER -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="mb-1">Daftar Pesanan Pelanggan</h2>
                <p class="text-secondary mb-0">Kelola status pengerjaan dan detail spesifikasi pesanan.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="index.php" class="btn-outline-gold">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <a href="pesanan-tambah.php" class="btn-gold">
                    <i class="bi bi-plus-lg"></i> + Tambah Pesanan
                </a>
            </div>
        </div>

        <!-- SEARCH DAN TAB FILTER STATUS -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div class="search-box" style="max-width: 380px;">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="adminSearchPesanan"
                    class="form-control"
                    placeholder="Cari nama, jenis perhiasan, ukuran..."
                >
            </div>

            <div class="d-flex flex-wrap gap-2" id="filterStatusGroup">
                <button type="button" class="btn btn-sm btn-outline-gold btn-filter active" data-status="all">
                    Semua (<?= $stats["total"]; ?>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-gold btn-filter" data-status="menunggu">
                    Menunggu (<?= $stats["menunggu"]; ?>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-gold btn-filter" data-status="diproses">
                    Diproses (<?= $stats["diproses"]; ?>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-gold btn-filter" data-status="selesai">
                    Selesai (<?= $stats["selesai"]; ?>)
                </button>
            </div>
        </div>

        <!-- TABEL PESANAN -->
        <div class="custom-form-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" id="pesananTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Pelanggan</th>
                            <th>Perhiasan</th>
                            <th>Ukuran</th>
                            <th>Kadar</th>
                            <th>Berat</th>
                            <th>Estimasi Harga</th>
                            <th>Status</th>
                            <th>Waktu Pesan</th>
                            <th class="text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $statusRaw = trim($row["status"] ?? "Menunggu");
                            $statusLower = strtolower($statusRaw);
                        ?>
                            <tr class="pesanan-row" data-status="<?= $statusLower; ?>">
                                <td class="text-center text-secondary"><?= $no++; ?></td>
                                <td>
                                    <strong class="text-white"><?= htmlspecialchars($row["nama"]); ?></strong>
                                </td>
                                <td>
                                    <span class="text-light">
                                        <i class="bi bi-gem text-gold me-1"></i>
                                        <?= htmlspecialchars($row["jenis_perhiasan"]); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row["ukuran"]); ?></td>
                                <td>
                                    <span class="badge bg-dark border border-secondary text-gold">
                                        <?= htmlspecialchars($row["kadar"]); ?>
                                    </span>
                                </td>
                                <td><?= number_format($row["berat"], 2, ",", "."); ?> gr</td>
                                <td>
                                    <strong style="color: #d4af37;">
                                        Rp <?= number_format($row["estimasi"], 0, ",", "."); ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php if ($statusLower === "menunggu"): ?>
                                        <span class="badge-status bg-warning-subtle text-warning border border-warning">
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        </span>
                                    <?php elseif ($statusLower === "diproses"): ?>
                                        <span class="badge-status bg-info-subtle text-info border border-info">
                                            <i class="bi bi-gear-wide-connected"></i> Diproses
                                        </span>
                                    <?php elseif ($statusLower === "selesai"): ?>
                                        <span class="badge-status bg-success-subtle text-success border border-success">
                                            <i class="bi bi-check-circle-fill"></i> Selesai
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status bg-secondary-subtle text-secondary border border-secondary">
                                            <?= htmlspecialchars($statusRaw); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small">
                                    <?= date("d/m/Y H:i", strtotime($row["tanggal"])); ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a
                                            href="pesanan-detail.php?id=<?= $row["id"]; ?>"
                                            class="btn btn-sm btn-outline-gold"
                                            title="Lihat Detail"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a
                                            href="pesanan-edit.php?id=<?= $row["id"]; ?>"
                                            class="btn btn-sm btn-gold"
                                            title="Edit Pesanan"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <a
                                            href="pesanan-hapus.php?id=<?= $row["id"]; ?>"
                                            class="btn btn-sm btn-outline-gold"
                                            title="Hapus Pesanan"
                                            onclick="return confirm('Yakin ingin menghapus pesanan atas nama <?= addslashes(htmlspecialchars($row['nama'])); ?>?');"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr id="emptyTableRow">
                            <td colspan="10" class="text-center py-5">
                                <i class="bi bi-inbox display-5 text-secondary d-block mb-3"></i>
                                <h5 class="text-light">Belum Ada Pesanan Masuk</h5>
                                <p class="text-secondary mb-0">Pesanan dari halaman custom pengunjung akan tercatat di sini secara otomatis.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="noMatchPesanan" class="custom-form-card text-center py-5 d-none mt-4">
            <i class="bi bi-search display-6 text-gold mb-3 d-block"></i>
            <h5>Pesanan tidak ditemukan</h5>
            <p class="text-secondary mb-0">Tidak ada pesanan yang sesuai dengan kata kunci pencarian atau filter yang dipilih.</p>
        </div>

    </div>
</section>

<?php include "logout-modal.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("adminSearchPesanan");
    const filterButtons = document.querySelectorAll(".btn-filter");
    const rows = document.querySelectorAll(".pesanan-row");
    const noMatch = document.getElementById("noMatchPesanan");

    let currentStatus = "all";
    let searchQuery = "";

    function filterTable() {
        let visibleCount = 0;

        rows.forEach(function (row) {
            const rowStatus = row.getAttribute("data-status") || "";
            const rowText = row.textContent.toLowerCase();

            const matchStatus = (currentStatus === "all" || rowStatus === currentStatus);
            const matchSearch = (searchQuery === "" || rowText.includes(searchQuery));

            if (matchStatus && matchSearch) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        if (noMatch) {
            if (visibleCount === 0 && rows.length > 0) {
                noMatch.classList.remove("d-none");
            } else {
                noMatch.classList.add("d-none");
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener("input", function (e) {
            searchQuery = e.target.value.toLowerCase().trim();
            filterTable();
        });
    }

    filterButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            filterButtons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
            currentStatus = this.getAttribute("data-status");
            filterTable();
        });
    });
});
</script>
</body>
</html>