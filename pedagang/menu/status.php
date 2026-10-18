<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('pedagang');

$userId = $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {

    header(
        'Location: index.php?error=' .
        urlencode('Menu tidak valid.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Ambil menu milik pedagang
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id, status
    FROM menu
    WHERE id = ?
      AND user_id = ?
    LIMIT 1
");

$stmt->execute([
    $id,
    $userId
]);

$menu = $stmt->fetch();

if (!$menu) {

    header(
        'Location: index.php?error=' .
        urlencode('Menu tidak ditemukan atau bukan milik Anda.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Toggle status
|--------------------------------------------------------------------------
*/

$newStatus = ($menu['status'] === 'aktif')
    ? 'nonaktif'
    : 'aktif';


$stmt = $conn->prepare("
    UPDATE menu
    SET status = ?
    WHERE id = ?
      AND user_id = ?
");

$stmt->execute([
    $newStatus,
    $id,
    $userId
]);


$message = $newStatus === 'aktif'
    ? 'Menu berhasil diaktifkan.'
    : 'Menu berhasil dinonaktifkan.';


header(
    'Location: index.php?success=' .
    urlencode($message)
);

exit;