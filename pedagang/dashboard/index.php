<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| TOTAL PENDAPATAN
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT COALESCE(SUM(total), 0) AS total_pendapatan
    FROM penjualan
    WHERE user_id = ?
");

$stmt->execute([$userId]);

$totalPendapatan = $stmt->fetch(PDO::FETCH_ASSOC)['total_pendapatan'];


/*
|--------------------------------------------------------------------------
| TOTAL TRANSAKSI
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_transaksi
    FROM penjualan
    WHERE user_id = ?
");

$stmt->execute([$userId]);

$totalTransaksi = $stmt->fetch(PDO::FETCH_ASSOC)['total_transaksi'];


/*
|--------------------------------------------------------------------------
| TOTAL MENU AKTIF
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_menu
    FROM menu
    WHERE user_id = ?
      AND status = 'aktif'
");

$stmt->execute([$userId]);

$totalMenu = $stmt->fetch(PDO::FETCH_ASSOC)['total_menu'];


/*
|--------------------------------------------------------------------------
| TRANSAKSI HARI INI
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT COUNT(*) AS transaksi_hari_ini
    FROM penjualan
    WHERE user_id = ?
      AND tanggal >= CURDATE()
      AND tanggal < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
");

$stmt->execute([$userId]);

$transaksiHariIni = $stmt->fetch(PDO::FETCH_ASSOC)['transaksi_hari_ini'];


/*
|--------------------------------------------------------------------------
| PENJUALAN TERBARU
|--------------------------------------------------------------------------
*/

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

    WHERE p.user_id = ?

    GROUP BY
        p.id,
        p.tanggal,
        p.total,
        p.catatan

    ORDER BY
        p.tanggal DESC,
        p.id DESC

    LIMIT 5
");

$stmt->execute([$userId]);

$penjualanTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| MENU TERLARIS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        m.nama_menu,
        SUM(dp.jumlah) AS total_terjual
    FROM detail_penjualan dp

    INNER JOIN penjualan p
        ON dp.penjualan_id = p.id

    INNER JOIN menu m
        ON dp.menu_id = m.id

    WHERE p.user_id = ?

    GROUP BY
        m.id,
        m.nama_menu

    ORDER BY
        total_terjual DESC

    LIMIT 5
");

$stmt->execute([$userId]);

$menuTerlaris = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once '../includes/header.php'; ?>
<?php require_once '../includes/sidebar.php'; ?>


<!-- ================================================================
     PAGE WRAPPER
================================================================ -->

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">


    <!-- ============================================================
         MAIN AREA
         
         MOBILE
         < 768px
         Full width

         TABLET
         768px - 1023px
         Sidebar 80px

         DESKTOP
         >= 1024px
         Sidebar 256px
    ============================================================= -->

    <main
        class="
            min-h-screen
            min-w-0
            w-full

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


        <!-- ========================================================
             CONTENT CONTAINER
        ========================================================= -->

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


            <!-- ====================================================
                 HEADER DASHBOARD
            ===================================================== -->

            <div class="mb-7 sm:mb-8">

                <div
                    class="
                        flex
                        flex-col
                        gap-4

                        sm:flex-row
                        sm:items-end
                        sm:justify-between
                    "
                >

                    <!-- TITLE -->

                    <div class="min-w-0 flex-1">

                        <!-- BREADCRUMB -->

                        <div
                            class="
                                flex
                                items-center
                                gap-2

                                text-xs
                                sm:text-sm

                                text-slate-400

                                mb-2
                            "
                        >

                            <i class="fa-solid fa-house"></i>

                            <span>/</span>

                            <span class="text-slate-500">
                                Dashboard
                            </span>

                        </div>


                        <!-- TITLE -->

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl
                                font-bold
                                text-slate-800
                                tracking-tight
                            "
                        >
                            Dashboard
                        </h1>


                        <p
                            class="
                                text-sm
                                sm:text-base
                                text-slate-500
                                mt-1
                                max-w-2xl
                            "
                        >
                            Pantau aktivitas dan ringkasan penjualan Anda.
                        </p>

                    </div>


                    <!-- QUICK BUTTON -->

                    <a
                        href="../penjualan/tambah.php"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2

                            w-full
                            sm:w-auto

                            px-4
                            sm:px-5
                            py-3

                            bg-blue-600
                            hover:bg-blue-700

                            text-white
                            rounded-xl

                            font-semibold

                            shadow-sm
                            hover:shadow

                            transition-all

                            whitespace-nowrap
                        "
                    >

                        <i class="fa-solid fa-plus"></i>

                        Catat Penjualan

                    </a>

                </div>

            </div>


            <!-- ====================================================
                 STATISTICS
            ===================================================== -->

            <div class="w-full mb-7">

                <div
                    class="
                        grid

                        grid-cols-1
                        sm:grid-cols-2

                        gap-4
                        sm:gap-5
                    "
                >


                    <!-- =================================================
                         TOTAL PENDAPATAN
                    ================================================== -->

                    <div
                        class="
                            group
                            min-w-0

                            bg-white
                            rounded-2xl

                            border
                            border-slate-200

                            p-5

                            shadow-sm
                            hover:shadow-md

                            transition-shadow
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                justify-between
                                gap-3
                            "
                        >

                            <div class="min-w-0 flex-1">

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Total Pendapatan
                                </p>

                                <h2
                                    class="
                                        text-xl
                                        sm:text-2xl

                                        font-bold
                                        text-slate-800

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
                                </h2>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-2
                                    "
                                >
                                    Akumulasi seluruh penjualan
                                </p>

                            </div>


                            <div
                                class="
                                    w-11
                                    h-11

                                    rounded-xl
                                    bg-emerald-50

                                    flex
                                    items-center
                                    justify-center

                                    flex-shrink-0

                                    group-hover:scale-105
                                    transition-transform
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-wallet
                                        text-emerald-600
                                    "
                                ></i>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         TOTAL TRANSAKSI
                    ================================================== -->

                    <div
                        class="
                            group
                            min-w-0

                            bg-white
                            rounded-2xl

                            border
                            border-slate-200

                            p-5

                            shadow-sm
                            hover:shadow-md

                            transition-shadow
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                justify-between
                                gap-3
                            "
                        >

                            <div class="min-w-0 flex-1">

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Total Transaksi
                                </p>

                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                        text-slate-800
                                        mt-2
                                    "
                                >
                                    <?= number_format($totalTransaksi) ?>
                                </h2>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-2
                                    "
                                >
                                    Seluruh transaksi tercatat
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

                                    flex-shrink-0

                                    group-hover:scale-105
                                    transition-transform
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-receipt
                                        text-blue-600
                                    "
                                ></i>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         MENU AKTIF
                    ================================================== -->

                    <div
                        class="
                            group
                            min-w-0

                            bg-white
                            rounded-2xl

                            border
                            border-slate-200

                            p-5

                            shadow-sm
                            hover:shadow-md

                            transition-shadow
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                justify-between
                                gap-3
                            "
                        >

                            <div class="min-w-0 flex-1">

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Menu Aktif
                                </p>

                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                        text-slate-800
                                        mt-2
                                    "
                                >
                                    <?= number_format($totalMenu) ?>
                                </h2>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-2
                                    "
                                >
                                    Menu yang tersedia
                                </p>

                            </div>


                            <div
                                class="
                                    w-11
                                    h-11

                                    rounded-xl
                                    bg-orange-50

                                    flex
                                    items-center
                                    justify-center

                                    flex-shrink-0

                                    group-hover:scale-105
                                    transition-transform
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-utensils
                                        text-orange-600
                                    "
                                ></i>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         TRANSAKSI HARI INI
                    ================================================== -->

                    <div
                        class="
                            group
                            min-w-0

                            bg-white
                            rounded-2xl

                            border
                            border-slate-200

                            p-5

                            shadow-sm
                            hover:shadow-md

                            transition-shadow
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                justify-between
                                gap-3
                            "
                        >

                            <div class="min-w-0 flex-1">

                                <p
                                    class="
                                        text-sm
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Transaksi Hari Ini
                                </p>

                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                        text-slate-800
                                        mt-2
                                    "
                                >
                                    <?= number_format($transaksiHariIni) ?>
                                </h2>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-2
                                    "
                                >
                                    Penjualan hari ini
                                </p>

                            </div>


                            <div
                                class="
                                    w-11
                                    h-11

                                    rounded-xl
                                    bg-violet-50

                                    flex
                                    items-center
                                    justify-center

                                    flex-shrink-0

                                    group-hover:scale-105
                                    transition-transform
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-calendar-day
                                        text-violet-600
                                    "
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ====================================================
                 MAIN CONTENT
            ===================================================== -->

            <div
                class="
                    grid

                    grid-cols-1
                    lg:grid-cols-3

                    gap-5
                    lg:gap-6

                    min-w-0
                "
            >


                <!-- =================================================
                     PENJUALAN TERBARU
                ================================================== -->

                <div
                    class="
                        lg:col-span-2

                        min-w-0

                        bg-white
                        rounded-2xl

                        border
                        border-slate-200

                        shadow-sm

                        overflow-hidden
                    "
                >


                    <!-- HEADER -->

                    <div
                        class="
                            px-5
                            sm:px-6

                            py-5

                            border-b
                            border-slate-100
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

                                <div
                                    class="
                                        flex
                                        items-center
                                        gap-3
                                    "
                                >

                                    <div
                                        class="
                                            w-9
                                            h-9

                                            rounded-xl
                                            bg-blue-50

                                            flex
                                            items-center
                                            justify-center

                                            flex-shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-clock-rotate-left

                                                text-blue-600
                                                text-sm
                                            "
                                        ></i>

                                    </div>


                                    <h2
                                        class="
                                            text-base
                                            sm:text-lg

                                            font-bold
                                            text-slate-800

                                            truncate
                                        "
                                    >
                                        Penjualan Terbaru
                                    </h2>

                                </div>


                                <p
                                    class="
                                        text-xs
                                        sm:text-sm

                                        text-slate-500

                                        mt-2
                                        ml-12
                                    "
                                >
                                    5 transaksi terakhir
                                </p>

                            </div>


                            <!-- DESKTOP/TABLET LINK -->

                            <a
                                href="../penjualan/index.php"
                                class="
                                    hidden
                                    sm:inline-flex

                                    items-center
                                    gap-1.5

                                    text-sm
                                    font-semibold

                                    text-blue-600
                                    hover:text-blue-700

                                    whitespace-nowrap
                                "
                            >

                                Lihat Semua

                                <i
                                    class="
                                        fa-solid
                                        fa-arrow-right
                                        text-xs
                                    "
                                ></i>

                            </a>

                        </div>

                    </div>


                    <!-- TABLE -->

                    <?php if (!empty($penjualanTerbaru)): ?>

                        <div class="w-full overflow-x-auto">

                            <table
                                class="
                                    w-full
                                    min-w-[680px]
                                    text-sm
                                "
                            >

                                <thead>

                                    <tr
                                        class="
                                            bg-slate-50/70

                                            border-b
                                            border-slate-100
                                        "
                                    >

                                        <th
                                            class="
                                                text-left

                                                px-5
                                                sm:px-6

                                                py-3.5

                                                font-semibold
                                                text-slate-500

                                                text-xs

                                                uppercase
                                                tracking-wide

                                                whitespace-nowrap
                                            "
                                        >
                                            Tanggal
                                        </th>


                                        <th
                                            class="
                                                text-center

                                                px-5

                                                py-3.5

                                                font-semibold
                                                text-slate-500

                                                text-xs

                                                uppercase
                                                tracking-wide

                                                whitespace-nowrap
                                            "
                                        >
                                            Item
                                        </th>


                                        <th
                                            class="
                                                text-left

                                                px-5

                                                py-3.5

                                                font-semibold
                                                text-slate-500

                                                text-xs

                                                uppercase
                                                tracking-wide
                                            "
                                        >
                                            Catatan
                                        </th>


                                        <th
                                            class="
                                                text-right

                                                px-5
                                                sm:px-6

                                                py-3.5

                                                font-semibold
                                                text-slate-500

                                                text-xs

                                                uppercase
                                                tracking-wide

                                                whitespace-nowrap
                                            "
                                        >
                                            Total
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
                                        $penjualanTerbaru
                                        as $penjualan
                                    ): ?>

                                        <tr
                                            class="
                                                group

                                                hover:bg-slate-50/70

                                                transition-colors
                                            "
                                        >


                                            <!-- TANGGAL -->

                                            <td
                                                class="
                                                    px-5
                                                    sm:px-6

                                                    py-4

                                                    whitespace-nowrap
                                                "
                                            >

                                                <div
                                                    class="
                                                        flex
                                                        items-center
                                                        gap-3
                                                    "
                                                >

                                                    <div
                                                        class="
                                                            w-9
                                                            h-9

                                                            rounded-xl
                                                            bg-slate-100

                                                            flex
                                                            items-center
                                                            justify-center

                                                            flex-shrink-0
                                                        "
                                                    >

                                                        <i
                                                            class="
                                                                fa-regular
                                                                fa-calendar

                                                                text-slate-500
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
                                                                'd M Y',
                                                                strtotime(
                                                                    $penjualan['tanggal']
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
                                                                    $penjualan['tanggal']
                                                                )
                                                            ) ?>

                                                        </p>

                                                    </div>

                                                </div>

                                            </td>


                                            <!-- ITEM -->

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
                                                        items-center
                                                        justify-center

                                                        min-w-8

                                                        px-2.5
                                                        py-1.5

                                                        rounded-lg

                                                        bg-blue-50
                                                        text-blue-700

                                                        text-xs
                                                        font-bold
                                                    "
                                                >

                                                    <?= number_format(
                                                        $penjualan['jumlah_item']
                                                    ) ?>

                                                </span>

                                            </td>


                                            <!-- CATATAN -->

                                            <td
                                                class="
                                                    px-5
                                                    py-4

                                                    max-w-[220px]
                                                "
                                            >

                                                <span
                                                    class="
                                                        block

                                                        truncate

                                                        text-slate-500
                                                    "
                                                >

                                                    <?= htmlspecialchars(
                                                        $penjualan['catatan']
                                                            ?: '-'
                                                    ) ?>

                                                </span>

                                            </td>


                                            <!-- TOTAL -->

                                            <td
                                                class="
                                                    px-5
                                                    sm:px-6

                                                    py-4

                                                    text-right

                                                    whitespace-nowrap
                                                "
                                            >

                                                <span
                                                    class="
                                                        font-bold
                                                        text-slate-800
                                                    "
                                                >

                                                    Rp <?= number_format(
                                                        $penjualan['total'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) ?>

                                                </span>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>


                        <!-- MOBILE LINK -->

                        <div
                            class="
                                sm:hidden

                                px-5
                                py-4

                                border-t
                                border-slate-100
                            "
                        >

                            <a
                                href="../penjualan/index.php"
                                class="
                                    flex
                                    items-center
                                    justify-center
                                    gap-2

                                    text-sm
                                    font-semibold

                                    text-blue-600
                                "
                            >

                                Lihat Semua Penjualan

                                <i
                                    class="
                                        fa-solid
                                        fa-arrow-right
                                        text-xs
                                    "
                                ></i>

                            </a>

                        </div>


                    <?php else: ?>


                        <!-- EMPTY STATE -->

                        <div
                            class="
                                p-10
                                sm:p-12

                                text-center
                            "
                        >

                            <div
                                class="
                                    w-16
                                    h-16

                                    mx-auto

                                    rounded-2xl
                                    bg-slate-100

                                    flex
                                    items-center
                                    justify-center

                                    mb-5
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-receipt

                                        text-slate-400
                                        text-xl
                                    "
                                ></i>

                            </div>


                            <h3
                                class="
                                    font-bold
                                    text-slate-700
                                "
                            >
                                Belum ada transaksi
                            </h3>


                            <p
                                class="
                                    text-sm
                                    text-slate-500

                                    mt-1

                                    max-w-xs
                                    mx-auto
                                "
                            >
                                Mulai catat penjualan pertama Anda untuk melihat
                                data di dashboard.
                            </p>


                            <a
                                href="../penjualan/tambah.php"
                                class="
                                    inline-flex
                                    items-center
                                    gap-2

                                    mt-5

                                    px-5
                                    py-2.5

                                    bg-blue-600
                                    text-white

                                    rounded-xl

                                    font-semibold

                                    hover:bg-blue-700

                                    transition
                                "
                            >

                                <i class="fa-solid fa-plus"></i>

                                Catat Penjualan

                            </a>

                        </div>


                    <?php endif; ?>

                </div>


                <!-- =================================================
                     MENU TERLARIS
                ================================================== -->

                <div
                    class="
                        min-w-0

                        bg-white
                        rounded-2xl

                        border
                        border-slate-200

                        shadow-sm

                        overflow-hidden
                    "
                >


                    <!-- HEADER -->

                    <div
                        class="
                            px-5
                            sm:px-6

                            py-5

                            border-b
                            border-slate-100
                        "
                    >

                        <div
                            class="
                                flex
                                items-center
                                gap-3
                            "
                        >

                            <div
                                class="
                                    w-9
                                    h-9

                                    rounded-xl
                                    bg-orange-50

                                    flex
                                    items-center
                                    justify-center

                                    flex-shrink-0
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-chart-simple

                                        text-orange-600
                                        text-sm
                                    "
                                ></i>

                            </div>


                            <div class="min-w-0">

                                <h2
                                    class="
                                        text-base
                                        sm:text-lg

                                        font-bold
                                        text-slate-800
                                    "
                                >
                                    Menu Terlaris
                                </h2>


                                <p
                                    class="
                                        text-xs
                                        text-slate-500
                                        mt-0.5
                                    "
                                >
                                    Berdasarkan jumlah terjual
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- MENU LIST -->

                    <?php if (!empty($menuTerlaris)): ?>

                        <div
                            class="
                                divide-y
                                divide-slate-100
                            "
                        >

                            <?php foreach (
                                $menuTerlaris
                                as $index => $menu
                            ): ?>

                                <?php

                                $rankClass =
                                    'bg-slate-100 text-slate-500';

                                if ($index === 0) {

                                    $rankClass =
                                        'bg-amber-50 text-amber-600';

                                } elseif ($index === 1) {

                                    $rankClass =
                                        'bg-slate-100 text-slate-600';

                                } elseif ($index === 2) {

                                    $rankClass =
                                        'bg-orange-50 text-orange-600';

                                }

                                ?>


                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between

                                        gap-3

                                        px-5
                                        sm:px-6

                                        py-4

                                        hover:bg-slate-50/70

                                        transition
                                    "
                                >

                                    <!-- LEFT -->

                                    <div
                                        class="
                                            flex
                                            items-center
                                            gap-3

                                            min-w-0
                                            flex-1
                                        "
                                    >

                                        <!-- RANK -->

                                        <div
                                            class="
                                                w-9
                                                h-9

                                                rounded-xl

                                                <?= $rankClass ?>

                                                flex
                                                items-center
                                                justify-center

                                                font-bold
                                                text-sm

                                                flex-shrink-0
                                            "
                                        >

                                            <?php if ($index === 0): ?>

                                                <i
                                                    class="
                                                        fa-solid
                                                        fa-crown
                                                        text-xs
                                                    "
                                                ></i>

                                            <?php else: ?>

                                                <?= $index + 1 ?>

                                            <?php endif; ?>

                                        </div>


                                        <!-- MENU -->

                                        <div class="min-w-0">

                                            <p
                                                class="
                                                    font-semibold
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
                                                    mt-0.5
                                                "
                                            >
                                                Menu terjual
                                            </p>

                                        </div>

                                    </div>


                                    <!-- RIGHT -->

                                    <div
                                        class="
                                            text-right
                                            flex-shrink-0
                                        "
                                    >

                                        <p
                                            class="
                                                font-bold
                                                text-slate-800
                                            "
                                        >

                                            <?= number_format(
                                                $menu['total_terjual']
                                            ) ?>

                                        </p>


                                        <p
                                            class="
                                                text-xs
                                                text-slate-400
                                            "
                                        >
                                            pcs
                                        </p>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>


                    <?php else: ?>


                        <!-- EMPTY -->

                        <div
                            class="
                                p-8

                                text-center
                            "
                        >

                            <div
                                class="
                                    w-12
                                    h-12

                                    mx-auto

                                    rounded-xl
                                    bg-slate-100

                                    flex
                                    items-center
                                    justify-center
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-chart-simple

                                        text-slate-400
                                    "
                                ></i>

                            </div>


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

            </div>


            <!-- ====================================================
                 QUICK ACTION
            ===================================================== -->

            <div
                class="
                    mt-5
                    lg:mt-6
                "
            >

                <div
                    class="
                        relative
                        overflow-hidden

                        bg-gradient-to-r
                        from-blue-600
                        to-indigo-600

                        rounded-2xl

                        p-6
                        sm:p-7

                        text-white

                        shadow-sm
                    "
                >


                    <!-- DECORATION -->

                    <div
                        class="
                            absolute

                            -right-10
                            -top-10

                            w-40
                            h-40

                            rounded-full

                            bg-white/10

                            pointer-events-none
                        "
                    ></div>


                    <div
                        class="
                            absolute

                            right-20
                            -bottom-16

                            w-40
                            h-40

                            rounded-full

                            bg-white/5

                            pointer-events-none
                        "
                    ></div>


                    <!-- CONTENT -->

                    <div
                        class="
                            relative

                            flex
                            flex-col

                            gap-5

                            sm:flex-row
                            sm:items-center
                            sm:justify-between
                        "
                    >

                        <div
                            class="
                                max-w-xl
                                min-w-0
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-2

                                    mb-2
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-bolt

                                        text-blue-200
                                    "
                                ></i>


                                <span
                                    class="
                                        text-xs
                                        font-semibold

                                        uppercase
                                        tracking-wider

                                        text-blue-100
                                    "
                                >
                                    Aksi Cepat
                                </span>

                            </div>


                            <h2
                                class="
                                    text-xl
                                    sm:text-2xl
                                    font-bold
                                "
                            >
                                Catat penjualan baru
                            </h2>


                            <p
                                class="
                                    text-sm

                                    text-blue-100

                                    mt-1.5

                                    max-w-lg
                                "
                            >
                                Tambahkan transaksi untuk memperbarui data
                                penjualan dan laporan Anda.
                            </p>

                        </div>


                        <a
                            href="../penjualan/tambah.php"
                            class="
                                relative

                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                w-full
                                sm:w-auto

                                px-5
                                py-3

                                bg-white
                                text-blue-600

                                rounded-xl

                                font-bold

                                hover:bg-blue-50

                                transition

                                shadow-sm

                                whitespace-nowrap

                                flex-shrink-0
                            "
                        >

                            <i class="fa-solid fa-plus"></i>

                            Catat Penjualan

                        </a>

                    </div>

                </div>

            </div>


        </div>
        <!-- END CONTENT CONTAINER -->


    </main>


</div>


<?php require_once '../includes/footer.php'; ?>