<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');

$pageTitle = 'Data Pedagang';

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';

/* =========================================================
   AMBIL DATA PEDAGANG
========================================================= */

$where = ["role = 'pedagang'"];
$params = [];

if ($search !== '') {

    $where[] = "
        (
            nama LIKE ?
            OR username LIKE ?
            OR no_hp LIKE ?
        )
    ";

    $keyword = '%' . $search . '%';

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
}

$whereSql = implode(' AND ', $where);

$stmt = $conn->prepare("
    SELECT
        id,
        nama,
        username,
        no_hp,
        alamat,
        status
    FROM users
    WHERE {$whereSql}
    ORDER BY nama ASC
");

$stmt->execute($params);

$pedagang = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   HEADER & SIDEBAR
========================================================= */

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main
        class="
            min-h-screen
            min-w-0

            pt-20
            pb-24

            md:ml-20
            lg:ml-64

            md:w-[calc(100%-5rem)]
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
                max-w-6xl
                mx-auto

                px-4
                sm:px-6
                lg:px-8
            "
        >

            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div
                class="
                    mb-6
                    flex
                    flex-col
                    gap-4

                    md:flex-row
                    md:items-center
                    md:justify-between
                "
            >

                <div class="min-w-0">

                    <p class="text-sm text-slate-400 mb-1">
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
                        Data Pedagang
                    </h1>

                    <p class="text-sm sm:text-base text-slate-500 mt-1">
                        Kelola akun pedagang yang menggunakan TradeTrack.
                    </p>

                </div>

                <a
                    href="tambah.php"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2

                        w-full
                        md:w-auto
                        shrink-0

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
                    "
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Pedagang
                </a>

            </div>


            <!-- =================================================
                 ALERT SUCCESS
            ================================================== -->

            <?php if (isset($_GET['success'])): ?>

                <div
                    class="
                        mb-5
                        rounded-xl
                        border border-green-200
                        bg-green-50
                        px-4 py-3
                        text-sm
                        text-green-700
                    "
                >
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>

                        <span>
                            <?= htmlspecialchars($_GET['success']) ?>
                        </span>
                    </div>
                </div>

            <?php endif; ?>


            <!-- =================================================
                 ALERT ERROR
            ================================================== -->

            <?php if (isset($_GET['error'])): ?>

                <div
                    class="
                        mb-5
                        rounded-xl
                        border border-red-200
                        bg-red-50
                        px-4 py-3
                        text-sm
                        text-red-700
                    "
                >
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>

                        <span>
                            <?= htmlspecialchars($_GET['error']) ?>
                        </span>
                    </div>
                </div>

            <?php endif; ?>


            <!-- =================================================
                 SEARCH CARD
            ================================================== -->

            <div
                class="
                    mb-6
                    bg-white
                    border border-slate-200
                    rounded-2xl
                    shadow-sm
                    p-4 sm:p-5
                "
            >

                <div
                    class="
                        flex
                        flex-col
                        gap-4

                        md:flex-row
                        md:items-center
                        md:justify-between
                    "
                >

                    <div class="min-w-0">

                        <h2 class="text-base sm:text-lg font-semibold text-slate-800">
                            Cari Pedagang
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Cari berdasarkan nama, username, atau nomor HP.
                        </p>

                    </div>


                    <!-- Search Form -->

                    <form
                        method="GET"
                        class="
                            w-full
                            md:max-w-xl
                        "
                    >

                        <div
                            class="
                                flex
                                flex-col
                                sm:flex-row
                                gap-2
                            "
                        >

                            <!-- Input -->

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
                                    placeholder="Nama, username, atau nomor HP..."
                                    class="
                                        w-full
                                        pl-11 pr-4
                                        py-3
                                        rounded-xl

                                        border border-slate-300
                                        bg-white

                                        text-sm
                                        text-slate-700

                                        placeholder:text-slate-400

                                        outline-none

                                        focus:border-blue-500
                                        focus:ring-2
                                        focus:ring-blue-100

                                        transition
                                    "
                                >

                            </div>


                            <!-- Cari -->

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    px-5
                                    py-3
                                    rounded-xl

                                    bg-blue-600
                                    hover:bg-blue-700

                                    text-white
                                    text-sm
                                    font-medium

                                    transition
                                "
                            >
                                <i class="fa-solid fa-magnifying-glass"></i>
                                Cari
                            </button>


                            <!-- Reset -->

                            <?php if ($search !== ''): ?>

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

                                        bg-slate-100
                                        hover:bg-slate-200

                                        text-slate-700
                                        text-sm
                                        font-medium

                                        transition
                                    "
                                >
                                    <i class="fa-solid fa-rotate-left"></i>
                                    Reset
                                </a>

                            <?php endif; ?>

                        </div>

                    </form>

                </div>

            </div>


            <!-- =================================================
                 DATA CARD
            ================================================== -->

            <div
                class="
                    bg-white
                    border border-slate-200
                    rounded-2xl
                    shadow-sm
                    overflow-hidden
                "
            >

                <!-- Card Header -->

                <div
                    class="
                        px-4 sm:px-6
                        py-5

                        border-b border-slate-100

                        flex
                        flex-col
                        gap-3

                        md:flex-row
                        md:items-center
                        md:justify-between
                    "
                >

                    <div class="min-w-0">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Daftar Pedagang
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-400 mt-1">

                            Menampilkan

                            <span class="font-semibold text-slate-600">
                                <?= count($pedagang) ?>
                            </span>

                            pedagang

                            <?php if ($search !== ''): ?>

                                untuk pencarian

                                <span class="font-medium text-slate-600">
                                    "<?= htmlspecialchars($search) ?>"
                                </span>

                            <?php endif; ?>

                        </p>

                    </div>


                    <!-- Total -->

                    <div
                        class="
                            inline-flex
                            items-center
                            gap-2
                            self-start

                            px-3 py-2

                            rounded-lg

                            bg-blue-50
                            text-blue-600

                            text-xs
                            font-semibold
                        "
                    >
                        <i class="fa-solid fa-users"></i>

                        <?= count($pedagang) ?> Pedagang
                    </div>

                </div>


                <!-- =================================================
                     TABLE
                ================================================== -->

                <?php if (!empty($pedagang)): ?>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[900px] text-sm">

                            <thead class="bg-slate-50 border-b border-slate-200">

                                <tr>

                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 whitespace-nowrap">
                                        No
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500">
                                        Pedagang
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500">
                                        Username
                                    </th>

                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500">
                                        Kontak
                                    </th>

                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500">
                                        Status
                                    </th>

                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-100">

                                <?php foreach ($pedagang as $index => $item): ?>

                                    <tr class="hover:bg-slate-50 transition">

                                        <!-- NO -->

                                        <td class="px-6 py-4 text-sm text-slate-500 align-middle">
                                            <?= $index + 1 ?>
                                        </td>


                                        <!-- PEDAGANG -->

                                        <td class="px-6 py-4 align-middle">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="
                                                        w-10 h-10
                                                        shrink-0

                                                        rounded-xl

                                                        bg-blue-100
                                                        text-blue-600

                                                        flex
                                                        items-center
                                                        justify-center

                                                        font-bold
                                                        uppercase
                                                    "
                                                >
                                                    <?= strtoupper(
                                                        substr(
                                                            trim($item['nama']),
                                                            0,
                                                            1
                                                        )
                                                    ) ?>
                                                </div>


                                                <div class="min-w-0">

                                                    <p
                                                        class="
                                                            font-semibold
                                                            text-slate-700
                                                            truncate
                                                            max-w-[220px]
                                                        "
                                                    >
                                                        <?= htmlspecialchars($item['nama']) ?>
                                                    </p>

                                                    <p
                                                        class="
                                                            text-xs
                                                            text-slate-400
                                                            truncate
                                                            max-w-[220px]
                                                            mt-1
                                                        "
                                                    >
                                                        <?= htmlspecialchars($item['alamat'] ?? '-') ?>
                                                    </p>

                                                </div>

                                            </div>

                                        </td>


                                        <!-- USERNAME -->

                                        <td class="px-6 py-4 text-sm text-slate-600">

                                            <span class="inline-flex items-center gap-2">

                                                <i class="fa-solid fa-at text-slate-400 text-xs"></i>

                                                <?= htmlspecialchars($item['username']) ?>

                                            </span>

                                        </td>


                                        <!-- KONTAK -->

                                        <td class="px-6 py-4 text-sm text-slate-600">

                                            <span class="inline-flex items-center gap-2">

                                                <i class="fa-solid fa-phone text-slate-400 text-xs"></i>

                                                <?= htmlspecialchars($item['no_hp'] ?? '-') ?>

                                            </span>

                                        </td>


                                        <!-- STATUS -->

                                        <td class="px-6 py-4 text-center">

                                            <?php if ($item['status'] === 'aktif'): ?>

                                                <span
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        gap-1.5

                                                        px-3 py-1.5
                                                        rounded-full

                                                        bg-green-50
                                                        text-green-600

                                                        text-xs
                                                        font-semibold
                                                    "
                                                >
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    Aktif
                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        gap-1.5

                                                        px-3 py-1.5
                                                        rounded-full

                                                        bg-red-50
                                                        text-red-600

                                                        text-xs
                                                        font-semibold
                                                    "
                                                >
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                    Nonaktif
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- AKSI -->

                                        <td class="px-6 py-4 text-right">

                                            <div class="flex items-center justify-end gap-2">

                                                <a
                                                    href="edit.php?id=<?= $item['id'] ?>"
                                                    title="Edit Pedagang"
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        justify-center

                                                        w-9 h-9
                                                        rounded-lg

                                                        bg-blue-50
                                                        text-blue-600
                                                        hover:bg-blue-100

                                                        transition
                                                    "
                                                >
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </a>


                                                <a
                                                    href="status.php?id=<?= $item['id'] ?>"
                                                    title="Ubah Status"
                                                    onclick="return confirm('Yakin ingin mengubah status pedagang ini?')"
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        justify-center

                                                        w-9 h-9
                                                        rounded-lg

                                                        bg-amber-50
                                                        text-amber-600
                                                        hover:bg-amber-100

                                                        transition
                                                    "
                                                >
                                                    <i class="fa-solid fa-power-off text-xs"></i>
                                                </a>


                                                <a
                                                    href="reset_password.php?id=<?= $item['id'] ?>"
                                                    title="Reset Password"
                                                    onclick="return confirm('Password pedagang akan direset. Lanjutkan?')"
                                                    class="
                                                        inline-flex
                                                        items-center
                                                        justify-center

                                                        w-9 h-9
                                                        rounded-lg

                                                        bg-slate-100
                                                        text-slate-600
                                                        hover:bg-slate-200

                                                        transition
                                                    "
                                                >
                                                    <i class="fa-solid fa-key text-xs"></i>
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

                    <div class="px-6 py-16 text-center">

                        <div
                            class="
                                w-16 h-16
                                mx-auto

                                rounded-2xl

                                bg-slate-100
                                text-slate-400

                                flex items-center justify-center
                            "
                        >
                            <i class="fa-solid fa-users text-2xl"></i>
                        </div>

                        <h3 class="mt-4 font-semibold text-slate-700">

                            <?php if ($search !== ''): ?>
                                Pedagang tidak ditemukan
                            <?php else: ?>
                                Belum ada pedagang
                            <?php endif; ?>

                        </h3>

                        <p class="text-sm text-slate-400 mt-1 max-w-md mx-auto">

                            <?php if ($search !== ''): ?>

                                Tidak ada pedagang yang sesuai dengan pencarian Anda.

                            <?php else: ?>

                                Tambahkan pedagang pertama untuk mulai menggunakan TradeTrack.

                            <?php endif; ?>

                        </p>


                        <?php if ($search !== ''): ?>

                            <a
                                href="index.php"
                                class="
                                    inline-flex
                                    items-center
                                    gap-2

                                    mt-5
                                    px-4 py-2.5

                                    rounded-xl

                                    bg-slate-100
                                    hover:bg-slate-200

                                    text-sm
                                    font-medium
                                    text-slate-700

                                    transition
                                "
                            >
                                <i class="fa-solid fa-rotate-left"></i>
                                Reset Pencarian
                            </a>

                        <?php else: ?>

                            <a
                                href="tambah.php"
                                class="
                                    inline-flex
                                    items-center
                                    gap-2

                                    mt-5
                                    px-4 py-2.5

                                    rounded-xl

                                    bg-blue-600
                                    hover:bg-blue-700

                                    text-sm
                                    font-medium
                                    text-white

                                    transition
                                "
                            >
                                <i class="fa-solid fa-plus"></i>
                                Tambah Pedagang
                            </a>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 FOOTER INFO
            ================================================== -->

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
                    TradeTrack · Manajemen Pedagang
                </p>

                <p>
                    Login sebagai:
                    <span class="font-medium text-slate-500">
                        <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>
                    </span>
                </p>

            </div>

        </div>

    </main>

</div>

<?php require_once '../includes/footer.php'; ?>