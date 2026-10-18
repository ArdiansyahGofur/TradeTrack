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

    $where[] = 'dp.menu_id = ?';

    $params[] = $menuId;
}


$whereSql = implode(' AND ', $where);


/* =========================================================
   LAPORAN PER MENU
========================================================= */

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.nama_menu,
        k.nama_kategori,

        SUM(dp.jumlah) AS total_terjual,

        SUM(
            dp.jumlah * dp.harga
        ) AS total_pendapatan

    FROM detail_penjualan dp

    INNER JOIN penjualan p
        ON p.id = dp.penjualan_id

    INNER JOIN menu m
        ON m.id = dp.menu_id

    LEFT JOIN kategori k
        ON k.id = m.kategori_id

    WHERE {$whereSql}

    GROUP BY
        m.id,
        m.nama_menu,
        k.nama_kategori

    ORDER BY
        total_terjual DESC,
        total_pendapatan DESC
");

$stmt->execute($params);

$laporanMenu = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   TOTAL
========================================================= */

$totalTerjual = 0;
$totalPendapatan = 0;

foreach ($laporanMenu as $row) {

    $totalTerjual += (int) $row['total_terjual'];

    $totalPendapatan += (float) $row['total_pendapatan'];
}


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
                            Laporan per Menu
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
                            Lihat jumlah penjualan dan pendapatan
                            setiap menu.
                        </p>

                    </div>


                    <!-- KEMBALI -->

                    <a
                        href="index.php"

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
                        <span>←</span>

                        <span>
                            Kembali ke Laporan
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

                <!-- TOTAL ITEM -->

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
                                Total Item Terjual
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
                                    $totalTerjual,
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
                            📦
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
                        Pilih periode atau menu tertentu.
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


                        <!-- BUTTON -->

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
                                href="menu.php"

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

                <!-- TABLE HEADER -->

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
                            Penjualan Berdasarkan Menu
                        </h2>

                        <p
                            class="
                                text-xs
                                text-slate-400

                                mt-1
                            "
                        >
                            Data penjualan berdasarkan menu
                            yang dipilih.
                        </p>

                    </div>

                </div>


                <?php if (!empty($laporanMenu)): ?>


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

                                min-w-[680px]

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
                                            md:px-6

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
                                            md:px-6

                                            py-3

                                            font-semibold
                                            text-slate-600
                                        "
                                    >
                                        Menu
                                    </th>


                                    <th
                                        class="
                                            text-left

                                            px-4
                                            sm:px-5
                                            md:px-6

                                            py-3

                                            font-semibold
                                            text-slate-600
                                        "
                                    >
                                        Kategori
                                    </th>


                                    <th
                                        class="
                                            text-right

                                            px-4
                                            sm:px-5
                                            md:px-6

                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Terjual
                                    </th>


                                    <th
                                        class="
                                            text-right

                                            px-4
                                            sm:px-5
                                            md:px-6

                                            py-3

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Pendapatan
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
                                    $laporanMenu
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
                                                md:px-6

                                                py-4

                                                text-slate-500

                                                whitespace-nowrap
                                            "
                                        >
                                            <?= $index + 1 ?>
                                        </td>


                                        <!-- MENU -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                md:px-6

                                                py-4

                                                font-medium
                                                text-slate-800
                                            "
                                        >
                                            <?= htmlspecialchars(
                                                $row['nama_menu']
                                            ) ?>
                                        </td>


                                        <!-- KATEGORI -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                md:px-6

                                                py-4

                                                text-slate-500
                                            "
                                        >

                                            <?= htmlspecialchars(
                                                $row['nama_kategori']
                                                    ?? '-'
                                            ) ?>

                                        </td>


                                        <!-- TERJUAL -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                md:px-6

                                                py-4

                                                text-right
                                            "
                                        >

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    justify-center

                                                    min-w-10

                                                    px-2.5
                                                    py-1

                                                    rounded-lg

                                                    bg-blue-50
                                                    text-blue-700

                                                    font-semibold

                                                    whitespace-nowrap
                                                "
                                            >
                                                <?= number_format(
                                                    $row['total_terjual'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </span>

                                        </td>


                                        <!-- PENDAPATAN -->

                                        <td
                                            class="
                                                px-4
                                                sm:px-5
                                                md:px-6

                                                py-4

                                                text-right

                                                font-semibold
                                                text-slate-800

                                                whitespace-nowrap
                                            "
                                        >

                                            Rp <?= number_format(
                                                $row['total_pendapatan'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>


                            <!-- =================================================
                                 TOTAL
                            ================================================== -->

                            <tfoot class="bg-slate-50">

                                <tr>

                                    <td
                                        colspan="3"

                                        class="
                                            px-4
                                            sm:px-5
                                            md:px-6

                                            py-4

                                            font-bold
                                            text-slate-800

                                            whitespace-nowrap
                                        "
                                    >
                                        Total
                                    </td>


                                    <td
                                        class="
                                            px-4
                                            sm:px-5
                                            md:px-6

                                            py-4

                                            text-right

                                            font-bold
                                            text-slate-800

                                            whitespace-nowrap
                                        "
                                    >

                                        <?= number_format(
                                            $totalTerjual,
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <td
                                        class="
                                            px-4
                                            sm:px-5
                                            md:px-6

                                            py-4

                                            text-right

                                            font-bold
                                            text-blue-600

                                            whitespace-nowrap
                                        "
                                    >

                                        Rp <?= number_format(
                                            $totalPendapatan,
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>


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
                            Tidak ada data penjualan
                            yang sesuai dengan filter.
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