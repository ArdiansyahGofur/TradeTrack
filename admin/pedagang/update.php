<?php

require_once '../../config/auth.php';
require_once '../../config/database.php';

requireRole('admin');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


$id = (int) ($_POST['id'] ?? 0);

$nama = trim($_POST['nama'] ?? '');

$username = trim($_POST['username'] ?? '');

$no_hp = trim($_POST['no_hp'] ?? '');

$alamat = trim($_POST['alamat'] ?? '');


if (
    $id <= 0 ||
    $nama === '' ||
    $username === ''
) {

    header(
        'Location: index.php?error=' .
        urlencode('Data tidak lengkap.')
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Cek username milik user lain
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM users
    WHERE username = ?
    AND id != ?
    LIMIT 1
");

$stmt->execute([
    $username,
    $id
]);


if ($stmt->fetch()) {

    header(
        'Location: edit.php?id=' . $id .
        '&error=' .
        urlencode('Username sudah digunakan.')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE users

    SET
        nama = ?,
        username = ?,
        no_hp = ?,
        alamat = ?

    WHERE id = ?
    AND role = 'pedagang'
");

$stmt->execute([
    $nama,
    $username,
    $no_hp ?: null,
    $alamat ?: null,
    $id
]);


header(
    'Location: index.php?success=' .
    urlencode('Data pedagang berhasil diperbarui.')
);

exit;