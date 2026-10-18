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
| Ambil status saat ini
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT status
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


/*
|--------------------------------------------------------------------------
| Tentukan status baru
|--------------------------------------------------------------------------
*/

$statusBaru =
    $pedagang['status'] === 'aktif'
    ? 'nonaktif'
    : 'aktif';


/*
|--------------------------------------------------------------------------
| Update status
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE users
    SET status = ?
    WHERE id = ?
    AND role = 'pedagang'
");

$stmt->execute([
    $statusBaru,
    $id
]);


$pesan =
    $statusBaru === 'aktif'
    ? 'Pedagang berhasil diaktifkan.'
    : 'Pedagang berhasil dinonaktifkan.';


header(
    'Location: index.php?success=' .
    urlencode($pesan)
);

exit;