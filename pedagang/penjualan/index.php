<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];

$pageTitle = 'Penjualan';


/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalAkhir = $_GET['tanggal_akhir'] ?? '';

$menuId = isset($_GET['menu_id'])
    ? (int) $_GET['menu_id']
    : 0;


/*
|--------------------------------------------------------------------------
| DAFTAR MENU
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$perPage = 20;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;


/*
|--------------------------------------------------------------------------
| WHERE
|--------------------------------------------------------------------------
*/

$where = [
    'p.user_id = ?'
];

$params = [
    $userId
];


/*
|--------------------------------------------------------------------------
| FILTER TANGGAL MULAI
|--------------------------------------------------------------------------
*/

if ($tanggalMulai !== '') {

    $where[] = 'p.tanggal >= ?';

    $params[] = $tanggalMulai . ' 00:00:00';
}


/*
|--------------------------------------------------------------------------
| FILTER TANGGAL AKHIR
|--------------------------------------------------------------------------
*/

if ($tanggalAkhir !== '') {

    $where[] = 'p.tanggal < ?';

    $params[] = date(
        'Y-m-d',
        strtotime($tanggalAkhir . ' +1 day')
    ) . ' 00:00:00';
}


/*
|--------------------------------------------------------------------------
| FILTER MENU
|--------------------------------------------------------------------------
*/

if ($menuId > 0) {

    $where[] = "
        EXISTS (
            SELECT 1
            FROM detail_penjualan dp_menu

            INNER JOIN menu m_menu
                ON m_menu.id = dp_menu.menu_id

            WHERE dp_menu.penjualan_id = p.id
              AND dp_menu.menu_id = ?
              AND m_menu.user_id = ?
        )
    ";

    $params[] = $menuId;
    $params[] = $userId;
}


$whereSql = implode(' AND ', $where);


/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

$stmtStats = $conn->prepare("
    SELECT
        COUNT(*) AS total_transaksi,
        COALESCE(SUM(p.total), 0) AS total_pendapatan

    FROM penjualan p

    WHERE {$whereSql}
");

$stmtStats->execute($params);

$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

$totalTransaksi = (int) (
    $stats['total_transaksi'] ?? 0
);

$totalPendapatan = (float) (
    $stats['total_pendapatan'] ?? 0
);


/*
|--------------------------------------------------------------------------
| TOTAL HALAMAN
|--------------------------------------------------------------------------
*/

$totalPages = max(
    1,
    (int) ceil(
        $totalTransaksi / $perPage
    )
);


if ($page > $totalPages) {

    $page = $totalPages;

}


$offset = ($page - 1) * $perPage;


/*
|--------------------------------------------------------------------------
| AMBIL TRANSAKSI
|--------------------------------------------------------------------------
*/

$sql = "
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
";


$stmt = $conn->prepare($sql);

$bind = 1;


/*
|--------------------------------------------------------------------------
| BIND PARAMETER
|--------------------------------------------------------------------------
*/

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

$penjualan = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);


/*
|--------------------------------------------------------------------------
| URL PAGINATION
|--------------------------------------------------------------------------
*/

$queryParams = [];


if ($tanggalMulai !== '') {

    $queryParams['tanggal_mulai']
        = $tanggalMulai;

}


if ($tanggalAkhir !== '') {

    $queryParams['tanggal_akhir']
        = $tanggalAkhir;

}


if ($menuId > 0) {

    $queryParams['menu_id']
        = $menuId;

}


/*
|--------------------------------------------------------------------------
| FUNGSI URL PAGINATION
|--------------------------------------------------------------------------
*/

function halamanUrl(
    int $halaman,
    array $queryParams
): string {

    $queryParams['page'] = $halaman;

    return '?' . http_build_query(
        $queryParams
    );

}


require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<!-- =========================================================
     PAGE WRAPPER
========================================================= -->

<div
    class="
        min-h-screen
        w-full
        bg-slate-50
        overflow-x-hidden
    "
>


    <!-- =====================================================
         MAIN AREA

         Mobile:
         Full width

         Tablet:
         Sidebar 80px

         Desktop:
         Sidebar 256px
    ====================================================== -->

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
             CONTAINER
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

            <div
                class="
                    flex
                    flex-col

                    gap-4

                    md:flex-row
                    md:items-center
                    md:justify-between

                    mb-6
                    sm:mb-8
                "
            >

                <!-- TITLE -->

                <div class="min-w-0">

                    <p
                        class="
                            text-xs
                            sm:text-sm

                            text-blue-600
                            font-semibold

                            mb-1
                        "
                    >
                        Transaksi
                    </p>


                    <h1
                        class="
                            text-2xl
                            sm:text-3xl

                            font-bold
                            text-slate-800

                            tracking-tight
                        "
                    >
                        Penjualan 🛒
                    </h1>


                    <p
                        class="
                            text-sm
                            sm:text-base

                            text-slate-500

                            mt-1
                        "
                    >
                        Catat dan lihat transaksi penjualan kamu.
                    </p>

                </div>


                <!-- TOMBOL TAMBAH -->

                <a
                    href="tambah.php"

                    class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2

                        w-full
                        md:w-auto

                        bg-blue-600
                        hover:bg-blue-700

                        text-white

                        font-semibold

                        px-5
                        py-3

                        rounded-xl

                        transition

                        shadow-sm

                        whitespace-nowrap
                    "
                >

                    <span>＋</span>

                    Catat Penjualan

                </a>

            </div>


            <!-- =================================================
                 FLASH SUCCESS
            ================================================== -->

            <?php if (isset($_GET['success'])): ?>

                <div
                    class="
                        mb-5
                        sm:mb-6

                        p-4

                        rounded-xl

                        bg-green-50
                        border
                        border-green-200

                        text-green-700
                        text-sm
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                        "
                    >

                        <span
                            class="
                                flex-shrink-0
                            "
                        >
                            ✅
                        </span>


                        <span class="min-w-0 break-words">

                            <?= htmlspecialchars(
                                $_GET['success']
                            ) ?>

                        </span>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FLASH ERROR
            ================================================== -->

            <?php if (isset($_GET['error'])): ?>

                <div
                    class="
                        mb-5
                        sm:mb-6

                        p-4

                        rounded-xl

                        bg-red-50
                        border
                        border-red-200

                        text-red-700
                        text-sm
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                        "
                    >

                        <span
                            class="
                                flex-shrink-0
                            "
                        >
                            ⚠️
                        </span>


                        <span class="min-w-0 break-words">

                            <?= htmlspecialchars(
                                $_GET['error']
                            ) ?>

                        </span>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 STATISTIK
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

                        border
                        border-slate-200

                        rounded-2xl

                        p-5

                        shadow-sm

                        min-w-0
                    "
                >

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

                            mt-1
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


                <!-- TOTAL PENDAPATAN -->

                <div
                    class="
                        bg-white

                        border
                        border-slate-200

                        rounded-2xl

                        p-5

                        shadow-sm

                        min-w-0
                    "
                >

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
                            text-xl
                            sm:text-2xl

                            font-bold

                            text-green-600

                            mt-1

                            break-words
                        "
                    >

                        Rp
                        <?= number_format(
                            $totalPendapatan,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </p>

                </div>

            </div>


            <!-- =================================================
                 FILTER CARD
            ================================================== -->

            <div
                class="
                    bg-white

                    border
                    border-slate-200

                    rounded-2xl

                    p-4
                    sm:p-5
                    md:p-6

                    shadow-sm

                    mb-6

                    min-w-0
                "
            >


                <!-- FILTER HEADER -->

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

                            bg-blue-50

                            flex
                            items-center
                            justify-center
                        "
                    >
                        🔎
                    </div>


                    <div class="min-w-0">

                        <h2
                            class="
                                font-bold
                                text-slate-800
                            "
                        >
                            Filter Transaksi
                        </h2>


                        <p
                            class="
                                text-xs
                                sm:text-sm

                                text-slate-500

                                mt-0.5
                            "
                        >
                            Gunakan filter untuk melihat transaksi
                            pada periode tertentu.
                        </p>

                    </div>

                </div>


                <!-- FORM FILTER -->

                <form
                    method="GET"

                    class="
                        grid

                        grid-cols-1

                        md:grid-cols-2

                        xl:grid-cols-4

                        gap-4
                    "
                >


                    <!-- =========================================
                         DARI TANGGAL
                    ========================================== -->

                    <div class="min-w-0">

                        <label
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
                            type="date"

                            name="tanggal_mulai"

                            value="<?= htmlspecialchars(
                                $tanggalMulai
                            ) ?>"

                            class="
                                w-full

                                px-4
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                text-slate-700
                                text-sm

                                outline-none

                                transition

                                focus:ring-2
                                focus:ring-blue-500
                                focus:border-blue-500
                            "
                        >

                    </div>


                    <!-- =========================================
                         SAMPAI TANGGAL
                    ========================================== -->

                    <div class="min-w-0">

                        <label
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
                            type="date"

                            name="tanggal_akhir"

                            value="<?= htmlspecialchars(
                                $tanggalAkhir
                            ) ?>"

                            class="
                                w-full

                                px-4
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                text-slate-700
                                text-sm

                                outline-none

                                transition

                                focus:ring-2
                                focus:ring-blue-500
                                focus:border-blue-500
                            "
                        >

                    </div>


                    <!-- =========================================
                         MENU
                    ========================================== -->

                    <div class="min-w-0">

                        <label
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
                            name="menu_id"

                            class="
                                w-full

                                px-4
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                text-slate-700
                                text-sm

                                outline-none

                                transition

                                focus:ring-2
                                focus:ring-blue-500
                                focus:border-blue-500
                            "
                        >

                            <option value="0">
                                Semua Menu
                            </option>


                            <?php foreach (
                                $menus
                                as $menu
                            ): ?>

                                <option
                                    value="<?= (int) $menu['id'] ?>"

                                    <?= $menuId == $menu['id']
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


                    <!-- =========================================
                         BUTTON FILTER
                    ========================================== -->

                    <div
                        class="
                            flex

                            flex-col
                            sm:flex-row

                            xl:flex-col

                            gap-2

                            justify-end

                            min-w-0
                        "
                    >

                        <button
                            type="submit"

                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                w-full
                                sm:flex-1
                                xl:w-full

                                px-5
                                py-3

                                rounded-xl

                                bg-blue-600
                                hover:bg-blue-700

                                text-white

                                text-sm
                                font-semibold

                                transition

                                shadow-sm

                                whitespace-nowrap
                            "
                        >

                            <span>🔎</span>

                            Filter

                        </button>


                        <a
                            href="index.php"

                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                w-full
                                sm:flex-1
                                xl:w-full

                                px-5
                                py-3

                                rounded-xl

                                bg-slate-100
                                hover:bg-slate-200

                                text-slate-700

                                text-sm
                                font-semibold

                                transition

                                whitespace-nowrap
                            "
                        >

                            <span>↻</span>

                            Reset

                        </a>

                    </div>

                </form>

            </div>


            <!-- =================================================
                 TABLE CARD
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


                <?php if (count($penjualan) > 0): ?>


                    <!-- =================================================
                         TABLE WRAPPER

                         Tablet/mobile:
                         Bisa scroll horizontal
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

                                min-w-[760px]

                                text-sm
                            "
                        >


                            <!-- =========================================
                                 TABLE HEADER
                            ========================================== -->

                            <thead
                                class="
                                    bg-slate-50
                                    border-b
                                    border-slate-200
                                "
                            >

                                <tr>


                                    <!-- TANGGAL -->

                                    <th
                                        class="
                                            text-left

                                            px-5
                                            sm:px-6

                                            py-4

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Tanggal
                                    </th>


                                    <!-- ITEM -->

                                    <th
                                        class="
                                            text-left

                                            px-5
                                            sm:px-6

                                            py-4

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Item
                                    </th>


                                    <!-- CATATAN -->

                                    <th
                                        class="
                                            text-left

                                            px-5
                                            sm:px-6

                                            py-4

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Catatan
                                    </th>


                                    <!-- TOTAL -->

                                    <th
                                        class="
                                            text-right

                                            px-5
                                            sm:px-6

                                            py-4

                                            font-semibold
                                            text-slate-600

                                            whitespace-nowrap
                                        "
                                    >
                                        Total
                                    </th>

                                </tr>

                            </thead>


                            <!-- =========================================
                                 TABLE BODY
                            ========================================== -->

                            <tbody
                                class="
                                    divide-y
                                    divide-slate-100
                                "
                            >

                                <?php foreach (
                                    $penjualan
                                    as $row
                                ): ?>

                                    <tr
                                        class="
                                            hover:bg-slate-50

                                            transition
                                        "
                                    >


                                        <!-- =================================
                                             TANGGAL
                                        ================================== -->

                                        <td
                                            class="
                                                px-5
                                                sm:px-6
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
                                                    'd M Y',
                                                    strtotime(
                                                        $row['tanggal']
                                                    )
                                                ) ?>

                                            </div>


                                            <div
                                                class="
                                                    text-xs
                                                    text-slate-400

                                                    mt-1

                                                    whitespace-nowrap
                                                "
                                            >

                                                <?= date(
                                                    'H:i',
                                                    strtotime(
                                                        $row['tanggal']
                                                    )
                                                ) ?>

                                                WIB

                                            </div>

                                        </td>


                                        <!-- =================================
                                             ITEM
                                        ================================== -->

                                        <td
                                            class="
                                                px-5
                                                sm:px-6
                                                py-4
                                            "
                                        >

                                            <span
                                                class="
                                                    inline-flex

                                                    px-3
                                                    py-1

                                                    rounded-full

                                                    bg-blue-50
                                                    text-blue-700

                                                    text-xs

                                                    font-semibold

                                                    whitespace-nowrap
                                                "
                                            >

                                                <?= (int) $row['jumlah_item'] ?>

                                                item

                                            </span>

                                        </td>


                                        <!-- =================================
                                             CATATAN
                                        ================================== -->

                                        <td
                                            class="
                                                px-5
                                                sm:px-6
                                                py-4

                                                text-slate-500

                                                max-w-xs
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

                                                        max-w-[240px]
                                                        lg:max-w-[360px]
                                                    "

                                                    title="<?= htmlspecialchars(
                                                        $row['catatan']
                                                    ) ?>"
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


                                        <!-- =================================
                                             TOTAL
                                        ================================== -->

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
                                                    justify-end

                                                    gap-3
                                                "
                                            >

                                                <span
                                                    class="
                                                        font-bold
                                                        text-slate-800

                                                        whitespace-nowrap
                                                    "
                                                >

                                                    Rp
                                                    <?= number_format(
                                                        $row['total'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) ?>

                                                </span>


                                                <a
                                                    href="detail.php?id=<?= (int) $row['id'] ?>"

                                                    class="
                                                        inline-flex
                                                        items-center
                                                        justify-center
                                                        gap-1

                                                        px-3
                                                        py-2

                                                        rounded-lg

                                                        bg-blue-50
                                                        hover:bg-blue-100

                                                        text-blue-700

                                                        font-semibold

                                                        transition

                                                        whitespace-nowrap
                                                    "
                                                >

                                                    🔎

                                                    <span>
                                                        Detail
                                                    </span>

                                                </a>

                                            </div>

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
                                border-t
                                border-slate-200

                                px-4
                                sm:px-6

                                py-4

                                flex
                                flex-col

                                sm:flex-row

                                items-center
                                justify-between

                                gap-4
                            "
                        >


                            <!-- =========================================
                                 INFO PAGINATION
                            ========================================== -->

                            <p
                                class="
                                    text-sm
                                    text-slate-500

                                    text-center
                                    sm:text-left
                                "
                            >

                                Menampilkan

                                <span
                                    class="
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    <?= $offset + 1 ?>
                                </span>

                                -

                                <span
                                    class="
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    <?= min(
                                        $offset + $perPage,
                                        $totalTransaksi
                                    ) ?>
                                </span>

                                dari

                                <span
                                    class="
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    <?= number_format(
                                        $totalTransaksi,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </span>

                                transaksi

                            </p>


                            <!-- =========================================
                                 PAGINATION BUTTON
                            ========================================== -->

                            <div
                                class="
                                    flex
                                    items-center

                                    gap-1.5

                                    max-w-full

                                    overflow-x-auto
                                "
                            >


                                <!-- PREVIOUS -->

                                <?php if ($page > 1): ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            halamanUrl(
                                                $page - 1,
                                                $queryParams
                                            )
                                        ) ?>"

                                        class="
                                            flex-shrink-0

                                            w-9
                                            h-9

                                            inline-flex
                                            items-center
                                            justify-center

                                            rounded-lg

                                            bg-slate-100
                                            hover:bg-slate-200

                                            text-slate-700

                                            font-semibold

                                            transition
                                        "
                                    >
                                        ←
                                    </a>

                                <?php endif; ?>


                                <?php

                                $startPage = max(
                                    1,
                                    $page - 2
                                );

                                $endPage = min(
                                    $totalPages,
                                    $page + 2
                                );

                                ?>


                                <!-- NUMBER -->

                                <?php for (
                                    $i = $startPage;
                                    $i <= $endPage;
                                    $i++
                                ): ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            halamanUrl(
                                                $i,
                                                $queryParams
                                            )
                                        ) ?>"

                                        class="
                                            flex-shrink-0

                                            min-w-9
                                            h-9

                                            px-3

                                            inline-flex
                                            items-center
                                            justify-center

                                            rounded-lg

                                            font-semibold

                                            transition

                                            <?= $i === $page

                                                ? 'bg-blue-600 text-white'

                                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                            ?>
                                        "
                                    >

                                        <?= $i ?>

                                    </a>

                                <?php endfor; ?>


                                <!-- NEXT -->

                                <?php if (
                                    $page < $totalPages
                                ): ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            halamanUrl(
                                                $page + 1,
                                                $queryParams
                                            )
                                        ) ?>"

                                        class="
                                            flex-shrink-0

                                            w-9
                                            h-9

                                            inline-flex
                                            items-center
                                            justify-center

                                            rounded-lg

                                            bg-slate-100
                                            hover:bg-slate-200

                                            text-slate-700

                                            font-semibold

                                            transition
                                        "
                                    >
                                        →
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
                            py-14
                            sm:py-16

                            px-5
                            sm:px-6

                            text-center
                        "
                    >

                        <div
                            class="
                                text-4xl
                                sm:text-5xl

                                mb-4
                            "
                        >
                            🛒
                        </div>


                        <h3
                            class="
                                text-lg
                                font-bold

                                text-slate-800

                                mb-2
                            "
                        >
                            Belum ada transaksi
                        </h3>


                        <p
                            class="
                                text-sm
                                sm:text-base

                                text-slate-500

                                max-w-md

                                mx-auto

                                mb-6
                            "
                        >
                            Mulai catat penjualan pertamamu.
                        </p>


                        <a
                            href="tambah.php"

                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                bg-blue-600
                                hover:bg-blue-700

                                text-white

                                font-semibold

                                px-5
                                py-3

                                rounded-xl

                                transition

                                shadow-sm
                            "
                        >

                            ＋

                            Catat Penjualan

                        </a>

                    </div>


                <?php endif; ?>


            </div>


        </div>

    </main>

</div>


<?php require_once '../includes/footer.php'; ?>