<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];

$namaMenu   = trim($_POST['nama_menu'] ?? '');
$kategoriId = (int) ($_POST['kategori_id'] ?? 0);
$harga      = $_POST['harga'] ?? '';


/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

if ($namaMenu === '') {
    header('Location: tambah.php?error=' . urlencode('Nama menu wajib diisi.'));
    exit;
}

if ($kategoriId <= 0) {
    header('Location: tambah.php?error=' . urlencode('Kategori wajib dipilih.'));
    exit;
}

if (!is_numeric($harga) || (float) $harga <= 0) {
    header('Location: tambah.php?error=' . urlencode('Harga harus lebih dari 0.'));
    exit;
}

$harga = (float) $harga;


/*
|--------------------------------------------------------------------------
| Cek kategori aktif
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM kategori
    WHERE id = ?
      AND status = 'aktif'
    LIMIT 1
");

$stmt->execute([$kategoriId]);

if (!$stmt->fetch()) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Kategori tidak tersedia atau sudah nonaktif.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Simpan menu
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO menu
    (
        user_id,
        kategori_id,
        nama_menu,
        harga,
        status
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        'aktif'
    )
");

$stmt->execute([
    $userId,
    $kategoriId,
    $namaMenu,
    $harga
]);


header(
    'Location: index.php?success=' .
    urlencode('Menu berhasil ditambahkan.')
);

exit;