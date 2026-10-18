<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];

$pageTitle = 'Penjualan';


/*
|--------------------------------------------------------------------------
| Ambil transaksi milik pedagang
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.tanggal,
        p.total,
        p.catatan,
        COUNT(dp.id) AS jumlah_item
    FROM penjualan p
    LEFT JOIN detail_penjualan dp
        ON p.id = dp.penjualan_id
    WHERE p.user_id = ?
    GROUP BY
        p.id,
        p.tanggal,
        p.total,
        p.catatan
    ORDER BY p.tanggal DESC
");

$stmt->execute([$userId]);

$penjualan = $stmt->fetchAll();


require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<main class="flex-1 min-w-0">

    <div class="p-6 md:p-8">

        <!-- Header -->

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between
                    gap-4 mb-8">

            <div>

                <p class="text-sm text-blue-600 font-semibold mb-1">
                    Transaksi
                </p>

                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                    Penjualan 🛒
                </h1>

                <p class="text-slate-500 mt-1">
                    Catat dan lihat transaksi penjualan kamu.
                </p>

            </div>


            <a href="tambah.php"
               class="inline-flex items-center justify-center gap-2
                      bg-blue-600 hover:bg-blue-700
                      text-white font-semibold
                      px-5 py-3 rounded-xl
                      transition shadow-sm">

                <span>＋</span>

                Catat Penjualan

            </a>

        </div>


        <!-- Success -->

        <?php if (isset($_GET['success'])): ?>

            <div class="mb-6 p-4 rounded-xl
                        bg-green-50 border border-green-200
                        text-green-700">

                ✅ <?= htmlspecialchars($_GET['success']) ?>

            </div>

        <?php endif; ?>


        <!-- Error -->

        <?php if (isset($_GET['error'])): ?>

            <div class="mb-6 p-4 rounded-xl
                        bg-red-50 border border-red-200
                        text-red-700">

                ⚠️ <?= htmlspecialchars($_GET['error']) ?>

            </div>

        <?php endif; ?>


        <!-- Statistik sederhana -->

        <div class="grid grid-cols-1 sm:grid-cols-2
                    gap-4 mb-6">

            <div class="bg-white border border-slate-200
                        rounded-2xl p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Total Transaksi
                </p>

                <p class="text-2xl font-bold text-slate-800 mt-1">
                    <?= count($penjualan) ?>
                </p>

            </div>


            <div class="bg-white border border-slate-200
                        rounded-2xl p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Total Pendapatan
                </p>

                <p class="text-2xl font-bold text-green-600 mt-1">

                    Rp
                    <?= number_format(
                        array_sum(array_column($penjualan, 'total')),
                        0,
                        ',',
                        '.'
                    ) ?>

                </p>

            </div>

        </div>


        <!-- Table -->

        <div class="bg-white rounded-2xl
                    border border-slate-200
                    shadow-sm overflow-hidden">

            <?php if (count($penjualan) > 0): ?>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-slate-50
                                      border-b border-slate-200">

                            <tr>

                                <th class="text-left px-6 py-4
                                           font-semibold text-slate-600">
                                    Tanggal
                                </th>

                                <th class="text-left px-6 py-4
                                           font-semibold text-slate-600">
                                    Item
                                </th>

                                <th class="text-left px-6 py-4
                                           font-semibold text-slate-600">
                                    Catatan
                                </th>

                                <th class="text-right px-6 py-4
                                           font-semibold text-slate-600">
                                    Total
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            <?php foreach ($penjualan as $row): ?>

                                <tr class="hover:bg-slate-50 transition">

                                    <td class="px-6 py-4">

                                        <div class="font-medium text-slate-800">

                                            <?= date(
                                                'd M Y',
                                                strtotime($row['tanggal'])
                                            ) ?>

                                        </div>

                                        <div class="text-xs text-slate-400 mt-1">

                                            <?= date(
                                                'H:i',
                                                strtotime($row['tanggal'])
                                            ) ?>

                                            WIB

                                        </div>

                                    </td>


                                    <td class="px-6 py-4">

                                        <span class="inline-flex
                                                     px-3 py-1
                                                     rounded-full
                                                     bg-blue-50
                                                     text-blue-700
                                                     text-xs
                                                     font-semibold">

                                            <?= (int) $row['jumlah_item'] ?>
                                            item

                                        </span>

                                    </td>


                                    <td class="px-6 py-4 text-slate-500">

                                        <?php if (!empty($row['catatan'])): ?>

                                            <?= htmlspecialchars($row['catatan']) ?>

                                        <?php else: ?>

                                            <span class="text-slate-400">
                                                -
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td class="px-6 py-4">

                                        <div class="flex items-center justify-between gap-4">

                                            <span class="font-bold text-slate-800">

                                                Rp
                                                <?= number_format(
                                                    $row['total'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </span>


                                            <a href="detail.php?id=<?= $row['id'] ?>"
                                            class="inline-flex items-center gap-1
                                                    px-3 py-2
                                                    rounded-lg
                                                    bg-blue-50
                                                    text-blue-700
                                                    hover:bg-blue-100
                                                    font-semibold
                                                    transition">

                                                🔎 Detail

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="py-16 px-6 text-center">

                    <div class="text-5xl mb-4">
                        🛒
                    </div>

                    <h3 class="text-lg font-bold text-slate-800 mb-2">
                        Belum ada transaksi
                    </h3>

                    <p class="text-slate-500 mb-6">
                        Mulai catat penjualan pertamamu.
                    </p>

                    <a href="tambah.php"
                       class="inline-flex items-center gap-2
                              bg-blue-600 hover:bg-blue-700
                              text-white font-semibold
                              px-5 py-3 rounded-xl">

                        ＋ Catat Penjualan

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

<?php require_once '../includes/footer.php'; ?>