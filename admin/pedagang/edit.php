<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


$id = (int) ($_GET['id'] ?? 0);


if ($id <= 0) {

    header('Location: index.php');
    exit;

}


$stmt = $conn->prepare("
    SELECT
        id,
        nama,
        username,
        no_hp,
        alamat,
        status
    FROM users
    WHERE id = ?
    AND role = 'pedagang'
    LIMIT 1
");

$stmt->execute([$id]);

$pedagang = $stmt->fetch();


if (!$pedagang) {

    header(
        'Location: index.php?error=' .
        urlencode('Pedagang tidak ditemukan.')
    );

    exit;

}


$pageTitle = 'Edit Pedagang';

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<main class="flex-1 min-w-0">

    <header
        class="bg-white
               border-b border-slate-200
               px-6 py-4"
    >

        <p class="text-sm text-slate-400">
            Pedagang
        </p>

        <h2 class="text-xl font-bold text-slate-800">
            Edit Pedagang
        </h2>

    </header>


    <div class="p-6">

        <div
            class="max-w-3xl
                   bg-white
                   border border-slate-200
                   rounded-2xl
                   shadow-sm
                   p-6"
        >

            <div class="mb-6">

                <h1
                    class="text-xl
                           font-bold
                           text-slate-800"
                >
                    Edit Data Pedagang ✏️
                </h1>

            </div>


            <?php if (isset($_GET['error'])): ?>

                <div
                    class="mb-5
                           bg-red-50
                           border border-red-200
                           text-red-700
                           rounded-xl
                           px-4 py-3
                           text-sm"
                >
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>

            <?php endif; ?>


            <form
                action="update.php"
                method="POST"
                class="space-y-5"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= $pedagang['id'] ?>"
                >


                <!-- Nama -->

                <div>

                    <label
                        class="block
                               text-sm
                               font-semibold
                               text-slate-700
                               mb-2"
                    >
                        Nama Pedagang
                    </label>

                    <input
                        type="text"
                        name="nama"
                        required
                        maxlength="100"
                        value="<?= htmlspecialchars(
                            $pedagang['nama']
                        ) ?>"
                        class="w-full
                               border border-slate-200
                               rounded-xl
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-200
                               focus:border-blue-500"
                    >

                </div>


                <!-- Username -->

                <div>

                    <label
                        class="block
                               text-sm
                               font-semibold
                               text-slate-700
                               mb-2"
                    >
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        required
                        maxlength="50"
                        value="<?= htmlspecialchars(
                            $pedagang['username']
                        ) ?>"
                        class="w-full
                               border border-slate-200
                               rounded-xl
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-200
                               focus:border-blue-500"
                    >

                </div>


                <!-- No HP -->

                <div>

                    <label
                        class="block
                               text-sm
                               font-semibold
                               text-slate-700
                               mb-2"
                    >
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        maxlength="20"
                        value="<?= htmlspecialchars(
                            $pedagang['no_hp'] ?? ''
                        ) ?>"
                        class="w-full
                               border border-slate-200
                               rounded-xl
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-200
                               focus:border-blue-500"
                    >

                </div>


                <!-- Alamat -->

                <div>

                    <label
                        class="block
                               text-sm
                               font-semibold
                               text-slate-700
                               mb-2"
                    >
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        rows="4"
                        class="w-full
                               border border-slate-200
                               rounded-xl
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-200
                               focus:border-blue-500"
                    ><?= htmlspecialchars(
                        $pedagang['alamat'] ?? ''
                    ) ?></textarea>

                </div>


                <div
                    class="flex
                           flex-col
                           sm:flex-row
                           gap-3
                           pt-3"
                >

                    <a
                        href="index.php"
                        class="px-5 py-3
                               rounded-xl
                               border border-slate-200
                               text-slate-600
                               hover:bg-slate-50
                               text-center
                               font-semibold"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="px-5 py-3
                               rounded-xl
                               bg-blue-600
                               hover:bg-blue-700
                               text-white
                               font-semibold"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

<?php

require_once '../includes/footer.php';

?>