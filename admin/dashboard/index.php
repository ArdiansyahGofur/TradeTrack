<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


/* =========================================================
   FILTER DASHBOARD
========================================================= */

$tahunFilter = isset($_GET['tahun'])
    ? (int) $_GET['tahun']
    : 0;

$bulanFilter = isset($_GET['bulan'])
    ? (int) $_GET['bulan']
    : 0;

$mingguFilter = isset($_GET['minggu'])
    ? (int) $_GET['minggu']
    : 0;

$pedagangFilter = isset($_GET['pedagang'])
    ? (int) $_GET['pedagang']
    : 0;

$kategoriFilter = isset($_GET['kategori'])
    ? (int) $_GET['kategori']
    : 0;

$menuFilter = isset($_GET['menu'])
    ? (int) $_GET['menu']
    : 0;


/* =========================================================
   DAFTAR FILTER
========================================================= */

$daftarPedagang = $conn->query("
    SELECT id, nama
    FROM users
    WHERE role = 'pedagang'
      AND status = 'aktif'
    ORDER BY nama ASC
")->fetchAll(PDO::FETCH_ASSOC);


$daftarKategori = $conn->query("
    SELECT
        id,
        nama_kategori
    FROM kategori
    ORDER BY nama_kategori ASC
")->fetchAll(PDO::FETCH_ASSOC);


$daftarMenu = $conn->query("
    SELECT
        id,
        nama_menu
    FROM menu
    WHERE status = 'aktif'
    ORDER BY nama_menu ASC
")->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   WHERE FILTER PENJUALAN
========================================================= */

$where = [];
$params = [];


/* Tahun */
if ($tahunFilter > 0) {

    $where[] = "YEAR(p.tanggal) = ?";
    $params[] = $tahunFilter;

}


/* Bulan */
if ($bulanFilter >= 1 && $bulanFilter <= 12) {

    $where[] = "MONTH(p.tanggal) = ?";
    $params[] = $bulanFilter;

}


/* Minggu */
if ($mingguFilter >= 1 && $mingguFilter <= 5) {

    $where[] = "
        WEEK(p.tanggal, 1)
        -
        WEEK(
            DATE_SUB(
                p.tanggal,
                INTERVAL DAYOFMONTH(p.tanggal)-1 DAY
            ),
            1
        )
        + 1 = ?
    ";

    $params[] = $mingguFilter;

}


/* Pedagang */
if ($pedagangFilter > 0) {

    $where[] = "p.user_id = ?";
    $params[] = $pedagangFilter;

}


/* Kategori */
if ($kategoriFilter > 0) {

    $where[] = "
        EXISTS (
            SELECT 1
            FROM detail_penjualan dp_filter_kategori

            INNER JOIN menu m_filter_kategori
                ON dp_filter_kategori.menu_id = m_filter_kategori.id

            WHERE dp_filter_kategori.penjualan_id = p.id

              AND m_filter_kategori.kategori_id = ?
        )
    ";

    $params[] = $kategoriFilter;

}


/* Menu */
if ($menuFilter > 0) {

    $where[] = "
        EXISTS (
            SELECT 1
            FROM detail_penjualan dp_filter_menu

            WHERE dp_filter_menu.penjualan_id = p.id

              AND dp_filter_menu.menu_id = ?
        )
    ";

    $params[] = $menuFilter;

}


/* WHERE FINAL */

$whereSql = '';

if (!empty($where)) {

    $whereSql = 'WHERE ' . implode(' AND ', $where);

}


/* =========================================================
   TOTAL PEDAGANG AKTIF
========================================================= */

$stmt = $conn->query("
    SELECT COUNT(*) AS total

    FROM users

    WHERE role = 'pedagang'
      AND status = 'aktif'
");

$totalPedagang = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];


/* =========================================================
   TOTAL MENU AKTIF
========================================================= */

$stmt = $conn->query("
    SELECT COUNT(*) AS total

    FROM menu

    WHERE status = 'aktif'
");

$totalMenu = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];


/* =========================================================
   TOTAL TRANSAKSI
========================================================= */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total

    FROM penjualan p

    $whereSql
");

$stmt->execute($params);

$totalTransaksi = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];


/* =========================================================
   TOTAL PENDAPATAN
========================================================= */

$stmt = $conn->prepare("
    SELECT
        COALESCE(SUM(p.total), 0) AS total

    FROM penjualan p

    $whereSql
");

$stmt->execute($params);

$totalPendapatan = (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];


/* =========================================================
   DATA DIAGRAM PENDAPATAN
========================================================= */

$stmt = $conn->prepare("
    SELECT
        DATE(p.tanggal) AS tanggal,
        COALESCE(SUM(p.total), 0) AS pendapatan

    FROM penjualan p

    $whereSql

    GROUP BY DATE(p.tanggal)

    ORDER BY tanggal ASC
");

$stmt->execute($params);

$dataPendapatan = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   TRANSAKSI TERBARU
========================================================= */

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.tanggal,
        p.total,
        p.catatan,

        u.nama AS nama_pedagang,

        COUNT(dp.id) AS jumlah_item

    FROM penjualan p

    INNER JOIN users u
        ON p.user_id = u.id

    LEFT JOIN detail_penjualan dp
        ON p.id = dp.penjualan_id

    $whereSql

    GROUP BY
        p.id,
        p.tanggal,
        p.total,
        p.catatan,
        u.nama

    ORDER BY
        p.tanggal DESC,
        p.id DESC

    LIMIT 5
");

$stmt->execute($params);

$transaksiTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   PEDAGANG TERATAS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        u.nama,

        COUNT(p.id) AS total_transaksi,

        COALESCE(SUM(p.total), 0)
            AS total_pendapatan

    FROM users u

    LEFT JOIN penjualan p
        ON u.id = p.user_id

    WHERE u.role = 'pedagang'
      AND u.status = 'aktif'

    GROUP BY
        u.id,
        u.nama

    ORDER BY
        total_pendapatan DESC

    LIMIT 5
");

/*
 * Pedagang teratas secara keseluruhan.
 * Filter pedagang/kategori/menu lebih kompleks untuk LEFT JOIN,
 * sehingga daftar tetap menampilkan ranking pedagang.
 */

$stmt->execute();

$pedagangTeratas = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   MENU TERLARIS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        m.nama_menu,

        u.nama AS nama_pedagang,

        SUM(dp.jumlah) AS total_terjual

    FROM detail_penjualan dp

    INNER JOIN penjualan p
        ON dp.penjualan_id = p.id

    INNER JOIN menu m
        ON dp.menu_id = m.id

    INNER JOIN users u
        ON m.user_id = u.id

    $whereSql

    GROUP BY
        m.id,
        m.nama_menu,
        u.nama

    ORDER BY
        total_terjual DESC

    LIMIT 5
");

$stmt->execute($params);

$menuTerlaris = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   DATA DIAGRAM MENU TERLARIS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        m.nama_menu,

        SUM(dp.jumlah) AS total_terjual

    FROM detail_penjualan dp

    INNER JOIN penjualan p
        ON dp.penjualan_id = p.id

    INNER JOIN menu m
        ON dp.menu_id = m.id

    $whereSql

    GROUP BY
        m.id,
        m.nama_menu

    ORDER BY
        total_terjual DESC

    LIMIT 5
");

$stmt->execute($params);

$dataMenuTerlaris = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   NAMA BULAN
========================================================= */

$namaBulan = [

    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'

];

?>

<?php require_once '../includes/header.php'; ?>

<?php require_once '../includes/sidebar.php'; ?>


<!-- =========================================================
     MAIN WRAPPER
========================================================= -->

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

    <main
        class="
            w-full
            min-w-0

            pt-20
            pb-24

            md:pl-20
            md:pt-8
            md:pb-8

            lg:pl-64
        "
    >

        <div
            class="
                w-full
                max-w-7xl
                mx-auto

                px-4
                sm:px-6
                lg:px-8
                xl:px-10
            "
        >


            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="mb-6 sm:mb-8">

                <div
                    class="
                        flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-4
                    "
                >

                    <div>

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl
                                font-bold
                                text-slate-800
                            "
                        >
                            Dashboard Admin
                        </h1>

                        <p
                            class="
                                text-sm
                                sm:text-base
                                text-slate-500
                                mt-1
                            "
                        >
                            Ringkasan aktivitas dan penjualan TradeTrack.
                        </p>

                    </div>

                </div>

            </div>



            <!-- =================================================
                 FILTER
            ================================================== -->

            <section
                class="
                    bg-white
                    border
                    border-slate-200
                    rounded-2xl
                    shadow-sm
                    p-4
                    sm:p-5
                    mb-6
                "
            >

                <div
                    class="
                        flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-3
                        mb-5
                    "
                >

                    <div>

                        <h2
                            class="
                                font-semibold
                                text-slate-800
                            "
                        >
                            Filter Data
                        </h2>

                        <p
                            class="
                                text-xs
                                sm:text-sm
                                text-slate-500
                                mt-1
                            "
                        >
                            Gunakan kombinasi filter untuk melihat data tertentu.
                        </p>

                    </div>


                    <a
                        href="index.php"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            px-3
                            py-2
                            rounded-lg
                            text-sm
                            text-slate-500
                            hover:text-blue-600
                            hover:bg-blue-50
                            transition
                        "
                    >
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        Reset
                    </a>

                </div>


                <form method="GET">

                    <div
                        class="
                            grid
                            grid-cols-1
                            sm:grid-cols-2
                            lg:grid-cols-3
                            xl:grid-cols-6
                            gap-3
                        "
                    >

                        <!-- Tahun -->
                        <div>

                            <label
                                class="
                                    block
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                    mb-1.5
                                "
                            >
                                Tahun
                            </label>

                            <select
                                name="tahun"
                                class="
                                    w-full
                                    px-3
                                    py-2.5
                                    bg-white
                                    border
                                    border-slate-300
                                    rounded-xl
                                    text-sm
                                    text-slate-700
                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100
                                    focus:outline-none
                                "
                            >

                                <option value="">
                                    Semua Tahun
                                </option>

                                <?php
                                for (
                                    $tahun = date('Y');
                                    $tahun >= date('Y') - 5;
                                    $tahun--
                                ):
                                ?>

                                    <option
                                        value="<?= $tahun ?>"
                                        <?= ($tahunFilter == $tahun)
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= $tahun ?>
                                    </option>

                                <?php endfor; ?>

                            </select>

                        </div>


                        <!-- Bulan -->
                        <div>

                            <label
                                class="
                                    block
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                    mb-1.5
                                "
                            >
                                Bulan
                            </label>

                            <select
                                name="bulan"
                                class="
                                    w-full
                                    px-3
                                    py-2.5
                                    bg-white
                                    border
                                    border-slate-300
                                    rounded-xl
                                    text-sm
                                    text-slate-700
                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100
                                    focus:outline-none
                                "
                            >

                                <option value="">
                                    Semua Bulan
                                </option>

                                <?php foreach (
                                    $namaBulan
                                    as $nomor => $nama
                                ): ?>

                                    <option
                                        value="<?= $nomor ?>"
                                        <?= ($bulanFilter == $nomor)
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= $nama ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Minggu -->
                        <div>

                            <label
                                class="
                                    block
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                    mb-1.5
                                "
                            >
                                Minggu
                            </label>

                            <select
                                name="minggu"
                                class="
                                    w-full
                                    px-3
                                    py-2.5
                                    bg-white
                                    border
                                    border-slate-300
                                    rounded-xl
                                    text-sm
                                    text-slate-700
                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100
                                    focus:outline-none
                                "
                            >

                                <option value="">
                                    Semua Minggu
                                </option>

                                <?php for (
                                    $minggu = 1;
                                    $minggu <= 5;
                                    $minggu++
                                ): ?>

                                    <option
                                        value="<?= $minggu ?>"
                                        <?= ($mingguFilter == $minggu)
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        Minggu <?= $minggu ?>
                                    </option>

                                <?php endfor; ?>

                            </select>

                        </div>

                        <!-- Pedagang -->
                        <div>

                            <label
                                class="
                                    block
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                    mb-1.5
                                "
                            >
                                Pedagang
                            </label>

                            <select
                                name="pedagang"
                                id="filterPedagang"
                                class="
                                    w-full
                                    px-3
                                    py-2.5
                                    bg-white
                                    border
                                    border-slate-300
                                    rounded-xl
                                    text-sm
                                    text-slate-700

                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100
                                    focus:outline-none

                                    transition
                                "
                            >

                                <option value="">
                                    Semua Pedagang
                                </option>

                                <?php foreach ($daftarPedagang as $pedagang): ?>

                                    <option
                                        value="<?= $pedagang['id'] ?>"
                                        <?= (
                                            $pedagangFilter == $pedagang['id']
                                        ) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $pedagang['nama']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Kategori -->
                        <div>

                            <label
                                class="
                                    block
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                    mb-1.5
                                "
                            >
                                Kategori
                            </label>

                            <select
                                name="kategori"
                                id="filterKategori"
                                class="
                                    w-full
                                    px-3
                                    py-2.5

                                    bg-white

                                    border
                                    border-slate-300

                                    rounded-xl

                                    text-sm
                                    text-slate-700

                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100
                                    focus:outline-none

                                    transition

                                    disabled:bg-slate-100
                                    disabled:text-slate-400
                                    disabled:cursor-not-allowed
                                "
                            >

                                <option value="">
                                    Semua Kategori
                                </option>

                                <?php foreach ($daftarKategori as $kategori): ?>

                                    <option
                                        value="<?= $kategori['id'] ?>"
                                        <?= (
                                            $kategoriFilter == $kategori['id']
                                        ) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $kategori['nama_kategori']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Menu -->
                        <div>

                            <label
                                class="
                                    block
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                    mb-1.5
                                "
                            >
                                Menu
                            </label>

                            <select
                                name="menu"
                                id="filterMenu"
                                class="
                                    w-full
                                    px-3
                                    py-2.5

                                    bg-white

                                    border
                                    border-slate-300

                                    rounded-xl

                                    text-sm
                                    text-slate-700

                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100
                                    focus:outline-none

                                    transition

                                    disabled:bg-slate-100
                                    disabled:text-slate-400
                                    disabled:cursor-not-allowed
                                "
                            >

                                <option value="">
                                    Semua Menu
                                </option>

                                <?php foreach ($daftarMenu as $menu): ?>

                                    <option
                                        value="<?= $menu['id'] ?>"
                                        <?= (
                                            $menuFilter == $menu['id']
                                        ) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $menu['nama_menu']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>


                    <div
                        class="
                            flex
                            justify-end
                            mt-4
                            pt-4
                            border-t
                            border-slate-100
                        "
                    >

                        <button
                            type="submit"
                            class="
                                w-full
                                sm:w-auto
                                inline-flex
                                items-center
                                justify-center
                                gap-2
                                px-5
                                py-2.5
                                bg-blue-600
                                text-white
                                text-sm
                                font-medium
                                rounded-xl
                                hover:bg-blue-700
                                active:bg-blue-800
                                transition
                            "
                        >
                            <i class="fa-solid fa-filter"></i>

                            Terapkan Filter

                        </button>

                    </div>

                </form>

            </section>



            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section
                class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    xl:grid-cols-4
                    gap-4
                    sm:gap-5
                    mb-6
                "
            >

                <!-- Pedagang -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        p-5
                        shadow-sm
                    "
                >

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-slate-500">
                                Pedagang Aktif
                            </p>

                            <h2
                                class="
                                    text-2xl
                                    sm:text-3xl
                                    font-bold
                                    text-slate-800
                                    mt-2
                                "
                            >
                                <?= number_format($totalPedagang) ?>
                            </h2>

                        </div>

                        <div
                            class="
                                w-11
                                h-11
                                shrink-0
                                rounded-xl
                                bg-blue-100
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-users text-blue-600"></i>
                        </div>

                    </div>

                </div>


                <!-- Menu -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        p-5
                        shadow-sm
                    "
                >

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-slate-500">
                                Menu Aktif
                            </p>

                            <h2
                                class="
                                    text-2xl
                                    sm:text-3xl
                                    font-bold
                                    text-slate-800
                                    mt-2
                                "
                            >
                                <?= number_format($totalMenu) ?>
                            </h2>

                        </div>

                        <div
                            class="
                                w-11
                                h-11
                                shrink-0
                                rounded-xl
                                bg-orange-100
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-utensils text-orange-600"></i>
                        </div>

                    </div>

                </div>


                <!-- Transaksi -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        p-5
                        shadow-sm
                    "
                >

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-slate-500">
                                Total Transaksi
                            </p>

                            <h2
                                class="
                                    text-2xl
                                    sm:text-3xl
                                    font-bold
                                    text-slate-800
                                    mt-2
                                "
                            >
                                <?= number_format($totalTransaksi) ?>
                            </h2>

                        </div>

                        <div
                            class="
                                w-11
                                h-11
                                shrink-0
                                rounded-xl
                                bg-purple-100
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-receipt text-purple-600"></i>
                        </div>

                    </div>

                </div>


                <!-- Pendapatan -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        p-5
                        shadow-sm
                    "
                >

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-slate-500">
                                Total Pendapatan
                            </p>

                            <h2
                                class="
                                    text-xl
                                    sm:text-2xl
                                    font-bold
                                    text-slate-800
                                    mt-2
                                    break-words
                                "
                            >
                                Rp <?= number_format(
                                    $totalPendapatan,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </h2>

                        </div>

                        <div
                            class="
                                w-11
                                h-11
                                shrink-0
                                rounded-xl
                                bg-green-100
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-wallet text-green-600"></i>
                        </div>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 CHARTS
            ================================================== -->

            <section
                class="
                    grid
                    grid-cols-1
                    xl:grid-cols-2
                    gap-5
                    sm:gap-6
                    mb-6
                "
            >

                <!-- Chart Pendapatan -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        shadow-sm
                        p-4
                        sm:p-5
                        min-w-0
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                            mb-5
                        "
                    >

                        <div class="min-w-0">

                            <h2
                                class="
                                    text-base
                                    sm:text-lg
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Tren Pendapatan
                            </h2>

                            <p
                                class="
                                    text-xs
                                    sm:text-sm
                                    text-slate-500
                                    mt-1
                                "
                            >
                                Pergerakan pendapatan berdasarkan tanggal
                            </p>

                        </div>

                        <div
                            class="
                                w-10
                                h-10
                                shrink-0
                                rounded-xl
                                bg-blue-50
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-chart-line text-blue-600"></i>
                        </div>

                    </div>


                    <div class="relative h-64 sm:h-72 w-full">

                        <canvas id="grafikPendapatan"></canvas>

                    </div>

                </div>


                <!-- Chart Menu -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        shadow-sm
                        p-4
                        sm:p-5
                        min-w-0
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                            mb-5
                        "
                    >

                        <div class="min-w-0">

                            <h2
                                class="
                                    text-base
                                    sm:text-lg
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Menu Terlaris
                            </h2>

                            <p
                                class="
                                    text-xs
                                    sm:text-sm
                                    text-slate-500
                                    mt-1
                                "
                            >
                                Berdasarkan jumlah menu terjual
                            </p>

                        </div>

                        <div
                            class="
                                w-10
                                h-10
                                shrink-0
                                rounded-xl
                                bg-orange-50
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-chart-column text-orange-600"></i>
                        </div>

                    </div>


                    <div class="relative h-64 sm:h-72 w-full">

                        <canvas id="grafikMenu"></canvas>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 TRANSAKSI + PEDAGANG
            ================================================== -->

            <section
                class="
                    grid
                    grid-cols-1
                    xl:grid-cols-3
                    gap-5
                    sm:gap-6
                    mb-6
                "
            >

                <!-- Transaksi -->
                <div
                    class="
                        xl:col-span-2
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        shadow-sm
                        overflow-hidden
                        min-w-0
                    "
                >

                    <div
                        class="
                            flex
                            flex-col
                            sm:flex-row
                            sm:items-center
                            sm:justify-between
                            gap-3
                            p-5
                            border-b
                            border-slate-200
                        "
                    >

                        <div>

                            <h2
                                class="
                                    text-lg
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Transaksi Terbaru
                            </h2>

                            <p
                                class="
                                    text-xs
                                    sm:text-sm
                                    text-slate-500
                                    mt-1
                                "
                            >
                                5 transaksi terakhir
                            </p>

                        </div>


                        <a
                            href="../laporan/index.php"
                            class="
                                inline-flex
                                items-center
                                gap-2
                                text-sm
                                font-medium
                                text-blue-600
                                hover:text-blue-700
                            "
                        >
                            Lihat Laporan

                            <i class="fa-solid fa-arrow-right text-xs"></i>

                        </a>

                    </div>


                    <?php if (!empty($transaksiTerbaru)): ?>

                        <div class="overflow-x-auto">

                            <table
                                class="
                                    w-full
                                    min-w-[680px]
                                    text-sm
                                "
                            >

                                <thead class="bg-slate-50">

                                    <tr>

                                        <th
                                            class="
                                                text-left
                                                px-5
                                                py-3
                                                font-semibold
                                                text-slate-600
                                            "
                                        >
                                            Tanggal
                                        </th>

                                        <th
                                            class="
                                                text-left
                                                px-5
                                                py-3
                                                font-semibold
                                                text-slate-600
                                            "
                                        >
                                            Pedagang
                                        </th>

                                        <th
                                            class="
                                                text-center
                                                px-5
                                                py-3
                                                font-semibold
                                                text-slate-600
                                            "
                                        >
                                            Item
                                        </th>

                                        <th
                                            class="
                                                text-right
                                                px-5
                                                py-3
                                                font-semibold
                                                text-slate-600
                                            "
                                        >
                                            Total
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-slate-100">

                                    <?php foreach (
                                        $transaksiTerbaru
                                        as $transaksi
                                    ): ?>

                                        <tr class="hover:bg-slate-50 transition">

                                            <td class="px-5 py-4 whitespace-nowrap">

                                                <div
                                                    class="
                                                        font-medium
                                                        text-slate-700
                                                    "
                                                >
                                                    <?= date(
                                                        'd M Y',
                                                        strtotime(
                                                            $transaksi['tanggal']
                                                        )
                                                    ) ?>
                                                </div>

                                                <div
                                                    class="
                                                        text-xs
                                                        text-slate-400
                                                        mt-1
                                                    "
                                                >
                                                    <?= date(
                                                        'H:i',
                                                        strtotime(
                                                            $transaksi['tanggal']
                                                        )
                                                    ) ?>
                                                </div>

                                            </td>


                                            <td class="px-5 py-4">

                                                <span
                                                    class="
                                                        font-medium
                                                        text-slate-700
                                                    "
                                                >
                                                    <?= htmlspecialchars(
                                                        $transaksi[
                                                            'nama_pedagang'
                                                        ]
                                                    ) ?>
                                                </span>

                                            </td>


                                            <td
                                                class="
                                                    px-5
                                                    py-4
                                                    text-center
                                                "
                                            >

                                                <span
                                                    class="
                                                        inline-flex
                                                        px-2.5
                                                        py-1
                                                        rounded-lg
                                                        bg-blue-50
                                                        text-blue-700
                                                        text-xs
                                                        font-medium
                                                    "
                                                >
                                                    <?= number_format(
                                                        $transaksi[
                                                            'jumlah_item'
                                                        ]
                                                    ) ?>
                                                </span>

                                            </td>


                                            <td
                                                class="
                                                    px-5
                                                    py-4
                                                    text-right
                                                    font-semibold
                                                    text-slate-800
                                                    whitespace-nowrap
                                                "
                                            >
                                                Rp <?= number_format(
                                                    $transaksi['total'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                        <div class="p-10 text-center">

                            <i
                                class="
                                    fa-solid
                                    fa-receipt
                                    text-3xl
                                    text-slate-300
                                "
                            ></i>

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    mt-3
                                "
                            >
                                Belum ada transaksi.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>



                <!-- Pedagang Teratas -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        shadow-sm
                        overflow-hidden
                    "
                >

                    <div
                        class="
                            p-5
                            border-b
                            border-slate-200
                        "
                    >

                        <h2
                            class="
                                text-lg
                                font-semibold
                                text-slate-800
                            "
                        >
                            Pedagang Teratas
                        </h2>

                        <p
                            class="
                                text-xs
                                sm:text-sm
                                text-slate-500
                                mt-1
                            "
                        >
                            Berdasarkan pendapatan
                        </p>

                    </div>


                    <?php if (!empty($pedagangTeratas)): ?>

                        <div class="divide-y divide-slate-100">

                            <?php foreach (
                                $pedagangTeratas
                                as $index => $pedagang
                            ): ?>

                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                        px-5
                                        py-4
                                    "
                                >

                                    <div
                                        class="
                                            flex
                                            items-center
                                            gap-3
                                            min-w-0
                                        "
                                    >

                                        <div
                                            class="
                                                w-9
                                                h-9
                                                shrink-0
                                                rounded-lg
                                                bg-blue-50
                                                text-blue-600
                                                flex
                                                items-center
                                                justify-center
                                                font-bold
                                                text-sm
                                            "
                                        >
                                            <?= $index + 1 ?>
                                        </div>


                                        <div class="min-w-0">

                                            <p
                                                class="
                                                    font-medium
                                                    text-slate-700
                                                    truncate
                                                "
                                            >
                                                <?= htmlspecialchars(
                                                    $pedagang['nama']
                                                ) ?>
                                            </p>

                                            <p
                                                class="
                                                    text-xs
                                                    text-slate-400
                                                    mt-1
                                                "
                                            >
                                                <?= number_format(
                                                    $pedagang[
                                                        'total_transaksi'
                                                    ]
                                                ) ?>
                                                transaksi
                                            </p>

                                        </div>

                                    </div>


                                    <div
                                        class="
                                            text-right
                                            shrink-0
                                        "
                                    >

                                        <p
                                            class="
                                                font-semibold
                                                text-slate-800
                                                text-sm
                                            "
                                        >
                                            Rp <?= number_format(
                                                $pedagang[
                                                    'total_pendapatan'
                                                ],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>
                                        </p>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="p-8 text-center">

                            <i
                                class="
                                    fa-solid
                                    fa-users
                                    text-3xl
                                    text-slate-300
                                "
                            ></i>

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    mt-3
                                "
                            >
                                Belum ada data pedagang.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </section>



            <!-- =================================================
                 MENU TERLARIS + QUICK ACTION
            ================================================== -->

            <section
                class="
                    grid
                    grid-cols-1
                    lg:grid-cols-2
                    gap-5
                    sm:gap-6
                    pb-6
                "
            >

                <!-- Menu Terlaris -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        shadow-sm
                        overflow-hidden
                    "
                >

                    <div
                        class="
                            p-5
                            border-b
                            border-slate-200
                        "
                    >

                        <h2
                            class="
                                text-lg
                                font-semibold
                                text-slate-800
                            "
                        >
                            Menu Terlaris
                        </h2>

                        <p
                            class="
                                text-xs
                                sm:text-sm
                                text-slate-500
                                mt-1
                            "
                        >
                            Berdasarkan jumlah menu terjual
                        </p>

                    </div>


                    <?php if (!empty($menuTerlaris)): ?>

                        <div class="divide-y divide-slate-100">

                            <?php foreach (
                                $menuTerlaris
                                as $index => $menu
                            ): ?>

                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-3
                                        px-5
                                        py-4
                                    "
                                >

                                    <div
                                        class="
                                            flex
                                            items-center
                                            gap-3
                                            min-w-0
                                        "
                                    >

                                        <div
                                            class="
                                                w-9
                                                h-9
                                                shrink-0
                                                rounded-lg
                                                bg-orange-50
                                                text-orange-600
                                                flex
                                                items-center
                                                justify-center
                                                font-bold
                                                text-sm
                                            "
                                        >
                                            <?= $index + 1 ?>
                                        </div>


                                        <div class="min-w-0">

                                            <p
                                                class="
                                                    font-medium
                                                    text-slate-700
                                                    truncate
                                                "
                                            >
                                                <?= htmlspecialchars(
                                                    $menu['nama_menu']
                                                ) ?>
                                            </p>

                                            <p
                                                class="
                                                    text-xs
                                                    text-slate-400
                                                    mt-1
                                                    truncate
                                                "
                                            >
                                                <?= htmlspecialchars(
                                                    $menu[
                                                        'nama_pedagang'
                                                    ]
                                                ) ?>
                                            </p>

                                        </div>

                                    </div>


                                    <div
                                        class="
                                            text-right
                                            shrink-0
                                        "
                                    >

                                        <p
                                            class="
                                                font-semibold
                                                text-slate-800
                                            "
                                        >
                                            <?= number_format(
                                                $menu[
                                                    'total_terjual'
                                                ]
                                            ) ?>
                                        </p>

                                        <p
                                            class="
                                                text-xs
                                                text-slate-400
                                            "
                                        >
                                            terjual
                                        </p>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="p-8 text-center">

                            <i
                                class="
                                    fa-solid
                                    fa-chart-simple
                                    text-3xl
                                    text-slate-300
                                "
                            ></i>

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    mt-3
                                "
                            >
                                Belum ada data penjualan.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>



                <!-- Quick Action -->
                <div
                    class="
                        bg-white
                        border
                        border-slate-200
                        rounded-2xl
                        shadow-sm
                        p-5
                        sm:p-6
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            gap-3
                            mb-6
                        "
                    >

                        <div
                            class="
                                w-11
                                h-11
                                shrink-0
                                rounded-xl
                                bg-blue-100
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <i class="fa-solid fa-bolt text-blue-600"></i>
                        </div>


                        <div>

                            <h2
                                class="
                                    text-lg
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Akses Cepat
                            </h2>

                            <p
                                class="
                                    text-xs
                                    sm:text-sm
                                    text-slate-500
                                "
                            >
                                Kelola TradeTrack
                            </p>

                        </div>

                    </div>


                    <div
                        class="
                            grid
                            grid-cols-1
                            sm:grid-cols-2
                            gap-3
                        "
                    >

                        <a
                            href="../pedagang/index.php"
                            class="
                                flex
                                items-center
                                gap-3
                                p-4
                                rounded-xl
                                border
                                border-slate-200
                                hover:border-blue-300
                                hover:bg-blue-50
                                transition
                            "
                        >

                            <i class="fa-solid fa-users text-blue-600"></i>

                            <span
                                class="
                                    font-medium
                                    text-slate-700
                                    text-sm
                                "
                            >
                                Kelola Pedagang
                            </span>

                        </a>


                        <a
                            href="../kategori/index.php"
                            class="
                                flex
                                items-center
                                gap-3
                                p-4
                                rounded-xl
                                border
                                border-slate-200
                                hover:border-orange-300
                                hover:bg-orange-50
                                transition
                            "
                        >

                            <i class="fa-solid fa-tags text-orange-600"></i>

                            <span
                                class="
                                    font-medium
                                    text-slate-700
                                    text-sm
                                "
                            >
                                Kelola Kategori
                            </span>

                        </a>


                        <a
                            href="../laporan/index.php"
                            class="
                                flex
                                items-center
                                gap-3
                                p-4
                                rounded-xl
                                border
                                border-slate-200
                                hover:border-green-300
                                hover:bg-green-50
                                transition
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-chart-column
                                    text-green-600
                                "
                            ></i>

                            <span
                                class="
                                    font-medium
                                    text-slate-700
                                    text-sm
                                "
                            >
                                Laporan Penjualan
                            </span>

                        </a>


                        <a
                            href="../profile/index.php"
                            class="
                                flex
                                items-center
                                gap-3
                                p-4
                                rounded-xl
                                border
                                border-slate-200
                                hover:border-purple-300
                                hover:bg-purple-50
                                transition
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-user
                                    text-purple-600
                                "
                            ></i>

                            <span
                                class="
                                    font-medium
                                    text-slate-700
                                    text-sm
                                "
                            >
                                Profil Saya
                            </span>

                        </a>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>



<!-- =========================================================
     CHART.JS
========================================================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

/* =========================================================
   DATA PHP
========================================================= */

const dataPendapatan =
    <?= json_encode(
        $dataPendapatan,
        JSON_UNESCAPED_UNICODE
    ) ?>;

const dataMenuTerlaris =
    <?= json_encode(
        $dataMenuTerlaris,
        JSON_UNESCAPED_UNICODE
    ) ?>;


/* =========================================================
   FORMAT RUPIAH
========================================================= */

const formatRupiah = (value) => {

    return new Intl.NumberFormat('id-ID').format(value);

};


/* =========================================================
   GRAFIK PENDAPATAN
========================================================= */

const grafikPendapatan =
    document.getElementById('grafikPendapatan');


if (
    grafikPendapatan &&
    typeof Chart !== 'undefined'
) {

    new Chart(
        grafikPendapatan,
        {

            type: 'line',

            data: {

                labels: dataPendapatan.map(
                    item => {

                        const tanggal =
                            new Date(
                                item.tanggal
                            );

                        return tanggal.toLocaleDateString(
                            'id-ID',
                            {
                                day: '2-digit',
                                month: 'short'
                            }
                        );

                    }
                ),

                datasets: [

                    {

                        label: 'Pendapatan',

                        data:
                            dataPendapatan.map(
                                item =>
                                    Number(
                                        item.pendapatan
                                    )
                            ),

                        borderWidth: 2,

                        tension: 0.35,

                        fill: true,

                        pointRadius: 3,

                        pointHoverRadius: 5

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {

                    intersect: false,

                    mode: 'index'

                },

                plugins: {

                    legend: {

                        display: false

                    },

                    tooltip: {

                        callbacks: {

                            label:
                                function(context) {

                                    return 'Rp ' +
                                        formatRupiah(
                                            context.raw
                                        );

                                }

                        }

                    }

                },

                scales: {

                    x: {

                        ticks: {

                            maxTicksLimit: 7,

                            autoSkip: true,

                            maxRotation: 0

                        },

                        grid: {

                            display: false

                        }

                    },

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback:
                                function(value) {

                                    return 'Rp ' +
                                        new Intl.NumberFormat(
                                            'id-ID',
                                            {
                                                notation:
                                                    'compact'
                                            }
                                        ).format(value);

                                }

                        }

                    }

                }

            }

        }
    );

}


/* =========================================================
   GRAFIK MENU TERLARIS
========================================================= */

const grafikMenu =
    document.getElementById('grafikMenu');


if (
    grafikMenu &&
    typeof Chart !== 'undefined'
) {

    new Chart(
        grafikMenu,
        {

            type: 'bar',

            data: {

                labels:
                    dataMenuTerlaris.map(
                        item =>
                            item.nama_menu
                    ),

                datasets: [

                    {

                        label: 'Terjual',

                        data:
                            dataMenuTerlaris.map(
                                item =>
                                    Number(
                                        item.total_terjual
                                    )
                            ),

                        borderWidth: 0,

                        borderRadius: 8

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        display: false

                    },

                    tooltip: {

                        callbacks: {

                            label:
                                function(context) {

                                    return (
                                        formatRupiah(
                                            context.raw
                                        )
                                        + ' terjual'
                                    );

                                }

                        }

                    }

                },

                scales: {

                    x: {

                        ticks: {

                            maxRotation: 45,

                            minRotation: 0

                        },

                        grid: {

                            display: false

                        }

                    },

                    y: {

                        beginAtZero: true,

                        ticks: {

                            precision: 0

                        }

                    }

                }

            }

        }
    );

}

</script>


<script>

/* =========================================================
   FILTER BERTINGKAT
   PEDAGANG -> KATEGORI -> MENU
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    const filterPedagang =
        document.getElementById('filterPedagang');

    const filterKategori =
        document.getElementById('filterKategori');

    const filterMenu =
        document.getElementById('filterMenu');


    if (
        !filterPedagang ||
        !filterKategori ||
        !filterMenu
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | NILAI FILTER SAAT INI
    |--------------------------------------------------------------------------
    */

    const selectedPedagang =
        "<?= (int) $pedagangFilter ?>";

    const selectedKategori =
        "<?= (int) $kategoriFilter ?>";

    const selectedMenu =
        "<?= (int) $menuFilter ?>";


    /*
    |--------------------------------------------------------------------------
    | LOADING OPTION
    |--------------------------------------------------------------------------
    */

    function setLoading(select, text) {

        select.innerHTML = '';

        const option =
            document.createElement('option');

        option.value = '';

        option.textContent = text;

        select.appendChild(option);

        select.disabled = true;
    }


    /*
    |--------------------------------------------------------------------------
    | RESET OPTION
    |--------------------------------------------------------------------------
    */

    function resetKategori() {

        filterKategori.innerHTML = '';

        const option =
            document.createElement('option');

        option.value = '';

        option.textContent = 'Semua Kategori';

        filterKategori.appendChild(option);

        filterKategori.disabled = false;
    }


    function resetMenu() {

        filterMenu.innerHTML = '';

        const option =
            document.createElement('option');

        option.value = '';

        option.textContent = 'Semua Menu';

        filterMenu.appendChild(option);

        filterMenu.disabled = false;
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD DATA FILTER
    |--------------------------------------------------------------------------
    */

    async function loadFilterOptions(
        pedagangId = '',
        kategoriId = '',
        keepKategori = false,
        keepMenu = false
    ) {

        try {

            setLoading(
                filterKategori,
                'Memuat kategori...'
            );

            setLoading(
                filterMenu,
                'Memuat menu...'
            );


            const params =
                new URLSearchParams();


            if (pedagangId) {

                params.set(
                    'pedagang',
                    pedagangId
                );

            }


            if (kategoriId) {

                params.set(
                    'kategori',
                    kategoriId
                );

            }


            const response =
                await fetch(
                    'filter_options.php?' +
                    params.toString(),
                    {
                        method: 'GET',
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data filter.'
                );

            }


            const data =
                await response.json();


            if (!data.success) {

                throw new Error(
                    'Data filter tidak valid.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | KATEGORI
            |--------------------------------------------------------------------------
            */

            resetKategori();


            if (Array.isArray(data.kategori)) {

                data.kategori.forEach(
                    function (item) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            item.id;

                        option.textContent =
                            item.nama_kategori;


                        if (
                            keepKategori &&
                            String(item.id)
                            === String(kategoriId)
                        ) {

                            option.selected = true;

                        }


                        filterKategori.appendChild(
                            option
                        );

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | MENU
            |--------------------------------------------------------------------------
            */

            resetMenu();


            if (Array.isArray(data.menu)) {

                data.menu.forEach(
                    function (item) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            item.id;

                        option.textContent =
                            item.nama_menu;


                        if (
                            keepMenu &&
                            String(item.id)
                            === String(selectedMenu)
                        ) {

                            option.selected = true;

                        }


                        filterMenu.appendChild(
                            option
                        );

                    }
                );

            }


        } catch (error) {

            console.error(
                'Filter error:',
                error
            );


            resetKategori();
            resetMenu();


            const errorKategori =
                document.createElement(
                    'option'
                );

            errorKategori.value = '';

            errorKategori.textContent =
                'Gagal memuat kategori';


            filterKategori.innerHTML = '';

            filterKategori.appendChild(
                errorKategori
            );


            const errorMenu =
                document.createElement(
                    'option'
                );

            errorMenu.value = '';

            errorMenu.textContent =
                'Gagal memuat menu';


            filterMenu.innerHTML = '';

            filterMenu.appendChild(
                errorMenu
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PEDAGANG BERUBAH
    |--------------------------------------------------------------------------
    */

    filterPedagang.addEventListener(
        'change',
        function () {

            const pedagangId =
                this.value;


            /*
             * Ketika pedagang berubah,
             * kategori dan menu harus di-reset.
             */

            if (!pedagangId) {

                loadFilterOptions(
                    '',
                    '',
                    false,
                    false
                );

                return;

            }


            loadFilterOptions(
                pedagangId,
                '',
                false,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | KATEGORI BERUBAH
    |--------------------------------------------------------------------------
    */

    filterKategori.addEventListener(
        'change',
        function () {

            const pedagangId =
                filterPedagang.value;

            const kategoriId =
                this.value;


            /*
             * Jika tidak ada kategori,
             * tampilkan semua menu milik pedagang.
             */

            if (!kategoriId) {

                loadFilterOptions(
                    pedagangId,
                    '',
                    true,
                    false
                );

                return;

            }


            loadFilterOptions(
                pedagangId,
                kategoriId,
                true,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD AWAL
    |--------------------------------------------------------------------------
    |
    | Bagian ini penting ketika halaman dibuka
    | menggunakan filter dari URL.
    |
    | Contoh:
    |
    | index.php?pedagang=2&kategori=5&menu=10
    |
    */

    if (selectedPedagang) {

        loadFilterOptions(
            selectedPedagang,
            selectedKategori,
            true,
            true
        );

    } else {

        /*
         * Jika tidak ada pedagang,
         * tampilkan semua kategori dan menu.
         */

        loadFilterOptions(
            '',
            '',
            false,
            false
        );

    }

});

</script>

<?php require_once '../includes/footer.php'; ?>