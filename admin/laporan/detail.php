<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


/* =========================================================
   ID TRANSAKSI
========================================================= */

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {

    header('Location: index.php');
    exit;
}


/* =========================================================
   DATA TRANSAKSI
========================================================= */

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.tanggal,
        p.total,
        p.catatan,

        u.nama AS nama_pedagang,
        u.username,
        u.no_hp,
        u.alamat

    FROM penjualan p

    INNER JOIN users u
        ON u.id = p.user_id

    WHERE p.id = ?

    LIMIT 1
");

$stmt->execute([$id]);

$penjualan = $stmt->fetch();


/* =========================================================
   TRANSAKSI TIDAK DITEMUKAN
========================================================= */

if (!$penjualan) {

    header('Location: index.php');
    exit;
}


/* =========================================================
   DETAIL ITEM
========================================================= */

$stmtDetail = $conn->prepare("
    SELECT
        dp.id,
        dp.jumlah,
        dp.harga,
        dp.subtotal,

        m.nama_menu,

        k.nama_kategori

    FROM detail_penjualan dp

    INNER JOIN menu m
        ON m.id = dp.menu_id

    LEFT JOIN kategori k
        ON k.id = m.kategori_id

    WHERE dp.penjualan_id = ?

    ORDER BY dp.id ASC
");

$stmtDetail->execute([$id]);

$detailItems = $stmtDetail->fetchAll();


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
            w-full

            pt-20
            pb-24

            md:ml-20
            md:w-[calc(100%-5rem)]

            lg:ml-64
            lg:w-[calc(100%-16rem)]

            md:pt-8
            md:pb-8

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
                lg:px-8
                xl:px-10
            "
        >


            <!-- =====================================================
                 HEADER
            ====================================================== -->

            <div class="mb-6">

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

                    <!-- JUDUL -->

                    <div class="min-w-0">

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl

                                font-bold

                                text-slate-800
                            "
                        >
                            Detail Transaksi
                        </h1>

                        <p
                            class="
                                text-sm
                                sm:text-base

                                text-slate-500

                                mt-1
                            "
                        >
                            Informasi lengkap transaksi penjualan.
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

                            bg-white
                            border
                            border-slate-200

                            hover:bg-slate-50

                            text-slate-700
                            font-medium

                            rounded-xl

                            px-4
                            py-2.5

                            text-sm

                            shadow-sm

                            transition

                            shrink-0
                        "
                    >

                        <i
                            class="
                                fa-solid
                                fa-arrow-left
                                text-xs
                            "
                        ></i>

                        Kembali ke Laporan

                    </a>

                </div>

            </div>


            <!-- =====================================================
                 INFORMASI TRANSAKSI
            ====================================================== -->

            <div
                class="
                    grid

                    grid-cols-1

                    lg:grid-cols-3

                    gap-5
                    lg:gap-6

                    mb-6
                "
            >


                <!-- =================================================
                     INFORMASI TRANSAKSI
                ================================================== -->

                <div
                    class="
                        lg:col-span-2

                        bg-white

                        rounded-2xl

                        border
                        border-slate-200

                        shadow-sm

                        p-5
                        sm:p-6
                    "
                >

                    <h2
                        class="
                            font-semibold
                            text-lg

                            text-slate-800

                            mb-5
                        "
                    >
                        Informasi Transaksi
                    </h2>


                    <div
                        class="
                            grid

                            grid-cols-1

                            sm:grid-cols-2

                            gap-x-6
                            gap-y-5
                        "
                    >

                        <!-- ID -->

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                ID Transaksi
                            </p>

                            <p
                                class="
                                    font-semibold

                                    text-slate-800

                                    mt-1
                                "
                            >
                                #<?= (int) $penjualan['id'] ?>
                            </p>

                        </div>


                        <!-- TANGGAL -->

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                Tanggal
                            </p>

                            <p
                                class="
                                    font-semibold

                                    text-slate-800

                                    mt-1

                                    break-words
                                "
                            >
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $penjualan['tanggal']
                                    )
                                ) ?>
                            </p>

                        </div>


                        <!-- PEDAGANG -->

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                Pedagang
                            </p>

                            <p
                                class="
                                    font-semibold

                                    text-slate-800

                                    mt-1

                                    break-words
                                "
                            >
                                <?= htmlspecialchars(
                                    $penjualan['nama_pedagang']
                                ) ?>
                            </p>

                        </div>


                        <!-- USERNAME -->

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                Username
                            </p>

                            <p
                                class="
                                    font-semibold

                                    text-slate-800

                                    mt-1

                                    break-all
                                "
                            >
                                <?= htmlspecialchars(
                                    $penjualan['username']
                                ) ?>
                            </p>

                        </div>


                        <!-- NO HP -->

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                No. HP
                            </p>

                            <p
                                class="
                                    font-semibold

                                    text-slate-800

                                    mt-1

                                    break-words
                                "
                            >
                                <?= !empty(
                                    $penjualan['no_hp']
                                )
                                    ? htmlspecialchars(
                                        $penjualan['no_hp']
                                    )
                                    : '-'
                                ?>
                            </p>

                        </div>


                        <!-- ALAMAT -->

                        <div class="min-w-0">

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                "
                            >
                                Alamat
                            </p>

                            <p
                                class="
                                    font-semibold

                                    text-slate-800

                                    mt-1

                                    break-words
                                "
                            >
                                <?= !empty(
                                    $penjualan['alamat']
                                )
                                    ? htmlspecialchars(
                                        $penjualan['alamat']
                                    )
                                    : '-'
                                ?>
                            </p>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     TOTAL
                ================================================== -->

                <div
                    class="
                        bg-emerald-600

                        rounded-2xl

                        shadow-sm

                        p-5
                        sm:p-6

                        text-white
                    "
                >

                    <p
                        class="
                            text-sm
                            text-emerald-100
                        "
                    >
                        Total Transaksi
                    </p>

                    <p
                        class="
                            text-2xl
                            sm:text-3xl

                            font-bold

                            mt-3

                            break-words
                        "
                    >

                        Rp <?= number_format(
                            $penjualan['total'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </p>


                    <div
                        class="
                            mt-6
                            pt-4

                            border-t
                            border-emerald-500
                        "
                    >

                        <p
                            class="
                                text-sm
                                text-emerald-100
                            "
                        >
                            Jumlah Item
                        </p>

                        <p
                            class="
                                text-xl
                                font-semibold

                                mt-1
                            "
                        >

                            <?= number_format(
                                count($detailItems),
                                0,
                                ',',
                                '.'
                            ) ?>

                        </p>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 DAFTAR ITEM
            ====================================================== -->

            <div
                class="
                    bg-white

                    rounded-2xl

                    border
                    border-slate-200

                    shadow-sm

                    overflow-hidden
                "
            >

                <!-- CARD HEADER -->

                <div
                    class="
                        px-5
                        sm:px-6

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

                    <div>

                        <h2
                            class="
                                font-semibold
                                text-slate-800
                            "
                        >
                            Rincian Menu
                        </h2>

                        <p
                            class="
                                text-xs
                                text-slate-400

                                mt-1
                            "
                        >
                            <?= count($detailItems) ?> item dalam transaksi
                        </p>

                    </div>

                </div>


                <!-- TABLE -->

                <div class="overflow-x-auto">

                    <table
                        class="
                            w-full

                            min-w-[760px]

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
                                        text-left

                                        px-5
                                        sm:px-6

                                        py-3

                                        font-semibold

                                        text-slate-600

                                        w-16
                                    "
                                >
                                    No
                                </th>


                                <th
                                    class="
                                        text-left

                                        px-5
                                        sm:px-6

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

                                        px-5
                                        sm:px-6

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

                                        px-5
                                        sm:px-6

                                        py-3

                                        font-semibold

                                        text-slate-600
                                    "
                                >
                                    Harga
                                </th>


                                <th
                                    class="
                                        text-center

                                        px-5
                                        sm:px-6

                                        py-3

                                        font-semibold

                                        text-slate-600
                                    "
                                >
                                    Jumlah
                                </th>


                                <th
                                    class="
                                        text-right

                                        px-5
                                        sm:px-6

                                        py-3

                                        font-semibold

                                        text-slate-600
                                    "
                                >
                                    Subtotal
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
                                $detailItems
                                as $index => $item
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

                                            text-slate-500
                                        "
                                    >
                                        <?= $index + 1 ?>
                                    </td>


                                    <!-- MENU -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6

                                            py-4

                                            font-medium

                                            text-slate-800
                                        "
                                    >
                                        <?= htmlspecialchars(
                                            $item['nama_menu']
                                        ) ?>
                                    </td>


                                    <!-- KATEGORI -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6

                                            py-4

                                            text-slate-500
                                        "
                                    >
                                        <?= htmlspecialchars(
                                            $item['nama_kategori']
                                                ?? '-'
                                        ) ?>
                                    </td>


                                    <!-- HARGA -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6

                                            py-4

                                            text-right

                                            text-slate-700

                                            whitespace-nowrap
                                        "
                                    >
                                        Rp <?= number_format(
                                            $item['harga'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </td>


                                    <!-- JUMLAH -->

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

                                                min-w-8
                                                h-8

                                                px-2

                                                rounded-lg

                                                bg-emerald-50
                                                text-emerald-700

                                                font-semibold
                                            "
                                        >
                                            <?= (int) $item['jumlah'] ?>
                                        </span>

                                    </td>


                                    <!-- SUBTOTAL -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6

                                            py-4

                                            text-right

                                            font-semibold

                                            text-slate-800

                                            whitespace-nowrap
                                        "
                                    >

                                        Rp <?= number_format(
                                            $item['subtotal'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>


                        <!-- TOTAL -->

                        <tfoot
                            class="
                                bg-slate-50
                            "
                        >

                            <tr>

                                <td
                                    colspan="5"
                                    class="
                                        px-5
                                        sm:px-6

                                        py-4

                                        text-right

                                        font-bold

                                        text-slate-800
                                    "
                                >
                                    Total
                                </td>

                                <td
                                    class="
                                        px-5
                                        sm:px-6

                                        py-4

                                        text-right

                                        font-bold

                                        text-emerald-600

                                        whitespace-nowrap
                                    "
                                >

                                    Rp <?= number_format(
                                        $penjualan['total'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>


            <!-- =====================================================
                 CATATAN
            ====================================================== -->

            <?php if (
                !empty($penjualan['catatan'])
            ): ?>

                <div
                    class="
                        mt-6

                        bg-white

                        rounded-2xl

                        border
                        border-slate-200

                        shadow-sm

                        p-5
                        sm:p-6
                    "
                >

                    <h2
                        class="
                            font-semibold

                            text-slate-800

                            mb-2
                        "
                    >
                        Catatan
                    </h2>

                    <p
                        class="
                            text-slate-600

                            whitespace-pre-line

                            break-words
                        "
                    >
                        <?= htmlspecialchars(
                            $penjualan['catatan']
                        ) ?>
                    </p>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 FOOTER SPACING
            ====================================================== -->

            <div class="h-2"></div>


        </div>

    </main>

</div>


<?php

require_once '../includes/footer.php';

?>