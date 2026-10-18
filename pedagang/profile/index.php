<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];

$success = '';
$error = '';


/* =========================================================
   AMBIL DATA USER
========================================================= */

$stmt = $conn->prepare("
    SELECT
        id,
        nama,
        username,
        no_hp,
        alamat
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    header('Location: ../../logout.php');
    exit;
}


/* =========================================================
   PROSES UPDATE PROFIL
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $noHp     = trim($_POST['no_hp'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');


    /* =====================================================
       VALIDASI
    ===================================================== */

    if ($nama === '' || $username === '') {

        $error = 'Nama dan username wajib diisi.';

    } else {


        /* =================================================
           CEK USERNAME
        ================================================= */

        $stmt = $conn->prepare("
            SELECT id
            FROM users
            WHERE username = ?
              AND id != ?
            LIMIT 1
        ");

        $stmt->execute([
            $username,
            $userId
        ]);

        $usernameExists = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($usernameExists) {

            $error = 'Username sudah digunakan oleh akun lain.';

        } else {


            /* =============================================
               UPDATE USER
            ============================================= */

            $stmt = $conn->prepare("
                UPDATE users
                SET
                    nama = ?,
                    username = ?,
                    no_hp = ?,
                    alamat = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $nama,
                $username,
                $noHp !== '' ? $noHp : null,
                $alamat !== '' ? $alamat : null,
                $userId
            ]);


            /* =============================================
               UPDATE SESSION
            ============================================= */

            $_SESSION['nama'] = $nama;
            $_SESSION['username'] = $username;


            $success = 'Profil berhasil diperbarui.';


            /* =============================================
               UPDATE DATA TAMPILAN
            ============================================= */

            $user['nama']     = $nama;
            $user['username'] = $username;
            $user['no_hp']    = $noHp;
            $user['alamat']   = $alamat;
        }
    }
}


/* =========================================================
   HEADER & SIDEBAR
========================================================= */

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<!-- =========================================================
     PAGE WRAPPER
========================================================= -->

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

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

        <!-- =================================================
             CONTENT
        ================================================== -->

        <div
            class="
                w-full
                max-w-6xl
                mx-auto

                px-4
                sm:px-5
                md:px-6
                lg:px-8
                xl:px-10
            "
        >


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="mb-6 lg:mb-8">

                <div
                    class="
                        flex
                        flex-col
                        sm:flex-row

                        sm:items-end
                        sm:justify-between

                        gap-4
                    "
                >

                    <div class="min-w-0">

                        <!-- BREADCRUMB -->

                        <div
                            class="
                                flex
                                items-center
                                gap-2

                                text-xs
                                sm:text-sm

                                text-slate-400

                                mb-2
                            "
                        >

                            <i class="fa-solid fa-house"></i>

                            <span>/</span>

                            <span class="text-slate-500">
                                Profil
                            </span>

                        </div>


                        <!-- TITLE -->

                        <h1
                            class="
                                text-2xl
                                sm:text-3xl

                                font-bold

                                text-slate-800

                                tracking-tight

                                leading-tight
                            "
                        >
                            Profil Saya
                        </h1>


                        <p
                            class="
                                text-sm
                                sm:text-base

                                text-slate-500

                                mt-1
                            "
                        >
                            Kelola informasi akun dan data pribadi Anda.
                        </p>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 ALERT SUCCESS
            ================================================== -->

            <?php if ($success !== ''): ?>

                <div
                    class="
                        mb-5

                        flex
                        items-start
                        gap-3

                        p-4

                        rounded-2xl

                        bg-emerald-50
                        border
                        border-emerald-200
                    "
                >

                    <div
                        class="
                            w-9
                            h-9

                            rounded-xl

                            bg-emerald-100

                            flex
                            items-center
                            justify-center

                            flex-shrink-0
                        "
                    >

                        <i
                            class="
                                fa-solid
                                fa-check

                                text-emerald-600
                            "
                        ></i>

                    </div>


                    <div class="min-w-0">

                        <p
                            class="
                                font-semibold
                                text-emerald-800
                                text-sm
                            "
                        >
                            Berhasil
                        </p>

                        <p
                            class="
                                text-sm
                                text-emerald-700
                                mt-0.5
                            "
                        >
                            <?= htmlspecialchars($success) ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 ALERT ERROR
            ================================================== -->

            <?php if ($error !== ''): ?>

                <div
                    class="
                        mb-5

                        flex
                        items-start
                        gap-3

                        p-4

                        rounded-2xl

                        bg-red-50
                        border
                        border-red-200
                    "
                >

                    <div
                        class="
                            w-9
                            h-9

                            rounded-xl

                            bg-red-100

                            flex
                            items-center
                            justify-center

                            flex-shrink-0
                        "
                    >

                        <i
                            class="
                                fa-solid
                                fa-exclamation

                                text-red-600
                            "
                        ></i>

                    </div>


                    <div class="min-w-0">

                        <p
                            class="
                                font-semibold
                                text-red-800
                                text-sm
                            "
                        >
                            Terjadi Kesalahan
                        </p>

                        <p
                            class="
                                text-sm
                                text-red-700
                                mt-0.5
                            "
                        >
                            <?= htmlspecialchars($error) ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 PROFILE LAYOUT
            ================================================== -->

            <div
                class="
                    grid
                    grid-cols-1

                    lg:grid-cols-3

                    gap-5
                    lg:gap-6
                "
            >


                <!-- =================================================
                     PROFILE SUMMARY
                ================================================== -->

                <div class="lg:col-span-1">

                    <div
                        class="
                            bg-white

                            border
                            border-slate-200

                            rounded-3xl

                            shadow-sm

                            overflow-hidden
                        "
                    >

                        <!-- COVER -->

                        <div
                            class="
                                h-24
                                sm:h-28

                                bg-gradient-to-r
                                from-blue-600
                                to-indigo-600

                                relative
                            "
                        >

                            <!-- DECORATION -->

                            <div
                                class="
                                    absolute
                                    inset-0

                                    opacity-10
                                "
                            >

                                <div
                                    class="w-full h-full"

                                    style="
                                        background-image:
                                        radial-gradient(
                                            circle at 20% 20%,
                                            white 1px,
                                            transparent 1px
                                        );

                                        background-size: 18px 18px;
                                    "
                                ></div>

                            </div>

                        </div>


                        <!-- PROFILE CONTENT -->

                        <div
                            class="
                                px-5
                                sm:px-6

                                pb-6
                            "
                        >

                            <!-- AVATAR / LOGO PEDAGANG -->

                            <div class="
                                relative
                                -mt-10
                                mb-4
                            ">

                                <div
                                    class="
                                        w-[80px]
                                        h-[80px]

                                        rounded-2xl

                                        bg-white

                                        p-1.5

                                        shadow-lg

                                        ring-1
                                        ring-slate-200
                                    "
                                >

                                    <div
                                        class="
                                            w-full
                                            h-full

                                            rounded-xl

                                            bg-slate-50

                                            flex
                                            items-center
                                            justify-center

                                            overflow-hidden
                                        "
                                    >

                                        <img
                                            src="../../assets/img/logo-pedagang.png"
                                            alt="Logo Pedagang"
                                            class="
                                                w-full
                                                h-full

                                                object-contain

                                                rounded-xl
                                            "
                                        >

                                    </div>

                                </div>

                            </div>


                            <!-- NAME -->

                            <h2
                                class="
                                    text-lg
                                    sm:text-xl

                                    font-bold

                                    text-slate-800

                                    truncate
                                "
                            >
                                <?= htmlspecialchars($user['nama']) ?>
                            </h2>


                            <!-- USERNAME -->

                            <p
                                class="
                                    text-sm
                                    text-slate-500

                                    mt-1

                                    truncate
                                "
                            >
                                @<?= htmlspecialchars($user['username']) ?>
                            </p>


                            <!-- ROLE -->

                            <div class="mt-4">

                                <span
                                    class="
                                        inline-flex
                                        items-center
                                        gap-2

                                        px-3
                                        py-1.5

                                        rounded-full

                                        bg-blue-50
                                        text-blue-700

                                        text-xs
                                        font-semibold
                                    "
                                >

                                    <span
                                        class="
                                            w-1.5
                                            h-1.5

                                            rounded-full

                                            bg-blue-500
                                        "
                                    ></span>

                                    Pedagang

                                </span>

                            </div>


                            <!-- PROFILE DETAILS -->

                            <div
                                class="
                                    mt-5
                                    pt-5

                                    border-t
                                    border-slate-100

                                    space-y-3
                                "
                            >

                                <!-- NAMA -->

                                <div
                                    class="
                                        flex
                                        items-start
                                        gap-3

                                        p-2.5

                                        rounded-xl

                                        hover:bg-slate-50

                                        transition
                                    "
                                >

                                    <div
                                        class="
                                            w-9
                                            h-9

                                            rounded-xl

                                            bg-slate-100

                                            flex
                                            items-center
                                            justify-center

                                            flex-shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-user

                                                text-slate-500
                                                text-sm
                                            "
                                        ></i>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="
                                                text-[11px]
                                                text-slate-400
                                                uppercase
                                                tracking-wide
                                            "
                                        >
                                            Nama Lengkap
                                        </p>

                                        <p
                                            class="
                                                text-sm
                                                font-medium
                                                text-slate-700

                                                truncate

                                                mt-0.5
                                            "
                                        >
                                            <?= htmlspecialchars(
                                                $user['nama']
                                            ) ?>
                                        </p>

                                    </div>

                                </div>


                                <!-- USERNAME -->

                                <div
                                    class="
                                        flex
                                        items-start
                                        gap-3

                                        p-2.5

                                        rounded-xl

                                        hover:bg-slate-50

                                        transition
                                    "
                                >

                                    <div
                                        class="
                                            w-9
                                            h-9

                                            rounded-xl

                                            bg-slate-100

                                            flex
                                            items-center
                                            justify-center

                                            flex-shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-at

                                                text-slate-500
                                                text-sm
                                            "
                                        ></i>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="
                                                text-[11px]
                                                text-slate-400
                                                uppercase
                                                tracking-wide
                                            "
                                        >
                                            Username
                                        </p>

                                        <p
                                            class="
                                                text-sm
                                                font-medium
                                                text-slate-700

                                                truncate

                                                mt-0.5
                                            "
                                        >
                                            <?= htmlspecialchars(
                                                $user['username']
                                            ) ?>
                                        </p>

                                    </div>

                                </div>


                                <!-- NOMOR HP -->

                                <div
                                    class="
                                        flex
                                        items-start
                                        gap-3

                                        p-2.5

                                        rounded-xl

                                        hover:bg-slate-50

                                        transition
                                    "
                                >

                                    <div
                                        class="
                                            w-9
                                            h-9

                                            rounded-xl

                                            bg-slate-100

                                            flex
                                            items-center
                                            justify-center

                                            flex-shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-phone

                                                text-slate-500
                                                text-sm
                                            "
                                        ></i>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="
                                                text-[11px]
                                                text-slate-400
                                                uppercase
                                                tracking-wide
                                            "
                                        >
                                            Nomor HP
                                        </p>

                                        <p
                                            class="
                                                text-sm
                                                font-medium
                                                text-slate-700

                                                truncate

                                                mt-0.5
                                            "
                                        >
                                            <?= htmlspecialchars(
                                                $user['no_hp'] ?: '-'
                                            ) ?>
                                        </p>

                                    </div>

                                </div>


                                <!-- ALAMAT -->

                                <div
                                    class="
                                        flex
                                        items-start
                                        gap-3

                                        p-2.5

                                        rounded-xl

                                        hover:bg-slate-50

                                        transition
                                    "
                                >

                                    <div
                                        class="
                                            w-9
                                            h-9

                                            rounded-xl

                                            bg-slate-100

                                            flex
                                            items-center
                                            justify-center

                                            flex-shrink-0
                                        "
                                    >

                                        <i
                                            class="
                                                fa-solid
                                                fa-location-dot

                                                text-slate-500
                                                text-sm
                                            "
                                        ></i>

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p
                                            class="
                                                text-[11px]
                                                text-slate-400
                                                uppercase
                                                tracking-wide
                                            "
                                        >
                                            Alamat
                                        </p>

                                        <p
                                            class="
                                                text-sm
                                                font-medium
                                                text-slate-700

                                                line-clamp-2

                                                mt-0.5
                                            "
                                        >
                                            <?= htmlspecialchars(
                                                $user['alamat'] ?: '-'
                                            ) ?>
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     FORM EDIT
                ================================================== -->

                <div class="lg:col-span-2">

                    <div
                        class="
                            bg-white

                            border
                            border-slate-200

                            rounded-3xl

                            shadow-sm

                            overflow-hidden
                        "
                    >

                        <!-- FORM HEADER -->

                        <div
                            class="
                                px-5
                                sm:px-6
                                md:px-7

                                py-5

                                border-b
                                border-slate-100
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

                                        rounded-xl

                                        bg-blue-50

                                        flex
                                        items-center
                                        justify-center

                                        flex-shrink-0
                                    "
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-user-pen

                                            text-blue-600
                                        "
                                    ></i>

                                </div>


                                <div class="min-w-0">

                                    <h2
                                        class="
                                            text-base
                                            sm:text-lg

                                            font-bold
                                            text-slate-800
                                        "
                                    >
                                        Informasi Akun
                                    </h2>

                                    <p
                                        class="
                                            text-xs
                                            sm:text-sm

                                            text-slate-500

                                            mt-0.5
                                        "
                                    >
                                        Perbarui informasi akun Anda.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- FORM -->

                        <form
                            method="POST"

                            class="
                                p-5
                                sm:p-6
                                md:p-7
                            "
                        >

                            <div
                                class="
                                    grid

                                    grid-cols-1

                                    md:grid-cols-2

                                    gap-4
                                    md:gap-5
                                "
                            >


                                <!-- NAMA -->

                                <div class="md:col-span-2">

                                    <label
                                        for="nama"

                                        class="
                                            block

                                            text-sm
                                            font-semibold
                                            text-slate-700

                                            mb-2
                                        "
                                    >

                                        Nama Lengkap

                                        <span class="text-red-500">
                                            *
                                        </span>

                                    </label>


                                    <div class="relative">

                                        <div
                                            class="
                                                absolute
                                                left-4
                                                top-1/2

                                                -translate-y-1/2

                                                text-slate-400

                                                pointer-events-none
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-user
                                                    text-sm
                                                "
                                            ></i>

                                        </div>


                                        <input
                                            id="nama"

                                            type="text"

                                            name="nama"

                                            value="<?= htmlspecialchars(
                                                $user['nama']
                                            ) ?>"

                                            required

                                            autocomplete="name"

                                            class="
                                                w-full

                                                pl-11
                                                pr-4

                                                py-3

                                                rounded-xl

                                                border
                                                border-slate-200

                                                bg-slate-50

                                                text-slate-700

                                                text-sm

                                                outline-none

                                                focus:bg-white

                                                focus:border-blue-500

                                                focus:ring-4
                                                focus:ring-blue-50

                                                transition
                                            "
                                        >

                                    </div>

                                </div>


                                <!-- USERNAME -->

                                <div>

                                    <label
                                        for="username"

                                        class="
                                            block

                                            text-sm
                                            font-semibold
                                            text-slate-700

                                            mb-2
                                        "
                                    >

                                        Username

                                        <span class="text-red-500">
                                            *
                                        </span>

                                    </label>


                                    <div class="relative">

                                        <div
                                            class="
                                                absolute
                                                left-4
                                                top-1/2

                                                -translate-y-1/2

                                                text-slate-400

                                                pointer-events-none
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-at
                                                    text-sm
                                                "
                                            ></i>

                                        </div>


                                        <input
                                            id="username"

                                            type="text"

                                            name="username"

                                            value="<?= htmlspecialchars(
                                                $user['username']
                                            ) ?>"

                                            required

                                            autocomplete="username"

                                            class="
                                                w-full

                                                pl-11
                                                pr-4

                                                py-3

                                                rounded-xl

                                                border
                                                border-slate-200

                                                bg-slate-50

                                                text-slate-700

                                                text-sm

                                                outline-none

                                                focus:bg-white

                                                focus:border-blue-500

                                                focus:ring-4
                                                focus:ring-blue-50

                                                transition
                                            "
                                        >

                                    </div>


                                    <p
                                        class="
                                            text-[11px]
                                            sm:text-xs

                                            text-slate-400

                                            mt-2
                                        "
                                    >
                                        Username digunakan untuk login.
                                    </p>

                                </div>


                                <!-- NOMOR HP -->

                                <div>

                                    <label
                                        for="no_hp"

                                        class="
                                            block

                                            text-sm
                                            font-semibold
                                            text-slate-700

                                            mb-2
                                        "
                                    >
                                        Nomor HP
                                    </label>


                                    <div class="relative">

                                        <div
                                            class="
                                                absolute
                                                left-4
                                                top-1/2

                                                -translate-y-1/2

                                                text-slate-400

                                                pointer-events-none
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-phone
                                                    text-sm
                                                "
                                            ></i>

                                        </div>


                                        <input
                                            id="no_hp"

                                            type="text"

                                            name="no_hp"

                                            value="<?= htmlspecialchars(
                                                $user['no_hp'] ?? ''
                                            ) ?>"

                                            placeholder="081234567890"

                                            autocomplete="tel"

                                            class="
                                                w-full

                                                pl-11
                                                pr-4

                                                py-3

                                                rounded-xl

                                                border
                                                border-slate-200

                                                bg-slate-50

                                                text-slate-700

                                                text-sm

                                                outline-none

                                                focus:bg-white

                                                focus:border-blue-500

                                                focus:ring-4
                                                focus:ring-blue-50

                                                transition
                                            "
                                        >

                                    </div>

                                </div>


                                <!-- ALAMAT -->

                                <div class="md:col-span-2">

                                    <label
                                        for="alamat"

                                        class="
                                            block

                                            text-sm
                                            font-semibold
                                            text-slate-700

                                            mb-2
                                        "
                                    >
                                        Alamat
                                    </label>


                                    <div class="relative">

                                        <div
                                            class="
                                                absolute
                                                left-4
                                                top-4

                                                text-slate-400

                                                pointer-events-none
                                            "
                                        >

                                            <i
                                                class="
                                                    fa-solid
                                                    fa-location-dot
                                                    text-sm
                                                "
                                            ></i>

                                        </div>


                                        <textarea
                                            id="alamat"

                                            name="alamat"

                                            rows="4"

                                            placeholder="Masukkan alamat lengkap..."

                                            class="
                                                w-full

                                                pl-11
                                                pr-4

                                                py-3

                                                rounded-xl

                                                border
                                                border-slate-200

                                                bg-slate-50

                                                text-slate-700

                                                text-sm

                                                outline-none

                                                focus:bg-white

                                                focus:border-blue-500

                                                focus:ring-4
                                                focus:ring-blue-50

                                                transition

                                                resize-none
                                            "
                                        ><?= htmlspecialchars(
                                            $user['alamat'] ?? ''
                                        ) ?></textarea>

                                    </div>

                                </div>

                            </div>


                            <!-- =================================================
                                 FORM FOOTER
                            ================================================== -->

                            <div
                                class="
                                    mt-6
                                    pt-5

                                    border-t
                                    border-slate-100

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
                                        gap-2

                                        text-xs
                                        text-slate-400

                                        min-w-0
                                    "
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-circle-info

                                            mt-0.5

                                            flex-shrink-0
                                        "
                                    ></i>

                                    <span>
                                        Kolom bertanda
                                        <span class="text-red-500">*</span>
                                        wajib diisi.
                                    </span>

                                </div>


                                <!-- BUTTON -->

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

                                        bg-blue-600
                                        hover:bg-blue-700
                                        active:bg-blue-800

                                        text-white

                                        rounded-xl

                                        font-semibold

                                        text-sm

                                        shadow-sm

                                        hover:shadow

                                        transition-all
                                    "
                                >

                                    <i
                                        class="
                                            fa-solid
                                            fa-floppy-disk
                                        "
                                    ></i>

                                    <span>
                                        Simpan Perubahan
                                    </span>

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


<?php

require_once '../includes/footer.php';

?>