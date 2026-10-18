<?php

require_once '../../config/auth.php';

requireRole('admin');

$pageTitle = 'Tambah Kategori';

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

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
                max-w-5xl
                mx-auto

                px-4
                sm:px-6
                md:px-6
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
                        items-center
                        gap-3
                        mb-4
                    "
                >

                    <!-- KEMBALI -->

                    <a
                        href="index.php"
                        class="
                            w-10
                            h-10
                            shrink-0

                            inline-flex
                            items-center
                            justify-center

                            rounded-xl

                            bg-white
                            border
                            border-slate-200

                            text-slate-500

                            hover:bg-slate-50
                            hover:text-slate-700

                            shadow-sm

                            transition
                        "
                        title="Kembali"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                    </a>


                    <!-- JUDUL -->

                    <div class="min-w-0">

                        <p
                            class="
                                text-sm
                                text-slate-400
                            "
                        >
                            Kategori
                        </p>

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl

                                font-bold

                                text-slate-800

                                truncate
                            "
                        >
                            Tambah Kategori
                        </h1>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 FORM CONTAINER
            ====================================================== -->

            <div
                class="
                    w-full
                    max-w-3xl
                    mx-auto
                "
            >

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


                    <!-- =================================================
                         CARD HEADER
                    ================================================== -->

                    <div
                        class="
                            px-5
                            sm:px-7
                            md:px-6
                            lg:px-7

                            py-5
                            sm:py-6

                            border-b
                            border-slate-100
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                gap-4
                            "
                        >

                            <!-- ICON -->

                            <div
                                class="
                                    w-12
                                    h-12
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
                                        text-lg
                                    "
                                ></i>

                            </div>


                            <!-- TEXT -->

                            <div class="min-w-0">

                                <h2
                                    class="
                                        text-lg
                                        sm:text-xl

                                        font-bold

                                        text-slate-800
                                    "
                                >
                                    Tambah Kategori
                                </h2>

                                <p
                                    class="
                                        text-sm
                                        text-slate-400

                                        mt-1

                                        leading-relaxed
                                    "
                                >
                                    Masukkan nama kategori baru
                                    untuk digunakan pada menu.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         FORM BODY
                    ================================================== -->

                    <div
                        class="
                            px-5
                            sm:px-7
                            md:px-6
                            lg:px-7

                            py-6
                            sm:py-7
                        "
                    >


                        <!-- =================================================
                             ERROR
                        ================================================== -->

                        <?php if (isset($_GET['error'])): ?>

                            <div
                                class="
                                    mb-6

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

                                    leading-relaxed
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

                                    <?= htmlspecialchars(
                                        $_GET['error']
                                    ) ?>

                                </span>

                            </div>

                        <?php endif; ?>


                        <!-- =================================================
                             FORM
                        ================================================== -->

                        <form
                            action="simpan.php"
                            method="POST"
                            class="space-y-6"
                        >


                            <!-- =================================================
                                 NAMA KATEGORI
                            ================================================== -->

                            <div>

                                <label
                                    for="nama_kategori"
                                    class="
                                        block

                                        text-sm
                                        font-semibold

                                        text-slate-700

                                        mb-2
                                    "
                                >

                                    Nama Kategori

                                    <span
                                        class="
                                            text-red-500
                                        "
                                    >
                                        *
                                    </span>

                                </label>


                                <div class="relative">

                                    <!-- ICON -->

                                    <i
                                        class="
                                            fa-solid
                                            fa-tag

                                            absolute
                                            left-4
                                            top-1/2
                                            -translate-y-1/2

                                            text-slate-400
                                        "
                                    ></i>


                                    <!-- INPUT -->

                                    <input
                                        id="nama_kategori"
                                        type="text"
                                        name="nama_kategori"
                                        required
                                        maxlength="100"
                                        autocomplete="off"
                                        placeholder="Contoh: Makanan"
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


                                <!-- INFO INPUT -->

                                <p
                                    class="
                                        text-xs
                                        text-slate-400

                                        mt-2

                                        leading-relaxed
                                    "
                                >
                                    Gunakan nama kategori yang singkat
                                    dan mudah dikenali.
                                </p>

                            </div>


                            <!-- =================================================
                                 BUTTON
                            ================================================== -->

                            <div
                                class="
                                    flex
                                    flex-col-reverse

                                    sm:flex-row

                                    gap-3

                                    pt-2
                                "
                            >

                                <!-- BATAL -->

                                <a
                                    href="index.php"
                                    class="
                                        w-full
                                        sm:w-auto

                                        inline-flex
                                        items-center
                                        justify-center
                                        gap-2

                                        px-5
                                        py-3

                                        rounded-xl

                                        border
                                        border-slate-200

                                        bg-white

                                        text-slate-600

                                        hover:bg-slate-50

                                        text-sm
                                        font-semibold

                                        transition
                                    "
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-arrow-left
                                        "
                                    ></i>

                                    Batal

                                </a>


                                <!-- SIMPAN -->

                                <button
                                    type="submit"
                                    class="
                                        w-full
                                        sm:flex-1

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
                                        font-semibold

                                        shadow-sm

                                        transition
                                    "
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-floppy-disk
                                        "
                                    ></i>

                                    Simpan Kategori

                                </button>

                            </div>


                        </form>

                    </div>

                </div>


                <!-- =====================================================
                     INFO
                ====================================================== -->

                <div
                    class="
                        mt-5

                        flex
                        items-start
                        gap-3

                        px-4
                        py-3

                        rounded-xl

                        bg-blue-50
                        border
                        border-blue-100

                        text-blue-700
                    "
                >

                    <i
                        class="
                            fa-solid
                            fa-circle-info
                            mt-0.5
                            shrink-0
                        "
                    ></i>

                    <p
                        class="
                            text-xs
                            sm:text-sm

                            leading-relaxed
                        "
                    >
                        Kategori yang dibuat dapat digunakan untuk
                        mengelompokkan menu pada TradeTrack.
                    </p>

                </div>


            </div>


        </div>

    </main>

</div>


<?php

require_once '../includes/footer.php';

?>