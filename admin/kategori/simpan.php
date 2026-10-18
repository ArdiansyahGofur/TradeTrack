<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


$namaKategori = trim(
    $_POST['nama_kategori'] ?? ''
);


if ($namaKategori === '') {

    header(
        'Location: tambah.php?error=' .
        urlencode('Nama kategori wajib diisi.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Cek kategori duplikat
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM kategori
    WHERE nama_kategori = ?
    LIMIT 1
");

$stmt->execute([
    $namaKategori
]);


if ($stmt->fetch()) {

    header(
        'Location: tambah.php?error=' .
        urlencode('Kategori tersebut sudah tersedia.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Simpan kategori
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO kategori
    (
        nama_kategori,
        status
    )
    VALUES
    (
        ?,
        'aktif'
    )
");

$stmt->execute([
    $namaKategori
]);


header(
    'Location: index.php?success=' .
    urlencode('Kategori berhasil ditambahkan.')
);

exit;