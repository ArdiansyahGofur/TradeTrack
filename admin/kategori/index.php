<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');

$pageTitle = 'Data Kategori';

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';


/*
|--------------------------------------------------------------------------
| Ambil Data Kategori
|--------------------------------------------------------------------------
*/

$where = [];
$params = [];

if ($search !== '') {

    $where[] = 'nama_kategori LIKE ?';

    $params[] = '%' . $search . '%';
}

$whereSql = '';

if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$stmt = $conn->prepare("
    SELECT
        id,
        nama_kategori,
        status,
        created_at
    FROM kategori
    {$whereSql}
    ORDER BY nama_kategori ASC
");

$stmt->execute($params);

$kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

    <!-- =====================================================
         MAIN CONTENT
         
         Mobile  : tanpa margin kiri
         Tablet  : margin kiri 80px sesuai sidebar w-20
         Desktop : margin kiri 256px sesuai sidebar w-64
    ====================================================== -->

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

                <!-- TITLE -->

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
                        Data Kategori
                    </h1>

                    <p
                        class="
                            text-sm
                            text-slate-500
                            mt-1
                        "
                    >
                        Kelola kategori menu pedagang.
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

                        <i class="fa-solid fa-user text-sm"></i>

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
                                max-w-[180px]
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
                 ALERT SUCCESS
            ====================================================== -->

            <?php if (isset($_GET['success'])): ?>

                <div
                    class="
                        mb-5

                        flex
                        items-start
                        gap-3

                        bg-green-50
                        border
                        border-green-200

                        text-green-700

                        rounded-xl

                        px-4
                        py-3

                        text-sm
                    "
                >

                    <i
                        class="
                            fa-solid
                            fa-circle-check
                            mt-0.5
                            shrink-0
                        "
                    ></i>

                    <span class="break-words">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </span>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 ALERT ERROR
            ====================================================== -->

            <?php if (isset($_GET['error'])): ?>

                <div
                    class="
                        mb-5

                        flex
                        items-start
                        gap-3

                        bg-red-50
                        border
                        border-red-200

                        text-red-700

                        rounded-xl

                        px-4
                        py-3

                        text-sm
                    "
                >

                    <i
                        class="
                            fa-solid
                            fa-circle-exclamation
                            mt-0.5
                            shrink-0
                        "
                    ></i>

                    <span class="break-words">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </span>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 INTRO CARD
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
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between

                        gap-4
                    "
                >

                    <!-- INFO -->

                    <div
                        class="
                            flex
                            items-start
                            gap-4

                            min-w-0
                        "
                    >

                        <div
                            class="
                                w-11
                                h-11
                                shrink-0

                                rounded-xl

                                bg-indigo-50
                                text-indigo-600

                                flex
                                items-center
                                justify-center
                            "
                        >

                            <i class="fa-solid fa-tags"></i>

                        </div>


                        <div class="min-w-0">

                            <h2
                                class="
                                    text-lg
                                    sm:text-xl

                                    font-bold

                                    text-slate-800
                                "
                            >
                                Kelola Kategori
                            </h2>

                            <p
                                class="
                                    text-sm
                                    text-slate-500

                                    mt-1

                                    max-w-xl
                                "
                            >
                                Kelola kategori yang digunakan
                                untuk mengelompokkan menu pedagang.
                            </p>

                        </div>

                    </div>


                    <!-- BUTTON -->

                    <a
                        href="tambah.php"
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

                            bg-blue-600
                            hover:bg-blue-700

                            text-white

                            text-sm
                            font-semibold

                            shadow-sm

                            transition

                            shrink-0
                        "
                    >

                        <i class="fa-solid fa-plus"></i>

                        Tambah Kategori

                    </a>

                </div>

            </div>


            <!-- =====================================================
                 SEARCH
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

                <div class="mb-4">

                    <h3
                        class="
                            text-sm
                            font-semibold
                            text-slate-800
                        "
                    >
                        Cari Kategori
                    </h3>

                    <p
                        class="
                            text-xs
                            text-slate-400
                            mt-1
                        "
                    >
                        Gunakan pencarian untuk menemukan kategori.
                    </p>

                </div>


                <form method="GET">

                    <div
                        class="
                            flex
                            flex-col
                            sm:flex-row

                            gap-3
                        "
                    >

                        <!-- SEARCH INPUT -->

                        <div class="relative flex-1 min-w-0">

                            <i
                                class="
                                    fa-solid
                                    fa-magnifying-glass

                                    absolute
                                    left-4
                                    top-1/2
                                    -translate-y-1/2

                                    text-slate-400
                                "
                            ></i>

                            <input
                                type="text"
                                name="search"
                                value="<?= htmlspecialchars($search) ?>"
                                placeholder="Cari nama kategori..."
                                class="
                                    w-full

                                    pl-11
                                    pr-4
                                    py-3

                                    rounded-xl

                                    border
                                    border-slate-300

                                    bg-white

                                    text-sm
                                    text-slate-700

                                    placeholder:text-slate-400

                                    focus:outline-none
                                    focus:border-blue-500
                                    focus:ring-2
                                    focus:ring-blue-100

                                    transition
                                "
                            >

                        </div>


                        <!-- SEARCH BUTTON -->

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

                                bg-blue-600
                                hover:bg-blue-700

                                text-white

                                text-sm
                                font-semibold

                                transition

                                shrink-0
                            "
                        >

                            <i class="fa-solid fa-magnifying-glass"></i>

                            Cari

                        </button>


                        <!-- RESET -->

                        <?php if ($search !== ''): ?>

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

                                    shrink-0
                                "
                            >

                                <i class="fa-solid fa-rotate-left"></i>

                                Reset

                            </a>

                        <?php endif; ?>

                    </div>

                </form>

            </div>


            <!-- =====================================================
                 DATA CARD
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

                        gap-2
                    "
                >

                    <div>

                        <h2
                            class="
                                font-bold
                                text-slate-800
                            "
                        >
                            Daftar Kategori
                        </h2>

                        <p
                            class="
                                text-sm
                                text-slate-400
                                mt-1
                            "
                        >
                            <?= count($kategori) ?> kategori terdaftar
                        </p>

                    </div>


                    <?php if ($search !== ''): ?>

                        <span
                            class="
                                inline-flex
                                items-center
                                gap-2

                                w-fit

                                px-3
                                py-1.5

                                rounded-lg

                                bg-blue-50
                                text-blue-600

                                text-xs
                                font-semibold
                            "
                        >

                            <i class="fa-solid fa-filter"></i>

                            Hasil pencarian

                        </span>

                    <?php endif; ?>

                </div>


                <!-- =================================================
                     TABLE
                     
                     Tablet/mobile:
                     tabel dapat digeser horizontal
                ================================================== -->

                <div class="overflow-x-auto">

                    <table
                        class="
                            w-full
                            min-w-[720px]
                        "
                    >

                        <thead class="bg-slate-50">

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
                                    Nama Kategori
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
                                    Status
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
                                    Dibuat
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

                        <?php if (empty($kategori)): ?>

                            <!-- =================================================
                                 EMPTY STATE
                            ================================================== -->

                            <tr>

                                <td
                                    colspan="5"
                                    class="
                                        px-6
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
                                                fa-tags
                                                text-xl
                                            "
                                        ></i>

                                    </div>


                                    <p
                                        class="
                                            font-semibold
                                            text-slate-700
                                        "
                                    >
                                        Belum ada kategori
                                    </p>


                                    <p
                                        class="
                                            text-sm
                                            text-slate-400
                                            mt-1
                                        "
                                    >
                                        Tambahkan kategori untuk
                                        mengelompokkan menu.
                                    </p>


                                    <a
                                        href="tambah.php"
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2

                                            mt-5

                                            px-4
                                            py-2.5

                                            rounded-xl

                                            bg-blue-50
                                            hover:bg-blue-100

                                            text-blue-600

                                            text-sm
                                            font-semibold

                                            transition
                                        "
                                    >

                                        <i class="fa-solid fa-plus"></i>

                                        Tambah Kategori

                                    </a>

                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($kategori as $index => $item): ?>

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

                                        <?= $index + 1 ?>

                                    </td>


                                    <!-- NAMA -->

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

                                            <div
                                                class="
                                                    w-10
                                                    h-10
                                                    shrink-0

                                                    rounded-xl

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
                                                        fa-tag
                                                        text-sm
                                                    "
                                                ></i>

                                            </div>


                                            <div class="min-w-0">

                                                <p
                                                    class="
                                                        font-semibold
                                                        text-slate-700

                                                        truncate
                                                    "
                                                >
                                                    <?= htmlspecialchars(
                                                        $item['nama_kategori']
                                                    ) ?>
                                                </p>

                                                <p
                                                    class="
                                                        text-xs
                                                        text-slate-400
                                                        mt-0.5
                                                    "
                                                >
                                                    Kategori menu
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- STATUS -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-center
                                        "
                                    >

                                        <?php if (
                                            $item['status'] === 'aktif'
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
                                                    text-green-600

                                                    text-xs
                                                    font-semibold
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

                                                    bg-red-50
                                                    text-red-600

                                                    text-xs
                                                    font-semibold
                                                "
                                            >

                                                <span
                                                    class="
                                                        w-1.5
                                                        h-1.5

                                                        rounded-full

                                                        bg-red-500
                                                    "
                                                ></span>

                                                Nonaktif

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- CREATED -->

                                    <td
                                        class="
                                            px-5
                                            sm:px-6
                                            py-4

                                            text-center

                                            text-sm
                                            text-slate-500
                                        "
                                    >

                                        <div
                                            class="
                                                inline-flex
                                                items-center
                                                gap-2
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-regular
                                                    fa-calendar
                                                    text-slate-400
                                                    text-xs
                                                "
                                            ></i>

                                            <?= date(
                                                'd M Y',
                                                strtotime(
                                                    $item['created_at']
                                                )
                                            ) ?>

                                        </div>

                                    </td>


                                    <!-- ACTION -->

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
                                                gap-2
                                            "
                                        >

                                            <!-- EDIT -->

                                            <a
                                                href="edit.php?id=<?= $item['id'] ?>"
                                                title="Edit kategori"
                                                class="
                                                    w-9
                                                    h-9

                                                    inline-flex
                                                    items-center
                                                    justify-center

                                                    rounded-lg

                                                    bg-blue-50
                                                    text-blue-600

                                                    hover:bg-blue-100

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
                                                href="status.php?id=<?= $item['id'] ?>"
                                                title="Ubah status"
                                                onclick="return confirm(
                                                    'Yakin ingin mengubah status kategori ini?'
                                                )"
                                                class="
                                                    w-9
                                                    h-9

                                                    inline-flex
                                                    items-center
                                                    justify-center

                                                    rounded-lg

                                                    bg-amber-50
                                                    text-amber-600

                                                    hover:bg-amber-100

                                                    transition
                                                "
                                            >

                                                <i
                                                    class="
                                                        fa-solid
                                                        fa-power-off
                                                        text-xs
                                                    "
                                                ></i>

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <!-- =================================================
                     TABLE FOOTER
                ================================================== -->

                <?php if (!empty($kategori)): ?>

                    <div
                        class="
                            px-5
                            sm:px-6

                            py-4

                            border-t
                            border-slate-100

                            bg-slate-50/50
                        "
                    >

                        <p
                            class="
                                text-xs
                                text-slate-400
                            "
                        >

                            Menampilkan

                            <span
                                class="
                                    font-semibold
                                    text-slate-600
                                "
                            >
                                <?= count($kategori) ?>
                            </span>

                            kategori

                            <?php if ($search !== ''): ?>

                                untuk pencarian

                                <span
                                    class="
                                        font-semibold
                                        text-slate-600
                                    "
                                >
                                    "<?= htmlspecialchars($search) ?>"
                                </span>

                            <?php endif; ?>

                        </p>

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
                    TradeTrack · Data Kategori
                </p>

                <p>

                    Total:

                    <span
                        class="
                            font-semibold
                            text-slate-500
                        "
                    >
                        <?= count($kategori) ?>
                    </span>

                    kategori

                </p>

            </div>


        </div>

    </main>

</div>


<?php require_once '../includes/footer.php'; ?>