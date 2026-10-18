<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$pageTitle = 'Tambah Menu';


/*
|--------------------------------------------------------------------------
| Ambil kategori aktif
|--------------------------------------------------------------------------
*/

$stmt = $conn->query("
    SELECT
        id,
        nama_kategori
    FROM kategori
    WHERE status = 'aktif'
    ORDER BY nama_kategori ASC
");

$kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);


require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<!-- =========================================================
     PAGE WRAPPER
========================================================= -->

<div class="
    min-h-screen
    w-full
    bg-slate-50
    overflow-x-hidden
">


    <!-- =====================================================
         MAIN CONTENT

         Mobile
         - Tidak ada sidebar
         - Full width

         Tablet
         - Sidebar 80px
         - Content dimulai setelah 80px

         Desktop
         - Sidebar 256px
         - Content dimulai setelah 256px
    ====================================================== -->

    <main
        class="
            min-h-screen
            w-full
            min-w-0
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
             CONTENT CONTAINER
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

                <!-- BACK BUTTON -->

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

                    <i class="
                        fa-solid
                        fa-arrow-left
                        text-xs
                        shrink-0
                    "></i>

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
                        Tambah Menu
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
                        Tambahkan menu baru yang kamu jual.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 ERROR MESSAGE
            ================================================== -->

            <?php if (isset($_GET['error'])): ?>

                <div
                    class="
                        mb-5
                        md:mb-6

                        w-full
                        min-w-0

                        rounded-2xl

                        border
                        border-red-200

                        bg-red-50

                        p-4

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
                 FORM
            ================================================== -->

            <form
                action="simpan.php"
                method="POST"

                class="
                    w-full
                    min-w-0

                    rounded-2xl
                    md:rounded-3xl

                    border
                    border-slate-200

                    bg-white

                    shadow-sm

                    p-5
                    sm:p-6
                    md:p-7
                    lg:p-8
                "
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

                            placeholder="Contoh: Nasi Goreng Spesial"

                            class="
                                block
                                w-full
                                min-w-0

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                px-4
                                py-3

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

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                px-4
                                py-3

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

                            <option value="">
                                -- Pilih Kategori --
                            </option>


                            <?php foreach ($kategori as $item): ?>

                                <option
                                    value="<?= (int) $item['id'] ?>"
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
                                    pointer-events-none

                                    absolute
                                    left-4
                                    top-1/2

                                    -translate-y-1/2

                                    font-medium

                                    text-slate-500
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

                                placeholder="15000"

                                class="
                                    block
                                    w-full
                                    min-w-0

                                    rounded-xl

                                    border
                                    border-slate-300

                                    bg-white

                                    pl-12
                                    pr-4
                                    py-3

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


                        <p
                            class="
                                mt-2

                                text-xs

                                leading-relaxed

                                text-slate-400
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

                                rounded-xl

                                border
                                border-slate-300

                                bg-white

                                px-5
                                py-3

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

                                rounded-xl

                                bg-blue-600

                                px-5
                                py-3

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

                            Simpan Menu

                        </button>

                    </div>

                </div>

            </form>


        </div>

    </main>

</div>


<?php require_once '../includes/footer.php'; ?>