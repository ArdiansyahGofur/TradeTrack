<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


$userId = $_SESSION['user_id'];

$tanggal = $_POST['tanggal'] ?? '';
$menuIds = $_POST['menu_id'] ?? [];
$jumlahs = $_POST['jumlah'] ?? [];
$catatan = trim($_POST['catatan'] ?? '');


if ($tanggal === '') {

    header(
        'Location: tambah.php?error=' .
        urlencode('Tanggal penjualan wajib diisi.')
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Validasi dasar
|--------------------------------------------------------------------------
*/

if (!is_array($menuIds) || !is_array($jumlahs)) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Data penjualan tidak valid.')
    );

    exit;

}


if (count($menuIds) === 0) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Minimal harus ada satu menu.')
    );

    exit;

}


if (count($menuIds) !== count($jumlahs)) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Data item penjualan tidak lengkap.')
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Bersihkan item
|--------------------------------------------------------------------------
*/

$items = [];

foreach ($menuIds as $index => $menuId) {

    $menuId = (int) $menuId;

    $jumlah = (int) ($jumlahs[$index] ?? 0);


    if ($menuId <= 0 || $jumlah <= 0) {
        continue;
    }


    $items[] = [
        'menu_id' => $menuId,
        'jumlah' => $jumlah
    ];

}


if (count($items) === 0) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Item penjualan belum dipilih.')
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Mulai transaksi database
|--------------------------------------------------------------------------
*/

try {

    $conn->beginTransaction();


    $total = 0;

    $detailItems = [];


    /*
    |--------------------------------------------------------------------------
    | Validasi menu dan ambil harga dari database
    |--------------------------------------------------------------------------
    |
    | Harga TIDAK kita percaya dari browser.
    | Harga selalu diambil kembali dari database.
    |
    */

    $stmtMenu = $conn->prepare("
        SELECT
            id,
            nama_menu,
            harga
        FROM menu
        WHERE id = ?
          AND user_id = ?
          AND status = 'aktif'
        LIMIT 1
    ");


    foreach ($items as $item) {

        $stmtMenu->execute([
            $item['menu_id'],
            $userId
        ]);

        $menu = $stmtMenu->fetch();


        if (!$menu) {

            throw new Exception(
                'Salah satu menu tidak ditemukan atau tidak aktif.'
            );

        }


        $harga = (float) $menu['harga'];

        $jumlah = (int) $item['jumlah'];

        $subtotal = $harga * $jumlah;


        $total += $subtotal;


        $detailItems[] = [
            'menu_id' => $menu['id'],
            'harga' => $harga,
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Pastikan total > 0
    |--------------------------------------------------------------------------
    */

    if ($total <= 0) {

        throw new Exception(
            'Total transaksi harus lebih dari 0.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Simpan transaksi utama
    |--------------------------------------------------------------------------
    */

    $stmtPenjualan = $conn->prepare("
        INSERT INTO penjualan
        (
            user_id,
            tanggal,
            total,
            catatan
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )
    ");


    $stmtPenjualan->execute([
        $userId,
        $tanggal,
        $total,
        $catatan !== '' ? $catatan : null
    ]);

    $penjualanId = $conn->lastInsertId();


    /*
    |--------------------------------------------------------------------------
    | Simpan detail transaksi
    |--------------------------------------------------------------------------
    */

    $stmtDetail = $conn->prepare("
        INSERT INTO detail_penjualan
        (
            penjualan_id,
            menu_id,
            harga,
            jumlah,
            subtotal
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");


    foreach ($detailItems as $detail) {

        $stmtDetail->execute([
            $penjualanId,
            $detail['menu_id'],
            $detail['harga'],
            $detail['jumlah'],
            $detail['subtotal']
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | Commit
    |--------------------------------------------------------------------------
    */

    $conn->commit();


    header(
        'Location: index.php?success=' .
        urlencode('Penjualan berhasil dicatat.')
    );

    exit;


} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }


    header(
        'Location: tambah.php?error=' .
        urlencode($e->getMessage())
    );

    exit;

}