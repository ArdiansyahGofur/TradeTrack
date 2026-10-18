<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];

$id         = (int) ($_POST['id'] ?? 0);
$namaMenu   = trim($_POST['nama_menu'] ?? '');
$kategoriId = (int) ($_POST['kategori_id'] ?? 0);
$harga      = $_POST['harga'] ?? '';


if ($id <= 0) {
    header('Location: index.php?error=' . urlencode('Menu tidak valid.'));
    exit;
}

if ($namaMenu === '') {
    header(
        'Location: edit.php?id=' . $id .
        '&error=' . urlencode('Nama menu wajib diisi.')
    );
    exit;
}

if ($kategoriId <= 0) {
    header(
        'Location: edit.php?id=' . $id .
        '&error=' . urlencode('Kategori wajib dipilih.')
    );
    exit;
}

if (!is_numeric($harga) || (float) $harga <= 0) {
    header(
        'Location: edit.php?id=' . $id .
        '&error=' . urlencode('Harga harus lebih dari 0.')
    );
    exit;
}

$harga = (float) $harga;


/*
|--------------------------------------------------------------------------
| Pastikan menu memang milik pedagang
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM menu
    WHERE id = ?
      AND user_id = ?
    LIMIT 1
");

$stmt->execute([
    $id,
    $userId
]);

if (!$stmt->fetch()) {

    header(
        'Location: index.php?error=' .
        urlencode('Anda tidak memiliki akses ke menu tersebut.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Pastikan kategori valid
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM kategori
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$kategoriId]);

if (!$stmt->fetch()) {

    header(
        'Location: edit.php?id=' . $id .
        '&error=' . urlencode('Kategori tidak ditemukan.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE menu
    SET
        kategori_id = ?,
        nama_menu = ?,
        harga = ?
    WHERE id = ?
      AND user_id = ?
");

$stmt->execute([
    $kategoriId,
    $namaMenu,
    $harga,
    $id,
    $userId
]);


header(
    'Location: index.php?success=' .
    urlencode('Menu berhasil diperbarui.')
);

exit;