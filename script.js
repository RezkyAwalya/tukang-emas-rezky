/* =========================================
   TUKANG EMAS REZKY - script.js
   Frontend Interactive Scripts
========================================= */

$(document).ready(function () {

    /* =====================================
       1. LOAD KOLEKSI DARI DATABASE
    ===================================== */
    function muatKoleksi() {
        const container = $("#productContainer");

        $.ajax({
            url: "proses/get-koleksi.php",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function (res) {
                if (res && res.status === "success" && Array.isArray(res.data)) {
                    if (res.data.length === 0) {
                        container.html('<div class="col-12 text-center py-5"><p>Belum ada produk koleksi.</p></div>');
                        return;
                    }

                    container.empty();
                    let html = "";

                    res.data.forEach(function (item) {
                        const catLower = (item.kategori || "").toLowerCase().trim();
                        const safeName = $("<div>").text(item.nama || "").html();
                        const safeDesc = $("<div>").text(item.deskripsi || "").html();
                        const safeCat = $("<div>").text(item.kategori || "").html();
                        const imgPath = encodeURI(item.gambar || "");

                        html += `
                        <div class="col-md-6 col-lg-4 product-item"
                             data-category="${catLower}"
                             data-name="${safeName}">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="${imgPath}" alt="${safeName}" loading="lazy">
                                    <span class="product-category">${safeCat}</span>
                                </div>
                                <div class="product-content">
                                    <h4>${safeName}</h4>
                                    <p>${safeDesc}</p>
                                    <button class="btn btn-outline-gold btn-detail"
                                            data-bs-toggle="modal"
                                            data-bs-target="#productModal"
                                            data-name="${safeName}"
                                            data-category="${safeCat}"
                                            data-image="${imgPath}"
                                            data-description="${safeDesc}">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    });

                    container.html(html);

                    // Terapkan filter aktif jika ada
                    const filter = $(".filter-btn.active").data("filter") || "all";
                    if (filter !== "all") {
                        $(".product-item").each(function () {
                            const category = $(this).data("category");
                            if (category === filter) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }
                }
            },
            error: function (err) {
                console.error("Gagal memuat koleksi dinamis:", err);
            }
        });
    }

    if ($("#productContainer").length) {
        muatKoleksi();
    }


    /* =====================================
       2. FILTER KATEGORI PRODUK
    ===================================== */
    $(document).on("click", ".filter-btn", function () {
        const filter = $(this).data("filter");

        $(".filter-btn").removeClass("active");
        $(this).addClass("active");

        $(".product-item").each(function () {
            const category = $(this).data("category");
            if (filter === "all" || category === filter) {
                $(this).fadeIn(300);
            } else {
                $(this).fadeOut(200);
            }
        });
    });


    /* =====================================
       3. PENCARIAN PRODUK
    ===================================== */
    $(document).on("keyup", "#searchProduct", function () {
        const keyword = $(this).val().toLowerCase().trim();

        $(".product-item").each(function () {
            const productName = ($(this).data("name") || "").toString().toLowerCase();
            if (productName.includes(keyword)) {
                $(this).fadeIn(200);
            } else {
                $(this).fadeOut(200);
            }
        });
    });


    /* =====================================
       4. MODAL DETAIL PRODUK
    ===================================== */
    $(document).on("click", ".btn-detail", function () {
        const name = $(this).data("name");
        const category = $(this).data("category");
        const image = $(this).data("image");
        const description = $(this).data("description");

        $("#modalProductTitle").text(name);
        $("#modalProductCategory").text(category);
        $("#modalProductDescription").text(description);
        $("#modalProductImage")
            .attr("src", image)
            .attr("alt", name);
    });


    /* =====================================
       5. NAVBAR MOBILE TOGGLE
    ===================================== */
    $(".nav-link").click(function () {
        $(".navbar-collapse").removeClass("show");
    });

});