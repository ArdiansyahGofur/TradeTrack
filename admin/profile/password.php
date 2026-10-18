<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');

$userId = $_SESSION['user_id'];

$success = '';
$error = '';

/*
|--------------------------------------------------------------------------
| PROSES GANTI PASSWORD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $passwordLama = $_POST['password_lama'] ?? '';
    $passwordBaru = $_POST['password_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if (
        $passwordLama === '' ||
        $passwordBaru === '' ||
        $konfirmasi === ''
    ) {

        $error = 'Semua password wajib diisi.';

    } elseif (strlen($passwordBaru) < 6) {

        $error = 'Password baru minimal 6 karakter.';

    } elseif ($passwordBaru !== $konfirmasi) {

        $error = 'Konfirmasi password tidak sesuai.';

    } else {

        $stmt = $conn->prepare("
            SELECT password
            FROM users
            WHERE id = ?
              AND role = 'admin'
            LIMIT 1
        ");

        $stmt->execute([$userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            $error = 'Data akun tidak ditemukan.';

        } elseif (!password_verify($passwordLama, $user['password'])) {

            $error = 'Password lama tidak benar.';

        } else {

            $passwordHash = password_hash(
                $passwordBaru,
                PASSWORD_DEFAULT
            );

            $stmtUpdate = $conn->prepare("
                UPDATE users
                SET password = ?
                WHERE id = ?
                  AND role = 'admin'
            ");

            $stmtUpdate->execute([
                $passwordHash,
                $userId
            ]);

            $success = 'Password berhasil diubah.';
        }
    }
}


/*
|--------------------------------------------------------------------------
| HEADER & SIDEBAR
|--------------------------------------------------------------------------
*/

$pageTitle = 'Ganti Password';

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
                max-w-5xl
                mx-auto

                px-4
                sm:px-6
                md:px-5
                lg:px-8
                xl:px-10
            "
        >

            <!-- =================================================
                 HEADER
            ================================================== -->

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
                                    w-9
                                    h-9
                                    rounded-xl
                                    bg-blue-100
                                    text-blue-600
                                    flex
                                    items-center
                                    justify-center
                                    shrink-0
                                "
                            >
                                <i class="fa-solid fa-key"></i>
                            </div>

                            <h1
                                class="
                                    text-xl
                                    sm:text-2xl
                                    font-bold
                                    text-slate-800
                                "
                            >
                                Ganti Password
                            </h1>

                        </div>

                        <p
                            class="
                                text-sm
                                text-slate-500
                                ml-0
                                sm:ml-11
                                break-words
                            "
                        >
                            Perbarui password akun administrator Anda.
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
                                w-8
                                h-8
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
                            w-7
                            h-7
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
                            w-7
                            h-7
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


            <!-- =================================================
                 CONTENT
            ================================================== -->

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
                     INFO PASSWORD
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

                    <!-- Icon -->

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-blue-100
                            text-blue-600
                            flex
                            items-center
                            justify-center
                            mb-4
                        "
                    >
                        <i class="fa-solid fa-lock text-lg"></i>
                    </div>


                    <h2
                        class="
                            text-lg
                            font-semibold
                            text-slate-800
                        "
                    >
                        Keamanan Akun
                    </h2>

                    <p
                        class="
                            text-sm
                            text-slate-500
                            mt-1
                            break-words
                        "
                    >
                        Pastikan password akun Anda tetap aman.
                    </p>


                    <!-- Divider -->

                    <div class="my-5 border-t border-slate-100"></div>


                    <!-- Tips -->

                    <div class="space-y-4">

                        <!-- Tip 1 -->

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    w-7
                                    h-7
                                    rounded-lg
                                    bg-blue-50
                                    text-blue-600
                                    flex
                                    items-center
                                    justify-center
                                    shrink-0
                                "
                            >
                                <i class="fa-solid fa-check text-xs"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-medium text-slate-700">
                                    Minimal 6 karakter
                                </p>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-0.5
                                        break-words
                                    "
                                >
                                    Gunakan password dengan panjang yang cukup.
                                </p>

                            </div>

                        </div>


                        <!-- Tip 2 -->

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    w-7
                                    h-7
                                    rounded-lg
                                    bg-blue-50
                                    text-blue-600
                                    flex
                                    items-center
                                    justify-center
                                    shrink-0
                                "
                            >
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-medium text-slate-700">
                                    Gunakan password yang aman
                                </p>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-0.5
                                        break-words
                                    "
                                >
                                    Hindari menggunakan password yang mudah ditebak.
                                </p>

                            </div>

                        </div>


                        <!-- Tip 3 -->

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    w-7
                                    h-7
                                    rounded-lg
                                    bg-blue-50
                                    text-blue-600
                                    flex
                                    items-center
                                    justify-center
                                    shrink-0
                                "
                            >
                                <i class="fa-solid fa-user-shield text-xs"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-medium text-slate-700">
                                    Jangan bagikan password
                                </p>

                                <p
                                    class="
                                        text-xs
                                        text-slate-400
                                        mt-0.5
                                        break-words
                                    "
                                >
                                    Simpan password hanya untuk penggunaan pribadi.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     FORM PASSWORD
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
                            <i class="fa-solid fa-key"></i>
                        </div>

                        <div class="min-w-0">

                            <h2
                                class="
                                    text-lg
                                    font-semibold
                                    text-slate-800
                                "
                            >
                                Password Akun
                            </h2>

                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    mt-1
                                    break-words
                                "
                            >
                                Masukkan password lama dan password baru Anda.
                            </p>

                        </div>

                    </div>


                    <!-- FORM -->

                    <form
                        method="POST"
                        class="space-y-5"
                    >

                        <!-- Password Lama -->

                        <div>

                            <label
                                for="password_lama"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
                                "
                            >
                                Password Lama
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
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <input
                                    id="password_lama"
                                    type="password"
                                    name="password_lama"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password lama"
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


                        <!-- Password Baru -->

                        <div>

                            <label
                                for="password_baru"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
                                "
                            >
                                Password Baru
                            </label>

                            <div class="relative">

                                <i
                                    class="
                                        fa-solid
                                        fa-key
                                        absolute
                                        left-4
                                        top-1/2
                                        -translate-y-1/2
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <input
                                    id="password_baru"
                                    type="password"
                                    name="password_baru"
                                    required
                                    minlength="6"
                                    autocomplete="new-password"
                                    placeholder="Masukkan password baru"
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

                            <p class="mt-2 text-xs text-slate-400">
                                Password baru minimal 6 karakter.
                            </p>

                        </div>


                        <!-- Konfirmasi Password -->

                        <div>

                            <label
                                for="konfirmasi_password"
                                class="
                                    block
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    mb-2
                                "
                            >
                                Konfirmasi Password Baru
                            </label>

                            <div class="relative">

                                <i
                                    class="
                                        fa-solid
                                        fa-circle-check
                                        absolute
                                        left-4
                                        top-1/2
                                        -translate-y-1/2
                                        text-slate-400
                                        text-sm
                                    "
                                ></i>

                                <input
                                    id="konfirmasi_password"
                                    type="password"
                                    name="konfirmasi_password"
                                    required
                                    minlength="6"
                                    autocomplete="new-password"
                                    placeholder="Ulangi password baru"
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
                                Simpan Password
                            </button>


                            <a
                                href="index.php"
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
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>

            </div>


            <!-- =================================================
                 FOOTER INFO
            ================================================== -->

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

                        <p
                            class="
                                text-xs
                                text-slate-500
                                break-words
                            "
                        >
                            Jangan berikan password kepada orang lain.
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