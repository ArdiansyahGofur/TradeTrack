<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];

$pageTitle = 'Catat Penjualan';


/*
|--------------------------------------------------------------------------
| Ambil menu aktif milik pedagang
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        m.id,
        m.nama_menu,
        m.harga,
        k.nama_kategori
    FROM menu m
    INNER JOIN kategori k
        ON m.kategori_id = k.id
    WHERE m.user_id = ?
      AND m.status = 'aktif'
    ORDER BY m.nama_menu ASC
");

$stmt->execute([$userId]);

$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);


require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>


<!-- =========================================================
     MAIN CONTENT
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
             CONTAINER CENTER
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
                 HEADER
            ================================================== -->

            <div
                class="
                    mb-6
                    md:mb-8
                "
            >

                <a
                    href="index.php"

                    class="
                        inline-flex
                        items-center
                        gap-1

                        text-sm
                        text-blue-600
                        hover:text-blue-700

                        font-medium

                        transition
                    "
                >

                    ← Kembali ke Penjualan

                </a>


                <div class="mt-4">

                    <p
                        class="
                            text-xs
                            sm:text-sm

                            text-blue-600
                            font-semibold

                            mb-1
                        "
                    >
                        Transaksi
                    </p>


                    <h1
                        class="
                            text-2xl
                            sm:text-3xl

                            font-bold
                            text-slate-800

                            tracking-tight
                        "
                    >
                        Catat Penjualan 🛒
                    </h1>


                    <p
                        class="
                            text-sm
                            sm:text-base

                            text-slate-500

                            mt-1

                            leading-relaxed
                        "
                    >
                        Tambahkan satu atau beberapa menu
                        dalam satu transaksi.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 TIDAK ADA MENU
            ================================================== -->

            <?php if (count($menus) === 0): ?>

                <div
                    class="
                        bg-amber-50

                        border
                        border-amber-200

                        rounded-2xl

                        p-4
                        sm:p-5
                        md:p-6

                        shadow-sm

                        w-full
                    "
                >

                    <div
                        class="
                            flex
                            items-start
                            gap-3
                            sm:gap-4
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                shrink-0

                                rounded-xl

                                bg-amber-100

                                flex
                                items-center
                                justify-center

                                text-xl
                            "
                        >
                            ⚠️
                        </div>


                        <div class="min-w-0">

                            <h3
                                class="
                                    font-bold
                                    text-amber-800
                                "
                            >
                                Belum ada menu aktif
                            </h3>


                            <p
                                class="
                                    text-sm
                                    text-amber-700

                                    mt-1

                                    leading-relaxed
                                "
                            >
                                Tambahkan menu aktif terlebih dahulu
                                sebelum mencatat penjualan.
                            </p>


                            <a
                                href="../menu/tambah.php"

                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    mt-4

                                    bg-amber-600
                                    hover:bg-amber-700

                                    text-white
                                    font-semibold

                                    px-4
                                    py-2.5

                                    rounded-xl

                                    transition
                                "
                            >

                                ＋ Tambah Menu

                            </a>

                        </div>

                    </div>

                </div>

            <?php else: ?>


                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    action="simpan.php"
                    method="POST"
                    id="formPenjualan"
                >

                    <!-- CSRF -->

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            csrfToken()
                        ) ?>"
                    >


                    <!-- =================================================
                         TANGGAL
                    ================================================== -->

                    <div
                        class="
                            bg-white

                            border
                            border-slate-200

                            rounded-2xl

                            shadow-sm

                            p-4
                            sm:p-5
                            md:p-6

                            mb-5
                            md:mb-6
                        "
                    >

                        <div class="mb-4">

                            <h2
                                class="
                                    font-bold
                                    text-slate-800
                                "
                            >
                                Tanggal Penjualan
                            </h2>


                            <p
                                class="
                                    text-sm
                                    text-slate-500
                                    mt-1
                                "
                            >
                                Tentukan tanggal transaksi penjualan.
                            </p>

                        </div>


                        <div
                            class="
                                w-full
                                sm:w-64
                                md:w-72
                            "
                        >

                            <label
                                for="tanggal"

                                class="
                                    block

                                    text-sm
                                    font-semibold
                                    text-slate-700

                                    mb-2
                                "
                            >
                                Tanggal
                            </label>


                            <input
                                id="tanggal"

                                type="date"
                                name="tanggal"

                                value="<?= date('Y-m-d') ?>"

                                required

                                class="
                                    w-full

                                    px-4
                                    py-3

                                    border
                                    border-slate-300

                                    rounded-xl

                                    bg-white
                                    text-slate-700

                                    outline-none

                                    focus:ring-2
                                    focus:ring-blue-500
                                    focus:border-blue-500
                                "
                            >

                        </div>

                    </div>


                    <!-- =================================================
                         ITEM PENJUALAN
                    ================================================== -->

                    <div
                        class="
                            bg-white

                            border
                            border-slate-200

                            rounded-2xl

                            shadow-sm

                            overflow-hidden

                            min-w-0
                        "
                    >

                        <!-- =================================================
                             HEADER CARD
                        ================================================== -->

                        <div
                            class="
                                px-4
                                sm:px-5
                                md:px-6

                                py-4
                                sm:py-5

                                border-b
                                border-slate-200

                                flex
                                flex-col

                                sm:flex-row
                                sm:items-center
                                sm:justify-between

                                gap-4
                            "
                        >

                            <div class="min-w-0">

                                <h2
                                    class="
                                        font-bold
                                        text-slate-800
                                    "
                                >
                                    Item Penjualan
                                </h2>


                                <p
                                    class="
                                        text-sm
                                        text-slate-500

                                        mt-1

                                        leading-relaxed
                                    "
                                >
                                    Pilih menu dan jumlah
                                    yang terjual.
                                </p>

                            </div>


                            <button
                                type="button"
                                onclick="tambahBaris()"

                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2

                                    w-full
                                    sm:w-auto

                                    bg-blue-50
                                    hover:bg-blue-100

                                    text-blue-700

                                    font-semibold

                                    px-4
                                    py-2.5

                                    rounded-xl

                                    transition

                                    whitespace-nowrap
                                "
                            >

                                ＋ Tambah Item

                            </button>

                        </div>


                        <!-- =================================================
                             ITEM CONTAINER
                        ================================================== -->

                        <div
                            class="
                                p-3
                                sm:p-4
                                md:p-5
                                lg:p-6

                                min-w-0
                            "
                        >

                            <div
                                id="itemsContainer"

                                class="
                                    space-y-4
                                    min-w-0
                                "
                            >


                                <!-- =================================================
                                     BARIS ITEM PERTAMA
                                ================================================== -->

                                <div
                                    class="
                                        item-row

                                        border
                                        border-slate-200

                                        rounded-2xl

                                        p-3
                                        sm:p-4
                                        md:p-5

                                        bg-slate-50

                                        min-w-0
                                    "
                                >

                                    <!--
                                    -------------------------------------------------
                                    DESKTOP
                                    -------------------------------------------------

                                    lg:
                                    Menu     = 5
                                    Harga    = 2
                                    Jumlah   = 2
                                    Subtotal = 2
                                    Hapus    = 1

                                    TABLET
                                    -------------------------------------------------

                                    md:
                                    Menu     = full
                                    Harga    = 4
                                    Jumlah   = 4
                                    Subtotal = 3
                                    Hapus    = 1

                                    MOBILE
                                    -------------------------------------------------

                                    Semua = full width
                                    -->

                                    <div
                                        class="
                                            grid

                                            grid-cols-1

                                            md:grid-cols-12

                                            gap-3
                                            md:gap-4

                                            items-end
                                        "
                                    >


                                        <!-- =====================================
                                             MENU
                                        ====================================== -->

                                        <div
                                            class="
                                                md:col-span-12
                                                lg:col-span-5

                                                min-w-0
                                            "
                                        >

                                            <label
                                                for="menu_id"

                                                class="
                                                    block

                                                    text-sm
                                                    font-semibold
                                                    text-slate-700

                                                    mb-2
                                                "
                                            >
                                                Menu
                                            </label>


                                            <select
                                                name="menu_id[]"

                                                required

                                                onchange="updateHarga(this)"

                                                class="
                                                    menu-select

                                                    w-full

                                                    min-w-0

                                                    px-3
                                                    sm:px-4
                                                    py-3

                                                    border
                                                    border-slate-300

                                                    rounded-xl

                                                    bg-white

                                                    text-slate-700

                                                    text-sm

                                                    outline-none

                                                    focus:ring-2
                                                    focus:ring-blue-500
                                                    focus:border-blue-500
                                                "
                                            >

                                                <option value="">
                                                    -- Pilih Menu --
                                                </option>


                                                <?php foreach (
                                                    $menus
                                                    as $menu
                                                ): ?>

                                                    <option
                                                        value="<?= (int) $menu['id'] ?>"

                                                        data-harga="<?= (float) $menu['harga'] ?>"
                                                    >

                                                        <?= htmlspecialchars(
                                                            $menu['nama_menu']
                                                        ) ?>

                                                        -

                                                        <?= htmlspecialchars(
                                                            $menu['nama_kategori']
                                                        ) ?>

                                                        (Rp
                                                        <?= number_format(
                                                            $menu['harga'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) ?>)

                                                    </option>

                                                <?php endforeach; ?>

                                            </select>

                                        </div>


                                        <!-- =====================================
                                             HARGA
                                        ====================================== -->

                                        <div
                                            class="
                                                md:col-span-4
                                                lg:col-span-2

                                                min-w-0
                                            "
                                        >

                                            <label
                                                class="
                                                    block

                                                    text-sm
                                                    font-semibold
                                                    text-slate-700

                                                    mb-2
                                                "
                                            >
                                                Harga
                                            </label>


                                            <input
                                                type="text"

                                                class="
                                                    harga-display

                                                    w-full
                                                    min-w-0

                                                    px-3
                                                    sm:px-4
                                                    py-3

                                                    border
                                                    border-slate-300

                                                    rounded-xl

                                                    bg-slate-100

                                                    text-slate-600

                                                    text-sm

                                                    outline-none
                                                "

                                                value="Rp 0"

                                                readonly
                                            >


                                            <input
                                                type="hidden"

                                                name="harga[]"

                                                class="harga-input"

                                                value="0"
                                            >

                                        </div>


                                        <!-- =====================================
                                             JUMLAH
                                        ====================================== -->

                                        <div
                                            class="
                                                md:col-span-4
                                                lg:col-span-2

                                                min-w-0
                                            "
                                        >

                                            <label
                                                for="jumlah"

                                                class="
                                                    block

                                                    text-sm
                                                    font-semibold
                                                    text-slate-700

                                                    mb-2
                                                "
                                            >
                                                Jumlah
                                            </label>


                                            <input
                                                type="number"

                                                name="jumlah[]"

                                                class="
                                                    jumlah-input

                                                    w-full
                                                    min-w-0

                                                    px-3
                                                    sm:px-4
                                                    py-3

                                                    border
                                                    border-slate-300

                                                    rounded-xl

                                                    bg-white

                                                    text-slate-700

                                                    text-sm

                                                    outline-none

                                                    focus:ring-2
                                                    focus:ring-blue-500
                                                    focus:border-blue-500
                                                "

                                                value="1"

                                                min="1"

                                                required
                                            >

                                        </div>


                                        <!-- =====================================
                                             SUBTOTAL
                                        ====================================== -->

                                        <div
                                            class="
                                                md:col-span-3
                                                lg:col-span-2

                                                min-w-0
                                            "
                                        >

                                            <label
                                                class="
                                                    block

                                                    text-sm
                                                    font-semibold
                                                    text-slate-700

                                                    mb-2
                                                "
                                            >
                                                Subtotal
                                            </label>


                                            <div
                                                class="
                                                    subtotal-display

                                                    w-full
                                                    min-w-0

                                                    px-3
                                                    sm:px-4
                                                    py-3

                                                    bg-green-50

                                                    border
                                                    border-green-200

                                                    rounded-xl

                                                    font-bold
                                                    text-green-700

                                                    text-sm

                                                    whitespace-nowrap

                                                    overflow-hidden
                                                    text-ellipsis
                                                "
                                            >
                                                Rp 0
                                            </div>

                                        </div>


                                        <!-- =====================================
                                             HAPUS
                                        ====================================== -->

                                        <div
                                            class="
                                                md:col-span-1
                                                lg:col-span-1

                                                min-w-0
                                            "
                                        >

                                            <button
                                                type="button"

                                                onclick="hapusBaris(this)"

                                                title="Hapus item"

                                                class="
                                                    w-full

                                                    min-h-[46px]

                                                    px-3
                                                    py-3

                                                    rounded-xl

                                                    bg-red-50
                                                    hover:bg-red-100

                                                    text-red-600

                                                    transition

                                                    flex
                                                    items-center
                                                    justify-center
                                                "
                                            >
                                                🗑️
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- =================================================
                                 TOTAL
                            ================================================== -->

                            <div
                                class="
                                    mt-5
                                    sm:mt-6

                                    flex
                                    justify-end

                                    min-w-0
                                "
                            >

                                <div
                                    class="
                                        w-full
                                        sm:w-80
                                        md:w-96

                                        bg-blue-50

                                        border
                                        border-blue-200

                                        rounded-2xl

                                        p-4
                                        sm:p-5

                                        min-w-0
                                    "
                                >

                                    <div
                                        class="
                                            flex
                                            flex-col

                                            sm:flex-row
                                            sm:items-center
                                            sm:justify-between

                                            gap-2
                                        "
                                    >

                                        <span
                                            class="
                                                text-slate-600
                                                font-medium
                                            "
                                        >
                                            Total Transaksi
                                        </span>


                                        <span
                                            id="totalDisplay"

                                            class="
                                                text-xl
                                                sm:text-2xl

                                                font-bold

                                                text-blue-700

                                                break-all
                                            "
                                        >
                                            Rp 0
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         CATATAN
                    ================================================== -->

                    <div
                        class="
                            mt-5
                            md:mt-6

                            bg-white

                            border
                            border-slate-200

                            rounded-2xl

                            shadow-sm

                            p-4
                            sm:p-5
                            md:p-6

                            min-w-0
                        "
                    >

                        <div class="mb-4">

                            <h2
                                class="
                                    font-bold
                                    text-slate-800
                                "
                            >
                                Catatan
                            </h2>


                            <p
                                class="
                                    text-sm
                                    text-slate-500

                                    mt-1

                                    leading-relaxed
                                "
                            >
                                Tambahkan keterangan jika diperlukan.
                            </p>

                        </div>


                        <label
                            for="catatan"

                            class="
                                block

                                text-sm
                                font-semibold
                                text-slate-700

                                mb-2
                            "
                        >

                            Catatan

                            <span
                                class="
                                    font-normal
                                    text-slate-400
                                "
                            >
                                (opsional)
                            </span>

                        </label>


                        <textarea
                            id="catatan"

                            name="catatan"

                            rows="3"

                            maxlength="500"

                            placeholder="Contoh: Pesanan untuk acara kantor..."

                            class="
                                w-full
                                min-w-0

                                px-3
                                sm:px-4
                                py-3

                                border
                                border-slate-300

                                rounded-xl

                                bg-white

                                text-slate-700

                                text-sm

                                outline-none

                                resize-y

                                focus:ring-2
                                focus:ring-blue-500
                                focus:border-blue-500
                            "
                        ></textarea>

                    </div>


                    <!-- =================================================
                         BUTTON ACTION
                    ================================================== -->

                    <div
                        class="
                            mt-5
                            md:mt-6

                            flex
                            flex-col

                            sm:flex-row

                            gap-3
                        "
                    >

                        <a
                            href="index.php"

                            class="
                                inline-flex
                                items-center
                                justify-center

                                flex-1

                                px-5
                                py-3

                                rounded-xl

                                border
                                border-slate-300

                                text-slate-600

                                hover:bg-slate-50

                                font-semibold

                                transition
                            "
                        >
                            Batal
                        </a>


                        <button
                            type="submit"

                            class="
                                inline-flex
                                items-center
                                justify-center

                                flex-1

                                px-5
                                py-3

                                rounded-xl

                                bg-blue-600
                                hover:bg-blue-700

                                text-white

                                font-semibold

                                transition

                                shadow-sm
                            "
                        >
                            💾 Simpan Penjualan
                        </button>

                    </div>


                </form>

            <?php endif; ?>

        </div>

    </main>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

function formatRupiah(angka) {

    return 'Rp ' +
        new Intl.NumberFormat('id-ID').format(angka);

}


/*
|--------------------------------------------------------------------------
| UPDATE HARGA
|--------------------------------------------------------------------------
*/

function updateHarga(select) {

    const row =
        select.closest('.item-row');

    const option =
        select.options[select.selectedIndex];

    const harga =
        parseFloat(
            option.dataset.harga || 0
        );


    const hargaInput =
        row.querySelector('.harga-input');

    const hargaDisplay =
        row.querySelector('.harga-display');


    hargaInput.value = harga;

    hargaDisplay.value =
        formatRupiah(harga);


    hitungSubtotal(row);

}


/*
|--------------------------------------------------------------------------
| HITUNG SUBTOTAL
|--------------------------------------------------------------------------
*/

function hitungSubtotal(row) {

    const harga =
        parseFloat(
            row.querySelector(
                '.harga-input'
            ).value || 0
        );


    const jumlah =
        parseInt(
            row.querySelector(
                '.jumlah-input'
            ).value || 0
        );


    const subtotal =
        harga * jumlah;


    row.querySelector(
        '.subtotal-display'
    ).textContent =
        formatRupiah(subtotal);


    hitungTotal();

}


/*
|--------------------------------------------------------------------------
| HITUNG TOTAL
|--------------------------------------------------------------------------
*/

function hitungTotal() {

    let total = 0;


    document
        .querySelectorAll('.item-row')
        .forEach(function(row) {

            const harga =
                parseFloat(
                    row.querySelector(
                        '.harga-input'
                    ).value || 0
                );


            const jumlah =
                parseInt(
                    row.querySelector(
                        '.jumlah-input'
                    ).value || 0
                );


            total += harga * jumlah;

        });


    const totalDisplay =
        document.getElementById(
            'totalDisplay'
        );


    if (totalDisplay) {

        totalDisplay.textContent =
            formatRupiah(total);

    }

}


/*
|--------------------------------------------------------------------------
| TAMBAH BARIS
|--------------------------------------------------------------------------
*/

function tambahBaris() {

    const container =
        document.getElementById(
            'itemsContainer'
        );


    const firstRow =
        container.querySelector(
            '.item-row'
        );


    const newRow =
        firstRow.cloneNode(true);


    /*
    | Reset menu
    */

    const select =
        newRow.querySelector(
            '.menu-select'
        );

    select.selectedIndex = 0;


    /*
    | Reset harga
    */

    newRow.querySelector(
        '.harga-input'
    ).value = 0;


    newRow.querySelector(
        '.harga-display'
    ).value = 'Rp 0';


    /*
    | Reset jumlah
    */

    newRow.querySelector(
        '.jumlah-input'
    ).value = 1;


    /*
    | Reset subtotal
    */

    newRow.querySelector(
        '.subtotal-display'
    ).textContent = 'Rp 0';


    container.appendChild(newRow);


    hitungTotal();

}


/*
|--------------------------------------------------------------------------
| HAPUS BARIS
|--------------------------------------------------------------------------
*/

function hapusBaris(button) {

    const rows =
        document.querySelectorAll(
            '.item-row'
        );


    if (rows.length <= 1) {

        alert(
            'Minimal harus ada satu item.'
        );

        return;

    }


    button
        .closest('.item-row')
        .remove();


    hitungTotal();

}


/*
|--------------------------------------------------------------------------
| EVENT JUMLAH
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'input',
    function(event) {

        if (
            event.target.classList.contains(
                'jumlah-input'
            )
        ) {

            hitungSubtotal(
                event.target.closest(
                    '.item-row'
                )
            );

        }

    }
);

</script>


<?php require_once '../includes/footer.php'; ?>