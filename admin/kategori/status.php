<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


$id = (int) ($_GET['id'] ?? 0);


if ($id <= 0) {

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| Ambil kategori
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        nama_kategori,
        status
    FROM kategori
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([
    $id
]);

$kategori = $stmt->fetch();


if (!$kategori) {

    header(
        'Location: index.php?error=' .
        urlencode('Kategori tidak ditemukan.')
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Cek apakah kategori digunakan menu
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM menu
    WHERE kategori_id = ?
");

$stmt->execute([
    $id
]);

$totalMenu = $stmt->fetch()['total'];


/*
|--------------------------------------------------------------------------
| Jika sedang aktif dan digunakan menu
|--------------------------------------------------------------------------
*/

if (
    $kategori['status'] === 'aktif' &&
    $totalMenu > 0
) {

    header(
        'Location: index.php?error=' .
        urlencode(
            'Kategori tidak dapat dinonaktifkan karena masih digunakan oleh menu.'
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Tentukan status baru
|--------------------------------------------------------------------------
*/

$statusBaru =
    $kategori['status'] === 'aktif'
    ? 'nonaktif'
    : 'aktif';


/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE kategori
    SET status = ?
    WHERE id = ?
");

$stmt->execute([
    $statusBaru,
    $id
]);


$pesan =
    $statusBaru === 'aktif'
    ? 'Kategori berhasil diaktifkan.'
    : 'Kategori berhasil dinonaktifkan.';


header(
    'Location: index.php?success=' .
    urlencode($pesan)
);

exit;