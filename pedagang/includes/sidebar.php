<?php

/*
|--------------------------------------------------------------------------
| DETEKSI HALAMAN AKTIF
|--------------------------------------------------------------------------
*/

$currentPage   = basename($_SERVER['PHP_SELF']);
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

?>



<!-- ========================================================= -->
<!-- SIDEBAR TABLET + DESKTOP -->
<!-- ========================================================= -->

<aside
    class="
        fixed
        inset-y-0
        left-0
        z-40

        hidden
        md:flex
        flex-col

        w-20
        lg:w-64

        bg-white
        border-r
        border-slate-200

        transition-all
        duration-300
    "
>


    <!-- ===================================================== -->
    <!-- LOGO -->
    <!-- ===================================================== -->

    <div
        class="
            h-20

            px-2
            lg:px-5

            border-b
            border-slate-200

            flex
            items-center
            justify-center
            lg:justify-start
        "
    >

        <a
            href="../dashboard/index.php"
            class="
                flex
                items-center

                gap-3

                min-w-0
            "
        >

            <!-- LOGO -->

            <div
                class="
                    w-10
                    h-10

                    rounded-xl

                    bg-white
                    border
                    border-slate-200

                    flex
                    items-center
                    justify-center

                    overflow-hidden
                    flex-shrink-0
                "
            >

                <img
                    src="../../assets/img/logo-new.png"
                    alt="TradeTrack"
                    class="w-8 h-8 object-contain"
                >

            </div>


            <!-- NAMA APLIKASI -->

            <div
                class="
                    hidden
                    lg:block

                    min-w-0
                    leading-tight
                "
            >

                <h1
                    class="
                        font-bold
                        text-lg
                        text-slate-800
                        truncate
                    "
                >
                    TradeTrack
                </h1>


                <p
                    class="
                        text-xs
                        text-slate-500
                        mt-0.5
                    "
                >
                    Pedagang
                </p>

            </div>

        </a>

    </div>



    <!-- ===================================================== -->
    <!-- NAVIGATION -->
    <!-- ===================================================== -->

    <nav
        class="
            flex-1

            p-2
            lg:p-4

            space-y-2

            overflow-y-auto
        "
    >


        <!-- =================================================
             DASHBOARD
        ================================================== -->

        <a
            href="../dashboard/index.php"
            title="Dashboard"
            class="
                group

                flex
                items-center

                justify-center
                lg:justify-start

                gap-3

                px-3
                lg:px-4

                py-3

                rounded-xl

                transition

                <?= $currentFolder === 'dashboard'
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >

            <span
                class="
                    w-6
                    text-center
                    text-lg
                    flex-shrink-0
                "
            >
                📊
            </span>


            <span
                class="
                    hidden
                    lg:block

                    font-medium

                    truncate
                "
            >
                Dashboard
            </span>

        </a>



        <!-- =================================================
             MENU
        ================================================== -->

        <a
            href="../menu/index.php"
            title="Menu Saya"
            class="
                group

                flex
                items-center

                justify-center
                lg:justify-start

                gap-3

                px-3
                lg:px-4

                py-3

                rounded-xl

                transition

                <?= $currentFolder === 'menu'
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >

            <span
                class="
                    w-6
                    text-center
                    text-lg
                    flex-shrink-0
                "
            >
                🍽️
            </span>


            <span
                class="
                    hidden
                    lg:block

                    font-medium

                    truncate
                "
            >
                Menu Saya
            </span>

        </a>



        <!-- =================================================
             PENJUALAN
        ================================================== -->

        <a
            href="../penjualan/index.php"
            title="Penjualan"
            class="
                group

                flex
                items-center

                justify-center
                lg:justify-start

                gap-3

                px-3
                lg:px-4

                py-3

                rounded-xl

                transition

                <?= $currentFolder === 'penjualan'
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >

            <span
                class="
                    w-6
                    text-center
                    text-lg
                    flex-shrink-0
                "
            >
                🛒
            </span>


            <span
                class="
                    hidden
                    lg:block

                    font-medium

                    truncate
                "
            >
                Penjualan
            </span>

        </a>



        <!-- =================================================
             LAPORAN
        ================================================== -->

        <a
            href="../laporan/index.php"
            title="Laporan"
            class="
                group

                flex
                items-center

                justify-center
                lg:justify-start

                gap-3

                px-3
                lg:px-4

                py-3

                rounded-xl

                transition

                <?= $currentFolder === 'laporan'
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >

            <span
                class="
                    w-6
                    text-center
                    text-lg
                    flex-shrink-0
                "
            >
                📑
            </span>


            <span
                class="
                    hidden
                    lg:block

                    font-medium

                    truncate
                "
            >
                Laporan
            </span>

        </a>



        <!-- =================================================
             PROFIL
        ================================================== -->

        <a
            href="../profile/index.php"
            title="Profil"
            class="
                group

                flex
                items-center

                justify-center
                lg:justify-start

                gap-3

                px-3
                lg:px-4

                py-3

                rounded-xl

                transition

                <?= $currentFolder === 'profile'
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >

            <span
                class="
                    w-6
                    text-center
                    text-lg
                    flex-shrink-0
                "
            >
                👤
            </span>


            <span
                class="
                    hidden
                    lg:block

                    font-medium

                    truncate
                "
            >
                Profil
            </span>

        </a>

    </nav>



    <!-- ===================================================== -->
    <!-- LOGOUT -->
    <!-- ===================================================== -->

    <div
        class="
            p-2
            lg:p-4

            border-t
            border-slate-200
        "
    >

        <a
            href="../../logout.php"
            title="Logout"
            class="
                flex
                items-center

                justify-center
                lg:justify-start

                gap-3

                px-3
                lg:px-4

                py-3

                rounded-xl

                text-red-600

                hover:bg-red-50

                transition
            "
        >

            <span
                class="
                    w-6
                    text-center
                    text-lg
                    flex-shrink-0
                "
            >
                🚪
            </span>


            <span
                class="
                    hidden
                    lg:block

                    font-medium
                "
            >
                Logout
            </span>

        </a>

    </div>

</aside>



<!-- ========================================================= -->
<!-- MOBILE TOP BAR -->
<!-- ========================================================= -->

<div
    class="
        md:hidden

        fixed
        top-0
        left-0
        right-0

        z-50

        h-16

        bg-white
        border-b
        border-slate-200
    "
>

    <div
        class="
            h-full
            w-full

            px-4

            flex
            items-center
            justify-between
        "
    >


        <!-- LOGO -->

        <a
            href="../dashboard/index.php"
            class="
                flex
                items-center
                gap-2

                min-w-0
            "
        >

            <div
                class="
                    w-9
                    h-9

                    rounded-lg

                    bg-white
                    border
                    border-slate-200

                    flex
                    items-center
                    justify-center

                    overflow-hidden

                    flex-shrink-0
                "
            >

                <img
                    src="../../assets/img/logo-new.png"
                    alt="TradeTrack"
                    class="w-7 h-7 object-contain"
                >

            </div>


            <div class="min-w-0">

                <div
                    class="
                        font-bold
                        text-slate-800

                        leading-tight

                        truncate
                    "
                >
                    TradeTrack
                </div>


                <div
                    class="
                        text-[10px]
                        text-slate-400
                    "
                >
                    Pedagang
                </div>

            </div>

        </a>



        <!-- LOGOUT -->

        <a
            href="../../logout.php"
            title="Logout"
            class="
                w-9
                h-9

                rounded-lg

                flex
                items-center
                justify-center

                text-red-500

                hover:bg-red-50

                transition

                flex-shrink-0
            "
        >
            🚪
        </a>

    </div>

</div>



<!-- ========================================================= -->
<!-- MOBILE BOTTOM NAVIGATION -->
<!-- ========================================================= -->

<nav
    class="
        md:hidden

        fixed
        bottom-0
        left-0
        right-0

        z-50

        h-16

        bg-white

        border-t
        border-slate-200

        shadow-[0_-2px_10px_rgba(0,0,0,0.05)]
    "
>

    <div class="grid grid-cols-5 h-full">


        <!-- DASHBOARD -->

        <a
            href="../dashboard/index.php"
            class="
                min-w-0

                flex
                flex-col

                items-center
                justify-center

                gap-1

                transition

                <?= $currentFolder === 'dashboard'
                    ? 'text-blue-600'
                    : 'text-slate-400 hover:text-blue-600'
                ?>
            "
        >

            <span class="text-lg leading-none">
                📊
            </span>

            <span
                class="
                    text-[10px]
                    font-semibold
                    truncate
                    max-w-full
                    px-1
                "
            >
                Dashboard
            </span>

        </a>



        <!-- MENU -->

        <a
            href="../menu/index.php"
            class="
                min-w-0

                flex
                flex-col

                items-center
                justify-center

                gap-1

                transition

                <?= $currentFolder === 'menu'
                    ? 'text-blue-600'
                    : 'text-slate-400 hover:text-blue-600'
                ?>
            "
        >

            <span class="text-lg leading-none">
                🍽️
            </span>

            <span
                class="
                    text-[10px]
                    font-semibold
                "
            >
                Menu
            </span>

        </a>



        <!-- PENJUALAN -->

        <a
            href="../penjualan/index.php"
            class="
                min-w-0

                flex
                flex-col

                items-center
                justify-center

                gap-1

                transition

                <?= $currentFolder === 'penjualan'
                    ? 'text-blue-600'
                    : 'text-slate-400 hover:text-blue-600'
                ?>
            "
        >

            <span class="text-lg leading-none">
                🛒
            </span>

            <span
                class="
                    text-[10px]
                    font-semibold
                    truncate
                    max-w-full
                    px-1
                "
            >
                Penjualan
            </span>

        </a>



        <!-- LAPORAN -->

        <a
            href="../laporan/index.php"
            class="
                min-w-0

                flex
                flex-col

                items-center
                justify-center

                gap-1

                transition

                <?= $currentFolder === 'laporan'
                    ? 'text-blue-600'
                    : 'text-slate-400 hover:text-blue-600'
                ?>
            "
        >

            <span class="text-lg leading-none">
                📑
            </span>

            <span
                class="
                    text-[10px]
                    font-semibold
                "
            >
                Laporan
            </span>

        </a>



        <!-- PROFIL -->

        <a
            href="../profile/index.php"
            class="
                min-w-0

                flex
                flex-col

                items-center
                justify-center

                gap-1

                transition

                <?= $currentFolder === 'profile'
                    ? 'text-blue-600'
                    : 'text-slate-400 hover:text-blue-600'
                ?>
            "
        >

            <span class="text-lg leading-none">
                👤
            </span>

            <span
                class="
                    text-[10px]
                    font-semibold
                "
            >
                Profil
            </span>

        </a>

    </div>

</nav>