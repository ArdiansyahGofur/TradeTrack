```php
<?php

require_once '../../config/auth.php';

requireRole('admin');

$pageTitle = 'Tambah Pedagang';

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<!-- =========================================================
     PAGE WRAPPER
========================================================= -->

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
                max-w-4xl
                mx-auto

                px-4
                sm:px-6
                lg:px-8
                xl:px-10
            "
        >

            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="mb-6">

                <div class="flex items-center gap-3 mb-2">

                    <!-- Back -->

                    <a
                        href="index.php"
                        title="Kembali"
                        class="
                            inline-flex
                            items-center
                            justify-center

                            w-9
                            h-9
                            shrink-0

                            rounded-xl

                            bg-white
                            border
                            border-slate-200

                            text-slate-500

                            shadow-sm

                            hover:bg-slate-50
                            hover:text-blue-600

                            transition
                        ">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>


                    <!-- Title -->

                    <div class="min-w-0">

                        <p class="text-sm text-slate-400">
                            Pedagang
                        </p>

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl
                                font-bold
                                text-slate-800
                            "
                        >
                            Tambah Pedagang
                        </h1>

                    </div>

                </div>


                <p
                    class="
                        ml-12
                        max-w-2xl

                        text-sm
                        sm:text-base
                        text-slate-500
                    "
                >
                    Buat akun baru untuk pedagang agar dapat
                    mengakses TradeTrack.
                </p>

            </div>


            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <div
                class="
                    overflow-hidden

                    rounded-2xl
                    border
                    border-slate-200

                    bg-white

                    shadow-sm
                "
            >

                <!-- =================================================
                     CARD HEADER
                ================================================== -->

                <div
                    class="
                        border-b
                        border-slate-100

                        px-5
                        py-5

                        sm:px-7
                    "
                >

                    <div class="flex items-center gap-4">

                        <!-- Icon -->

                        <div
                            class="
                                flex
                                h-11
                                w-11
                                shrink-0
                                items-center
                                justify-center

                                rounded-xl

                                bg-blue-100
                                text-blue-600
                            "
                        >
                            <i class="fa-solid fa-user-plus"></i>
                        </div>


                        <!-- Text -->

                        <div class="min-w-0">

                            <h2
                                class="
                                    text-lg
                                    sm:text-xl
                                    font-bold
                                    text-slate-800
                                "
                            >
                                Tambah Akun Pedagang
                            </h2>

                            <p
                                class="
                                    mt-1
                                    text-xs
                                    sm:text-sm
                                    text-slate-400
                                "
                            >
                                Lengkapi informasi akun pedagang di bawah.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     ERROR MESSAGE
                ================================================== -->

                <?php if (isset($_GET['error'])): ?>

                    <div
                        class="
                            mx-5
                            mt-5

                            rounded-xl
                            border
                            border-red-200

                            bg-red-50

                            px-4
                            py-3

                            text-sm
                            text-red-700

                            sm:mx-7
                        "
                    >

                        <div class="flex items-start gap-3">

                            <i
                                class="
                                    fa-solid
                                    fa-circle-exclamation
                                    mt-0.5
                                    shrink-0
                                "
                            ></i>

                            <span class="leading-relaxed">
                                <?= htmlspecialchars($_GET['error']) ?>
                            </span>

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
                        space-y-6
                        p-5
                        sm:p-7
                    "
                >

                    <!-- =================================================
                         INFORMASI AKUN
                    ================================================== -->

                    <section>

                        <div class="mb-4">

                            <h3
                                class="
                                    text-sm
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Informasi Akun
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Informasi yang digunakan untuk login.
                            </p>

                        </div>


                        <div
                            class="
                                grid
                                grid-cols-1
                                gap-5

                                md:grid-cols-2
                            "
                        >

                            <!-- NAMA -->

                            <div class="md:col-span-2">

                                <label
                                    for="nama"
                                    class="
                                        mb-2
                                        block

                                        text-sm
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    Nama Pedagang

                                    <span class="text-red-500">*</span>
                                </label>


                                <div class="relative">

                                    <i
                                        class="
                                            fa-solid
                                            fa-user

                                            absolute
                                            left-4
                                            top-1/2

                                            -translate-y-1/2

                                            text-sm
                                            text-slate-400
                                        "
                                    ></i>

                                    <input
                                        id="nama"
                                        type="text"
                                        name="nama"
                                        required
                                        maxlength="100"
                                        autocomplete="name"
                                        placeholder="Contoh: Budi Santoso"
                                        class="
                                            w-full

                                            rounded-xl
                                            border
                                            border-slate-300
                                            bg-white

                                            py-3
                                            pl-11
                                            pr-4

                                            text-sm
                                            text-slate-700

                                            outline-none

                                            placeholder:text-slate-400

                                            transition

                                            focus:border-blue-500
                                            focus:ring-2
                                            focus:ring-blue-100
                                        "
                                    >

                                </div>

                            </div>


                            <!-- USERNAME -->

                            <div>

                                <label
                                    for="username"
                                    class="
                                        mb-2
                                        block

                                        text-sm
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    Username

                                    <span class="text-red-500">*</span>
                                </label>


                                <div class="relative">

                                    <i
                                        class="
                                            fa-solid
                                            fa-at

                                            absolute
                                            left-4
                                            top-1/2

                                            -translate-y-1/2

                                            text-sm
                                            text-slate-400
                                        "
                                    ></i>

                                    <input
                                        id="username"
                                        type="text"
                                        name="username"
                                        required
                                        maxlength="50"
                                        autocomplete="username"
                                        placeholder="Contoh: budi"
                                        class="
                                            w-full

                                            rounded-xl
                                            border
                                            border-slate-300
                                            bg-white

                                            py-3
                                            pl-11
                                            pr-4

                                            text-sm
                                            text-slate-700

                                            outline-none

                                            placeholder:text-slate-400

                                            transition

                                            focus:border-blue-500
                                            focus:ring-2
                                            focus:ring-blue-100
                                        "
                                    >

                                </div>

                                <p class="mt-1.5 text-xs text-slate-400">
                                    Username digunakan untuk login.
                                </p>

                            </div>


                            <!-- PASSWORD -->

                            <div>

                                <label
                                    for="password"
                                    class="
                                        mb-2
                                        block

                                        text-sm
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    Password

                                    <span class="text-red-500">*</span>
                                </label>


                                <div class="relative">

                                    <i
                                        class="
                                            fa-solid
                                            fa-lock

                                            absolute
                                            left-4
                                            top-1/2

                                            -translate-y-1/2

                                            text-sm
                                            text-slate-400
                                        "
                                    ></i>

                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        required
                                        minlength="6"
                                        autocomplete="new-password"
                                        placeholder="Minimal 6 karakter"
                                        class="
                                            w-full

                                            rounded-xl
                                            border
                                            border-slate-300
                                            bg-white

                                            py-3
                                            pl-11
                                            pr-4

                                            text-sm
                                            text-slate-700

                                            outline-none

                                            placeholder:text-slate-400

                                            transition

                                            focus:border-blue-500
                                            focus:ring-2
                                            focus:ring-blue-100
                                        "
                                    >

                                </div>

                                <p class="mt-1.5 text-xs text-slate-400">
                                    Minimal 6 karakter.
                                </p>

                            </div>

                        </div>

                    </section>


                    <!-- =================================================
                         PEMISAH
                    ================================================== -->

                    <div class="border-t border-slate-100"></div>


                    <!-- =================================================
                         INFORMASI KONTAK
                    ================================================== -->

                    <section>

                        <div class="mb-4">

                            <h3
                                class="
                                    text-sm
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Informasi Kontak
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Informasi tambahan pedagang.
                            </p>

                        </div>


                        <div
                            class="
                                grid
                                grid-cols-1
                                gap-5

                                md:grid-cols-2
                            "
                        >

                            <!-- NO HP -->

                            <div>

                                <label
                                    for="no_hp"
                                    class="
                                        mb-2
                                        block

                                        text-sm
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    Nomor HP
                                </label>


                                <div class="relative">

                                    <i
                                        class="
                                            fa-solid
                                            fa-phone

                                            absolute
                                            left-4
                                            top-1/2

                                            -translate-y-1/2

                                            text-sm
                                            text-slate-400
                                        "
                                    ></i>

                                    <input
                                        id="no_hp"
                                        type="text"
                                        name="no_hp"
                                        maxlength="20"
                                        inputmode="tel"
                                        autocomplete="tel"
                                        placeholder="Contoh: 08123456789"
                                        class="
                                            w-full

                                            rounded-xl
                                            border
                                            border-slate-300
                                            bg-white

                                            py-3
                                            pl-11
                                            pr-4

                                            text-sm
                                            text-slate-700

                                            outline-none

                                            placeholder:text-slate-400

                                            transition

                                            focus:border-blue-500
                                            focus:ring-2
                                            focus:ring-blue-100
                                        "
                                    >

                                </div>

                            </div>


                            <!-- STATUS INFO -->

                            <div
                                class="
                                    flex
                                    items-start
                                    gap-3

                                    rounded-xl
                                    border
                                    border-blue-100

                                    bg-blue-50

                                    px-4
                                    py-3
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-circle-info

                                        mt-0.5
                                        shrink-0

                                        text-blue-500
                                    "
                                ></i>


                                <div class="min-w-0">

                                    <p
                                        class="
                                            text-sm
                                            font-medium
                                            text-blue-700
                                        "
                                    >
                                        Status akun
                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            text-xs
                                            leading-relaxed
                                            text-blue-600
                                        "
                                    >
                                        Akun baru akan dibuat sebagai
                                        pedagang aktif.
                                    </p>

                                </div>

                            </div>


                            <!-- ALAMAT -->

                            <div class="md:col-span-2">

                                <label
                                    for="alamat"
                                    class="
                                        mb-2
                                        block

                                        text-sm
                                        font-semibold
                                        text-slate-700
                                    "
                                >
                                    Alamat
                                </label>


                                <textarea
                                    id="alamat"
                                    name="alamat"
                                    rows="4"
                                    placeholder="Alamat lengkap pedagang..."
                                    class="
                                        w-full

                                        resize-y

                                        rounded-xl
                                        border
                                        border-slate-300
                                        bg-white

                                        px-4
                                        py-3

                                        text-sm
                                        text-slate-700

                                        outline-none

                                        placeholder:text-slate-400

                                        transition

                                        focus:border-blue-500
                                        focus:ring-2
                                        focus:ring-blue-100
                                    "
                                ></textarea>

                            </div>

                        </div>

                    </section>


                    <!-- =================================================
                         BUTTONS
                    ================================================== -->

                    <div
                        class="
                            flex
                            flex-col-reverse
                            gap-3

                            border-t
                            border-slate-100

                            pt-5

                            sm:flex-row
                            sm:justify-end
                        "
                    >

                        <!-- BATAL -->

                        <a
                            href="index.php"
                            class="
                                inline-flex
                                w-full
                                items-center
                                justify-center
                                gap-2

                                rounded-xl

                                border
                                border-slate-200

                                bg-white

                                px-5
                                py-3

                                text-sm
                                font-semibold
                                text-slate-600

                                transition

                                hover:bg-slate-50

                                sm:w-auto
                            "
                        >
                            <i class="fa-solid fa-xmark"></i>
                            Batal
                        </a>


                        <!-- SIMPAN -->

                        <button
                            type="submit"
                            class="
                                inline-flex
                                w-full
                                items-center
                                justify-center
                                gap-2

                                rounded-xl

                                bg-blue-600

                                px-6
                                py-3

                                text-sm
                                font-semibold
                                text-white

                                shadow-sm

                                transition

                                hover:bg-blue-700

                                sm:w-auto
                            "
                        >
                            <i class="fa-solid fa-user-plus"></i>
                            Simpan Pedagang
                        </button>

                    </div>

                </form>

            </div>


            <!-- =================================================
                 FOOTER INFO
            ================================================== -->

            <div
                class="
                    mt-5

                    flex
                    flex-col
                    gap-2

                    text-xs
                    text-slate-400

                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                "
            >

                <p>
                    TradeTrack · Tambah Pedagang
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
```