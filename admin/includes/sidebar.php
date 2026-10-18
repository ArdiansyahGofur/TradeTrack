<?php

/* =========================================================
   DETEKSI MENU AKTIF
   Berdasarkan nama folder halaman saat ini
========================================================= */

$currentSection = basename(dirname($_SERVER['PHP_SELF']));

?>


<!-- =========================================================
     DESKTOP SIDEBAR
     Tampil mulai ukuran lg (1024px)
========================================================= -->

<aside
    class="
        hidden lg:flex
        fixed inset-y-0 left-0
        z-40
        w-64
        bg-white
        border-r border-slate-200
        flex-col
    "
>

    <!-- =====================================================
         LOGO
    ====================================================== -->

    <div class="p-6 border-b border-slate-100">

        <div class="flex items-center gap-3">

            <!-- Logo -->
            <div
                class="
                    w-11 h-11
                    shrink-0
                    flex items-center justify-center
                    rounded-xl
                    bg-white
                    border border-slate-200
                    shadow-sm
                    overflow-hidden
                "
            >
                <img
                    src="../../assets/img/logo-new.png"
                    alt="TradeTrack Logo"
                    class="w-8 h-8 object-contain"
                >
            </div>

            <!-- Brand -->
            <div class="min-w-0">

                <h1 class="text-lg font-bold text-slate-800 leading-tight">
                    TradeTrack
                </h1>

                <p class="mt-0.5 text-xs text-slate-400">
                    Admin Panel
                </p>

            </div>

        </div>

    </div>


    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">

        <!-- Dashboard -->
        <a
            href="../dashboard/index.php"
            class="
                flex items-center gap-3
                px-4 py-3
                rounded-xl
                transition
                <?= $currentSection === 'dashboard'
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-lg shrink-0">
                📊
            </span>

            <span>
                Dashboard
            </span>
        </a>


        <!-- Pedagang -->
        <a
            href="../pedagang/index.php"
            class="
                flex items-center gap-3
                px-4 py-3
                rounded-xl
                transition
                <?= $currentSection === 'pedagang'
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-lg shrink-0">
                👥
            </span>

            <span>
                Pedagang
            </span>
        </a>


        <!-- Kategori -->
        <a
            href="../kategori/index.php"
            class="
                flex items-center gap-3
                px-4 py-3
                rounded-xl
                transition
                <?= $currentSection === 'kategori'
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-lg shrink-0">
                🏷️
            </span>

            <span>
                Kategori
            </span>
        </a>


        <!-- Laporan -->
        <a
            href="../laporan/index.php"
            class="
                flex items-center gap-3
                px-4 py-3
                rounded-xl
                transition
                <?= $currentSection === 'laporan'
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-lg shrink-0">
                📈
            </span>

            <span>
                Laporan
            </span>
        </a>


        <!-- Profil -->
        <a
            href="../profile/index.php"
            class="
                flex items-center gap-3
                px-4 py-3
                rounded-xl
                transition
                <?= $currentSection === 'profile'
                    ? 'bg-blue-50 text-blue-600 font-semibold'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-lg shrink-0">
                👤
            </span>

            <span>
                Profil
            </span>
        </a>

    </nav>


    <!-- =====================================================
         LOGOUT
    ====================================================== -->

    <div class="p-4 border-t border-slate-100">

        <a
            href="../../logout.php"
            class="
                flex items-center gap-3
                px-4 py-3
                rounded-xl
                text-red-500
                hover:bg-red-50
                transition
            "
        >
            <span class="text-lg shrink-0">
                🚪
            </span>

            <span>
                Logout
            </span>
        </a>

    </div>

</aside>



<!-- =========================================================
     TABLET SIDEBAR
     Tampil 768px - 1023px
========================================================= -->

<aside
    class="
        hidden md:flex lg:hidden
        fixed inset-y-0 left-0
        z-40
        w-20
        bg-white
        border-r border-slate-200
        flex-col
        items-center
    "
>

    <!-- =====================================================
         LOGO
    ====================================================== -->

    <div
        class="
            w-full
            p-4
            flex items-center justify-center
            border-b border-slate-100
        "
    >

        <div
            class="
                w-11 h-11
                flex items-center justify-center
                rounded-xl
                bg-white
                border border-slate-200
                shadow-sm
                overflow-hidden
            "
        >

            <img
                src="../../assets/img/logo-new.png"
                alt="TradeTrack Logo"
                class="w-8 h-8 object-contain"
            >

        </div>

    </div>


    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <nav class="flex-1 w-full p-3 space-y-2">

        <!-- Dashboard -->
        <a
            href="../dashboard/index.php"
            title="Dashboard"
            class="
                flex items-center justify-center
                w-full h-12
                rounded-xl
                transition
                <?= $currentSection === 'dashboard'
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-xl">
                📊
            </span>
        </a>


        <!-- Pedagang -->
        <a
            href="../pedagang/index.php"
            title="Pedagang"
            class="
                flex items-center justify-center
                w-full h-12
                rounded-xl
                transition
                <?= $currentSection === 'pedagang'
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-xl">
                👥
            </span>
        </a>


        <!-- Kategori -->
        <a
            href="../kategori/index.php"
            title="Kategori"
            class="
                flex items-center justify-center
                w-full h-12
                rounded-xl
                transition
                <?= $currentSection === 'kategori'
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-xl">
                🏷️
            </span>
        </a>


        <!-- Laporan -->
        <a
            href="../laporan/index.php"
            title="Laporan"
            class="
                flex items-center justify-center
                w-full h-12
                rounded-xl
                transition
                <?= $currentSection === 'laporan'
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-xl">
                📈
            </span>
        </a>


        <!-- Profil -->
        <a
            href="../profile/index.php"
            title="Profil"
            class="
                flex items-center justify-center
                w-full h-12
                rounded-xl
                transition
                <?= $currentSection === 'profile'
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                ?>
            "
        >
            <span class="text-xl">
                👤
            </span>
        </a>

    </nav>


    <!-- =====================================================
         LOGOUT
    ====================================================== -->

    <div
        class="
            w-full
            p-3
            border-t border-slate-100
        "
    >

        <a
            href="../../logout.php"
            title="Logout"
            class="
                flex items-center justify-center
                w-full h-12
                rounded-xl
                text-red-500
                hover:bg-red-50
                transition
            "
        >
            <span class="text-xl">
                🚪
            </span>
        </a>

    </div>

</aside>



<!-- =========================================================
     MOBILE TOP BAR
     Tampil < 768px
========================================================= -->

<div
    class="
        md:hidden
        fixed top-0 left-0 right-0
        z-50
        h-16
        bg-white
        border-b border-slate-200
        flex items-center justify-between
        px-4
    "
>

    <!-- Brand -->
    <div class="flex items-center gap-3">

        <!-- Logo -->
        <div
            class="
                flex items-center justify-center
                w-9 h-9
                shrink-0
                rounded-lg
                bg-white
                border border-slate-200
                shadow-sm
                overflow-hidden
            "
        >

            <img
                src="../../assets/img/logo-new.png"
                alt="TradeTrack Logo"
                class="w-7 h-7 object-contain"
            >

        </div>


        <!-- Brand Text -->
        <div class="min-w-0">

            <h1 class="font-bold text-slate-800 leading-tight">
                TradeTrack
            </h1>

            <p class="text-[10px] text-slate-400 leading-tight mt-0.5">
                Admin Panel
            </p>

        </div>

    </div>


    <!-- Logout -->
    <a
        href="../../logout.php"
        class="
            w-10 h-10
            rounded-xl
            flex items-center justify-center
            text-red-500
            hover:bg-red-50
            transition
        "
        title="Logout"
    >
        🚪
    </a>

</div>



<!-- =========================================================
     MOBILE BOTTOM NAVIGATION
     Tampil < 768px
========================================================= -->

<nav
    class="
        md:hidden
        fixed bottom-0 left-0 right-0
        z-50
        h-16
        bg-white
        border-t border-slate-200
        flex items-center
        justify-around
        px-2
    "
>

    <!-- Dashboard -->
    <a
        href="../dashboard/index.php"
        class="
            flex flex-col items-center justify-center
            gap-0.5
            w-16 h-14
            rounded-xl
            <?= $currentSection === 'dashboard'
                ? 'text-blue-600 bg-blue-50'
                : 'text-slate-500 hover:bg-slate-50'
            ?>
        "
    >
        <span class="text-lg">
            📊
        </span>

        <span class="text-[10px] font-medium">
            Home
        </span>
    </a>


    <!-- Pedagang -->
    <a
        href="../pedagang/index.php"
        class="
            flex flex-col items-center justify-center
            gap-0.5
            w-16 h-14
            rounded-xl
            <?= $currentSection === 'pedagang'
                ? 'text-blue-600 bg-blue-50'
                : 'text-slate-500 hover:bg-slate-50'
            ?>
        "
    >
        <span class="text-lg">
            👥
        </span>

        <span class="text-[10px] font-medium">
            Pedagang
        </span>
    </a>


    <!-- Kategori -->
    <a
        href="../kategori/index.php"
        class="
            flex flex-col items-center justify-center
            gap-0.5
            w-16 h-14
            rounded-xl
            <?= $currentSection === 'kategori'
                ? 'text-blue-600 bg-blue-50'
                : 'text-slate-500 hover:bg-slate-50'
            ?>
        "
    >
        <span class="text-lg">
            🏷️
        </span>

        <span class="text-[10px] font-medium">
            Kategori
        </span>
    </a>


    <!-- Laporan -->
    <a
        href="../laporan/index.php"
        class="
            flex flex-col items-center justify-center
            gap-0.5
            w-16 h-14
            rounded-xl
            <?= $currentSection === 'laporan'
                ? 'text-blue-600 bg-blue-50'
                : 'text-slate-500 hover:bg-slate-50'
            ?>
        "
    >
        <span class="text-lg">
            📈
        </span>

        <span class="text-[10px] font-medium">
            Laporan
        </span>
    </a>


    <!-- Profil -->
    <a
        href="../profile/index.php"
        class="
            flex flex-col items-center justify-center
            gap-0.5
            w-16 h-14
            rounded-xl
            <?= $currentSection === 'profile'
                ? 'text-blue-600 bg-blue-50'
                : 'text-slate-500 hover:bg-slate-50'
            ?>
        "
    >
        <span class="text-lg">
            👤
        </span>

        <span class="text-[10px] font-medium">
            Profil
        </span>
    </a>

</nav>