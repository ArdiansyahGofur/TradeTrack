<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');

$userId = $_SESSION['user_id'];

$success = '';
$error = '';

/*
|--------------------------------------------------------------------------
| AMBIL DATA PROFILE
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id, nama, username, no_hp, alamat
    FROM users
    WHERE id = ?
      AND role = 'admin'
    LIMIT 1
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../dashboard/index.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE PROFILE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $noHp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nama === '' || $username === '') {

        $error = 'Nama dan username wajib diisi.';

    } else {

        // Cek username
        $stmtCheck = $conn->prepare("
            SELECT id
            FROM users
            WHERE username = ?
              AND id != ?
            LIMIT 1
        ");

        $stmtCheck->execute([
            $username,
            $userId
        ]);

        $usernameExists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($usernameExists) {

            $error = 'Username sudah digunakan. Silakan gunakan username lain.';

        } else {

            // Update data
            $stmtUpdate = $conn->prepare("
                UPDATE users
                SET nama = ?,
                    username = ?,
                    no_hp = ?,
                    alamat = ?
                WHERE id = ?
                  AND role = 'admin'
            ");

            $stmtUpdate->execute([
                $nama,
                $username,
                $noHp !== '' ? $noHp : null,
                $alamat !== '' ? $alamat : null,
                $userId
            ]);

            // Update session
            $_SESSION['nama'] = $nama;
            $_SESSION['username'] = $username;

            $success = 'Profil berhasil diperbarui.';

            // Ambil ulang data
            $stmt = $conn->prepare("
                SELECT id, nama, username, no_hp, alamat
                FROM users
                WHERE id = ?
                  AND role = 'admin'
                LIMIT 1
            ");

            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

$pageTitle = 'Profil Saya';

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<div class="min-h-screen w-full bg-slate-50 overflow-x-hidden">

    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->

    <main
        class="
            min-h-screen
            min-w-0
            w-full

            pt-20
            pb-24

            md:ml-20
            md:w-[calc(100%-5rem)]

            lg:ml-64
            lg:w-[calc(100%-16rem)]

            md:pt-8
            md:pb-8

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
                md:px-5
                lg:px-8
                xl:px-10
            "
        >

            <!-- =====================================================
                 PAGE HEADER
            ====================================================== -->

            <div class="mb-6">

                <div
                    class="
                        flex
                        flex-col
                        gap-4

                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <div class="min-w-0">

                        <div class="flex items-center gap-2 mb-1">

                            <div
                                class="
                                    w-9 h-9
                                    rounded-xl
                                    bg-blue-100
                                    text-blue-600
                                    flex
                                    items-center
                                    justify-center
                                    shrink-0
                                "
                            >
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <h1
                                class="
                                    text-xl
                                    sm:text-2xl
                                    font-bold
                                    text-slate-800
                                "
                            >
                                Profil Saya
                            </h1>

                        </div>

                        <p
                            class="
                                text-sm
                                text-slate-500
                                ml-0
                                sm:ml-11
                            "
                        >
                            Kelola informasi profil akun administrator.
                        </p>

                    </div>


                    <!-- Role -->

                    <div
                        class="
                            inline-flex
                            items-center
                            gap-2
                            self-start
                            sm:self-auto

                            px-3
                            py-2
                            rounded-xl

                            bg-white
                            border
                            border-slate-200
                            shadow-sm

                            shrink-0
                        "
                    >

                        <div
                            class="
                                w-8 h-8
                                rounded-lg
                                bg-blue-100
                                text-blue-600
                                flex
                                items-center
                                justify-center
                                shrink-0
                            "
                        >
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>

                        <div>

                            <p class="text-xs text-slate-400">
                                Role
                            </p>

                            <p class="text-sm font-semibold text-slate-700">
                                Administrator
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 ALERT SUCCESS
            ====================================================== -->

            <?php if ($success !== ''): ?>

                <div
                    class="
                        mb-5
                        flex
                        items-start
                        gap-3
                        rounded-xl
                        border
                        border-green-200
                        bg-green-50
                        px-4
                        py-3
                    "
                >

                    <div
                        class="
                            mt-0.5
                            w-7 h-7
                            rounded-lg
                            bg-green-100
                            text-green-600
                            flex
                            items-center
                            justify-center
                            shrink-0
                        "
                    >
                        <i class="fa-solid fa-check text-sm"></i>
                    </div>

                    <div class="min-w-0">

                        <p class="text-sm font-semibold text-green-800">
                            Berhasil
                        </p>

                        <p class="text-sm text-green-700 mt-0.5 break-words">
                            <?= htmlspecialchars($success) ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 ALERT ERROR
            ====================================================== -->

            <?php if ($error !== ''): ?>

                <div
                    class="
                        mb-5
                        flex
                        items-start
                        gap-3
                        rounded-xl
                        border
                        border-red-200
                        bg-red-50
                        px-4
                        py-3
                    "
                >

                    <div
                        class="
                            mt-0.5
                            w-7 h-7
                            rounded-lg
                            bg-red-100
                            text-red-600
                            flex
                            items-center
                            justify-center
                            shrink-0
                        "
                    >
                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                    </div>

                    <div class="min-w-0">

                        <p class="text-sm font-semibold text-red-800">
                            Terjadi Kesalahan
                        </p>

                        <p class="text-sm text-red-700 mt-0.5 break-words">
                            <?= htmlspecialchars($error) ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =====================================================
                 CONTENT
            ====================================================== -->

            <div
                class="
                    grid
                    grid-cols-1
                    md:grid-cols-2
                    lg:grid-cols-3
                    gap-5
                    lg:gap-6
                "
            >


                <!-- =================================================
                     INFORMASI AKUN
                ================================================== -->

                <div
                    class="
                        bg-white
                        rounded-2xl
                        border
                        border-slate-200
                        shadow-sm
                        p-5
                        sm:p-6
                    "
                >

                    <div class="flex items-center gap-4">

                        <!-- Avatar Admin -->

                        <div
                            class="
                                w-14
                                h-14
                                sm:w-16
                                sm:h-16

                                rounded-2xl

                                bg-white

                                p-1

                                shadow-sm
                                ring-1
                                ring-slate-200

                                flex
                                items-center
                                justify-center

                                shrink-0

                                overflow-hidden
                            "
                        >

                            <img
                                src="../../assets/img/logo-admin.png"
                                alt="Profil Admin"
                                class="
                                    w-full
                                    h-full

                                    rounded-xl

                                    object-contain
                                "
                            >

                        </div>


                        <!-- Identity -->

                        <div class="min-w-0 flex-1">

                            <h2
                                class="
                                    font-semibold
                                    text-slate-800
                                    truncate
                                "
                            >
                                <?= htmlspecialchars($user['nama']) ?>
                            </h2>

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    truncate
                                "
                            >
                                <?= htmlspecialchars($user['username']) ?>
                            </p>

                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    mt-2
                                    px-2.5
                                    py-1
                                    rounded-full
                                    text-xs
                                    font-medium
                                    bg-blue-100
                                    text-blue-700
                                "
                            >
                                <i class="fa-solid fa-user-shield text-[10px]"></i>
                                Administrator
                            </span>

                        </div>

                    </div>


                    <!-- Divider -->

                    <div class="my-6 border-t border-slate-100"></div>


                    <!-- Status -->

                    <div>

                        <p
                            class="
                                text-xs
                                text-slate-400
                                uppercase
                                tracking-wide
                            "
                        >
                            Status Akun
                        </p>

                        <div class="flex items-center gap-2 mt-2">

                            <span
                                class="
                                    w-2
                                    h-2
                                    rounded-full
                                    bg-green-500
                                    shrink-0
                                "
                            ></span>

                            <p
                                class="
                                    text-sm
                                    font-medium
                                    text-green-600
                                "
                            >
                                Aktif
                            </p>

                        </div>

                    </div>


                    <!-- Username -->

                    <div class="mt-5">

                        <p
                            class="
                                text-xs
                                text-slate-400
                                uppercase
                                tracking-wide
                            "
                        >
                            Username
                        </p>

                        <p
                            class="
                                mt-1
                                text-sm
                                font-medium
                                text-slate-700
                                break-all
                            "
                        >
                            <?= htmlspecialchars($user['username']) ?>
                        </p>

                    </div>


                    <!-- Nomor HP -->

                    <div class="mt-5">

                        <p
                            class="
                                text-xs
                                text-slate-400
                                uppercase
                                tracking-wide
                            "
                        >
                            Nomor HP
                        </p>

                        <p
                            class="
                                mt-1
                                text-sm
                                font-medium
                                text-slate-700
                                break-words
                            "
                        >
                            <?= htmlspecialchars($user['no_hp'] ?: '-') ?>
                        </p>

                    </div>

                </div>


                <!-- =================================================
                     FORM PROFIL
                ================================================== -->

                <div
                    class="
                        md:col-span-1
                        lg:col-span-2

                        bg-white
                        rounded-2xl
                        border
                        border-slate-200
                        shadow-sm

                        p-5
                        sm:p-6
                    "
                >

                    <!-- Form Header -->

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                            mb-6
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                rounded-xl
                                bg-blue-50
                                text-blue-600
                                flex
                                items-center
                                justify-center
                                shrink-0
                            "
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>

                        <div class="min-w-0">

                            <h2
                                class="
                                    text-lg
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Informasi Profil
                            </h2>

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    mt-1
                                    break-words
                                "
                            >
                                Perbarui informasi akun administrator Anda.
                            </p>

                        </div>

                    </div>


                    <!-- FORM -->

                    <form
                        method="POST"
                        class="space-y-5"
                    >


                        <!-- Nama -->

                        <div>

                            <label
                                for="nama"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
                                "
                            >
                                Nama Lengkap
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
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <input
                                    id="nama"
                                    type="text"
                                    name="nama"
                                    value="<?= htmlspecialchars($user['nama']) ?>"
                                    required
                                    maxlength="100"
                                    autocomplete="name"
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-300
                                        bg-white
                                        pl-11
                                        pr-4
                                        py-2.5
                                        text-sm
                                        text-slate-700
                                        outline-none
                                        transition
                                        focus:border-blue-500
                                        focus:ring-2
                                        focus:ring-blue-100
                                    "
                                >

                            </div>

                        </div>


                        <!-- Username -->

                        <div>

                            <label
                                for="username"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
                                "
                            >
                                Username
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
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="<?= htmlspecialchars($user['username']) ?>"
                                    required
                                    maxlength="50"
                                    autocomplete="username"
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-300
                                        bg-white
                                        pl-11
                                        pr-4
                                        py-2.5
                                        text-sm
                                        text-slate-700
                                        outline-none
                                        transition
                                        focus:border-blue-500
                                        focus:ring-2
                                        focus:ring-blue-100
                                    "
                                >

                            </div>

                        </div>


                        <!-- Nomor HP -->

                        <div>

                            <label
                                for="no_hp"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
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
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <input
                                    id="no_hp"
                                    type="text"
                                    name="no_hp"
                                    value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>"
                                    maxlength="20"
                                    inputmode="tel"
                                    autocomplete="tel"
                                    placeholder="Contoh: 081234567890"
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-300
                                        bg-white
                                        pl-11
                                        pr-4
                                        py-2.5
                                        text-sm
                                        text-slate-700
                                        outline-none
                                        transition
                                        focus:border-blue-500
                                        focus:ring-2
                                        focus:ring-blue-100
                                    "
                                >

                            </div>

                        </div>


                        <!-- Alamat -->

                        <div>

                            <label
                                for="alamat"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
                                "
                            >
                                Alamat
                            </label>

                            <div class="relative">

                                <i
                                    class="
                                        fa-solid
                                        fa-location-dot
                                        absolute
                                        left-4
                                        top-3.5
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <textarea
                                    id="alamat"
                                    name="alamat"
                                    rows="4"
                                    placeholder="Masukkan alamat..."
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-300
                                        bg-white
                                        pl-11
                                        pr-4
                                        py-2.5
                                        text-sm
                                        text-slate-700
                                        outline-none
                                        transition
                                        focus:border-blue-500
                                        focus:ring-2
                                        focus:ring-blue-100
                                        resize-none
                                    "
                                ><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>

                            </div>

                        </div>


                        <!-- BUTTON -->

                        <div
                            class="
                                flex
                                flex-col
                                sm:flex-row
                                gap-3
                                pt-2
                            "
                        >

                            <button
                                type="submit"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2
                                    px-5
                                    py-2.5
                                    rounded-xl
                                    bg-blue-600
                                    text-white
                                    text-sm
                                    font-medium
                                    hover:bg-blue-700
                                    active:bg-blue-800
                                    transition
                                    w-full
                                    sm:w-auto
                                "
                            >
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Perubahan
                            </button>


                            <a
                                href="password.php"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2
                                    px-5
                                    py-2.5
                                    rounded-xl
                                    border
                                    border-slate-300
                                    bg-white
                                    text-slate-700
                                    text-sm
                                    font-medium
                                    hover:bg-slate-50
                                    transition
                                    w-full
                                    sm:w-auto
                                "
                            >
                                <i class="fa-solid fa-key"></i>
                                Ganti Password
                            </a>

                        </div>

                    </form>

                </div>

            </div>


            <!-- =====================================================
                 FOOTER INFO
            ====================================================== -->

            <div class="mt-6">

                <div
                    class="
                        flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-2
                        px-4
                        py-3
                        rounded-xl
                        bg-white
                        border
                        border-slate-200
                    "
                >

                    <div class="flex items-center gap-2 min-w-0">

                        <i
                            class="
                                fa-solid
                                fa-circle-info
                                text-blue-500
                                text-sm
                                shrink-0
                            "
                        ></i>

                        <p class="text-xs text-slate-500 break-words">
                            Pastikan informasi profil Anda selalu diperbarui.
                        </p>

                    </div>

                    <p
                        class="
                            text-xs
                            text-slate-400
                            shrink-0
                        "
                    >
                        TradeTrack Administrator
                    </p>

                </div>

            </div>

        </div>

    </main>

</div>

<?php require_once '../includes/footer.php'; ?>