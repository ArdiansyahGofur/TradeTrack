<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


$id = (int) ($_POST['id'] ?? 0);

$namaKategori = trim(
    $_POST['nama_kategori'] ?? ''
);


if (
    $id <= 0 ||
    $namaKategori === ''
) {

    header(
        'Location: index.php?error=' .
        urlencode('Data kategori tidak lengkap.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Cek duplikat
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM kategori
    WHERE nama_kategori = ?
    AND id != ?
    LIMIT 1
");

$stmt->execute([
    $namaKategori,
    $id
]);


if ($stmt->fetch()) {

    header(
        'Location: edit.php?id=' .
        $id .
        '&error=' .
        urlencode('Nama kategori sudah digunakan.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE kategori

    SET nama_kategori = ?

    WHERE id = ?
");

$stmt->execute([
    $namaKategori,
    $id
]);


header(
    'Location: index.php?success=' .
    urlencode('Kategori berhasil diperbarui.')
);

exit;