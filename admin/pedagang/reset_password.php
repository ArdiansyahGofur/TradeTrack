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
| Pastikan pedagang tersedia
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id, nama
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
| Password sementara
|--------------------------------------------------------------------------
|
| Untuk tahap pengembangan:
| Password baru = username + 123
|
*/

$stmt = $conn->prepare("
    SELECT username
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$user = $stmt->fetch();


$passwordBaru = $user['username'] . '123';

$passwordHash = password_hash(
    $passwordBaru,
    PASSWORD_DEFAULT
);


/*
|--------------------------------------------------------------------------
| Update password
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE users
    SET password = ?
    WHERE id = ?
    AND role = 'pedagang'
");

$stmt->execute([
    $passwordHash,
    $id
]);


$pesan =
    'Password berhasil direset. Password sementara: ' .
    $passwordBaru;


header(
    'Location: index.php?success=' .
    urlencode($pesan)
);

exit;