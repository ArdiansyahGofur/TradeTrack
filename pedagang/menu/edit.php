<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


/*
|--------------------------------------------------------------------------
| Validasi ID
|--------------------------------------------------------------------------
*/

if ($id <= 0) {

    header(
        'Location: index.php?error=' .
        urlencode('Menu tidak valid.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil menu milik pedagang
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM menu
    WHERE id = ?
      AND user_id = ?
    LIMIT 1
");

$stmt->execute([
    $id,
    $userId
]);

$menu = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Menu tidak ditemukan
|--------------------------------------------------------------------------
*/

if (!$menu) {

    header(
        'Location: index.php?error=' .
        urlencode('Menu tidak ditemukan atau bukan milik Anda.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil kategori
|--------------------------------------------------------------------------
|
| Kategori aktif tetap tersedia.
| Kategori yang sedang digunakan tetap ditampilkan
| walaupun statusnya sudah nonaktif.
|
*/

$stmt = $conn->prepare("
    SELECT
        id,
        nama_kategori
    FROM kategori
    WHERE status = 'aktif'
       OR id = ?
    ORDER BY nama_kategori ASC
");

$stmt->execute([
    $menu['kategori_id']
]);

$kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);


$pageTitle = 'Edit Menu';


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
         MAIN CONTENT

         Mobile  : full width
         Tablet  : setelah sidebar 80px
         Desktop : setelah sidebar 256px
    ====================================================== -->

    <main
        class="
            w-full
            min-w-0
            min-h-screen

            overflow-x-hidden

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
                max-w-3xl
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
                    mb-6
                    sm:mb-7
                    md:mb-8
                "
            >


                <!-- BACK -->

                <a
                    href="index.php"

                    class="
                        inline-flex
                        items-center
                        gap-2

                        max-w-full

                        text-sm
                        font-medium

                        text-blue-600
                        hover:text-blue-700

                        transition
                    "
                >

                    <i
                        class="
                            fa-solid
                            fa-arrow-left
                            text-xs
                            shrink-0
                        "
                    ></i>

                    <span class="truncate">
                        Kembali ke Menu Saya
                    </span>

                </a>


                <!-- TITLE -->

                <div class="mt-4 md:mt-5">

                    <p
                        class="
                            mb-1

                            text-xs
                            sm:text-sm

                            font-semibold

                            text-blue-600
                        "
                    >
                        Menu Saya
                    </p>


                    <h1
                        class="
                            text-2xl
                            sm:text-3xl

                            font-bold

                            tracking-tight
                            leading-tight

                            text-slate-800
                        "
                    >
                        Edit Menu
                    </h1>


                    <p
                        class="
                            mt-1

                            text-sm
                            sm:text-base

                            leading-relaxed

                            text-slate-500
                        "
                    >
                        Perbarui informasi menu kamu.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 ERROR MESSAGE
            ================================================== -->

            <?php if (isset($_GET['error'])): ?>

                <div
                    class="
                        w-full
                        min-w-0

                        mb-5
                        md:mb-6

                        p-4

                        rounded-2xl

                        border
                        border-red-200

                        bg-red-50

                        text-sm
                        text-red-700
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                        "
                    >


                        <!-- ICON -->

                        <div
                            class="
                                w-9
                                h-9
                                shrink-0

                                rounded-xl

                                bg-red-100

                                flex
                                items-center
                                justify-center
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-circle-exclamation

                                    text-red-600
                                "
                            ></i>

                        </div>


                        <!-- MESSAGE -->

                        <div
                            class="
                                min-w-0
                                flex-1
                            "
                        >

                            <p
                                class="
                                    font-semibold
                                    text-red-800
                                "
                            >
                                Gagal
                            </p>


                            <p
                                class="
                                    mt-0.5
                                    leading-relaxed
                                    break-words
                                "
                            >
                                <?= htmlspecialchars(
                                    $_GET['error']
                                ) ?>
                            </p>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <form
                action="update.php"
                method="POST"

                class="
                    w-full
                    min-w-0

                    bg-white

                    rounded-2xl
                    md:rounded-3xl

                    border
                    border-slate-200

                    shadow-sm

                    p-5
                    sm:p-6
                    md:p-7
                    lg:p-8
                "
            >


                <!-- HIDDEN ID -->

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $menu['id'] ?>"
                >


                <div
                    class="
                        space-y-5
                        sm:space-y-6
                    "
                >


                    <!-- =================================================
                         NAMA MENU
                    ================================================== -->

                    <div class="w-full min-w-0">

                        <label
                            for="nama_menu"

                            class="
                                block

                                mb-2

                                text-sm
                                font-semibold

                                text-slate-700
                            "
                        >
                            Nama Menu
                        </label>


                        <input
                            id="nama_menu"

                            type="text"

                            name="nama_menu"

                            required

                            maxlength="150"

                            autocomplete="off"

                            value="<?= htmlspecialchars(
                                $menu['nama_menu']
                            ) ?>"

                            class="
                                block
                                w-full
                                min-w-0

                                px-4
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                text-sm
                                sm:text-base

                                text-slate-700

                                outline-none

                                placeholder:text-slate-400

                                transition

                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-50
                            "
                        >

                    </div>


                    <!-- =================================================
                         KATEGORI
                    ================================================== -->

                    <div class="w-full min-w-0">

                        <label
                            for="kategori_id"

                            class="
                                block

                                mb-2

                                text-sm
                                font-semibold

                                text-slate-700
                            "
                        >
                            Kategori
                        </label>


                        <select
                            id="kategori_id"

                            name="kategori_id"

                            required

                            class="
                                block
                                w-full
                                min-w-0

                                px-4
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                text-sm
                                sm:text-base

                                text-slate-700

                                outline-none

                                transition

                                focus:border-blue-500
                                focus:ring-4
                                focus:ring-blue-50
                            "
                        >

                            <?php foreach ($kategori as $item): ?>

                                <option
                                    value="<?= (int) $item['id'] ?>"

                                    <?= (int) $item['id'] ===
                                        (int) $menu['kategori_id']
                                            ? 'selected'
                                            : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $item['nama_kategori']
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- =================================================
                         HARGA
                    ================================================== -->

                    <div class="w-full min-w-0">

                        <label
                            for="harga"

                            class="
                                block

                                mb-2

                                text-sm
                                font-semibold

                                text-slate-700
                            "
                        >
                            Harga
                        </label>


                        <div class="relative w-full">


                            <!-- RP -->

                            <span
                                class="
                                    absolute

                                    left-4
                                    top-1/2

                                    -translate-y-1/2

                                    pointer-events-none

                                    text-slate-500
                                    font-medium
                                "
                            >
                                Rp
                            </span>


                            <!-- INPUT HARGA -->

                            <input
                                id="harga"

                                type="number"

                                name="harga"

                                required

                                min="1"

                                step="0.01"

                                inputmode="decimal"

                                value="<?= htmlspecialchars(
                                    $menu['harga']
                                ) ?>"

                                class="
                                    block
                                    w-full
                                    min-w-0

                                    pl-12
                                    pr-4

                                    py-3

                                    rounded-xl

                                    border
                                    border-slate-300

                                    bg-white

                                    text-sm
                                    sm:text-base

                                    text-slate-700

                                    outline-none

                                    transition

                                    focus:border-blue-500
                                    focus:ring-4
                                    focus:ring-blue-50
                                "
                            >

                        </div>


                        <p
                            class="
                                mt-2

                                text-xs

                                text-slate-400

                                leading-relaxed
                            "
                        >
                            Masukkan harga jual menu.
                        </p>

                    </div>


                    <!-- =================================================
                         BUTTON
                    ================================================== -->

                    <div
                        class="
                            flex
                            flex-col

                            gap-3

                            pt-2

                            sm:flex-row
                        "
                    >


                        <!-- BATAL -->

                        <a
                            href="index.php"

                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                w-full
                                sm:flex-1

                                px-5
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                text-sm
                                sm:text-base

                                font-semibold

                                text-slate-600

                                transition

                                hover:bg-slate-50

                                focus:outline-none
                                focus:ring-4
                                focus:ring-slate-100
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-xmark
                                    text-xs
                                "
                            ></i>

                            Batal

                        </a>


                        <!-- SIMPAN -->

                        <button
                            type="submit"

                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2

                                w-full
                                sm:flex-1

                                px-5
                                py-3

                                rounded-xl

                                bg-blue-600

                                text-sm
                                sm:text-base

                                font-semibold

                                text-white

                                shadow-sm

                                transition

                                hover:bg-blue-700

                                focus:outline-none
                                focus:ring-4
                                focus:ring-blue-100
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-floppy-disk
                                    text-sm
                                "
                            ></i>

                            Simpan Perubahan

                        </button>

                    </div>


                </div>

            </form>


        </div>

    </main>

</div>


<?php require_once '../includes/footer.php'; ?>