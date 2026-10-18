<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| ID TRANSAKSI
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($id <= 0) {

    header(
        'Location: index.php?error=' .
        urlencode('Transaksi tidak valid.')
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| AMBIL TRANSAKSI UTAMA
|--------------------------------------------------------------------------
|
| user_id memastikan pedagang hanya dapat
| melihat transaksi miliknya sendiri.
|
*/

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.user_id,
        p.tanggal,
        p.total,
        p.catatan,
        u.nama AS nama_pedagang
    FROM penjualan p

    INNER JOIN users u
        ON p.user_id = u.id

    WHERE p.id = ?
      AND p.user_id = ?

    LIMIT 1
");

$stmt->execute([
    $id,
    $userId
]);

$penjualan = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$penjualan) {

    header(
        'Location: index.php?error=' .
        urlencode(
            'Transaksi tidak ditemukan atau bukan milik Anda.'
        )
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| AMBIL DETAIL TRANSAKSI
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        dp.id,
        dp.menu_id,
        dp.harga,
        dp.jumlah,
        dp.subtotal,
        m.nama_menu,
        k.nama_kategori

    FROM detail_penjualan dp

    INNER JOIN menu m
        ON dp.menu_id = m.id

    INNER JOIN kategori k
        ON m.kategori_id = k.id

    WHERE dp.penjualan_id = ?

    ORDER BY dp.id ASC
");

$stmt->execute([$id]);

$detail = $stmt->fetchAll(PDO::FETCH_ASSOC);


$pageTitle = 'Detail Transaksi';


require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

    <main
        class="
            w-full
            min-w-0

            pt-20
            pb-24

            lg:ml-64
            lg:w-[calc(100%-16rem)]

            lg:pt-8
            lg:pb-8
        "
    >

        <!-- =================================================
             CONTAINER CENTER
        ================================================== -->

        <div
            class="
                w-full
                max-w-6xl
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

            <div class="mb-6 lg:mb-8">

                <!-- BACK -->

                <a
                    href="index.php"

                    class="
                        inline-flex
                        items-center
                        gap-1

                        text-sm

                        text-blue-600
                        hover:text-blue-700

                        font-medium

                        transition
                    "
                >

                    ← Kembali ke Penjualan

                </a>


                <!-- HEADER CONTENT -->

                <div
                    class="
                        mt-4

                        flex
                        flex-col
                        md:flex-row

                        md:items-start
                        md:justify-between

                        gap-5
                    "
                >

                    <!-- TITLE -->

                    <div class="min-w-0">

                        <p
                            class="
                                text-sm
                                text-blue-600
                                font-semibold

                                mb-1
                            "
                        >
                            Detail Transaksi
                        </p>


                        <h1
                            class="
                                text-2xl
                                sm:text-3xl

                                font-bold
                                text-slate-800

                                break-words
                            "
                        >

                            Transaksi #<?= (int) $penjualan['id'] ?>

                        </h1>


                        <p
                            class="
                                text-sm
                                sm:text-base

                                text-slate-500

                                mt-1
                            "
                        >

                            <?= date(
                                'd F Y, H:i',
                                strtotime(
                                    $penjualan['tanggal']
                                )
                            ) ?>

                            WIB

                        </p>

                    </div>


                    <!-- TOTAL -->

                    <div
                        class="
                            w-full
                            md:w-auto

                            min-w-0
                            md:min-w-[220px]

                            bg-green-50

                            border
                            border-green-200

                            rounded-2xl

                            px-5
                            py-4
                        "
                    >

                        <p
                            class="
                                text-sm
                                text-green-700

                                mb-1
                            "
                        >
                            Total Transaksi
                        </p>


                        <p
                            class="
                                text-2xl
                                font-bold
                                text-green-700

                                break-words
                            "
                        >

                            Rp
                            <?= number_format(
                                $penjualan['total'],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </p>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 INFORMASI TRANSAKSI
            ================================================== -->

            <div
                class="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    md:grid-cols-3

                    gap-4

                    mb-6
                "
            >


                <!-- PEDAGANG -->

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
                            font-bold
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


                <!-- JUMLAH ITEM -->

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

                    <p
                        class="
                            text-sm
                            text-slate-500
                        "
                    >
                        Jumlah Item
                    </p>


                    <p
                        class="
                            font-bold
                            text-slate-800

                            mt-1
                        "
                    >

                        <?= count($detail) ?>

                        jenis menu

                    </p>

                </div>


                <!-- WAKTU -->

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

                    <p
                        class="
                            text-sm
                            text-slate-500
                        "
                    >
                        Waktu
                    </p>


                    <p
                        class="
                            font-bold
                            text-slate-800

                            mt-1
                        "
                    >

                        <?= date(
                            'H:i',
                            strtotime(
                                $penjualan['tanggal']
                            )
                        ) ?>

                        WIB

                    </p>

                </div>

            </div>


            <!-- =================================================
                 RINCIAN PESANAN
            ================================================== -->

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

                <!-- HEADER -->

                <div
                    class="
                        px-5
                        sm:px-6

                        py-5

                        border-b
                        border-slate-200
                    "
                >

                    <h2
                        class="
                            font-bold
                            text-slate-800
                        "
                    >
                        Rincian Pesanan
                    </h2>


                    <p
                        class="
                            text-sm
                            text-slate-500

                            mt-1
                        "
                    >
                        Daftar menu dalam transaksi ini.
                    </p>

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

                        <!-- TABLE HEADER -->

                        <thead
                            class="
                                bg-slate-50

                                border-b
                                border-slate-200
                            "
                        >

                            <tr>

                                <th
                                    class="
                                        text-left

                                        px-5
                                        sm:px-6
                                        py-4

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
                                        py-4

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
                                        py-4

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
                                        py-4

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
                                        py-4

                                        font-semibold
                                        text-slate-600
                                    "
                                >
                                    Subtotal
                                </th>

                            </tr>

                        </thead>


                        <!-- TABLE BODY -->

                        <tbody
                            class="
                                divide-y
                                divide-slate-100
                            "
                        >

                            <?php foreach (
                                $detail
                                as $item
                            ): ?>

                                <tr
                                    class="
                                        hover:bg-slate-50
                                        transition
                                    "
                                >


                                    <!-- MENU -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4
                                        "
                                    >

                                        <div
                                            class="
                                                font-semibold
                                                text-slate-800
                                            "
                                        >

                                            <?= htmlspecialchars(
                                                $item['nama_menu']
                                            ) ?>

                                        </div>

                                    </td>


                                    <!-- KATEGORI -->

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

                                            <?= htmlspecialchars(
                                                $item['nama_kategori']
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- HARGA -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-right

                                            whitespace-nowrap

                                            text-slate-600
                                        "
                                    >

                                        Rp
                                        <?= number_format(
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
                                                px-2
                                                py-1

                                                rounded-lg

                                                bg-slate-100

                                                font-semibold
                                                text-slate-700
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

                                            font-bold
                                            text-slate-800

                                            whitespace-nowrap
                                        "
                                    >

                                        Rp
                                        <?= number_format(
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
                                bg-blue-50

                                border-t
                                border-blue-100
                            "
                        >

                            <tr>

                                <td
                                    colspan="4"

                                    class="
                                        px-5
                                        sm:px-6
                                        py-5

                                        text-right

                                        font-bold
                                        text-slate-700
                                    "
                                >
                                    TOTAL
                                </td>


                                <td
                                    class="
                                        px-5
                                        sm:px-6
                                        py-5

                                        text-right

                                        font-bold
                                        text-xl

                                        text-blue-700

                                        whitespace-nowrap
                                    "
                                >

                                    Rp
                                    <?= number_format(
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


            <!-- =================================================
                 CATATAN
            ================================================== -->

            <?php if (
                !empty(
                    $penjualan['catatan']
                )
            ): ?>

                <div
                    class="
                        mt-6

                        bg-amber-50

                        border
                        border-amber-200

                        rounded-2xl

                        p-5
                        sm:p-6

                        shadow-sm
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                shrink-0

                                rounded-xl

                                bg-amber-100

                                flex
                                items-center
                                justify-center

                                text-lg
                            "
                        >
                            📝
                        </div>


                        <div
                            class="
                                min-w-0
                            "
                        >

                            <h3
                                class="
                                    font-bold
                                    text-amber-800
                                "
                            >
                                Catatan
                            </h3>


                            <p
                                class="
                                    text-sm
                                    text-amber-700

                                    mt-1

                                    leading-relaxed

                                    break-words
                                "
                            >

                                <?= nl2br(
                                    htmlspecialchars(
                                        $penjualan['catatan']
                                    )
                                ) ?>

                            </p>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 BACK BUTTON
            ================================================== -->

            <div class="mt-6">

                <a
                    href="index.php"

                    class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2

                        px-5
                        py-3

                        rounded-xl

                        border
                        border-slate-300

                        bg-white

                        text-slate-600

                        hover:bg-slate-50

                        font-semibold

                        transition

                        shadow-sm
                    "
                >

                    ← Kembali ke Penjualan

                </a>

            </div>


        </div>

    </main>

</div>


<?php require_once '../includes/footer.php'; ?>