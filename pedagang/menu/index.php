<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';


/*
|--------------------------------------------------------------------------
| FILTER QUERY
|--------------------------------------------------------------------------
*/

$where = [
    'm.user_id = ?'
];

$params = [$userId];


if ($search !== '') {

    $where[] = "
        (
            m.nama_menu LIKE ?
            OR k.nama_kategori LIKE ?
        )
    ";

    $keyword = '%' . $search . '%';

    $params[] = $keyword;
    $params[] = $keyword;
}


$whereSql = implode(' AND ', $where);


/*
|--------------------------------------------------------------------------
| AMBIL MENU
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.nama_menu,
        m.harga,
        m.status,
        m.created_at,
        k.nama_kategori
    FROM menu m

    INNER JOIN kategori k
        ON m.kategori_id = k.id

    WHERE {$whereSql}

    ORDER BY m.nama_menu ASC
");

$stmt->execute($params);

$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<?php require_once '../includes/header.php'; ?>
<?php require_once '../includes/sidebar.php'; ?>


<!-- ================================================================
     PAGE WRAPPER
================================================================ -->

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">


    <!-- ============================================================
         MAIN AREA

         Mobile:
         Full width

         Tablet:
         Sidebar mini = 80px

         Desktop:
         Sidebar penuh = 256px
    ============================================================= -->

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


        <!-- ========================================================
             CENTER CONTAINER
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
                 PAGE HEADER
            ===================================================== -->

            <div
                class="
                    flex
                    flex-col
                    gap-4

                    md:flex-row
                    md:items-end
                    md:justify-between

                    mb-6
                    sm:mb-8
                "
            >


                <!-- =================================================
                     TITLE
                ================================================== -->

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
                        Kelola Produk
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
                        Menu Saya 🍽️
                    </h1>


                    <p
                        class="
                            text-sm
                            sm:text-base
                            text-slate-500
                            mt-1
                        "
                    >
                        Kelola daftar menu yang kamu jual.
                    </p>

                </div>


                <!-- =================================================
                     ADD BUTTON
                ================================================== -->

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

                    Tambah Menu

                </a>

            </div>


            <!-- ====================================================
                 FLASH MESSAGE - SUCCESS
            ===================================================== -->

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

                    <div class="flex items-start gap-3">

                        <span class="text-base flex-shrink-0">
                            ✅
                        </span>

                        <span class="min-w-0 break-words">
                            <?= htmlspecialchars($_GET['success']) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>


            <!-- ====================================================
                 FLASH MESSAGE - ERROR
            ===================================================== -->

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

                    <div class="flex items-start gap-3">

                        <span class="text-base flex-shrink-0">
                            ⚠️
                        </span>

                        <span class="min-w-0 break-words">
                            <?= htmlspecialchars($_GET['error']) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>


            <!-- ====================================================
                 MENU CARD
            ===================================================== -->

            <div
                class="
                    w-full
                    bg-white

                    rounded-2xl

                    border
                    border-slate-200

                    shadow-sm

                    overflow-hidden
                "
            >


                <?php if (count($menus) > 0): ?>


                    <!-- =================================================
                         CARD HEADER
                    ================================================== -->

                    <div
                        class="
                            px-4
                            sm:px-6
                            py-5

                            border-b
                            border-slate-100
                        "
                    >

                        <div
                            class="
                                flex
                                flex-col

                                gap-5

                                lg:flex-row
                                lg:items-center
                                lg:justify-between
                            "
                        >


                            <!-- =============================================
                                 TITLE
                            ============================================== -->

                            <div class="min-w-0">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="
                                            w-9
                                            h-9

                                            rounded-xl

                                            bg-blue-50
                                            text-blue-600

                                            flex
                                            items-center
                                            justify-center

                                            flex-shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-utensils
                                                text-sm
                                            "
                                        ></i>

                                    </div>


                                    <div class="min-w-0">

                                        <h3
                                            class="
                                                font-bold
                                                text-slate-800
                                            "
                                        >
                                            Daftar Menu
                                        </h3>


                                        <p
                                            class="
                                                text-xs
                                                text-slate-400
                                                mt-0.5
                                            "
                                        >
                                            <?= count($menus) ?> menu tersedia
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- =============================================
                                 SEARCH FORM
                            ============================================== -->

                            <form
                                method="GET"
                                class="
                                    w-full
                                    lg:w-auto
                                "
                            >

                                <div
                                    class="
                                        flex
                                        flex-col

                                        sm:flex-row

                                        gap-2

                                        w-full
                                    "
                                >


                                    <!-- =====================================
                                         SEARCH INPUT
                                    ====================================== -->

                                    <div
                                        class="
                                            relative

                                            w-full
                                            sm:w-72
                                            md:w-64
                                            lg:w-80
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-magnifying-glass

                                                absolute

                                                left-4
                                                top-1/2

                                                -translate-y-1/2

                                                text-slate-400
                                                text-sm

                                                pointer-events-none
                                            "
                                        ></i>


                                        <input
                                            type="text"
                                            name="search"
                                            value="<?= htmlspecialchars($search) ?>"
                                            placeholder="Cari menu atau kategori..."

                                            class="
                                                w-full

                                                pl-10
                                                pr-4
                                                py-2.5

                                                rounded-xl

                                                border
                                                border-slate-200

                                                bg-slate-50

                                                text-sm
                                                text-slate-700

                                                placeholder-slate-400

                                                outline-none

                                                transition

                                                focus:bg-white
                                                focus:border-blue-500
                                                focus:ring-2
                                                focus:ring-blue-100
                                            "
                                        >

                                    </div>


                                    <!-- =====================================
                                         SEARCH BUTTON
                                    ====================================== -->

                                    <button
                                        type="submit"
                                        class="
                                            inline-flex
                                            items-center
                                            justify-center
                                            gap-2

                                            px-4
                                            py-2.5

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

                                        <i
                                            class="
                                                fa-solid
                                                fa-magnifying-glass
                                            "
                                        ></i>

                                        <span>Cari</span>

                                    </button>


                                    <!-- =====================================
                                         RESET
                                    ====================================== -->

                                    <?php if ($search !== ''): ?>

                                        <a
                                            href="index.php"
                                            class="
                                                inline-flex
                                                items-center
                                                justify-center
                                                gap-2

                                                px-4
                                                py-2.5

                                                rounded-xl

                                                bg-slate-100
                                                hover:bg-slate-200

                                                text-slate-600

                                                text-sm
                                                font-semibold

                                                transition

                                                whitespace-nowrap
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-rotate-left
                                                "
                                            ></i>

                                            <span>Reset</span>

                                        </a>

                                    <?php endif; ?>


                                </div>

                            </form>

                        </div>

                    </div>


                    <!-- =================================================
                         TABLE WRAPPER

                         Tablet:
                         Tabel tetap utuh.
                         Jika terlalu lebar, scroll horizontal.
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
                                    bg-slate-50/80
                                    border-b
                                    border-slate-200
                                "
                            >

                                <tr>


                                    <!-- MENU -->

                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-left

                                            whitespace-nowrap

                                            text-xs
                                            font-semibold
                                            uppercase
                                            tracking-wide

                                            text-slate-500
                                        "
                                    >
                                        Menu
                                    </th>


                                    <!-- KATEGORI -->

                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-left

                                            whitespace-nowrap

                                            text-xs
                                            font-semibold
                                            uppercase
                                            tracking-wide

                                            text-slate-500
                                        "
                                    >
                                        Kategori
                                    </th>


                                    <!-- HARGA -->

                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-left

                                            whitespace-nowrap

                                            text-xs
                                            font-semibold
                                            uppercase
                                            tracking-wide

                                            text-slate-500
                                        "
                                    >
                                        Harga
                                    </th>


                                    <!-- STATUS -->

                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-left

                                            whitespace-nowrap

                                            text-xs
                                            font-semibold
                                            uppercase
                                            tracking-wide

                                            text-slate-500
                                        "
                                    >
                                        Status
                                    </th>


                                    <!-- AKSI -->

                                    <th
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-right

                                            whitespace-nowrap

                                            text-xs
                                            font-semibold
                                            uppercase
                                            tracking-wide

                                            text-slate-500
                                        "
                                    >
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <!-- =========================================
                                 TABLE BODY
                            ========================================== -->

                            <tbody class="divide-y divide-slate-100">


                                <?php foreach ($menus as $menu): ?>


                                    <tr
                                        class="
                                            group

                                            hover:bg-slate-50/70

                                            transition
                                        "
                                    >


                                        <!-- =================================
                                             MENU
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
                                                    gap-3
                                                "
                                            >


                                                <!-- ICON -->

                                                <div
                                                    class="
                                                        w-10
                                                        h-10

                                                        flex-shrink-0

                                                        rounded-xl

                                                        bg-blue-50
                                                        text-blue-600

                                                        flex
                                                        items-center
                                                        justify-center

                                                        group-hover:bg-blue-100

                                                        transition
                                                    "
                                                >

                                                    <i
                                                        class="
                                                            fa-solid
                                                            fa-utensils
                                                            text-sm
                                                        "
                                                    ></i>

                                                </div>


                                                <!-- NAME -->

                                                <div class="min-w-0">

                                                    <p
                                                        class="
                                                            font-semibold
                                                            text-slate-800

                                                            truncate
                                                            max-w-[220px]
                                                            md:max-w-[180px]
                                                            lg:max-w-[260px]
                                                        "
                                                        title="<?= htmlspecialchars($menu['nama_menu']) ?>"
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

                                                            whitespace-nowrap
                                                        "
                                                    >

                                                        <i
                                                            class="
                                                                fa-regular
                                                                fa-calendar
                                                                mr-1
                                                            "
                                                        ></i>

                                                        <?= date(
                                                            'd M Y',
                                                            strtotime(
                                                                $menu['created_at']
                                                            )
                                                        ) ?>

                                                    </p>

                                                </div>

                                            </div>

                                        </td>


                                        <!-- =================================
                                             KATEGORI
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
                                                    items-center

                                                    px-3
                                                    py-1.5

                                                    rounded-lg

                                                    bg-indigo-50
                                                    text-indigo-700

                                                    text-xs
                                                    font-semibold

                                                    whitespace-nowrap
                                                "
                                            >

                                                <?= htmlspecialchars(
                                                    $menu['nama_kategori']
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- =================================
                                             HARGA
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
                                                    font-bold
                                                    text-slate-800

                                                    whitespace-nowrap
                                                "
                                            >

                                                Rp <?= number_format(
                                                    $menu['harga'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- =================================
                                             STATUS
                                        ================================== -->

                                        <td
                                            class="
                                                px-5
                                                sm:px-6
                                                py-4
                                            "
                                        >


                                            <?php if (
                                                $menu['status'] === 'aktif'
                                            ): ?>


                                                <span
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        gap-1.5

                                                        px-3
                                                        py-1.5

                                                        rounded-full

                                                        bg-green-50
                                                        text-green-700

                                                        text-xs
                                                        font-semibold

                                                        whitespace-nowrap
                                                    "
                                                >

                                                    <span
                                                        class="
                                                            w-1.5
                                                            h-1.5

                                                            rounded-full

                                                            bg-green-500
                                                        "
                                                    ></span>

                                                    Aktif

                                                </span>


                                            <?php else: ?>


                                                <span
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        gap-1.5

                                                        px-3
                                                        py-1.5

                                                        rounded-full

                                                        bg-slate-100
                                                        text-slate-600

                                                        text-xs
                                                        font-semibold

                                                        whitespace-nowrap
                                                    "
                                                >

                                                    <span
                                                        class="
                                                            w-1.5
                                                            h-1.5

                                                            rounded-full

                                                            bg-slate-400
                                                        "
                                                    ></span>

                                                    Nonaktif

                                                </span>


                                            <?php endif; ?>

                                        </td>


                                        <!-- =================================
                                             AKSI
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
                                                    justify-end
                                                    items-center
                                                    gap-2
                                                "
                                            >


                                                <!-- EDIT -->

                                                <a
                                                    href="edit.php?id=<?= $menu['id'] ?>"
                                                    title="Edit menu"

                                                    class="
                                                        w-9
                                                        h-9

                                                        flex-shrink-0

                                                        inline-flex
                                                        items-center
                                                        justify-center

                                                        rounded-lg

                                                        bg-amber-50
                                                        text-amber-600

                                                        hover:bg-amber-100
                                                        hover:text-amber-700

                                                        transition
                                                    "
                                                >

                                                    <i
                                                        class="
                                                            fa-solid
                                                            fa-pen
                                                            text-xs
                                                        "
                                                    ></i>

                                                </a>


                                                <!-- STATUS -->

                                                <a
                                                    href="status.php?id=<?= $menu['id'] ?>"
                                                    title="Ubah status"

                                                    onclick="
                                                        return confirm(
                                                            'Ubah status menu ini?'
                                                        )
                                                    "

                                                    class="
                                                        w-9
                                                        h-9

                                                        flex-shrink-0

                                                        inline-flex
                                                        items-center
                                                        justify-center

                                                        rounded-lg

                                                        bg-blue-50
                                                        text-blue-600

                                                        hover:bg-blue-100
                                                        hover:text-blue-700

                                                        transition
                                                    "
                                                >

                                                    <i
                                                        class="
                                                            fa-solid
                                                            fa-rotate
                                                            text-xs
                                                        "
                                                    ></i>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>


                                <?php endforeach; ?>


                            </tbody>

                        </table>

                    </div>


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

                        <!-- ICON -->

                        <div
                            class="
                                w-16
                                h-16

                                mx-auto
                                mb-5

                                rounded-2xl

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
                                    fa-utensils
                                    text-2xl
                                "
                            ></i>

                        </div>


                        <?php if ($search !== ''): ?>


                            <!-- =========================================
                                 EMPTY SEARCH
                            ========================================== -->

                            <h3
                                class="
                                    text-lg
                                    font-bold
                                    text-slate-800

                                    mb-2
                                "
                            >
                                Menu tidak ditemukan
                            </h3>


                            <p
                                class="
                                    text-slate-500
                                    text-sm

                                    max-w-md
                                    mx-auto

                                    mb-6
                                "
                            >

                                Tidak ada menu atau kategori yang cocok
                                dengan pencarian

                                <strong>
                                    "<?= htmlspecialchars($search) ?>"
                                </strong>.

                            </p>


                            <a
                                href="index.php"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    bg-slate-100
                                    hover:bg-slate-200

                                    text-slate-700

                                    font-semibold

                                    px-5
                                    py-3

                                    rounded-xl

                                    transition
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-rotate-left
                                    "
                                ></i>

                                Reset Pencarian

                            </a>


                        <?php else: ?>


                            <!-- =========================================
                                 EMPTY MENU
                            ========================================== -->

                            <h3
                                class="
                                    text-lg
                                    font-bold
                                    text-slate-800

                                    mb-2
                                "
                            >
                                Belum ada menu
                            </h3>


                            <p
                                class="
                                    text-slate-500
                                    text-sm

                                    max-w-md
                                    mx-auto

                                    mb-6
                                "
                            >

                                Tambahkan menu pertama kamu untuk mulai
                                mencatat dan mengelola penjualan.

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

                                <i class="fa-solid fa-plus"></i>

                                Tambah Menu

                            </a>


                        <?php endif; ?>

                    </div>


                <?php endif; ?>


            </div>
            <!-- END MENU CARD -->


        </div>
        <!-- END CENTER CONTAINER -->


    </main>


</div>


<?php require_once '../includes/footer.php'; ?>