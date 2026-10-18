<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];


/* =========================================================
   FILTER
========================================================= */

$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalAkhir = $_GET['tanggal_akhir'] ?? '';
$menuId       = isset($_GET['menu_id']) ? (int) $_GET['menu_id'] : 0;


/* =========================================================
   DAFTAR MENU MILIK PEDAGANG
========================================================= */

$stmtMenu = $conn->prepare("
    SELECT
        id,
        nama_menu
    FROM menu
    WHERE user_id = ?
      AND status = 'aktif'
    ORDER BY nama_menu ASC
");

$stmtMenu->execute([$userId]);

$menus = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   WHERE
========================================================= */

$where = [
    'p.user_id = ?'
];

$params = [
    $userId
];


/* =========================================================
   FILTER TANGGAL MULAI
========================================================= */

if ($tanggalMulai !== '') {

    $where[] = 'p.tanggal >= ?';

    $params[] = $tanggalMulai . ' 00:00:00';
}


/* =========================================================
   FILTER TANGGAL AKHIR
========================================================= */

if ($tanggalAkhir !== '') {

    $where[] = 'p.tanggal < ?';

    $params[] = date(
        'Y-m-d',
        strtotime($tanggalAkhir . ' +1 day')
    ) . ' 00:00:00';
}


/* =========================================================
   FILTER MENU
========================================================= */

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


$whereSql = implode(' AND ', $where);


/* =========================================================
   RINGKASAN LAPORAN
========================================================= */

$stmtSummary = $conn->prepare("
    SELECT
        COUNT(*) AS total_transaksi,
        COALESCE(SUM(p.total), 0) AS total_pendapatan
    FROM penjualan p
    WHERE {$whereSql}
");

$stmtSummary->execute($params);

$summary = $stmtSummary->fetch(PDO::FETCH_ASSOC);

$totalTransaksi  = (int) ($summary['total_transaksi'] ?? 0);
$totalPendapatan = (float) ($summary['total_pendapatan'] ?? 0);


/* =========================================================
   PAGINATION
========================================================= */

$perPage = 20;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;


/* =========================================================
   HITUNG TOTAL DATA
========================================================= */

$stmtCount = $conn->prepare("
    SELECT COUNT(*)
    FROM penjualan p
    WHERE {$whereSql}
");

$stmtCount->execute($params);

$totalData = (int) $stmtCount->fetchColumn();

$totalPages = max(
    1,
    (int) ceil($totalData / $perPage)
);

$page = min($page, $totalPages);

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
        COUNT(dp.id) AS jumlah_item
    FROM penjualan p

    LEFT JOIN detail_penjualan dp
        ON p.id = dp.penjualan_id

    WHERE {$whereSql}

    GROUP BY
        p.id,
        p.tanggal,
        p.total,
        p.catatan

    ORDER BY
        p.tanggal DESC,
        p.id DESC

    LIMIT ? OFFSET ?
");


$bind = 1;

foreach ($params as $param) {

    $stmt->bindValue(
        $bind++,
        $param
    );
}


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

            md:ml-20
            md:w-[calc(100%-5rem)]

            md:pt-8
            md:pb-8

            lg:ml-64
            lg:w-[calc(100%-16rem)]

            lg:pt-8
            lg:pb-8
        "
    >

        <!-- =================================================
             CONTENT CONTAINER
        ================================================== -->

        <div
            class="
                w-full
                max-w-7xl
                mx-auto

                px-4
                sm:px-5
                md:px-6
                lg:px-8
                xl:px-10
            "
        >


            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="mb-6 lg:mb-8">

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

                    <div class="min-w-0">

                        <p
                            class="
                                text-sm
                                font-semibold
                                text-blue-600
                                mb-1
                            "
                        >
                            Penjualan
                        </p>


                        <h1
                            class="
                                text-2xl
                                sm:text-3xl

                                font-bold
                                text-slate-800

                                leading-tight
                            "
                        >
                            Laporan Penjualan
                        </h1>


                        <p
                            class="
                                text-sm
                                sm:text-base

                                text-slate-500
                                mt-1

                                max-w-xl
                            "
                        >
                            Lihat dan filter laporan penjualan Anda.
                        </p>

                    </div>


                    <!-- ANALISIS MENU -->

                    <a
                        href="menu.php"

                        class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2

                            w-full
                            sm:w-auto

                            bg-blue-600
                            hover:bg-blue-700

                            text-white
                            font-medium

                            rounded-xl

                            px-4
                            py-2.5

                            transition

                            shadow-sm

                            flex-shrink-0
                        "
                    >

                        <span>📊</span>

                        <span>
                            Analisis per Menu
                        </span>

                    </a>

                </div>

            </div>


            <!-- =================================================
                 RINGKASAN
            ================================================== -->

            <div
                class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2

                    gap-4
                    sm:gap-5

                    mb-6
                "
            >

                <!-- TOTAL TRANSAKSI -->

                <div
                    class="
                        bg-white
                        rounded-2xl

                        border
                        border-slate-200

                        shadow-sm

                        p-4
                        sm:p-5

                        min-w-0
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-3
                        "
                    >

                        <div class="min-w-0">

                            <p class="text-sm text-slate-500">
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

                        </div>


                        <div
                            class="
                                w-11
                                h-11

                                rounded-xl

                                bg-blue-50

                                flex
                                items-center
                                justify-center

                                text-xl

                                flex-shrink-0
                            "
                        >
                            🧾
                        </div>

                    </div>

                </div>


                <!-- TOTAL PENDAPATAN -->

                <div
                    class="
                        bg-white
                        rounded-2xl

                        border
                        border-slate-200

                        shadow-sm

                        p-4
                        sm:p-5

                        min-w-0
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-3
                        "
                    >

                        <div class="min-w-0">

                            <p class="text-sm text-slate-500">
                                Total Pendapatan
                            </p>

                            <p
                                class="
                                    text-xl
                                    sm:text-2xl

                                    font-bold
                                    text-blue-600

                                    mt-2

                                    truncate
                                "
                            >
                                Rp <?= number_format(
                                    $totalPendapatan,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </p>

                        </div>


                        <div
                            class="
                                w-11
                                h-11

                                rounded-xl

                                bg-blue-50

                                flex
                                items-center
                                justify-center

                                text-xl

                                flex-shrink-0
                            "
                        >
                            💰
                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 FILTER
            ================================================== -->

            <div
                class="
                    bg-white

                    rounded-2xl

                    border
                    border-slate-200

                    shadow-sm

                    p-4
                    sm:p-5
                    md:p-6

                    mb-6

                    min-w-0
                "
            >

                <div class="mb-5">

                    <h2
                        class="
                            font-semibold
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
                        Gunakan filter untuk melihat laporan tertentu.
                    </p>

                </div>


                <form method="GET">

                    <!-- =================================================
                         FILTER GRID
                    ================================================== -->

                    <div
                        class="
                            grid

                            grid-cols-1
                            sm:grid-cols-2
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
                                    font-medium
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

                                    rounded-xl

                                    border
                                    border-slate-300

                                    px-4
                                    py-2.5

                                    bg-white

                                    outline-none

                                    focus:ring-2
                                    focus:ring-blue-500

                                    focus:border-blue-500
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
                                    font-medium
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

                                    rounded-xl

                                    border
                                    border-slate-300

                                    px-4
                                    py-2.5

                                    bg-white

                                    outline-none

                                    focus:ring-2
                                    focus:ring-blue-500

                                    focus:border-blue-500
                                "
                            >

                        </div>


                        <!-- MENU -->

                        <div class="min-w-0">

                            <label
                                for="menu_id"

                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700

                                    mb-2
                                "
                            >
                                Menu
                            </label>

                            <select
                                id="menu_id"

                                name="menu_id"

                                class="
                                    w-full

                                    rounded-xl

                                    border
                                    border-slate-300

                                    px-4
                                    py-2.5

                                    bg-white

                                    outline-none

                                    focus:ring-2
                                    focus:ring-blue-500

                                    focus:border-blue-500
                                "
                            >

                                <option value="0">
                                    Semua Menu
                                </option>


                                <?php foreach ($menus as $menu): ?>

                                    <option
                                        value="<?= (int) $menu['id'] ?>"

                                        <?= $menuId === (int) $menu['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $menu['nama_menu']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- BUTTON FILTER -->

                        <div
                            class="
                                flex
                                items-end

                                gap-2

                                min-w-0
                            "
                        >

                            <button
                                type="submit"

                                class="
                                    flex-1
                                    min-w-0

                                    bg-blue-600
                                    hover:bg-blue-700

                                    text-white

                                    font-medium

                                    rounded-xl

                                    px-4
                                    py-2.5

                                    transition

                                    shadow-sm
                                "
                            >
                                Filter
                            </button>


                            <a
                                href="index.php"

                                class="
                                    flex-shrink-0

                                    bg-slate-100
                                    hover:bg-slate-200

                                    text-slate-700

                                    font-medium

                                    rounded-xl

                                    px-4
                                    py-2.5

                                    transition
                                "
                            >
                                Reset
                            </a>

                        </div>

                    </div>


                    <!-- =================================================
                         EXPORT
                    ================================================== -->

                    <div
                        class="
                            mt-5
                            pt-5

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
                                text-xs
                                sm:text-sm

                                text-slate-400

                                leading-relaxed
                            "
                        >
                            Export menggunakan filter yang sedang aktif.
                        </p>


                        <a
                            href="export.php?<?= http_build_query([
                                'tanggal_mulai' => $tanggalMulai,
                                'tanggal_akhir' => $tanggalAkhir,
                                'menu_id' => $menuId
                            ]) ?>"

                            class="
                                inline-flex
                                items-center
                                justify-center

                                gap-2

                                w-full
                                sm:w-auto

                                bg-blue-600
                                hover:bg-blue-700

                                text-white

                                font-medium

                                rounded-xl

                                px-4
                                py-2.5

                                transition

                                shadow-sm

                                flex-shrink-0
                            "
                        >

                            <span>📥</span>

                            <span>
                                Export Excel
                            </span>

                        </a>

                    </div>

                </form>

            </div>


            <!-- =================================================
                 TABEL
            ================================================== -->

            <div
                class="
                    bg-white

                    rounded-2xl

                    border
                    border-slate-200

                    shadow-sm

                    overflow-hidden

                    min-w-0
                "
            >

                <!-- =================================================
                     TABLE HEADER
                ================================================== -->

                <div
                    class="
                        px-4
                        sm:px-5
                        md:px-6

                        py-4

                        border-b
                        border-slate-200

                        flex
                        flex-col
                        sm:flex-row

                        sm:items-center
                        sm:justify-between

                        gap-2
                    "
                >

                    <div class="min-w-0">

                        <h2
                            class="
                                font-semibold
                                text-slate-800
                            "
                        >
                            Data Penjualan
                        </h2>

                        <p
                            class="
                                text-xs
                                text-slate-400

                                mt-1
                            "
                        >
                            Menampilkan <?= count($laporan) ?> data
                            pada halaman ini.
                        </p>

                    </div>

                </div>


                <?php if (!empty($laporan)): ?>


                    <!-- =================================================
                         TABLE RESPONSIVE
                    ================================================== -->

                    <div
                        class="
                            w-full
                            overflow-x-auto
                            overscroll-x-contain
                        "
                    >

                        <table
                            class="
                                w-full
                                min-w-[720px]

                                text-sm
                            "
                        >

                            <thead class="bg-slate-50">

                                <tr>

                                    <th
                                        class="
                                            text-left

                                            px-4
                                            sm:px-5
                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        No
                                    </th>


                                    <th
                                        class="
                                            text-left

                                            px-4
                                            sm:px-5
                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Tanggal
                                    </th>


                                    <th
                                        class="
                                            text-center

                                            px-4
                                            sm:px-5
                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Item
                                    </th>


                                    <th
                                        class="
                                            text-left

                                            px-4
                                            sm:px-5
                                            py-3

                                            font-semibold
                                            text-slate-600
                                        "
                                    >
                                        Catatan
                                    </th>


                                    <th
                                        class="
                                            text-right

                                            px-4
                                            sm:px-5
                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Total
                                    </th>


                                    <th
                                        class="
                                            text-center

                                            px-4
                                            sm:px-5
                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
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
                                                px-4
                                                sm:px-5
                                                py-4

                                                text-slate-500
                                            "
                                        >
                                            <?= $offset + $index + 1 ?>
                                        </td>


                                        <!-- TANGGAL -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                py-4
                                            "
                                        >

                                            <div
                                                class="
                                                    font-medium
                                                    text-slate-800
                                                    whitespace-nowrap
                                                "
                                            >
                                                <?= date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $row['tanggal']
                                                    )
                                                ) ?>
                                            </div>

                                            <div
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
                                            </div>

                                        </td>


                                        <!-- ITEM -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                py-4

                                                text-center
                                            "
                                        >

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    justify-center

                                                    min-w-8
                                                    h-8

                                                    px-2

                                                    rounded-lg

                                                    bg-blue-50
                                                    text-blue-700

                                                    font-semibold
                                                "
                                            >
                                                <?= (int) $row['jumlah_item'] ?>
                                            </span>

                                        </td>


                                        <!-- CATATAN -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                py-4

                                                text-slate-600

                                                max-w-[280px]
                                            "
                                        >

                                            <?php if (
                                                !empty(
                                                    $row['catatan']
                                                )
                                            ): ?>

                                                <span
                                                    class="
                                                        block
                                                        truncate
                                                    "
                                                >
                                                    <?= htmlspecialchars(
                                                        $row['catatan']
                                                    ) ?>
                                                </span>

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
                                                px-4
                                                sm:px-5
                                                py-4

                                                text-right

                                                whitespace-nowrap
                                            "
                                        >

                                            <span
                                                class="
                                                    font-semibold
                                                    text-slate-800
                                                "
                                            >
                                                Rp <?= number_format(
                                                    $row['total'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </span>

                                        </td>


                                        <!-- AKSI -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                py-4

                                                text-center
                                            "
                                        >

                                            <a
                                                href="../penjualan/detail.php?id=<?= (int) $row['id'] ?>"

                                                class="
                                                    inline-flex
                                                    items-center
                                                    justify-center

                                                    px-3
                                                    py-2

                                                    rounded-lg

                                                    bg-blue-50
                                                    hover:bg-blue-100

                                                    text-blue-700

                                                    font-medium

                                                    transition

                                                    whitespace-nowrap
                                                "
                                            >
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
                                px-4
                                sm:px-5
                                md:px-6

                                py-4

                                border-t
                                border-slate-200

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
                                        font-medium
                                        text-slate-700
                                    "
                                >
                                    <?= $page ?>
                                </span>

                                dari

                                <span
                                    class="
                                        font-medium
                                        text-slate-700
                                    "
                                >
                                    <?= $totalPages ?>
                                </span>

                            </p>


                            <div
                                class="
                                    flex
                                    items-center
                                    gap-2

                                    w-full
                                    sm:w-auto
                                "
                            >

                                <?php if ($page > 1): ?>

                                    <a
                                        href="?<?= http_build_query([
                                            'tanggal_mulai' => $tanggalMulai,
                                            'tanggal_akhir' => $tanggalAkhir,
                                            'menu_id' => $menuId,
                                            'page' => $page - 1
                                        ]) ?>"

                                        class="
                                            flex-1
                                            sm:flex-none

                                            text-center

                                            px-3
                                            py-2

                                            rounded-lg

                                            bg-slate-100
                                            hover:bg-slate-200

                                            text-slate-700

                                            text-sm

                                            transition

                                            whitespace-nowrap
                                        "
                                    >
                                        Sebelumnya
                                    </a>

                                <?php endif; ?>


                                <?php if ($page < $totalPages): ?>

                                    <a
                                        href="?<?= http_build_query([
                                            'tanggal_mulai' => $tanggalMulai,
                                            'tanggal_akhir' => $tanggalAkhir,
                                            'menu_id' => $menuId,
                                            'page' => $page + 1
                                        ]) ?>"

                                        class="
                                            flex-1
                                            sm:flex-none

                                            text-center

                                            px-3
                                            py-2

                                            rounded-lg

                                            bg-blue-600
                                            hover:bg-blue-700

                                            text-white

                                            text-sm

                                            transition

                                            whitespace-nowrap
                                        "
                                    >
                                        Berikutnya
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

                        <div class="text-4xl mb-3">
                            📊
                        </div>


                        <h3
                            class="
                                font-semibold
                                text-slate-800
                            "
                        >
                            Belum ada data
                        </h3>


                        <p
                            class="
                                text-sm
                                text-slate-500

                                mt-1
                            "
                        >
                            Tidak ada penjualan yang sesuai
                            dengan filter.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>


<?php

require_once '../includes/footer.php';

?>