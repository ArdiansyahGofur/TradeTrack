<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


/* =========================================================
   FILTER
========================================================= */

$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalAkhir = $_GET['tanggal_akhir'] ?? '';

$pedagangId = isset($_GET['pedagang_id'])
    ? (int) $_GET['pedagang_id']
    : 0;

$menuId = isset($_GET['menu_id'])
    ? (int) $_GET['menu_id']
    : 0;


/* =========================================================
   DAFTAR PEDAGANG
========================================================= */

$stmtPedagang = $conn->query("
    SELECT
        id,
        nama
    FROM users
    WHERE role = 'pedagang'
    ORDER BY nama ASC
");

$pedagangList = $stmtPedagang->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   DAFTAR MENU
========================================================= */

$stmtMenu = $conn->query("
    SELECT
        id,
        nama_menu,
        user_id
    FROM menu
    WHERE status = 'aktif'
    ORDER BY nama_menu ASC
");

$menuList = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   WHERE
========================================================= */

$where = [];
$params = [];


/* Tanggal mulai */

if ($tanggalMulai !== '') {

    $where[] = 'p.tanggal >= ?';

    $params[] = $tanggalMulai . ' 00:00:00';
}


/* Tanggal akhir */

if ($tanggalAkhir !== '') {

    $where[] = 'p.tanggal < ?';

    $params[] = date(
        'Y-m-d',
        strtotime($tanggalAkhir . ' +1 day')
    ) . ' 00:00:00';
}


/* Pedagang */

if ($pedagangId > 0) {

    $where[] = 'p.user_id = ?';

    $params[] = $pedagangId;
}


/* Menu */

if ($menuId > 0) {

    $where[] = "
        EXISTS (
            SELECT 1
            FROM detail_penjualan dp_menu
            WHERE dp_menu.penjualan_id = p.id
              AND dp_menu.menu_id = ?
        )
    ";

    $params[] = $menuId;
}


$whereSql = '';

if (!empty($where)) {

    $whereSql = 'WHERE ' . implode(' AND ', $where);
}


/* =========================================================
   RINGKASAN
========================================================= */

$stmtSummary = $conn->prepare("
    SELECT
        COUNT(*) AS total_transaksi,
        COALESCE(SUM(p.total), 0) AS total_pendapatan

    FROM penjualan p

    {$whereSql}
");

$stmtSummary->execute($params);

$summary = $stmtSummary->fetch(PDO::FETCH_ASSOC);

$totalTransaksi = (int) (
    $summary['total_transaksi'] ?? 0
);

$totalPendapatan = (float) (
    $summary['total_pendapatan'] ?? 0
);


/* =========================================================
   PAGINATION
========================================================= */

$perPage = 20;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;


/* =========================================================
   TOTAL DATA
========================================================= */

$stmtCount = $conn->prepare("
    SELECT COUNT(*)

    FROM penjualan p

    {$whereSql}
");

$stmtCount->execute($params);

$totalData = (int) $stmtCount->fetchColumn();

$totalPages = max(
    1,
    (int) ceil($totalData / $perPage)
);

$page = min(
    $page,
    $totalPages
);

$offset = ($page - 1) * $perPage;


/* =========================================================
   DATA LAPORAN
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
        ON u.id = p.user_id

    LEFT JOIN detail_penjualan dp
        ON dp.penjualan_id = p.id

    {$whereSql}

    GROUP BY
        p.id,
        p.tanggal,
        p.total,
        p.catatan,
        u.nama

    ORDER BY
        p.tanggal DESC,
        p.id DESC

    LIMIT ? OFFSET ?
");


$bind = 1;


/* Parameter filter */

foreach ($params as $param) {

    $stmt->bindValue(
        $bind++,
        $param
    );
}


/* Pagination */

$stmt->bindValue(
    $bind++,
    $perPage,
    PDO::PARAM_INT
);

$stmt->bindValue(
    $bind++,
    $offset,
    PDO::PARAM_INT
);

$stmt->execute();

$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   HEADER
========================================================= */

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

    <main
        class="
            min-h-screen
            min-w-0

            pt-20
            pb-24

            md:ml-20
            md:w-[calc(100%-5rem)]

            lg:ml-64
            lg:w-[calc(100%-16rem)]

            lg:pt-8
            lg:pb-8
        "
    >

        <div
            class="
                w-full
                max-w-7xl
                mx-auto

                px-4
                sm:px-6
                md:px-6
                lg:px-8
                xl:px-10
            "
        >


            <!-- =====================================================
                 HEADER
            ====================================================== -->

            <div
                class="
                    flex
                    flex-col
                    sm:flex-row

                    sm:items-center
                    sm:justify-between

                    gap-4

                    mb-6
                "
            >

                <div class="min-w-0">

                    <p
                        class="
                            text-sm
                            text-slate-400
                            mb-1
                        "
                    >
                        Manajemen
                    </p>

                    <h1
                        class="
                            text-2xl
                            sm:text-3xl

                            font-bold

                            text-slate-800
                        "
                    >
                        Laporan Penjualan
                    </h1>

                    <p
                        class="
                            text-sm
                            text-slate-500
                            mt-1
                        "
                    >
                        Analisis transaksi penjualan pedagang.
                    </p>

                </div>


                <!-- USER -->

                <div
                    class="
                        hidden
                        sm:flex

                        items-center
                        gap-3

                        bg-white

                        border
                        border-slate-200

                        rounded-xl

                        px-4
                        py-2.5

                        shadow-sm

                        shrink-0
                    "
                >

                    <div
                        class="
                            w-9
                            h-9

                            rounded-lg

                            bg-blue-100
                            text-blue-600

                            flex
                            items-center
                            justify-center

                            shrink-0
                        "
                    >

                        <i
                            class="
                                fa-solid
                                fa-user
                                text-sm
                            "
                        ></i>

                    </div>


                    <div class="min-w-0">

                        <p
                            class="
                                text-xs
                                text-slate-400
                            "
                        >
                            Login sebagai
                        </p>

                        <p
                            class="
                                text-sm
                                font-semibold
                                text-slate-700

                                truncate
                                max-w-[160px]
                            "
                        >
                            <?= htmlspecialchars(
                                $_SESSION['nama'] ?? 'Admin'
                            ) ?>
                        </p>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 SUMMARY
            ====================================================== -->

            <div
                class="
                    grid

                    grid-cols-1
                    sm:grid-cols-2

                    gap-4

                    mb-6
                "
            >

                <!-- TRANSAKSI -->

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
                            justify-between
                            gap-4
                        "
                    >

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                Total Transaksi
                            </p>

                            <p
                                class="
                                    text-2xl
                                    sm:text-3xl

                                    font-bold

                                    text-slate-800

                                    mt-2
                                "
                            >
                                <?= number_format(
                                    $totalTransaksi,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </p>

                            <p
                                class="
                                    text-xs
                                    text-slate-400
                                    mt-1
                                "
                            >
                                Transaksi sesuai filter
                            </p>

                        </div>


                        <div
                            class="
                                w-12
                                h-12

                                shrink-0

                                rounded-xl

                                bg-blue-50
                                text-blue-600

                                flex
                                items-center
                                justify-center
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-receipt
                                "
                            ></i>

                        </div>

                    </div>

                </div>


                <!-- PENDAPATAN -->

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
                            justify-between
                            gap-4
                        "
                    >

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                Total Pendapatan
                            </p>

                            <p
                                class="
                                    text-2xl
                                    sm:text-3xl

                                    font-bold

                                    text-emerald-600

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
                            </p>

                            <p
                                class="
                                    text-xs
                                    text-slate-400
                                    mt-1
                                "
                            >
                                Berdasarkan filter aktif
                            </p>

                        </div>


                        <div
                            class="
                                w-12
                                h-12

                                shrink-0

                                rounded-xl

                                bg-emerald-50
                                text-emerald-600

                                flex
                                items-center
                                justify-center
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-money-bill-wave
                                "
                            ></i>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 FILTER
            ====================================================== -->

            <div
                class="
                    bg-white

                    border
                    border-slate-200

                    rounded-2xl

                    shadow-sm

                    p-5
                    sm:p-6

                    mb-6
                "
            >

                <div
                    class="
                        flex
                        items-start
                        gap-3

                        mb-5
                    "
                >

                    <div
                        class="
                            w-10
                            h-10

                            shrink-0

                            rounded-xl

                            bg-emerald-50
                            text-emerald-600

                            flex
                            items-center
                            justify-center
                        "
                    >

                        <i
                            class="
                                fa-solid
                                fa-filter
                            "
                        ></i>

                    </div>


                    <div class="min-w-0">

                        <h2
                            class="
                                font-bold
                                text-slate-800
                            "
                        >
                            Filter Laporan
                        </h2>

                        <p
                            class="
                                text-sm
                                text-slate-500
                                mt-1
                            "
                        >
                            Kombinasikan filter untuk mendapatkan
                            laporan tertentu.
                        </p>

                    </div>

                </div>


                <form
                    method="GET"
                    class="
                        grid

                        grid-cols-1

                        sm:grid-cols-2

                        md:grid-cols-2

                        lg:grid-cols-4

                        gap-4
                    "
                >


                    <!-- TANGGAL MULAI -->

                    <div class="min-w-0">

                        <label
                            for="tanggal_mulai"
                            class="
                                block

                                text-sm
                                font-semibold

                                text-slate-700

                                mb-2
                            "
                        >
                            Dari Tanggal
                        </label>

                        <input
                            id="tanggal_mulai"
                            type="date"
                            name="tanggal_mulai"
                            value="<?= htmlspecialchars(
                                $tanggalMulai
                            ) ?>"
                            class="
                                w-full
                                min-w-0

                                rounded-xl

                                border
                                border-slate-300

                                px-4
                                py-3

                                text-sm
                                text-slate-700

                                bg-white

                                focus:outline-none
                                focus:ring-2
                                focus:ring-emerald-100
                                focus:border-emerald-500

                                transition
                            "
                        >

                    </div>


                    <!-- TANGGAL AKHIR -->

                    <div class="min-w-0">

                        <label
                            for="tanggal_akhir"
                            class="
                                block

                                text-sm
                                font-semibold

                                text-slate-700

                                mb-2
                            "
                        >
                            Sampai Tanggal
                        </label>

                        <input
                            id="tanggal_akhir"
                            type="date"
                            name="tanggal_akhir"
                            value="<?= htmlspecialchars(
                                $tanggalAkhir
                            ) ?>"
                            class="
                                w-full
                                min-w-0

                                rounded-xl

                                border
                                border-slate-300

                                px-4
                                py-3

                                text-sm
                                text-slate-700

                                bg-white

                                focus:outline-none
                                focus:ring-2
                                focus:ring-emerald-100
                                focus:border-emerald-500

                                transition
                            "
                        >

                    </div>


                    <!-- PEDAGANG -->

                    <div class="min-w-0">

                        <label
                            for="pedagang_id"
                            class="
                                block

                                text-sm
                                font-semibold

                                text-slate-700

                                mb-2
                            "
                        >
                            Pedagang
                        </label>

                        <select
                            id="pedagang_id"
                            name="pedagang_id"
                            class="
                                w-full
                                min-w-0

                                rounded-xl

                                border
                                border-slate-300

                                px-4
                                py-3

                                text-sm
                                text-slate-700

                                bg-white

                                focus:outline-none
                                focus:ring-2
                                focus:ring-emerald-100
                                focus:border-emerald-500

                                transition

                                truncate
                            "
                        >

                            <option value="0">
                                Semua Pedagang
                            </option>

                            <?php foreach (
                                $pedagangList
                                as $pedagang
                            ): ?>

                                <option
                                    value="<?= (int) $pedagang['id'] ?>"
                                    <?= $pedagangId ===
                                        (int) $pedagang['id']
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $pedagang['nama']
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- MENU -->

                    <div class="min-w-0">

                        <label
                            for="menu_id"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                                mb-2
                            "
                        >
                            Menu
                        </label>

                        <select
                            id="menu_id"
                            name="menu_id"
                            data-selected="<?= $menuId ?>"
                            class="
                                w-full
                                min-w-0

                                rounded-xl

                                border
                                border-slate-300

                                px-4
                                py-3

                                text-sm
                                text-slate-700

                                bg-white

                                focus:outline-none
                                focus:ring-2
                                focus:ring-emerald-100
                                focus:border-emerald-500

                                transition

                                truncate
                            "
                        >

                            <option value="0">
                                Semua Menu
                            </option>

                            <?php foreach ($menuList as $menu): ?>

                                <option
                                    value="<?= (int) $menu['id'] ?>"
                                    data-pedagang="<?= (int) $menu['user_id'] ?>"
                                    <?= $menuId === (int) $menu['id']
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars($menu['nama_menu']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- BUTTON -->

                    <div
                        class="
                            sm:col-span-2
                            lg:col-span-4
                        "
                    >

                        <div
                            class="
                                flex
                                flex-col

                                sm:flex-row

                                gap-3

                                pt-1
                            "
                        >

                            <!-- FILTER -->

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    w-full
                                    sm:w-auto

                                    px-5
                                    py-3

                                    rounded-xl

                                    bg-emerald-600
                                    hover:bg-emerald-700

                                    text-white

                                    text-sm
                                    font-semibold

                                    transition
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-filter
                                    "
                                ></i>

                                Terapkan Filter

                            </button>


                            <!-- RESET -->

                            <a
                                href="index.php"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    w-full
                                    sm:w-auto

                                    px-5
                                    py-3

                                    rounded-xl

                                    bg-slate-100
                                    hover:bg-slate-200

                                    text-slate-700

                                    text-sm
                                    font-semibold

                                    transition
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-rotate-left
                                    "
                                ></i>

                                Reset

                            </a>


                            <!-- EXPORT -->

                            <a
                                href="export.php?<?= http_build_query([
                                    'tanggal_mulai'
                                        => $tanggalMulai,

                                    'tanggal_akhir'
                                        => $tanggalAkhir,

                                    'pedagang_id'
                                        => $pedagangId,

                                    'menu_id'
                                        => $menuId
                                ]) ?>"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    w-full
                                    sm:w-auto

                                    px-5
                                    py-3

                                    rounded-xl

                                    bg-green-600
                                    hover:bg-green-700

                                    text-white

                                    text-sm
                                    font-semibold

                                    transition
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-file-excel
                                    "
                                ></i>

                                Export Excel

                            </a>

                        </div>

                    </div>

                </form>

            </div>


            <!-- =====================================================
                 DATA LAPORAN
            ====================================================== -->

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

                <!-- CARD HEADER -->

                <div
                    class="
                        px-5
                        sm:px-6

                        py-5

                        border-b
                        border-slate-100

                        flex
                        flex-col

                        sm:flex-row

                        sm:items-center
                        sm:justify-between

                        gap-3
                    "
                >

                    <div class="min-w-0">

                        <h2
                            class="
                                font-bold
                                text-slate-800
                            "
                        >
                            Data Penjualan
                        </h2>

                        <p
                            class="
                                text-sm
                                text-slate-400

                                mt-1
                            "
                        >
                            <?= number_format(
                                $totalData,
                                0,
                                ',',
                                '.'
                            ) ?>
                            transaksi ditemukan
                        </p>

                    </div>


                    <?php if (
                        $tanggalMulai !== '' ||
                        $tanggalAkhir !== '' ||
                        $pedagangId > 0 ||
                        $menuId > 0
                    ): ?>

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-2

                                w-fit

                                px-3
                                py-1.5

                                rounded-lg

                                bg-emerald-50
                                text-emerald-600

                                text-xs
                                font-semibold

                                shrink-0
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-filter
                                "
                            ></i>

                            Filter aktif

                        </span>

                    <?php endif; ?>

                </div>


                <!-- =================================================
                     TABLE
                ================================================== -->

                <?php if (!empty($laporan)): ?>

                    <div
                        class="
                            w-full
                            overflow-x-auto
                        "
                    >

                        <table
                            class="
                                w-full

                                min-w-[900px]

                                text-sm
                            "
                        >

                            <thead
                                class="
                                    bg-slate-50
                                "
                            >

                                <tr>

                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-left

                                            text-xs
                                            font-semibold

                                            text-slate-500

                                            w-16
                                        "
                                    >
                                        No
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-left

                                            text-xs
                                            font-semibold

                                            text-slate-500
                                        "
                                    >
                                        Tanggal
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-left

                                            text-xs
                                            font-semibold

                                            text-slate-500
                                        "
                                    >
                                        Pedagang
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-center

                                            text-xs
                                            font-semibold

                                            text-slate-500
                                        "
                                    >
                                        Item
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-left

                                            text-xs
                                            font-semibold

                                            text-slate-500
                                        "
                                    >
                                        Catatan
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-right

                                            text-xs
                                            font-semibold

                                            text-slate-500
                                        "
                                    >
                                        Total
                                    </th>


                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-3

                                            text-center

                                            text-xs
                                            font-semibold

                                            text-slate-500
                                        "
                                    >
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody
                                class="
                                    divide-y
                                    divide-slate-100
                                "
                            >

                            <?php foreach (
                                $laporan
                                as $index => $row
                            ): ?>

                                <tr
                                    class="
                                        hover:bg-slate-50
                                        transition
                                    "
                                >

                                    <!-- NO -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-sm
                                            text-slate-500
                                        "
                                    >
                                        <?= $offset + $index + 1 ?>
                                    </td>


                                    <!-- TANGGAL -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4
                                        "
                                    >

                                        <div
                                            class="
                                                flex
                                                items-center
                                                gap-2
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
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-regular
                                                        fa-calendar
                                                        text-xs
                                                    "
                                                ></i>

                                            </div>


                                            <div>

                                                <p
                                                    class="
                                                        font-semibold
                                                        text-slate-700
                                                    "
                                                >
                                                    <?= date(
                                                        'd/m/Y',
                                                        strtotime(
                                                            $row['tanggal']
                                                        )
                                                    ) ?>
                                                </p>

                                                <p
                                                    class="
                                                        text-xs
                                                        text-slate-400
                                                        mt-0.5
                                                    "
                                                >
                                                    <?= date(
                                                        'H:i',
                                                        strtotime(
                                                            $row['tanggal']
                                                        )
                                                    ) ?>
                                                    WIB
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- PEDAGANG -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4
                                        "
                                    >

                                        <div
                                            class="
                                                flex
                                                items-center
                                                gap-2
                                            "
                                        >

                                            <div
                                                class="
                                                    w-9
                                                    h-9

                                                    shrink-0

                                                    rounded-lg

                                                    bg-indigo-50
                                                    text-indigo-600

                                                    flex
                                                    items-center
                                                    justify-center
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-solid
                                                        fa-user
                                                        text-xs
                                                    "
                                                ></i>

                                            </div>


                                            <span
                                                class="
                                                    font-semibold
                                                    text-slate-700
                                                "
                                            >
                                                <?= htmlspecialchars(
                                                    $row['nama_pedagang']
                                                ) ?>
                                            </span>

                                        </div>

                                    </td>


                                    <!-- ITEM -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-center
                                        "
                                    >

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                justify-center

                                                min-w-9
                                                h-9
                                                px-2

                                                rounded-lg

                                                bg-emerald-50
                                                text-emerald-700

                                                font-semibold
                                            "
                                        >
                                            <?= (int) $row['jumlah_item'] ?>
                                        </span>

                                    </td>


                                    <!-- CATATAN -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            max-w-xs
                                        "
                                    >

                                        <?php if (
                                            !empty($row['catatan'])
                                        ): ?>

                                            <p
                                                class="
                                                    text-slate-600

                                                    truncate

                                                    max-w-[220px]
                                                "
                                                title="<?= htmlspecialchars(
                                                    $row['catatan']
                                                ) ?>"
                                            >
                                                <?= htmlspecialchars(
                                                    $row['catatan']
                                                ) ?>
                                            </p>

                                        <?php else: ?>

                                            <span
                                                class="
                                                    text-slate-400
                                                "
                                            >
                                                -
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- TOTAL -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-right

                                            font-bold
                                            text-slate-800

                                            whitespace-nowrap
                                        "
                                    >

                                        Rp <?= number_format(
                                            $row['total'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- AKSI -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-center
                                        "
                                    >

                                        <a
                                            href="detail.php?id=<?= (int) $row['id'] ?>"
                                            class="
                                                inline-flex
                                                items-center
                                                justify-center
                                                gap-2

                                                px-3
                                                py-2

                                                rounded-lg

                                                bg-slate-100
                                                hover:bg-slate-200

                                                text-slate-700

                                                text-sm
                                                font-semibold

                                                transition
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-eye
                                                    text-xs
                                                "
                                            ></i>

                                            Detail

                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- =================================================
                         PAGINATION
                    ================================================== -->

                    <?php if ($totalPages > 1): ?>

                        <div
                            class="
                                px-5
                                sm:px-6

                                py-4

                                border-t
                                border-slate-100

                                flex
                                flex-col

                                sm:flex-row

                                sm:items-center
                                sm:justify-between

                                gap-3
                            "
                        >

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >

                                Halaman

                                <span
                                    class="
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    <?= $page ?>
                                </span>

                                dari

                                <span
                                    class="
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    <?= $totalPages ?>
                                </span>

                            </p>


                            <div
                                class="
                                    flex
                                    flex-wrap
                                    items-center
                                    gap-2
                                "
                            >

                                <?php if ($page > 1): ?>

                                    <a
                                        href="?<?= http_build_query([
                                            'tanggal_mulai'
                                                => $tanggalMulai,

                                            'tanggal_akhir'
                                                => $tanggalAkhir,

                                            'pedagang_id'
                                                => $pedagangId,

                                            'menu_id'
                                                => $menuId,

                                            'page'
                                                => $page - 1
                                        ]) ?>"
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2

                                            px-3
                                            py-2

                                            rounded-lg

                                            bg-slate-100
                                            hover:bg-slate-200

                                            text-slate-700

                                            text-sm
                                            font-medium

                                            transition
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-chevron-left
                                                text-xs
                                            "
                                        ></i>

                                        Sebelumnya

                                    </a>

                                <?php endif; ?>


                                <?php if (
                                    $page < $totalPages
                                ): ?>

                                    <a
                                        href="?<?= http_build_query([
                                            'tanggal_mulai'
                                                => $tanggalMulai,

                                            'tanggal_akhir'
                                                => $tanggalAkhir,

                                            'pedagang_id'
                                                => $pedagangId,

                                            'menu_id'
                                                => $menuId,

                                            'page'
                                                => $page + 1
                                        ]) ?>"
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2

                                            px-3
                                            py-2

                                            rounded-lg

                                            bg-emerald-600
                                            hover:bg-emerald-700

                                            text-white

                                            text-sm
                                            font-medium

                                            transition
                                        "
                                    >

                                        Berikutnya

                                        <i
                                            class="
                                                fa-solid
                                                fa-chevron-right
                                                text-xs
                                            "
                                        ></i>

                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>


                <?php else: ?>


                    <!-- =================================================
                         EMPTY STATE
                    ================================================== -->

                    <div
                        class="
                            px-5
                            py-16

                            text-center
                        "
                    >

                        <div
                            class="
                                w-14
                                h-14

                                mx-auto
                                mb-4

                                rounded-2xl

                                bg-slate-100
                                text-slate-400

                                flex
                                items-center
                                justify-center
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-chart-column
                                    text-xl
                                "
                            ></i>

                        </div>


                        <h3
                            class="
                                font-semibold
                                text-slate-700
                            "
                        >
                            Belum ada data
                        </h3>


                        <p
                            class="
                                text-sm
                                text-slate-400

                                mt-1
                            "
                        >
                            Tidak ada penjualan yang sesuai
                            dengan filter.
                        </p>


                        <?php if (
                            $tanggalMulai !== '' ||
                            $tanggalAkhir !== '' ||
                            $pedagangId > 0 ||
                            $menuId > 0
                        ): ?>

                            <a
                                href="index.php"
                                class="
                                    inline-flex
                                    items-center
                                    gap-2

                                    mt-5

                                    px-4
                                    py-2.5

                                    rounded-xl

                                    bg-slate-100
                                    hover:bg-slate-200

                                    text-slate-700

                                    text-sm
                                    font-semibold

                                    transition
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-rotate-left
                                    "
                                ></i>

                                Reset Filter

                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =====================================================
                 FOOTER INFO
            ====================================================== -->

            <div
                class="
                    mt-5

                    flex
                    flex-col

                    sm:flex-row

                    sm:items-center
                    sm:justify-between

                    gap-2

                    text-xs
                    text-slate-400
                "
            >

                <p>
                    TradeTrack · Laporan Penjualan
                </p>

                <p>

                    Menampilkan

                    <span
                        class="
                            font-semibold
                            text-slate-500
                        "
                    >
                        <?= count($laporan) ?>
                    </span>

                    dari

                    <span
                        class="
                            font-semibold
                            text-slate-500
                        "
                    >
                        <?= number_format(
                            $totalData,
                            0,
                            ',',
                            '.'
                        ) ?>
                    </span>

                    transaksi

                </p>

            </div>


        </div>

    </main>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const pedagangSelect = document.getElementById('pedagang_id');
    const menuSelect = document.getElementById('menu_id');

    if (!pedagangSelect || !menuSelect) {
        return;
    }

    const allMenuOptions = Array.from(
        menuSelect.querySelectorAll('option[data-pedagang]')
    ).map(option => ({
        value: option.value,
        text: option.textContent.trim(),
        pedagang: option.dataset.pedagang
    }));


    function updateMenu() {

        const pedagangId = pedagangSelect.value;
        const currentMenu = menuSelect.value;

        menuSelect.innerHTML = '';

        /*
        |--------------------------------------------------------------------------
        | SEMUA MENU
        |--------------------------------------------------------------------------
        */

        const allOption = document.createElement('option');

        allOption.value = '0';
        allOption.textContent = 'Semua Menu';

        menuSelect.appendChild(allOption);


        /*
        |--------------------------------------------------------------------------
        | FILTER MENU
        |--------------------------------------------------------------------------
        */

        allMenuOptions.forEach(function (menu) {

            if (
                pedagangId !== '0' &&
                menu.pedagang !== pedagangId
            ) {
                return;
            }

            const option = document.createElement('option');

            option.value = menu.value;
            option.textContent = menu.text;

            menuSelect.appendChild(option);
        });


        /*
        |--------------------------------------------------------------------------
        | PERTAHANKAN MENU JIKA MASIH SESUAI
        |--------------------------------------------------------------------------
        */

        const menuMasihTersedia = Array.from(
            menuSelect.options
        ).some(function (option) {

            return option.value === currentMenu;
        });


        if (menuMasihTersedia) {

            menuSelect.value = currentMenu;

        } else {

            menuSelect.value = '0';
        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT PEDAGANG
    |--------------------------------------------------------------------------
    */

    pedagangSelect.addEventListener(
        'change',
        updateMenu
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD AWAL
    |--------------------------------------------------------------------------
    */

    const selectedMenu =
        menuSelect.dataset.selected || '0';

    updateMenu();

    /*
     * Setelah filter awal selesai,
     * kembalikan menu dari URL jika masih valid.
     */

    if (
        selectedMenu !== '0' &&
        Array.from(menuSelect.options).some(
            option => option.value === selectedMenu
        )
    ) {
        menuSelect.value = selectedMenu;
    }

});
</script>

<?php

require_once '../includes/footer.php';

?>